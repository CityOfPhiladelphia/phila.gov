#!/bin/bash

set -e

# Set up ssh
openssl aes-256-cbc -K $encrypted_16a3424d8998_key -iv $encrypted_16a3424d8998_iv -in .travis/philagov2.pem.enc -out ~/.ssh/philagov2.pem -d
chmod 400 ~/.ssh/philagov2.pem
cat >> ~/.ssh/config <<EOF
Host *
  User ubuntu
Host master
  HostName $STAG
  IdentityFile ~/.ssh/philagov2.pem
Host production
  HostName $PROD
  IdentityFile ~/.ssh/philagov2.pem
EOF
ssh-keyscan $STAG >> ~/.ssh/known_hosts 2> /dev/null
ssh-keyscan $PROD >> ~/.ssh/known_hosts 2> /dev/null
