<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the file information in the plugin
 * admin area. This file include all the dependencies used by the plugin.
 *
 * @package micors
 *
 * @wordpress-plugin
 * Plugin Name:  Micros
 * Plugin URI: https://github.com/BaapWP/Micros/
 * Description: Extends WordPress to add a decentralised micro-customisation distribution & management system.
 * Version:      0.0.1
 * Author:       Jitender Singh
 * Author URI:   https://github.com/jitendersinghwp/
 * License:      GPL2
 * License URI:  https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:  micros
 * Domain Path:  /languages
 */

/**
 * Creates plugin root directory path
 *
 * @var string
 */
define( 'MICROS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

if ( ! defined( 'MICROS_UPLOAD_DIR' ) ) {
	/**
	 * Creates upload directory path where all micors store
     *
     * @var string
	 */
	define( 'MICROS_UPLOAD_DIR', WP_CONTENT_DIR . '/micros' );
}

/**
 * Provides functions to create admin pages
 */
include_once( MICROS_PLUGIN_DIR . 'admin/class-micros-admin.php' );

// Makes instance of 'MICRO_ADMIN' class
$micros_admin = new Micros_Admin();

// Render admin pages
$micros_admin->hook();

// Unset instance
unset( $micros_admin );