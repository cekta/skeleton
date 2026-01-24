FROM php:8.4-cli-alpine AS base
WORKDIR /app
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=ghcr.io/roadrunner-server/roadrunner:2025.1.2 /usr/bin/rr /usr/local/bin/rr
RUN install-php-extensions sockets

FROM base AS dev
RUN install-php-extensions @composer \
    && apk add --no-cache make \
    && cat <<'EOF' > /usr/bin/app-dev \
    && chmod +x /usr/bin/app-dev
#!/usr/bin/env sh

composer install
./app.php migrate
exec rr serve
EOF
CMD ["/usr/bin/app-dev"]

FROM dev AS builder
COPY composer.json composer.lock ./
RUN composer install
COPY ./ ./
RUN composer before-build-checks \
    && composer install -a --no-dev # remove dev from build \
    && CEKTA_MODE=build ./app.php # finish compile

FROM base AS prod
ARG RR_SERVER_COMMAND_ARG="php -d opcache.enable_cli=1 app.php"
ARG RR_POOL_DEBUG_ARG=false
ARG RR_LOGS_ENCODING_ARG=json
ARG RR_LOGS_MODE_ARG=production
ARG RR_LOGS_LEVEL_ARG=error
ENV RR_SERVER_COMMAND=$RR_SERVER_COMMAND_ARG \
    RR_POOL_DEBUG=$RR_POOL_DEBUG_ARG \
    RR_LOGS_ENCODING=$RR_LOGS_ENCODING_ARG \
    RR_LOGS_MODE=$RR_LOGS_MODE_ARG \
    RR_LOGS_LEVEL=$RR_LOGS_LEVEL_ARG

COPY --from=builder /app /app

CMD ["rr", "serve", "-s"]