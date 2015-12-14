#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
sudo -E apt-get install -y mysql-server-5.6

echo "Resetting database (creating if not yet)"
wp db reset --yes

echo "Loading database dump into database"
_dir="$(dirname "$0")"
curl -s "$("$_dir/s3url.sh" "$PHILA_DB_BUCKET" current.sql)" | wp db import -
