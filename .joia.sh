# joia config

joia_install () {
  joia_ssh "
    scripts/install.sh
    scripts/wp-config.sh
  "

  if [ "$PHILA_TEST" ]; then
    joia_ssh "
      scripts/gen-cert.sh
      scripts/local-db.sh
      scripts/unison.sh
    "
  fi
}

joia_deploy () {
  joia_ssh "scripts/deploy.sh"
}
