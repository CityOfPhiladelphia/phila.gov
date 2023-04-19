echo 'Running clean machine'
sudo apt-get autoremove
sudo apt-get autoclean
sudo find /var/log -type f -name '*[0-9]*' -delete
echo 'Done clean machine'