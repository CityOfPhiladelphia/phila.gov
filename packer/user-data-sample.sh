#!/bin/bash

cat > /home/ubuntu/.ssh/environment <<'EOF'
APP_ROOT=/home/ubuntu/phila.gov
WP_SITEURL=http://example.com
WP_HOME=http://example.com
WP_DEBUG=0
WP_AUTH_KEY=uniquePhrase
WP_SECURE_AUTH_KEY=uniquePhrase
WP_LOGGED_IN_KEY=uniquePhrase
WP_NONCE_KEY=uniquePhrase
WP_AUTH_SALT=uniquePhrase
WP_SECURE_AUTH_SALT=uniquePhrase
WP_LOGGED_IN_SALT=uniquePhrase
WP_NONCE_SALT=uniquePhrase
AWS_ACCESS_KEY_ID=accessKey
AWS_SECRET_ACCESS_KEY=accessSecretKey
S3_BUCKET=bucketName
SWIFTYPE_ID=swiftypeId
COMPOSER_URL=192.0.3.4
DB_NAME=wp
DB_USER=wp
DB_PASSWORD=dbPass
DB_HOST=dbmachine.example.com
HTTPS=on
ROBOTS_DISALLOW=/
EOF
