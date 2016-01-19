# joia config

joia_install () {
  # Set up .ssh/environment
  joia_ssh "scripts/user-env.sh"

  # Run install scripts
  if [ "$PHILA_TEST" ]; then
    joia_ssh "
      scripts/install.sh
      scripts/gen-cert.sh
      scripts/wp-config.sh
      scripts/local-db.sh
      scripts/unison.sh
    "
  else
    joia_ssh "
      scripts/install.sh
      scripts/wp-config.sh
    "
  fi
}

joia_deploy () {
  joia_ssh "scripts/deploy.sh"
}
