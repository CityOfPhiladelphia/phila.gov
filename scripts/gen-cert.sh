#!/bin/bash

echo "Generating SSL certificate"
openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/self-signed.key \
  -out /etc/ssl/certs/self-signed.crt \
  -subj "/C=US/ST=Pennsylvania/L=Philadelphia/O=City of Philadelphia/OU=Office of Innovation and Technology/CN=$HOSTNAME"
