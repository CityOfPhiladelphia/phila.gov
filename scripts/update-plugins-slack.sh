#!/bin/bash

[ "$SLACK_HOOK" ] || exit

payload=$(wp plugin list --update=available --format=json | jq -c '{
    channel: "#alpha",
    username: "dingding",
    icon_emoji: ":bell:",
    text: [.[].name] | sort | @sh "Plugins with updates available: \(.)"
  }' | tr -d "'")

#echo $payload
curl -XPOST --data-urlencode "payload=$payload" $SLACK_HOOK
