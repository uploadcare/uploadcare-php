#!/usr/bin/env bash

DIR=$(dirname "$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )" )
VERSIONS=("5.6-fpm-alpine" "7.0-fpm-alpine" "7.4-fpm-alpine")
COMMAND=$(cat <<EOF
curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer && \
rm -rf vendor composer.lock && \
composer install -q
EOF
)

for i in "${VERSIONS[@]}" ; do
    echo "Tests with php:${i}:"

    docker run -t --rm \
        -v "${DIR}":/opt/app \
        -w /opt/app \
        "php:${i}" sh -c "$(echo -e "${COMMAND}") && vendor/bin/phpunit"
done
