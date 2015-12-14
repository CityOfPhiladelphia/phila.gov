#!/bin/bash
#
# Script for installing Unison (http://www.cis.upenn.edu/~bcpierce/unison/)

export DEBIAN_FRONTEND=noninteractive
sudo apt-get install -y exuberant-ctags make ocaml-native-compilers

version=2.48.3

cd /tmp
wget http://www.seas.upenn.edu/~bcpierce/unison//download/releases/stable/unison-$version.tar.gz
tar xzf unison-$version.tar.gz
cd unison-$version
make -s
sudo cp -v unison /usr/local/bin/
