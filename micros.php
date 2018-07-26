<?php
/*
  Plugin Name:  Micros
  Version:      0.0.1
  Author:       Jitender Singh
  Author URI:   https://github.com/jitendersinghwp/
  License:      GPL2
  License URI:  https://www.gnu.org/licenses/gpl-2.0.html
  Text Domain:  micros
  Domain Path:  /languages
 */

/**
 * Summary.
 *
 * @var string The
 */
define( 'MICROS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'MICROS_UPLOAD_DIR' ) ) {
	/**
	 *
	 */
	define( 'MICROS_UPLOAD_DIR', WP_CONTENT_DIR . '/micros' );
}

/**
 *
 */
include_once( MICROS_PLUGIN_DIR . 'admin/class-micros-admin.php' );

$micros_admin = new Micros_Admin();

$micros_admin->hook();

unset( $micros_admin );
