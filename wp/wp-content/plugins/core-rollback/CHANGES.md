[ unreleased ]

#### 1.4.3 / 2026-07-30
* update guard in `add_rollback_offer()`

#### 1.4.2 / 2026-03-29
* remove null coalescing so works with PHP 5.6

#### 1.4.1 / 2025-12-09
* add hardening to `get_core_versions()`
* account for `update_core` transient as `false`

#### 1.4.0 / 2025-11-17
* return rollback offer with `update_core` transient
* update POT GitHub Action

#### 1.3.7 / 2024-12-02
* start in `init` hook
* add GitHub Action to generate POT

#### 1.3.6 / 2024-11-01
* remove `load_plugin_textdomain()`
* composer update

#### 1.3.5 / 2023-07-12
* update rollback choices for PHP version

#### 1.3.4 / 2023-02-07
* composer update

#### 1.3.3 / 2023-02-05
* update for PHP 8.1

#### 1.3.2 / 2022-05-10
* use `wp_is_block_theme()` for check

#### 1.3.1 / 2022-02-08
* use `sanitize_key()` for nonces

#### 1.3.0 / 2022-01-28
* filter WP versions with significant deprecation notices/errors for newer PHP versions
* add notice about limiting of rollback options for PHP versions

#### 1.2.4 / 2022-01-14
* proper nonce verification of settings page
* remove unneededd `version_compare` check for notice

#### 1.2.3 / 2022-01-11
* I suck and so do typos

#### 1.2.2 / 2022-01-11
* need to use `method_exists` in check to properly function

#### 1.2.1 / 2022-01-11
* add `function_exists( 'is_block_theme' )` check to avoid fatal

#### 1.2.0 / 2022-01-10
* add method to return array of block capable WP versions for rollback

#### 1.1.2 / 2022-01-10
* add notice if currently using block theme, thanks @costdev

#### 1.1.1 / 2021-10-14
* use `sanitize_title_with_dashes()` as `sanitize_file_name()` maybe have attached filter that changes output

#### 1.1.0 / 2021-07-07
* add @10up GitHub Actions for WordPress SVN

#### 1.0.1 / 2020-12-04
* fix text-domain in string, thanks Alex

#### 1.0.0 / 2020-11-17
* initial release to dot org repository
* add `Class Bootstrap` to intiate process
* updated instructions, etc
* add assets, screenshots, banners, icon
* add `readme.txt` and clean up `create_admin_page()`
* add warning notice to settings
* add `force-check` query arg to redirect to more consistently display the `Re-install Now` button.
* limit rollbacks to WP > 4.0, I found some non-recoverable issue in my local testing
* initial release
