# joia config

joia_install () {
  joia_ssh "sudo phila.gov/scripts/user-env.sh"
  joia_ssh "echo PUBLIC_HOSTNAME=$JOIA_HOSTNAME >> phila.gov/.env"
  joia_ssh "sudo -E phila.gov/scripts/install.sh"
  joia_ssh "cd phila.gov; scripts/gen-cert.sh; scripts/local-db.sh; scripts/wp-config.sh"
}

joia_deploy () {
  joia_ssh "cd phila.gov; scripts/deploy.sh"
  echo "Deployed to https://$JOIA_HOSTNAME"
}
