#!/bin/bash

bucket=$1
key=$2

encode() {
  local string="$1"
  local strlen=${#string}
  local encoded=""

  for (( pos=0 ; pos<strlen ; pos++ )); do
     c=${string:$pos:1}
     case "$c" in
        [-_.~a-zA-Z0-9] ) o="$c" ;;
        * )               printf -v o '%%%02x' "'$c"
     esac
     encoded+="$o"
  done
  echo "$encoded"
}

expire="$(($(date +'%s') + 600))"
signature="$(echo -en "GET\n\n\n$expire\n/$bucket/$key" | \
  openssl dgst -sha1 -binary -hmac "$AWS_SECRET" | \
  openssl base64)"
query="AWSAccessKeyId=$AWS_ID&Expires=$expire&Signature=$(encode "$signature")"

echo "https://s3.amazonaws.com/$bucket/$key?$query"
