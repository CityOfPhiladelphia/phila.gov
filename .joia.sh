# joia config

joia_install () {
  # Set up .ssh/environment
  joia_ssh "scripts/user-env.sh"

  # Run install scripts
  joia_ssh "
scripts/install.sh
scripts/gen-cert.sh
scripts/wp-config.sh
scripts/local-db.sh
scripts/unison.sh
"
}

joia_deploy () {
  joia_ssh "scripts/deploy.sh"
  echo "Deployed to https://$DOMAIN"
}
