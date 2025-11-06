FROM php:8.4-cli as dev
WORKDIR /app
COPY --from=ghcr.io/mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=ghcr.io/roadrunner-server/roadrunner:2025.1.2 /usr/bin/rr /usr/local/bin/rr
RUN install-php-extensions @composer sockets
CMD ["/app/run.sh"]