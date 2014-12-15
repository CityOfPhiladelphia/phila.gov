#!/bin/bash
export HOME=/root
cd /phila.gov
/usr/local/bin/composer install
# Run it again for the styles
/usr/local/bin/composer install
