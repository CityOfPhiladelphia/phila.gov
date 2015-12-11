#!/bin/bash

echo 'Copying .env to ~/.ssh/environment'
cp .env ~/.ssh/environment

echo 'Permitting user env vars for ssh deploys'
echo 'PermitUserEnvironment yes' | sudo tee -a /etc/ssh/sshd_config > /dev/null
sudo service ssh restart
