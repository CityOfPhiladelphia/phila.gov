#!/bin/bash

_dir="$(dirname "$0")"

export DEBIAN_FRONTEND=noninteractive

sudo apt-get update
sudo apt-get Dpkg::Options::="--force-confdef" -o Dpkg::Options::="--force-confold" dist-upgrade

echo "Allowing hq access"
echo 'ssh-rsa AAAAB3NzaC1yc2EAAAADAQABAAABAQDde/ohFVNJyZgI3KXXt9uwipFNpu0oVFxZzeht/WAu9PRuiqOh9PUCHT+Ca/R1y70ltrO8OqGjOkmbmxBt99nvncxF8LPjQUG1rUePDOqOGNWs/d56rhLAsVM7cWXZG7xdLkJt8c5VrtXHsbRzaJ28RRwqT6C/x+ZPtx7POl/x1t8gNGeagAbbS3hq5O77ymHe4lukgcz4K5TuU8y36fH0Qp1Doe3exCDaN2DdBIz/OXYQru6vO5yRWvxMUTkxjh7GO1qsn0efWEfknrZekINNck/wP1hbvbP9xjwntyhOjxLAQrwYF6nH1iNY6J+hW/0qYrFdZqpC7dL+cmm+XVUH hq' >> ~/.ssh/authorized_keys

if [ "$PHILA_TEST" ]; then
  $_dir/unison.sh
  $_dir/gen-cert.sh
  $_dir/wp-config.sh
  $_dir/local-db.sh
fi
