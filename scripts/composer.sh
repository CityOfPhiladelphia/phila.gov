source ~/.ssh/environment
sudo chmod 777 /app/wp
echo '
{
  "name": "cityofphiladelphia/phila.gov",
  "description": "Phila.gov",
  "repositories":[
    {
        "type":"composer",
        "url":"https://wpackagist.org"
    },
    {
      "type": "composer",
      "url": "https://packages.metabox.io/'$METABOX_LICENSE'"
    }
  ],
  "require": {
    "meta-box/mb-admin-columns":"dev-master",
    "meta-box/mb-revision":"dev-master",
    "meta-box/mb-settings-page":"dev-master",
    "meta-box/mb-term-meta":"dev-master",
    "meta-box/meta-box-columns":"dev-master",
    "meta-box/meta-box-conditional-logic":"dev-master",
    "meta-box/meta-box-group":"dev-master",
    "meta-box/meta-box-include-exclude":"dev-master",
    "meta-box/meta-box-tabs":"dev-master",
    "meta-box/meta-box-tooltip":"dev-master",
    "wpackagist-plugin/meta-box":"^5.6.5",
    "wpackagist-plugin/mb-rest-api":"^1.4.1",
    "wpackagist-plugin/amazon-s3-and-cloudfront":"^2.6.2",
    "wpackagist-plugin/classic-editor":"^1.6.2",
    "wpackagist-plugin/wp-rest-api-v2-menus":"^0.10",
    "wpackagist-plugin/tinymce-advanced":"^5.6.0"
  },
  "config": {
    "allow-plugins": {
      "composer/installers": true
    }
  }
}
' > /app/wp/composer.json