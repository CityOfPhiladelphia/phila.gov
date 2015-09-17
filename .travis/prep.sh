#!/bin/bash

set -e

mkdir -p ~/.aws
cat > ~/.aws/config <<EOF
[default]
aws_access_key_id = $AWS_ID
aws_secret_access_key = $AWS_SECRET
output = text
region = us-east-1
EOF

IP=`aws ec2 describe-instances --filters \
  "Name=tag:Branch,Values=$TRAVIS_BRANCH" \
  "Name=tag:Project,Values=phila.gov" | \
  grep '^INSTANCES' | cut -f13`

# Only deploy if we have an IP
if [ -z "$IP" ]; then exit 1; fi

# Set up ssh
openssl aes-256-cbc -K $encrypted_16a3424d8998_key -iv $encrypted_16a3424d8998_iv -in .travis/philagov2.pem.enc -out ~/.ssh/philagov2.pem -d
chmod 400 ~/.ssh/philagov2.pem
cat >> ~/.ssh/config <<EOF
Host target
  User ubuntu
  HostName $IP
  IdentityFile ~/.ssh/philagov2.pem
EOF
ssh-keyscan $IP >> ~/.ssh/known_hosts 2> /dev/null
