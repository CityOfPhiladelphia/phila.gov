#!/bin/bash
#
# Run this script to build a particular version of Unison with a particular
# version of OCaml (needs to match those installed on dev machines). Then
# upload the file to S3 for the unison.sh script to grab it.

export DEBIAN_FRONTEND=noninteractive
sudo apt-get install -y exuberant-ctags gcc make

ocaml_minor_version="ocaml-4.02"
ocaml_version="ocaml-4.02.3"
unison_version="unison-2.48.3"

wget http://caml.inria.fr/pub/distrib/$ocaml_minor_version/$ocaml_version.tar.gz
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
