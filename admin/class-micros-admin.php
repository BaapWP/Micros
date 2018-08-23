<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package micros
 * @subpackage micros/admin
 * @since 0.0.1
 */

//Exits if 'ABSPATH' constant not defined
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'Micros_Admin' ) ) {

	/**
     * The admin-specific functionality of the plugin.
     *
	 * This is the main class used to register and render admin pages.
     *
     * @package micros
     * @subpackage micros/admin
     * @author Saurabh Skukla
	 */
	class Micros_Admin {

        /**
         * Registers micros's menu and submenu pages to the admin panel
         *
         * @since 0.0.1
         */
		public function hook() {

			// Hook to admin_menu
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

            // Hook to admin_menu
			add_action( 'admin_menu', array( $this, 'admin_submenu' ) );
		}

		/**
		 * Registers micros page
         *
         * @since 0.0.1
		 */
		public function admin_menu() {
			add_menu_page( __( 'Micros', 'micros' ), __( 'Micros', 'micros' ), 'manage_options', 'micros', array( $this, 'admin_menu_callback' ), 'dashicons-editor-expand', 70 );
		}

		/**
		 * Renders HTML of micros page
         *
         * @since 0.0.1
		 */
		public function admin_menu_callback() {

			global $title;
			?>
			<div class="wrap">
				<h1><?php echo $title; ?></h1>
			</div>
			<?php
		}

        /**
         * Registers micros editor page
         *
         * @since 0.0.1
         */
		public function admin_submenu() {
			add_submenu_page( 'micros', __( 'Editor', 'micros' ), __( 'Editor', 'micros' ), 'manage_options', 'editor', array( $this, 'admin_submenu_callback' ) );
		}

		/**
		 * Renders HTML of micros editor page
         *
         * @since 0.0.1
		 */
		public function admin_submenu_callback() {

			global $title;

			// Adds codeEditor settings
			$settings = array(
				'codeEditor' => wp_enqueue_code_editor( array( 'type' => 'text/html' ) ),
			);

			// Enqueues code editor script
			wp_enqueue_script( 'wp-theme-plugin-editor' );

			// Adds inline script to initialize to code mirror
			wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { wp.themePluginEditor.init( $( "#sub-template" ), %s ); } )', wp_json_encode( $settings ) ) );

			// Adds inline script to set plugin name in  'wp.themePluginEditor.themeOrPlugin'
			wp_add_inline_script( 'wp-theme-plugin-editor', 'wp.themePluginEditor.themeOrPlugin = "micros";' );
			?>
			<div class="wrap">
				<div id="icon-tools" class="icon32"></div>
				<h2><?php echo $title; ?></h2>
				<form action="#" id="sub-template">
					<textarea cols="70" rows="30" name="newcontent" id="newcontent"><h1>WP Dev Classes</h1></textarea>
				</form>
			</div>
			<?php
		}

	}

}