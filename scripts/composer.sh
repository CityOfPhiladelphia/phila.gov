source ~/.ssh/environment
sudo chmod 777 ~/app/wp
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
    "wpackagist-plugin/better-search-replace":"1.4.1",
    "wpackagist-plugin/disable-gutenberg":"2.8.1",
    "wpackagist-plugin/jwt-auth":"2.1.3",
    "wpackagist-plugin/admin-email-as-from-address":"^1.2",
    "wpackagist-plugin/gathercontent-import":"3.2.12",
    "wpackagist-plugin/jwt-authentication-for-wp-rest-api":"1.3.2",
    "wpackagist-plugin/meta-box":"^5.6.5",
    "wpackagist-plugin/meta-box-text-limiter":"^1.1.3",
    "wpackagist-plugin/mb-relationships":"^1.10.11",
    "wpackagist-plugin/easy-wp-smtp":"^1.5.2",
    "wpackagist-plugin/mb-rest-api":"^1.4.1",
    "wpackagist-plugin/miniorange-saml-20-single-sign-on":"^4.9.29",
    "wpackagist-plugin/amazon-s3-and-cloudfront":"^2.6.2",
    "wpackagist-plugin/reusable-text-blocks":"^1.5.3",
    "wpackagist-plugin/classic-editor":"^1.6.2",
    "wpackagist-plugin/wp-rest-api-v2-menus":"^0.10",
    "wpackagist-plugin/wordpress-importer":"^0.8",
    "wpackagist-plugin/tinymce-advanced":"^5.6.0"
  },
  "config": {
    "allow-plugins": {
      "composer/installers": true
    }
  }
}
' > ~/app/wp/composer.json