=== WP Offload Media Lite for Amazon S3, DigitalOcean Spaces, and Google Cloud Storage ===
Contributors: bradt, deliciousbrains, ianmjones
Tags: uploads, amazon, s3, amazon s3, digitalocean, digitalocean spaces, google cloud storage, gcs, mirror, admin, media, cdn, cloudfront
Requires at least: 4.9
Tested up to: 5.2
Requires PHP: 5.5
Stable tag: 2.2
License: GPLv3

Copies files to Amazon S3, DigitalOcean Spaces or Google Cloud Storage as they are uploaded to the Media Library. Optionally configure Amazon CloudFront or another CDN for faster delivery.

== Description ==

FORMERLY WP OFFLOAD S3 LITE

https://www.youtube.com/watch?v=_PVybEGaRXc

This plugin automatically copies images, videos, documents, and any other media added through WordPress' media uploader to [Amazon S3](http://aws.amazon.com/s3/), [DigitalOcean Spaces](https://www.digitalocean.com/products/spaces/) or [Google Cloud Storage](https://cloud.google.com/storage/). It then automatically replaces the URL to each media file with their respective Amazon S3, DigitalOcean Spaces or Google Cloud Storage URL or, if you have configured [Amazon CloudFront](http://aws.amazon.com/cloudfront/) or another CDN with or without a custom domain, that URL instead. Image thumbnails are also copied to the bucket and delivered through the correct remote URL.

Uploading files *directly* to your Amazon S3, DigitalOcean Spaces or Google Cloud Storage account is not currently supported by this plugin. They are uploaded to your server first, then copied to the bucket. There is an option to automatically remove the files from your server once they are copied to the bucket however.

