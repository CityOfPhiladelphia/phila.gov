#!/bin/bash

set -e

echo 'Running WP CLI deploy steps...'
wp rewrite flush
wp core update-db
