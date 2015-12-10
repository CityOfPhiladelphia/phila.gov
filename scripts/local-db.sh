#!/bin/bash

apt-get install -y mysql-server-5.6

echo "Loading database dump into database"
mysql -uroot <<EOF
DROP DATABASE IF EXISTS wp;
CREATE DATABASE wp;
EOF

dirn="$(dirname "$0")"
curl -s "$("$dirn/s3url.sh" "$PHILA_DB_BUCKET" current.sql)" | wp db import -
