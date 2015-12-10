#!/bin/bash

sudo apt-get install -y mysql-server-5.6

echo "Resetting database (creating if not yet)"
wp db reset --yes

echo "Loading database dump into database"
dirn="$(dirname "$0")"
curl -s "$("$dirn/s3url.sh" "$PHILA_DB_BUCKET" current.sql)" | wp db import -