If you're adding this plugin to a site that's been around for a while, your existing media files will not be copied to or served from Amazon S3, DigitalOcean Spaces or Google Cloud Storage. Only newly uploaded files will be copied to and served from the bucket. [The pro upgrade](https://deliciousbrains.com/wp-offload-media/upgrade/?utm_campaign=WP%2BOffload%2BS3&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting) has an upload tool to handle existing media files.

**Image Optimization**

Although WP Offload Media doesn't include image optimization features, we work closely with the author of [EWWW Image Optimizer](https://wordpress.org/plugins/ewww-image-optimizer/) to ensure they always work well together. Not only do we recommend EWWW Image Optimizer but we officially support its integration with WP Offload Media.

**PRO Upgrade with Email Support and More Features**

* Upload existing Media Library to Amazon S3, DigitalOcean Spaces or Google Cloud Storage
* Control offloaded files from the Media Library
* [Assets Pull addon](https://deliciousbrains.com/wp-offload-media/?utm_campaign=WP%2BOffload%2BS3&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting&utm_content=assets%2Baddon#addons) - Serve your CSS, JS and fonts via CloudFront or another CDN
* [WooCommerce integration](https://deliciousbrains.com/wp-offload-media/?utm_campaign=WP%2BOffload%2BS3&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting&utm_content=woocommerce%2Baddon#integrations)
* [Easy Digital Downloads integration](https://deliciousbrains.com/wp-offload-media/?utm_campaign=WP%2BOffload%2BS3&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting&utm_content=edd%2Baddon#integrations)
* PriorityExpert&trade; email support

[Compare pro vs free &rarr;](https://deliciousbrains.com/wp-offload-media/upgrade/?utm_campaign=WP%2BOffload%2BS3&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)

The video below runs through the pro upgrade features...

https://www.youtube.com/watch?v=I-wTMXMeFu4

== Installation ==

1. Install this plugin using WordPress' built-in installer
2. Access the *Offload Media* option under *Settings*
3. Follow the instructions to set up your AWS or DigitalOcean access keys and configure

Check out the [Quick Start Guide](https://deliciousbrains.com/wp-offload-media/doc/quick-start-guide/?utm_campaign=WP%2BOffload%2BS3&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting) for more information on configuring WP Offload Media.

== Frequently Asked Questions ==

= What are the minimum requirements? =

You can see the minimum requirements [here](https://deliciousbrains.com/wp-offload-media/pricing/?utm_campaign=WP%2BOffload%2BS3&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting&utm_content=requirements#requirements).

= Do you offer email support? =

If you upgrade to the pro version of [WP Offload Media](https://deliciousbrains.com/wp-offload-media/upgrade/?utm_campaign=WP%2BOffload%2BS3&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting), we will gladly provide you with email support. We take pride in delivering exceptional customer support. We do not provide email support for the free version.

== Screenshots ==

1. Select Cloud Storage Provider
2. Select or Create Bucket
3. Settings Screen
3. Custom Domain Used With CDN

== Upgrade Notice ==

= 2.0 =
This is a major upgrade that introduces support for DigitalOcean Spaces, renames the plugin to WP Offload Media Lite, and coincidentally upgrades some of its database settings. You may not be able to downgrade to WP Offload S3 Lite 1.x after upgrading to WP Offload Media Lite 2.0+.

= 1.1 =
This is a major change, which ensures S3 URLs are no longer saved in post content. Instead, local URLs are filtered on page generation and replaced with the S3 version. If you depend on the S3 URLs being stored in post content you will need to make modifications to support this version.

= 0.6 =
This version requires PHP 5.3.3+ and the Amazon Web Services plugin

== Changelog ==

= WP Offload Media Lite 2.2 - 2019-06-10 =
* [Release Summary Blog Post](https://deliciousbrains.com/wp-offload-media-2-2-released/?utm_campaign=changelogs&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)
* New: Use IAM Roles without having to update wp-config.php
* New: Frankfurt (FRA1) region now supported on DigitalOcean Spaces
* Improvement: WP dashboard performance
* Bug fix: Uploaded media files with uppercase extensions get second extension added

= WP Offload Media Lite 2.1.1 - 2019-04-29 =
* New: Multisite domain mapping via WordPress MU Domain Mapping plugin is now supported
* Improvement: Local to Provider content filtering performance improvements
* Improvement: Warning notice shown when changing storage provider and media already offloaded
* Bug fix: Media title not retaining characters stripped from filename
* Bug fix: Warning: is_readable(): open_basedir restriction in effect. File(~/.aws/config) is not within the allowed path(s)
* Bug fix: Fatal error when GCS Key File not accessible
* Bug fix: Non-image offloads on subsites with 4 digit IDs get duplicate subsite ID in bucket path
* Bug fix: No srcset added to img tag if filename includes non-ASCII characters
* Bug fix: Full size image URL saved to img tag src attribute when thumbnail picked if filename includes non-ASCII characters

= WP Offload Media Lite 2.1 - 2019-03-05 =
* [Release Summary Blog Post](https://deliciousbrains.com/wp-offload-media-2-1-released/?utm_campaign=changelogs&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)
* New: Google Cloud Storage is now supported
* Improvement: AWS PHP SDK updated
* Improvement: Diagnostic Info shows more complete settings information
* Bug fix: Year/Month path prefix incorrectly set in bucket for non-image media files
* Bug fix: PHP Fatal error: Class 'XMLWriter' not found
* Bug fix: PHP Fatal error: Uncaught Error: Call to undefined method ...\Aws3\Aws\S3\Exception\S3Exception::search() in .../classes/providers/aws-provider.php:439
* Bug fix: PHP Warning: filesize(): stat failed for [file-path] in classes/amazon-s3-and-cloudfront.php on line 1309

= WP Offload Media Lite 2.0.1 - 2018-12-17 =
* Improvement: Streamlined UI for setting Storage Provider and Bucket
* Bug fix: On/Off switches in settings look reversed
* Bug fix: Latest upgrade routine runs on fresh install
* Bug fix: Defined settings still found in database
* Bug fix: More Info links in Storage Provider settings incorrect
* Tested: WordPress 5.0

= WP Offload Media Lite 2.0 - 2018-09-24 =
* [Release Summary Blog Post](https://deliciousbrains.com/wp-offload-s3-is-now-wp-offload-media-and-adds-support-for-digitalocean-spaces/?utm_campaign=changelogs&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)
* New: DigitalOcean Spaces is now supported
* New: Plugin name updated from WP Offload S3 Lite to WP Offload Media Lite
* Improvement: More logical UI layout and better description of each setting

= WP Offload S3 Lite 1.4.3 - 2018-08-02 =
* Bug fix: Images remotely edited via stream wrapper sometimes set as private on S3

= WP Offload S3 Lite 1.4.2 - 2018-07-03 =
* Bug fix: Error getting bucket region
* Bug fix: Child themes with missing or broken style.css break diagnostic info

= WP Offload S3 Lite 1.4.1 - 2018-06-21 =
* Bug fix: Incorrect filesize saved to metadata when image removed from local server

= WP Offload S3 Lite 1.4 - 2018-06-12 =
* New: Using AWS PHP SDK v3
* New: Requires PHP 5.5+
* Improvement: Supported AWS S3 regions updated and names changed to match current AWS nomenclature
* Bug fix: PHP Warning: Declaration of AS3CF_Stream_Wrapper::register should be compatible with Aws\S3\StreamWrapper::register
* Bug fix: File size not stored in _wp_attachment_metadata for audio/video files
* Bug fix: Image srcset uses full size image if metadata size is stored as string
* Bug fix: PHP Warning: preg_match() expects parameter 2 to be string, array given
* Bug fix: SQL syntax error when using `attachment_url_to_postid()` with non-ascii file name
* Tested: WordPress 4.9.6
* Tested: Gutenberg 3.0

= WP Offload S3 Lite 1.3.2 - 2018-02-22 =
* Bug fix: Fatal error: Uncaught Error: Call to undefined method Composer\Autoload\ClassLoader::setClassMapAuthoritative()
* Bug fix: AWS keys stored in database by Amazon Web Services plugin are not being migrated to new settings record
* Bug fix: Notice in settings page that Amazon Web Services plugin no longer required is not being shown when Amazon Web Services active

= WP Offload S3 Lite 1.3.1 - 2018-02-20 =
* Bug fix: Fatal error in stream wrapper setup when AWS Keys not set

= WP Offload S3 Lite 1.3 - 2018-02-20 =
* [Release Summary Blog Post](https://deliciousbrains.com/wp-offload-s3-1-6-released/?utm_campaign=changelogs&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)
* New: [Amazon Web Services plugin](https://wordpress.org/plugins/amazon-web-services/) is no longer required to run WP Offload S3 Lite
* New: Added [`as3cf_local_domains`](https://deliciousbrains.com/wp-offload-s3/doc/filtering-urls-for-multiple-domains/?utm_campaign=changelogs&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting) filter for when site content is updated through multiple domains
* Improvement: AWS keys can be set in new "Settings" tab if not defined in wp-config.php
* Improvement: Minor tweaks to settings page UI including right aligning tabs and consistent title across Lite and Pro plugins
* Improvement: cURL version, theme version and parent theme information added to diagnostics
* Bug fix: Incompatible with plugins that include AWS PHP SDK v3
* Bug fix: Regenerate Thumbnails v3.0+ does not work with Remove Files From Server option
* Bug fix: "Warning: count(): Parameter must be an array or an object that implements Countable" error with PHP 7.2
* Bug fix: Force HTTPS not being applied to non-srcset S3 URLs in pages served over HTTP
* Bug fix: Content URLs not filtered to S3 URLs if AWS keys not set
* Bug fix: URL preview can be coerced to display invalid URL
* Bug fix: Changes to upload made via `as3cf_object_meta` filter are not reflected in amazonS3_info records
* Bug fix: Settings link not showing in network admin plugins page
* Bug fix: License in composer.json fails Packagist validation

= WP Offload S3 Lite 1.2.1 - 2017-11-20 =
* New: Compatibility with HTML Widget
* New: Dismissible admin notice that WP Offload S3 Lite will soon require PHP 5.5+
* Improvement: Compatibility with WordPress 4.9
* Bug fix: Incorrect region used when changing bucket by defining it in WPOS3_SETTINGS
* Bug fix: Media library notices render inside the upload tool
* Bug fix: Save notices disappear on settings page
* Bug fix: Improper use of jQuery.attr logged to browser console
* Bug fix: "Content Filtering Upgrade" URL in notice incorrect
* Bug fix: "More info" links can be broken across two lines

= WP Offload S3 Lite 1.2 - 2017-06-19 =
* New: Compatibility with WordPress 4.8
* New: Support for WP CLI `wp media regenerate`
* Improvement: Intermediate image sizes are now passed through the `as3cf_object_meta` filter
* Improvement: Content filtering cache now uses the external object when available
* Bug fix: Timeouts on large multisite installs due to excessive database queries on upgrade routines
* Bug fix: Video files with private ACL not working with WordPress's default media player
* Bug fix: Bucket permissions check not using configured path
* Bug fix: WordPress image editor sometimes shows a 404 when 'Remove Files From Server' enabled
* Bug fix: Notice: Undefined index: region

= WP Offload S3 Lite 1.1.6 - 2017-03-13 =
* New: Compatibility with [Advanced Custom Fields](https://wordpress.org/plugins/advanced-custom-fields/)
* New: `as3cf_filter_post_local_to_s3` and `as3cf_filter_post_s3_to_local` filters added for filtering S3 URLs in custom content
* Improvement: Ensure files uploaded using `media_handle_sideload` have unique filename on S3 when 'Remove Files From Server' enabled
* Bug fix: Files uploaded to S3 with empty filenames when the filename started with non-latin characters
* Bug fix: Audio files with private ACL not working with WordPress's default media player
* Bug fix: S3 API version not passed to S3 client
* Bug fix: Content added to text widgets via the Customizer not saved
* Bug fix: Original file not removed locally when cropped via the Customizer and 'Remove Files From Server' enabled
* Bug fix: Incorrect Media Library URLs saved to the database when WordPress installed in a subdirectory

= WP Offload S3 Lite 1.1.5 - 2017-01-12 =
* Improvement: Filter custom CSS - S3 URLs will no longer be saved to the database
* Bug fix: PDF previews have incorrect MIME type
* Bug fix: Original PDF not removed from S3 on attachment delete when image previews exist

= WP Offload S3 Lite 1.1.4 - 2016-12-13 =
* New: Upgrade routine to replace all S3 URLs in post excerpts with local URLs
* Improvement: Performance improvements
* Improvement: Allow expires time to be filtered for private content using the `as3cf_expires` filter
* Bug fix: Image `srcset` not correctly applied when file names contain special characters

= WP Offload S3 Lite 1.1.3 - 2016-11-28 =
* Bug fix: Private URL signing params stripped in some circumstances
* Improvement: Performance improvements for URL filtering, especially on large sites

= WP Offload S3 Lite 1.1.2 - 2016-11-02 =
* Improvement: Better content filtering support for third party plugins and themes
* Bug fix: PHP Warning: Division by zero

= WP Offload S3 Lite 1.1.1 - 2016-10-17 =
* New: Filter post excerpts - S3 URLs will no longer be saved to the database
* Bug fix: PHP 5.3 Fatal error: Using $this when not in object context
* Bug fix: Query string parameters incorrectly encoded for Media Library items

= WP Offload S3 Lite 1.1 - 2016-09-29 =
* New: Filter post content. S3 URLs will no longer be saved to the database
* New: Upgrade routine to replace all S3 URLs in content with local URLs
* New: Support for theme custom logos
* New: Control the ACL for intermediate image sizes using the `as3cf_upload_acl_sizes` filter
* Bug fix: File names containing special characters double encoded
* Bug fix: `srcset` not working for file names containing special characters
* Bug fix: Incorrect placeholder text for 'Path' option
* Bug fix: Objects in root of bucket not deleted when removed from the Media Library
* Bug fix: No longer use deprecated functions in WordPress 4.6
* Bug fix: Don't delete local file when 'Remove Files From Server' enabled and upload to S3 fails

= WP Offload S3 Lite 1.0.5 - 2016-09-01 =
* New: Compatibility with WordPress 4.6
* Improvement: No longer delete plugin data on uninstall. Manual removal possible, as per this [doc](https://deliciousbrains.com/wp-offload-s3/doc/uninstall/?utm_campaign=changelogs&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)

= WP Offload S3 Lite 1.0.4 - 2016-05-30 =
* New: Now using simpler Force HTTPS setting, removed redundant Always Use HTTP setting
* New: `as3cf_cloudfront_path_parts` filter allows changing served CloudFront path (useful when distribution pulls subdirectory)
* Improvement: Better compatibility with non-standard notices from other plugins and themes
* Improvement: Added basic auth and proxy info to diagnostic info
* Improvement: Added `allow_url_fopen` status to diagnostic info
* Improvement: Added memory usage to diagnostic info
* Improvement: Ensure notice text is 800px or less in width
* Improvement: Reduced database queries on settings screen
* Bug fix: Properly handle _wp_attachment_data metadata when it is a serialized WP_Error

= WP Offload S3 Lite 1.0.3 - 2016-03-23 =
* Bug fix: Don't replace srcset URLs when Rewrite File URLs option disabled
* Bug fix: Fatal error: Cannot redeclare as3cf_get_secure_attachment_url()

= WP Offload S3 Lite 1.0.2 - 2016-03-08 =
* Bug fix: Uninstall would run even if pro plugin installed

= WP Offload S3 Lite 1.0.1 - 2016-03-08 =
* Bug fix: Fatal error on plugin activation
* Bug fix: Unable to activate Pro upgrade

= WP Offload S3 Lite 1.0 - 2016-03-07 =
* New: Plugin renamed to "WP Offload S3 Lite"
* New: Define any and all settings with a constant in wp-config.php
* New: Documentation links for each setting
* Improvement: Simplified domain setting UI
* Improvement: Far future expiration header set by default
* Improvement: Newly created bucket now immediately appears in the bucket list
* Improvement: Cleanup user meta on uninstall
* Improvement: WP Retina 2x integration removed
* Bug fix: Year/Month folder structure on S3 not created if the 'Organise my uploads into month and year-based folders' WordPress setting is disabled
* Bug fix: Responsive srcset PHP notices
* Bug fix: Compatibility addon notices displayed to non-admin users
* Bug fix: Potential PHP fatal error in MySQL version check in diagnostic log
* Bug fix: Missing image library notices displaying before plugin is setup

= WP Offload S3 0.9.12 - 2016-02-03 =
* Improvement: Compatibility with WP Offload S3 Assets 1.1
* Bug fix: Object versioned responsive images in post content not working when served from S3 on WordPress 4.4+

= WP Offload S3 0.9.11 - 2015-12-19 =
* Bug fix: Responsive images in post content not working when served from S3
* Bug fix: Responsive images using wrong image size when there are multiple images with the same width

= WP Offload S3 0.9.10 - 2015-11-26 =
* Improvement: Support for responsive images in WP 4.4
* Bug fix: Incorrect file path for intermediate image size files uploaded to S3 with no prefix
* Bug fix: Thumbnail previews return 404 error during image edit screen due to character encoding

= WP Offload S3 0.9.9 - 2015-11-12 =
* Improvement: Improve wording of compatibility notices
* Improvement: Compatibility with Easy Digital Downloads 1.0.1 and WooCommerce 1.0.3 addons
* Improvement: Better determine available memory for background processes
* Bug fix: URL previews incorrect due to stripping `/` characters
* Bug fix: PHP Warning: stream_wrapper_register(): Protocol s3:// is already defined
* Bug fix: PHP Fatal error:  Call to undefined method WP_Error::get()

= WP Offload S3 0.9.8 - 2015-11-02 =
* Bug fix: Attachment URLs containing query string parameters incorrectly encoded

= WP Offload S3 0.9.7 - 2015-10-26 =
* Improvement: Improve compatibility with third party plugins when the _Remove Files From Server_ option is enabled
* Improvement: Fix inconsistent spacing on the WP Offload S3 settings screen
* Improvement: Validate _CloudFront or custom domain_ input field
* Improvement: Link to current S3 bucket added to WP Offload S3 settings screen
* Improvement: Show notice when neither GD or Imagick image libraries are not installed
* Improvement: Supply Cache-Control header to S3 when the _Far Future Expiration Header_ option is enabled
* Improvement: Additional information added to _Diagnostic Information_
* Improvement: Added warning when _Remove Files From Server_ option is enabled
* Improvement: Filter added to allow additional image versions to be uploaded to S3
* Bug fix: File size not stored in _wp_attachment_metadata_ when _Remove Files From Server_ option is enabled
* Bug fix: Uploads on Multisite installs allowed after surpassing upload limit
* Bug fix: Site icon in WordPress customizer returns 404
* Bug fix: Image versions remain locally and on S3 after deletion, when the file name contains characters which require escaping
* Bug fix: Files with the same file name overwritten when __Remove Files From Server_ option is enabled
* Bug fix: Cron tasks incorrectly scheduled due to passing the wrong time to `wp_schedule_event`
* Bug fix: Default options not shown in the UI after first install

= WP Offload S3 0.9.6 - 2015-10-01 =
* Improvement: Update text domains for translate.wordpress.org integration

= WP Offload S3 0.9.5 - 2015-09-01 =
* Bug fix: Fatal error: Cannot use object of type WP_Error as array

= WP Offload S3 0.9.4 - 2015-08-27 =
* New: Update all existing attachments with missing file sizes when the 'Remove Files From Server' option is enabled (automatically runs in the background)
* Improvement: Show when constants are used to set bucket and region options
* Improvement: Don't show compatibility notices on plugin update screen
* Improvement: On Multisite installs don't call `restore_current_blog()` on successive loop iterations
* Bug fix: 'Error getting URL preview' alert shown when enter key pressed on settings screen
* Bug fix: Unable to crop header images when the 'Remove Files From Server' option is enabled
* Bug fix: Incorrect storage space shown on Multisite installs when the 'Remove Files From Server' option is enabled
* Bug fix: Upload attempted to non existent bucket when defined by constant
* Bug fix: 'SignatureDoesNotMatch' error shown when using signed URLs with bucket names containing '.' characters

= WP Offload S3 0.9.3 - 2015-08-17 =
* New: Pro upgrade sidebar
* Bug fix: Create buckets in US standard region causing S3 URLs to 404 errors

= WP Offload S3 0.9.2 - 2015-07-29 =
* Bug fix: Accidentally released the sidebar for after we launch the pro version

= WP Offload S3 0.9.1 - 2015-07-29 =
* Improvement: Access denied sample IAM policy replaced with link to [Quick Start Guide](https://deliciousbrains.com/wp-offload-s3/doc/quick-start-guide/?utm_campaign=changelogs&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)
* Improvement: Access denied messages on bucket selection or bucket creation now link to [Quick Start Guide](https://deliciousbrains.com/wp-offload-s3/doc/quick-start-guide/?utm_campaign=changelogs&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)
* Improvement: Object expires time can now be filtered using the `as3cf_object_meta` filter
* Bug fix: Error not always shown when S3 bucket inaccessible due to incorrect permissions
* Bug fix: Permission checks fail when S3 bucket is in a non-default region and defined by `AS3CF_BUCKET` constant
* Bug fix: Restore `as3cf_get_attached_file_copy_back_to_local` filter
* Bug fix: Image versions not uploaded to S3 when an edited image is restored
* Bug fix: Original image version not deleted from server when _Remove Files From Server_ option enabled
* Bug fix: Media library items with non-ascii characters in the file name are not removed from S3
* Bug fix: Compatibility notices shown on plugin install pages
* Bug fix: WordPress footer overlaps WP Offload S3 sidebar
* Bug fix: Upon initial setup the settings changed alert shows when no settings have changed

= WP Offload S3 0.9 - 2015-07-08 =
* New: Plugin rebranded to WP Offload S3
* New: Support tab added to _Offload S3_ screen containing diagnostic information
* New: Compatibility with the [Media Replace](https://wordpress.org/plugins/enable-media-replace/) plugin
* New: Select bucket region when creating a new bucket
* New: Toggle switches redesigned
* Improvement: Compatibility with release candidate of Pro plugin
* Improvement: Example IAM policy more secure
* Improvement: Set default bucket region using the `AS3CF_REGION` constant
* Improvement: Added `as3cf_object_meta` filter for developers
* Improvement: Bucket selection moved to modal window
* Improvement: Don't allow bucket names to contain invalid characters on creation
* Improvement: More verbose error messages on bucket selection
* Improvement: Settings link added to plugin row on _Plugins_ screen
* Improvement: Object versioning enabled by default
* Improvement: Uninstall routines added
* Improvement: JavaScript coding standards
* Improvement: Cache result when checking S3 bucket permissions
* Bug fix: Bucket region errors result in blank WP Offload S3 screen
* Bug fix: Editing an image when _Remove Files From Server_ option is enabled results in error
* Bug fix: Metadata upgrade procedure triggered on new installs
* Bug fix: File URLs when uploaded to a subdirectory result in incorrect S3 URLs
* Bug fix: Errors logged when trying to delete non-existent HiDPI images
* Bug fix: SignatureDoesNotMatch errors on regions with v4 authentication
* Bug fix: Customizer background image not editable
* Bug fix: Error when creating buckets with US Standard region
* Bug fix: Notices appearing incorrectly on some admin screens
* Bug fix: Subsite upload paths repeated on multisite installs
* Bug fix: Handle multisite installs where `BLOG_ID_CURRENT_SITE` is not 1

= WP Offload S3 0.8.2 - 2015-01-31 =
* New: Input bucket in settings to avoid listing all buckets
* New: Specify bucket with 'AS3CF_BUCKET' constant
* Improvement: Compatibility with beta release of Pro plugin
* Bug Fix: Incorrect file prefix in S3 permission check

= WP Offload S3 0.8.1 - 2015-01-19 =
* Bug Fix: Permission problems on installs running on EC2s
* Bug Fix: Blank settings page due to WP_Error on S3 permission check
* Bug Fix: Warning: strtolower() expects parameter 1 to be string, object given
* Bug Fix: Region post meta update running on subsites of Multisite installs

= WP Offload S3 0.8 - 2015-01-10 =
* New: Redesigned settings UI
* Improvement: SSL setting can be fully controlled, HTTPS for urls always, based on request or never
* Improvement: Download files from S3 that are not found on server when running Regenerate Thumbnails plugin
* Improvement: When calling `get_attached_file()` and file is missing from server, return S3 URL
* Improvement: Code cleanup to WordPress coding standards
* Bug Fix: Files for all subsites going into the same S3 folder on multisite installs setup prior to WP 3.5
* Bug Fix: 'attempting to access local file system' error for some installs

= WP Offload S3 0.7.2 - 2014-12-11 =
* Bug: Some buckets in the EU region causing permission and HTTP errors
* Bug: Undefined variable: message in view/error.php also causing white screens

= WP Offload S3 0.7.1 - 2014-12-05 =
* Bug: Read-only error on settings page sometimes false positive

= WP Offload S3 0.7 - 2014-12-04 =
* New: Proper S3 region subdomain in URLs for buckets not in the US Standard region (e.g. https://s3-us-west-2.amazonaws.com/...)
* New: Update all existing attachment meta with bucket region (automatically runs in the background)
* New: Get secure URL for different image sizes (iamzozo)
* New: S3 bucket can be set with constant in wp-config.php (dberube)
* New: Filter for allowing/disallowing file types: `as3cf_allowed_mime_types`
* New: Filter to cancel upload to S3 for any reason: `as3cf_pre_update_attachment_metadata`
* New: Sidebar with email opt-in
* Improvement: Show warning when S3 policy is read-only
* Improvement: Tooltip added to clarify option
* Improvement: Move object versioning option to make it clear it does not require CloudFront
* Improvement: By default only allow file types in `get_allowed_mime_types()` to be uploaded to S3
* Improvement: Compatibility with WPML Media plugin
* Bug Fix: Edited images not removed on S3 when restoring image and IMAGE_EDIT_OVERWRITE true
* Bug Fix: File names with certain characters broken not working
* Bug Fix: Edited image uploaded to incorrect month folder
* Bug Fix: When creating a new bucket the bucket select box appears empty on success
* Bug Fix: SSL not working in regions other than US Standard
* Bug Fix: 'Error uploading' and 'Error removing local file' messages when editing an image
* Bug Fix: Upload and delete failing when bucket is non-US-region and bucket name contains dot
* Bug Fix: S3 file overwritten when file with same name uploaded and local file removed (dataferret)
* Bug Fix: Manually resized images not uploaded (gmauricio)

= WP Offload S3 0.6.1 - 2013-09-21 =
* WP.org download of Amazon Web Services plugin is giving a 404 Not Found, so directing people to download from Github instead

= WP Offload S3 0.6 - 2013-09-20 =
* Complete rewrite
* Now requires PHP 5.3.3+
* Now requires the [Amazon Web Services plugin](http://wordpress.org/extend/plugins/amazon-web-services/) which contains the latest PHP libraries from Amazon
* Now works with multisite
* New Option: Custom S3 object path
* New Option: Always serve files over https (SSL)
* New Option: Enable object versioning by appending a timestamp to the S3 file path
* New Option: Remove uploaded file from local filesystem once it has been copied to S3
* New Option: Copy any HiDPI (@2x) images to S3 (works with WP Retina 2x plugin)

= WP Offload S3 0.5 - 2013-01-29 =
* Forked [Amazon S3 for WordPress with CloudFront](http://wordpress.org/extend/plugins/tantan-s3-cloudfront/)
* Cleaned up the UI to fit with today's WP UI
* Fixed issues causing error messages when WP_DEBUG is on
* [Delete files on S3 when deleting WP attachment](https://github.com/deliciousbrains/wp-amazon-s3-and-cloudfront/commit/e777cd49a4b6999f999bd969241fb24cbbcece60)
* [Added filter to the get_attachment_url function](https://github.com/deliciousbrains/wp-amazon-s3-and-cloudfront/commit/bbe1aed5c2ae900e9ba1b16ba6806c28ab8e2f1c)
* [Added function to get a temporary, secure download URL for private files](https://github.com/deliciousbrains/wp-amazon-s3-and-cloudfront/commit/11f46ec2714d34907009e37ad3b97f4421aefed3)
