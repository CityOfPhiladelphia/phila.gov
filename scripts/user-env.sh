#!/bin/bash

echo 'Linking .env to .ssh/environment'
cd .ssh
ln -fs ../phila.gov/.env environment

echo 'Permitting user env vars for ssh deploys'
echo 'PermitUserEnvironment yes' >> /etc/ssh/sshd_config
service ssh restart
