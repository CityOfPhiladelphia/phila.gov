# joia config

joia_install () {
  # Set up .ssh/environment -> phila.gov/.env
  joia_ssh "sudo phila.gov/scripts/user-env.sh"

  # Alias for JOIA_HOSTNAME
  joia_ssh "echo PUBLIC_HOSTNAME=$JOIA_HOSTNAME >> phila.gov/.env"

  # Run install scripts
  joia_ssh "
cd phila.gov
sudo -E scripts/install.sh
scripts/gen-cert.sh
scripts/local-db.sh
scripts/wp-config.sh
"
}

joia_deploy () {
  joia_ssh "cd phila.gov; scripts/deploy.sh"
  echo "Deployed to https://$JOIA_HOSTNAME"
}
