#!/bin/bash

export DEBIAN_FRONTEND=noninteractive
apt-get update
# Don't apt-get upgrade http://stackoverflow.com/a/15093460/589391
apt-get install -y mysql-server-5.6 nginx php5-fpm

# TODO cd to static files directory and
# python -m SimpleHTTPServer
