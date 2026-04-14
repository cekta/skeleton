#!/usr/bin/env sh

set -e

if [ "$*" = "builder" ]; then
  composer install
  composer build
else 
  exec "$@"
fi