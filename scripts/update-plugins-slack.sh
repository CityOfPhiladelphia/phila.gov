#!/bin/bash

[ "$1" ] || exit

payload=$(wp plugin list --update=available --format=json | jq -c '{
    channel: "#alpha",
    username: "dingding",
    icon_emoji: ":bell:",
    text: [.[].name] | sort | @sh "Plugins with updates available: \(.)"
  }' | tr -d "'")

#echo $payload

curl -s -XPOST --data-urlencode "payload=$payload" $1 > /dev/null
