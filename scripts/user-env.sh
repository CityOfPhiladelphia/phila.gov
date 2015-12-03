#!/bin/bash

echo 'Linking .joia/config to .ssh/environment'
cd .ssh
ln -fs ../phila.gov/.joia/config environment

echo 'Permitting user env vars for ssh deploys'
echo 'PermitUserEnvironment yes' >> /etc/ssh/sshd_config
service ssh restart
