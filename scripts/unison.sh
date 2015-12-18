#!/bin/bash
#
# Script for installing Unison (http://www.cis.upenn.edu/~bcpierce/unison/)

export DEBIAN_FRONTEND=noninteractive
sudo apt-get install -y exuberant-ctags gcc make

unison_version="unison-2.48.3"
ocaml_version="ocaml-4.02.3"

cd /tmp

wget http://caml.inria.fr/pub/distrib/ocaml-4.02/$ocaml_version.tar.gz
tar xzf $ocaml_version.tar.gz
cd $ocaml_version
./configure
make -s world opt
sudo make -s install
cd -

wget http://www.seas.upenn.edu/~bcpierce/unison/download/releases/stable/$unison_version.tar.gz
tar xzf $unison_version.tar.gz
cd $unison_version
make -s
sudo cp -v unison /usr/local/bin/
cd -
