#!/bin/bash

set -e

echo 'Syncing to target machine'
rsync -rz --delete --exclude=".*" --delete-excluded ./ target:phila.gov

echo 'Executing deploy in app directory on target'
ssh target 'cd phila.gov && scripts/deploy.sh'
