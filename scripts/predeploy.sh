#!/bin/bash

export DEBIAN_FRONTEND=noninteractive

sudo apt-get update
sudo apt-get -yq upgrade
sudo apt-get autoremove
sudo du -sh /var/cache/apt
sudo apt-get autoclean
sudo rm -r /var/log/*
source /home/ubuntu/.bashrc