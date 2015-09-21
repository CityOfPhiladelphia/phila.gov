#!/bin/bash

set -e

# Deploy to target machine identified in .ssh/config
rsync -rz --delete --exclude=.git ./ target:phila.gov

# Execute deploy script from the app directory
ssh target 'cd phila.gov && scripts/deploy.sh'
