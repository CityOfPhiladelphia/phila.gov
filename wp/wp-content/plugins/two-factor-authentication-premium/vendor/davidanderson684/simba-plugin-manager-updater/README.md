# Simba Plugin Manager - Updater Class

This class is an updates checker and UI for WordPress plugins that are hosted using [the Simba Plugin Manager plugin](https://wordpress.org/plugins/simba-plugin-updates-manager/).

It is intended for plugins that require the supply of access credentials (a customer email address and password) to gain access to updates. For plugins that are available to everyone, you should instead use Yahnis Elsts' plugin update class without any modifications: https://github.com/YahnisElsts/plugin-update-checker

## How to use this class

There are various ways you can install this class, depending on how much composer you want to use or not use.

### 1. Install: Method one: Via composer

If you do not already have a composer.json file, then create one:

```
{
    "require": {
		"davidanderson684/simba-plugin-manager-updater": "1.8.*"
    }
}
```

If you already have one, then just add davidanderson684/simba-plugin-manager-updater to the list of requirements (remember to keep the JSON valid, of course).

Then, in the same directory, run "composer update" (assuming you already have composer installed).

### 1. Install: Method two: Less composer, or entirely manually

Check out a copy of Yahnis' Elsts' plugin update class, version (https://github.com/YahnisElsts/plugin-update-checker). You can do this in two ways:

#### a. Via composer

Whilst in this component's directory (where the composer.json file is), run "composer install". This will then create a sub-directory "vendor", with the plugin update class in vendor/yahnis-elsts/plugin-update-checker. It will be looked for there. You can keep it up to date with "composer update".

#### b. Or, manually

If you prefer to download manually, then download from https://github.com/YahnisElsts/plugin-update-checker and you can place it in a subdirectory "puc", relative to where this class is housed, such that the plugin updater class is in puc/plugin-update-checker.php. It will be looked for there if the composer directory does not exist.

### 2. Include the class in your plugin

Now that you're installed, you need to include the class. Your plugin's constructor is a good place to do this.

If you are using composer, you should copy the updater.php file into your plugin (if you edit the bundled copy in-place, then it will get over-written when you update), in the same directory as your composer.json and vendor directory are (or if otherwise, modify the paths mentioned in it).

`include_once('path/to/your/plugin/updater.php');`

### 3. Edit updater.php to point to where your plugins are hosted

updater.php is a very short file. Find this line ...

`new Updraft_Manager_Updater_1_8('https://example.com/your/WP/mothership/homeurl', 1, 'plugin-dir/plugin-file.php');`

... and:

1. Change it to point to the home URL of your WordPress site that is hosting the plugin (i.e. that is running the Simba Plugin Manager).

2. Change the 1 to be the user ID of the user on your WordPress plugin-distributing site who is hosting this plugin (i.e. the user ID logging into Simba Plugin Manager to provide updates of this plugin).

3. Change the plugin path to be the path (relative to the WP plugin directory, by default wp-content/plugins) to your plugin's main file.

That's it! A fourth parameter is also available, an options array. Available options (specified as key/value pairs) include: debug (boolean), require_login (boolean), auto_backoff (boolean), interval_hours (integer).
