<?php
/**
 * Plugin Name:       WP Community Connector
 * Plugin URI:        https://github.com/achaitanyajami/wp-community-connector
 * Requires WP:       6.0 ( Minimal )
 * Requires PHP:      8.0
 * Version:           1.0.1
 * Author:            achaitanyajami
 * Text Domain:       WpCommunityConnector
 * Domain Path:       /language/
 *
 * @package           wp-community-connector
 * @sub-package       WordPress
 */

namespace Acj\Wpcc;

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
define( 'ACJ_WPCC_PLUGIN_VERSION', '1.0.1' );
define( 'ACJ_WPCC_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACJ_WPCC_DIR_URL', plugin_dir_url( __FILE__ ) );
define( 'ACJ_WPCC_REPORTS_ENDPOINT', 'reports' );

/**
 * Composer Autoload file.
 */
if ( is_readable( __DIR__ . '/vendor/autoload.php' ) ) {
	include __DIR__ . '/vendor/autoload.php';
}

use Acj\Wpcc\Admin\Menu;
use Acj\Wpcc\ResponseParser as ResponseConverter;
use Acj\Wpcc\RestApi\Route;

/**
 * Generate a report endpoint.
 */
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		foreach ( $endpoints as $route => $endpoint ) {
			$modified_route               = $route . '/' . ACJ_WPCC_REPORTS_ENDPOINT;
			$endpoints[ $modified_route ] = $endpoint;
		}

		return $endpoints;
	}
);

/**
 * Transform all the reports endpoint for CC connector.
 */
add_filter(
	'rest_request_after_callbacks',
	function ( $response, $handler, $request ) {
		return ( new ResponseConverter( $response, $request ) )->init();
	},
	10,
	3
);

/**
 * Initiate the menu here.
 */
( new Menu() )->init();

/**
 * Register Rest.
 */
( new Route() )->init();
