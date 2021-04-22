<?php declare(strict_types=1); // phpcs:disable Generic.Files.LineEndings.InvalidEOLChar

/**
 * Plugin Name:       Inpsyde Users
 * Description:       Load user list and details from API call
 * Plugin URI:        https://github.com/josualeonard/inpsyde-users
 * GitHub URI:        josualeonard/inpsyde-users
 * GitHub Plugin URI: josualeonard/inpsyde-users
 * Author:            Josua Leonard
 * Version:           1.0.0
 * Licence:           MIT
 * License URI:       ./LICENSE
 * Author URI:        https://github.com/josualeonard/inpsyde-users
 * Last Change:       2021-04-19
 */

 // If we haven't loaded this plugin from Composer we need to add our own autoloader
if (!class_exists('InpsydeUsers\Plugin')) {
    // Get a reference to our PSR-4 Autoloader function that we can use to add our
    // Acme namespace
    $autoloader = require_once('autoload.php');

    // Use the autoload function to setup our class mapping
    $autoloader('InpsydeUsers\\', __DIR__ . '/src/InpsydeUsers/');

    // Also autoload required libraries
    require "vendor/autoload.php";
}

if (!function_exists('get_plugin_data')) {
    require_once ABSPATH.'/wp-admin/includes/plugin.php';
}

// Pass plugin data to init
\InpsydeUsers\Plugin::init(get_plugin_data(__FILE__));
