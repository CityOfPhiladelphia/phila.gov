#!/bin/bash

read -p "Paste in the latest github commit and hit <Enter> " commitid

# Strip off the URL and just leave the commit ID
if echo "$commitid" | grep -q "/commit/"
then
        commitid="$(echo "$commitid" | sed 's/^.*\/commit\///'
)"
fi

# Run the deployment
aws --region us-east-1 deploy create-deployment --application-name beta.phila.gov --deployment-group-name beta.phila.gov --github-location commitId="$commitid",repository="cityofphiladelphia/phila.gov"
