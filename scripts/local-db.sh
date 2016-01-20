#!/bin/bash

# Limit mysql memory use for install
# https://mariadb.com/blog/starting-mysql-low-memory-virtual-machines
sudo mkdir -p /etc/mysql/conf.d
cat << EOF | sudo tee /etc/mysql/conf.d/low_mem.cnf > /dev/null
[mysqld]
performance_schema = off
EOF

export DEBIAN_FRONTEND=noninteractive
sudo -E apt-get install -y mysql-server-5.6

echo "Resetting database (creating if not yet)"
wp db reset --yes

echo "Loading database dump into database"
_dir="$(dirname "$0")"
curl -s "$("$_dir/s3url.sh" "$PHILA_DB_BUCKET" current.sql)" | wp db import -
