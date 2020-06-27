#!/usr/bin/env bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

docker stop uploadcare_swagger || true && docker rm uploadcare_swagger || true

docker run \
    -d \
    --name 'uploadcare_swagger' \
    --rm \
    -p 8080:8080 \
    -e SWAGGER_JSON=/swgData/upload.json \
    -v "${DIR}/api-spec":/swgData \
    swaggerapi/swagger-ui
