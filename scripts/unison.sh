#!/bin/bash
#
# Script for downloading unison command for file syncing with joia

_dir="$(dirname "$0")"

curl -s "$("$_dir/s3url.sh" "$PHILA_DEPLOY_BUCKET" bin/unison)" | sudo tee /usr/local/bin/unison > /dev/null
sudo chmod 755 /usr/local/bin/unison
