Changelog
=========

#### 3.0.9 - December 17, 2015

**Fixes**

Not being able to select a list when creating a new form.

#### 3.0.8 - December 15, 2015

**Fixes**

- Make sure `mc4wp_show_form()` works without passing a form ID.

**Improvements**

- Remove UI for bulk-enabling integrations, as every integration needs specific settings anyway.
- Do not print inline JavaScript for forms until it's surely needed.
- Add `position` key to `mc4wp_admin_menu_items` filter to set a menu position.
- Various minor code improvements.

#### 3.0.7 - December 10, 2015

**Fixes**

Workaround for [SSL certification bug in WordPress 4.4](https://core.trac.wordpress.org/ticket/34935), affecting servers with an older versions of OpenSSL installed.

**Additions**

Added `mc4wp_use_sslverify` filter to disable or explicitly enable SSL certificate verification.


#### 3.0.4 - December 7, 2015

**Fixes**

- Fixes compatibility issues with add-on plugins performing validation, like Goodbye Captcha and BWS Captcha.

**Improvements**

- Now using group ID's for interest grouping fields, so changing the group in MailChimp does not require updating your form code.
- Never load enabled integrations which are not installed.
- Reintroduce support for automatically sending `OPTIN_IP`

**Additions**

- Add filter: `mc4wp_form_data`, filters form data before it is processed.


#### 3.0.3 - November 30, 2015

**Fixes**

- Added backwards compatibility for [Goodbye Captcha](https://wordpress.org/plugins/goodbye-captcha/) integration.

**Improvements**

- Prevented notice when saving Form widget settings for the first time.
- Add `autofill="off"` to honeypot field.
- Remove nonces from forms as they're not really useful for publicly available features.
- Errors returned by MailChimp are now logged for Forms as well.
- Pre-select MailChimp list if there's just one list in the connected account.
- Added missing translation calls for Form Editor.

#### 3.0.2 - November 25, 2015

**Fixes**

- Redirect on success not working.
- Forms overview page redirected to main WP Admin page (edge case).
- Safari was always showing the leave-page confirmation dialog.

**Improvements**

- Add form-specific classes to preview form element. This allows the [Styles Builder](https://mc4wp.com/features/) to work with the Form Preview.
- Form events are now triggered _after_ the page has finished loading, so all scripts are loaded & ready to use.
- Reset background-color in Form Themes stylesheets.

#### 3.0.0 & 3.0.1 - November 23, 2015

Version 3.0 is a total revamp of the plugin. For a quick overview of the changes, please [read this post on our blog](https://mc4wp.com/blog/whats-new-in-mailchimp-for-wordpress-the-big-three-o/).

Before upgrading, please go through the [upgrade guide](https://mc4wp.com/kb/upgrading-to-3-0/) as some things have changed.

**Breaking Changes**

- Captcha fields: `{captcha}` field is now handled by the [Captcha add-on plugin](https://wordpress.org/plugins/mc4wp-captcha/).
- New dynamic content tags syntax: `{data_NAME}` is now `{data key="NAME"}`
- Event binding: `jQuery(document).on('subscribe.mc4wp','.mc4wp-form', function(){ ... })` is now `mc4wp.forms.on('subscribed', function(form) { ... })`
- Removed integrations: MultiSite & bbPress.

**Improvements**

- New form editor with syntax highlighting, more advanced field options & better visual feedback.
- Better support for MailChimp `address` fields.
- Better support for choice fields (eg groupings, list choice & country fields).
- All fields marked as `required` are now validated server-side as well (instead of just MailChimp required fields).
- All integrations have their own settings page now.
- Events Manager: checkbox is now automatically added to booking forms.
- Tons of usability & accessibility improvements.
- Tons of code improvements: improved memory usage, 100+ new unit tests & better usage of various best practices.
- The [premium plugin](https://mc4wp.com/) is now an add-on of this plugin.

**Additions**

- New "Preview Form" option, showing unsaved form changes.
- Integrations can now be "implicit", thus no longer showing a checkbox option to visitors.
- New JavaScript API, replacing jQuery event hooks.
- Ninja Forms integration
- Introduced various new filter & action hooks, please see the new [code reference for developers](http://developer.mc4wp.com/) for more information.

#### 2.3.18 - November 2, 2015

**Fixes**

- Incorrect number of parameters for `error_log` statement in integrations class.

**Improvements**

- Usage tracking is now scheduled once a week (instead of daily).
- Preparations for [the upcoming MailChimp for WordPress version 3.0 release](https://mc4wp.com/blog/breaking-backwards-compatibility-in-version-3-0/).
- Tested compatibility with WordPress 4.4

#### 2.3.17 - October 22, 2015

**Fixes**

- Honeypot field being autofilled in Chrome, causing a form error.

**Improvements**

- Updated Portugese translations.


#### 2.3.16 - October 14, 2015

**Fixes**

- Error in Russian translation, causing a broken link on the MailChimp settings page.

**Improvements**

- Textual improvements to MailChimp settings page.
- Connectivity issues with MailChimp will now _always_ show an error message.
- Renewing MailChimp lists will now also update the output of the `{subscriber_count}` tag.

#### 2.3.15 - October 9, 2015

**Fixes**

- Fixes JS error when form contains no submit button

**Improvements**

- Only prefix `url` fields with `http://` if it is filled.
- Updated Spanish & Catalan translations, thanks to [Xavier Gimeno Torrent](http://www.xaviergimeno.net/).
- Fix `mc4wp_form_before_fields` being applied twice.
- Position honeypot field to the right for Right-To-Left sites.
- `_mc4wp_lists` can now be a comma-separated string of MailChimp list ID's to subscribe to (or an array).
- Minor other defensive coding improvements to prevent clashes with other plugins.

**Additions**

- Added opt-in usage tracking to help us make the plugin better. No sensitive data is tracked.

#### 2.3.14 - September 25

**Fixes**

- Use of undefined constant in previous update.

#### 2.3.13 - September 25, 2015

**Fixes**

- Honeypot causing horizontal scrollbar on RTL sites.
- List choice fields not showing when using one of the default form themes.

**Improvements**

- Minor styling improvements for RTL sites.
- MailChimp list fields of type "website" will now become HTML5 `url` type fields.
- Auto-prefix fields of type `url` with `http://`

#### 2.3.12 - September 21, 2015

**Fixes**

- Issue with interest groupings not being fetched after updating to version 2.3.11

#### 2.3.11 - September 21, 2015

**Fixes**

- Honeypot field being filled by browser's autocomplete.
- Styling issue for submit buttons in Mobile Safari.
- Empty response from MailChimp API

**Improvements**

- Do not query MailChimp API for interest groupings if list has none.
- Integration errors are now logged to PHP's error log for easier debugging.

**Additions**

- You can now use shortcodes in the form content.

#### 2.3.10 - September 7, 2015

**Fixes**

- Showing "not connected" when the plugin was actually connected to MailChimp.
- Issue with `address` fields when `addr1` was not given.
- Comment form checkbox not outputted for some older themes.

**Improvements**

- Do not flush MailChimp cache on every settings save.
- Add default CSS styles for `number` fields.
- Placeholders will now work in older version of IE as well.

#### 2.3.9 - September 1, 2015

**Improvements**

- MailChimp lists cache is now automatically flushed after changing your API key setting.
- Better field population after submitting a form with errors.
- More helpful error message when no list is selected.
- Translate options when installing plugin from a language other than English.
- Add form mark-up to WPML configuration file.
- Sign-up checkbox in comment form is now shown before the "submit comment" button.
- URL-encode variables in "Redirect URL" setting.
- Better error message when connected to MailChimp but account has no lists.

**Additions**

- Add `mc4wp_form_action` filter to set a custom `action` attribute on the form element.

#### 2.3.8 - August 18, 2015

**Fixes**

- Prevented JS error when outputting forms with no submit button.
- Using `0` as a Redirect URL resulted in a blank page.
- Sign-up checkbox was showing twice in the Easy Digital Downloads checkout when showing registration fields, thanks [Daniel Espinoza](https://github.com/growdev).
- Default form was not automatically translated for languages other than English.

**Improvements**

- Better way to hide the honeypot field, which stops bots from subscribing to your lists.
- role="form" is no longer needed, thanks [XhmikosR](https://github.com/XhmikosR)!
- Filter `mc4wp_form_animate_scroll` now disables just the scroll animation, not the scroll itself.
- Revamped UI for MailChimp lists overview
- Updated German & Greek translations.

**Additions**

- Added `mc4wp_form_is_submitted()` and `mc4wp_form_get_response_html()` functions.

#### 2.3.7 - July 13, 2015

**Improvements**

- Use the same order as MailChimp.com, which is useful when you have over 100 MailChimp lists.
- Use `/* ... */` for inline JavaScript comments to prevent errors with minified HTML - props [Ed Gifford](https://github.com/egifford)

**Additions**

- Filter: `mc4wp_form_animate_scroll` to disable animated scroll-to after submitting a form.
- Add `{current_path}` variable to use in form templates.
- Add `default` attribute to `{data_name}` variables, usage: `{data_something default="The default value"}`

#### 2.3.6 - July 6, 2015

**Fixes**

- Undefined index notice when visitor's USER_AGENT is not set.

**Improvements**

- Relayed the browser's Accept-Language header to MailChimp for auto-detecting a subscriber's language.
- Better CSS for form reset
- Updated HTML5 placeholder polyfill

#### 2.3.5 - June 24, 2015

**Fixes**

- Faulty update for v3.0 appearing for people running GitHub updater plugin.

**Improvements**

- Updated language files.
- Now passing the form as a parameter to `mc4wp_form_css_classes` filter.

#### 2.3.4 - May 29, 2015

**Fixes**

- Issue with GROUPINGS not being sent to MailChimp

**Improvements**

- Code preview in Field Builder is now read-only

#### 2.3.3 - May 27, 2015

**Fixes**

- Get correct IP address when using proxy like Cloudflare or Sucuri WAF.
- Use strict type check for printing inline CSS that hides honeypot field

**Improvements**

- Add `contactemail` and `contactname` to field name guesses when integrating with third-party form.
- Re-enable `sslverify`

#### 2.3.2 - May 12, 2015

**Fixes**

- Groupings not being sent to MailChimp
- Issue when using more than one `{data_xx}` replacement

**Improvements**

- IE8 compatibility for honeypot fallback script.

#### 2.3.1 - May 6, 2015

**Fixes**

- PHP notice in `includes/class-tools.php`, introduced by version 2.3.

#### 2.3 - May 6, 2015

**Fixes**

- The email address is no longer automatically added to the Redirect URL as this is against Google Analytics policy. To add it again, use `?email={email}` in your Redirect URL setting.
- Registration type integrations were not correctly picking up on first- and last names.
- JavaScript error in IE8 because of `setAttribute` call on honeypot field.
- API class `subscribe` method now always returns a boolean.

**Improvements**

- Add `role` attribute to form elements
- Major code refactoring for easier unit testing and improved code readability.
- Use Composer for autoloading all plugin classes (PHP 5.2 compatible)
- You can now use [form variables in both forms, messages as checkbox label texts](https://mc4wp.com/kb/using-variables-in-your-form-or-messages/).

**Additions**

- You can now handle unsubscribe calls with our forms too.
- Added Portugese, Indonesian, German (CH) and Spanish (PR) translations.

#### 2.2.9 - April 15, 2015

**Fixes**

- Menu item for settings page not appearing on Google App Engine ([#88](https://github.com/ibericode/mailchimp-for-wordpress/issues/88))

**Improvements**

- Updated Italian, Russian & Turkish translations. [Want to help translate the plugin? Full translations get a free Pro license](https://www.transifex.com/projects/p/mailchimp-for-wordpress/).

#### 2.2.8 - March 24, 2015

**Fixes**

- API key field value was not properly escaped.
- Background images were stripped from submit buttons.

**Improvements**

- Better sanitising of all settings
- Updated all translations

**Additions**

- Added `mc4wp_before_checkbox` and `mc4wp_after_checkbox` filters to easily add more fields to sign-up checkbox integrations.
- Added some helper methods related to interest groupings to `MC4WP_MailChimp` class.
- Allow setting custom MailChimp lists to subscribe to using `lists` attribute on shortcode.

#### 2.2.7 - March 11, 2015

**Fixes**

- Honeypot field was visible for themes or templates not calling `wp_head()` and `wp_footer()`

**Improvements**

- Various minor code improvements
- Updated German, Spanish, Brazilian, French, Hungarian and Russian translations.

**Additions**

- Added [mc4wp_form_success](https://github.com/ibericode/mailchimp-for-wordpress/blob/06f0c833027f347a288d2cb9805e0614767409b6/includes/class-form-request.php#L292-L301) action hook to hook into successful sign-ups
- Added [mc4wp_form_data](https://github.com/ibericode/mailchimp-for-wordpress/blob/06f0c833027f347a288d2cb9805e0614767409b6/includes/class-form-request.php#L138-L142) filter hook to modify all form data before processing


#### 2.2.6 - February 26, 2015

**Fixes**

- CSS reset wasn't working for WooCommerce checkout sign-up checkbox.
- `mc4wp-submitted` class was not added in IE8
- Incorrect `action` attribute on form element for some server configurations

**Improvements**

- Anti-SPAM improvements: a better honeypot field and a timestamp field to prevent instant form submissions.
- Reset `background-image` on submit buttons when using CSS themes
- Smarter email detection when integrating with third-party forms
- Updated all translations

**Additions**

- Custom fallback for browsers not supporting `input[type="date"]`


#### 2.2.5 - February 13, 2015

**Fixed**

- Issue where WooCommerce checkout sign-up was not working for cheque payments.
- Translation were loaded too late to properly translate some strings, like the admin menu items.

**Improvements**

- The presence of required list fields in form mark-up is now checked as you type.
- Number fields will now repopulate if an error occurred.
- Updated all translations.
- Make sure there is only one plugin instance.
- Various other code improvements.

**Additions**

- Added support for [GitHub Updater Plugin](https://github.com/afragen/github-updater).
- You can now specify whether you want to send a welcome email (only with double opt-in disabled).

A huge thank you to [Stefan Oderbolz](http://metaodi.ch/) for various fixed and improvements related to translations in this release.


#### 2.2.4 - February 4, 2015

**Fixed**

- Textual fix as entering "0" for no redirection does not work.

**Improvements**

- Moved third-party scripts to their own directory for easier exclusion
- All code is now adhering to the WP Code Standards
- Updated [Dutch, German, Spanish, Hungarian, French, Italian and Turkish translations](https://www.transifex.com/projects/p/mailchimp-for-wordpress/).

**Additions**

- Now showing a heads up when at limit of 100 MailChimp lists. ([#71](https://github.com/ibericode/mailchimp-for-wordpress/issues/71))
- Added `wpml-config.xml` file for better WPML compatibility
- Added filter `mc4wp_menu_items` for adding & removing menu items from add-ons

#### 2.2.3 - January 24, 2015

Minor improvements and additions for compatibility with the [MailChimp Sync plugin](https://wordpress.org/plugins/mailchimp-sync/).

#### 2.2.2 - January 13, 2015

**Fixes**

- Plugin wasn't connecting to MailChimp for users on MailChimp server `us10` (API keys ending in `-us10`)

#### 2.2.1 - January 12, 2015

**Improvements**

- Use JS object to transfer lists data to Field Wizard.
- Field Wizard strings are now translatable
- Add `is_spam` method to checkbox integration to battle spam sign-ups
- Minor code & code style improvements
- Updated Danish, German, Spanish, French, Italian and Portugese (Brazil) translations

**Additions**

- You can now set `MC_LOCATION`, `MC_NOTES` and `MC_LANGUAGE` from your form HTML
- The submit button now has a default value when generating HTML for it

#### 2.2 - December 9, 2014

**Fixes**

- "Select at least one list" notice appearing when unselecting any MailChimp list in Form settings
- If an error occurs, textareas will no longer lose their value

**Improvements**

- Improved the way form submissions are handled
- Minor code & documentation improvements
- Updated Dutch, French, Portugese and Spanish translations

**Additions**

- Added sign-up checkbox integration for [WooCommerce](https://wordpress.org/plugins/woocommerce/) checkout.
- Added sign-up checkbox integration for [Easy Digital Downloads](https://wordpress.org/plugins/easy-digital-downloads/) checkout.
- The entered email will now be appended to the URL when redirecting to another page