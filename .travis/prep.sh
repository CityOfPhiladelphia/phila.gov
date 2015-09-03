#!/bin/bash

set -e

# Set up ssh
openssl aes-256-cbc -K $encrypted_16a3424d8998_key -iv $encrypted_16a3424d8998_iv -in .travis/deploy.pem.enc -out .travis/deploy.pem -d
chmod 400 .travis/deploy.pem
eval `ssh-agent`
ssh-add .travis/deploy.pem
echo -e "Host master\n  User ubuntu\n  HostName $STAG\nHost production\n  User ubuntu\n  HostName $PROD" >> ~/.ssh/config
ssh-keyscan $STAG >> ~/.ssh/known_hosts 2> /dev/null
ssh-keyscan $PROD >> ~/.ssh/known_hosts 2> /dev/null
