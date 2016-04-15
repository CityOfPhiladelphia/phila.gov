#!/bin/bash

sed -i "/^define('FS_METHOD', 'direct');/a /*Debug true on test instances */\ndefine('WP_DEBUG', true);" /home/ubuntu/app/wp/wp-config.php

echo 'Executing test-instances.sh'
