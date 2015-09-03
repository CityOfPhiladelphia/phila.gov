#!/bin/bash

set -e

# Set up ssh
openssl aes-256-cbc -K $encrypted_16a3424d8998_key -iv $encrypted_16a3424d8998_iv -in .travis/deploy.pem.enc -out ~/.ssh/id_rsa -d
chmod 400 ~/.ssh/id_rsa
cat >> ~/.ssh/config <<EOF
Host *
  User ubuntu
Host master
  HostName $STAG
Host production
  HostName $PROD
EOF
ssh-keyscan $STAG >> ~/.ssh/known_hosts 2> /dev/null
ssh-keyscan $PROD >> ~/.ssh/known_hosts 2> /dev/null
