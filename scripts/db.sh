#!/bin/bash

echo "Loading database dump into database"
mysql -uroot <<EOF
DROP DATABASE IF EXISTS wp;
CREATE DATABASE wp;
EOF

aws s3 cp s3://$PHILA_DB_BUCKET/current.sql - | mysql -uroot wp
