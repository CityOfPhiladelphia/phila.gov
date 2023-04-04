#!/bin/bash

FILE=/home/ubuntu/.ssh/environment
if test -f "$FILE"; then
		source $FILE
		echo "location ^~ /wp-login.php {
			return 301 $SSO_LOGIN;
		}

		location /wp-login {
			return 301 $SSO_LOGIN;
		}
    " > '/home/ubuntu/app/nginx/server.d/sso.conf'
fi