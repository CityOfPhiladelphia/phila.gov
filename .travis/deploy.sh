#!/bin/bash

set -e

# $TRAVIS_BRANCH mapped to dest in .ssh/config
rsync -rz --delete --exclude=".git" ./ $TRAVIS_BRANCH:phila.gov

# Execute root and ubuntu user deploy scripts from the app directory
ssh $TRAVIS_BRANCH 'cd phila.gov && .travis/remote.sh'
