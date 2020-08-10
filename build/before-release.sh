#!/usr/bin/env bash

set -e

# run tests
./vendor/bin/phpunit

# webpack build
yarn prod

# generate ide helpers
php build/generate-ide-helper.php
