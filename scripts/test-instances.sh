#!/bin/bash

printf "\n/*Debug true on test instances */\ndefine('WP_DEBUG', true);" >> /home/ubuntu/app/wp/wp-config.php

echo 'Executing test-instances.sh'
