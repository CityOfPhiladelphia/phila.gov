sudo apt-get autoremove
sudo du -sh /var/cache/apt
sudo apt-get autoclean
find /var/log -type f -name '*[0-9]*' -delete