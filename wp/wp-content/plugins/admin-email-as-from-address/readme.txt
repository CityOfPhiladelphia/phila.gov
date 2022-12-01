=== Admin Email As From Address ===
Contributors: BjornW, Ramon Fincken
Tags: email, admin email, from email address, from email sender, from email name
Requires at least: 4.5
Tested up to: 4.9.6
Stable tag:trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use the admin email address as the from email address

== Description ==
Use the admin email address set in Settings->General as the email address from which WordPress sends email.

The plugin has two easy settings and programmer hook support to set as new From email address and a From name. It also should work perfectly fine with Multisite installations.


== Screenshots ==

1. A screenshot of the WordPress Settings->General section with the admin email field marked red.
2. A screenshot of the WordPress Settings->General section with the extra fields for setting the From email address and name.

== Installation ==
1. Upload the `admin-email-as-from-address` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Done!

== Notes == 

Since version 1.1 you may also overrule this plugin by using the following filters: 

- aeafa_mail_from, [similar to wp_mail_from](https://developer.wordpress.org/reference/hooks/wp_mail_from/)  
- aeafa_mail_from_name, [similar to wp_mail_from_name](https://developer.wordpress.org/reference/hooks/wp_mail_from_name/)

These work exactly the same as their core WordPress counterparts and allows you to implement your own mail_from and mail_from_name 

*Credits* 

Icon used in the WordPress plugin repository and found in /assets/icon*
From the series ['Hatch'](http://www.toicon.com/series/hatch) By [Carol Liao](http://www.toicon.com/authors/3)
Licensed Creative Commons Attribution 4.0 International License.

Thanks Carol Liao and to[icon] for sharing your work!

WordPress repository header image found in /assets: 
['Postage Stamps'](https://www.flickr.com/photos/internetarchivebookimages/14598000897/)

"Image from page 1051 of "Universal dictionary of the english language: 
a new and original work presenting for convenient reference the orthography, 
pronunciation, meaning, use, origin and development of every word in the english language ..." (1898)"

Year: 1898 (1890s)
Authors: Hunter, Robert, 1823-1897 Morris, Charles, 1833-1922
Subjects:
Publisher: New York : Peter Fenelon Collier
Contributing Library: University of California Libraries
Digitizing Sponsor: Internet Archive
License: No known copyrights, see https://www.flickr.com/commons/usage/


From the Internet Archive Book Images on Flickr Commons 
Thanks Internet Archive & Flickr Commons - flickr.com/commons

== Changelog ==
#### 1.2 - November 16, 2016
- Created General settings option fields to set From sender name and From sender email address. The filters aeafa_mail_from and aeafa_mail_from_name will still override these fields. Props Ramon Fincken. 
See also the included screenshot-2.png

#### 1.1 - November 15, 2016
- Made the plugin extensible using filters. These filters are available: aeafa_mail_from and aeafa_mail_from_name 

#### 1.0 - September 5, 2016
- First release


