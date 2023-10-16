<?php
/**
 * Plugin Name:       WP Community Connector
 * Plugin URI:        https://github.com/achaitanyajami/wp-community-connector
 * Requires WP:       6.0 ( Minimal )
 * Requires PHP:      8.0
 * Version:           1.0.0
 * Author:            achaitanyajami
 * Text Domain:       WpCommunityConnector
 * Domain Path:       /language/
 *
 * @package           wp-community-connector
 * @sub-package       WordPress
 */

namespace Acj;

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ACJ_PLUGIN_VERSION', '1.0.0' );
define( 'ACJ_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACJ_DIR_URL', plugin_dir_url( __FILE__ ) );

/**
 * Composer Autoload file.
 */
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include __DIR__ . '/vendor/autoload.php';
}
