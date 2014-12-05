#!/bin/bash
rm -rf $1
ln -s ../../repos/`basename $1` $1
