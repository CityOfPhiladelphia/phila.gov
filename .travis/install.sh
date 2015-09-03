#!/bin/bash

set -e

# Pull in plugins, etc. with composer
composer self-update 1.0.0-alpha10
composer config -g repositories.private composer $COMPOSER_REPOSITORY_URL
composer install
