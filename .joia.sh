# joia config

joia_install () {
  joia_ssh "scripts/install.sh"

  if [ "$PHILA_TEST" ]; then
    joia_ssh "
      scripts/gen-cert.sh
      scripts/wp-config.sh
      scripts/local-db.sh
      scripts/unison.sh
    "
  fi
}

joia_deploy () {
  joia_ssh "scripts/deploy.sh"
}
