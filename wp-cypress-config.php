<?php
// phpcs:disable
ini_set( 'display_errors', 'Off' );
ini_set( 'error_reporting', E_ALL );
define( 'WP_DEBUG', false );
define( 'WP_DEBUG_DISPLAY', false );
// phpcs:enable

// Load VIP config.
$vip_config = ABSPATH . '/wp-content/vip-config/vip-config.php';

if ( file_exists( $vip_config ) ) {
	require_once ( $vip_config );
}