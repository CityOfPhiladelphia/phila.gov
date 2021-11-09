#!/bin/bash

FILE=/home/ubuntu/.ssh/environment
if test -f "$FILE"; then
		source $FILE
		echo "location ^~ /wp-login.php {
  return 301 https://github.com/CityOfPhiladelphia/phila.gov;
}

location /wp-login {
  return 301 https://github.com/CityOfPhiladelphia/phila.gov;
}
" > '/home/ubuntu/app/nginx/server.d/sso.conf'
fi