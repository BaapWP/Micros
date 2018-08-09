<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package micros
 * @subpackage micros/admin
 * @since 0.0.1
 */

//exit if 'ABSPATH' constant not defined
defined( 'ABSPATH' ) || exit();

// create 'Micros_Admin' class when not defined
if ( ! class_exists( 'Micros_Admin' ) ) {

	/**
	 * This is the main class used to register and render admin pages.
	 */
	class Micros_Admin {

		/**
		 * fires action hooks to register pages.
		 */
		public function hook() {

			// hook to 'admin_menu' to register admin page
			add_action( 'admin_menu', array( &$this, 'admin_menu' ) );

            // hook to 'admin_menu' to register admin submenu page - 'Editor'
			add_action( 'admin_menu', array( &$this, 'admin_submenu' ) );
		}

		/**
		 * Register admin page
		 */
		public function admin_menu() {
			add_menu_page(
				__( 'Micros', 'micros' ), __( 'Micros', 'micros' ), 'manage_options', 'micros', array(&$this, 'admin_menu_callback'), 'dashicons-editor-expand', 70
			);
		}

		/**
		 * Render HTML of admin page
		 */
		public function admin_menu_callback() {
		    // admin page title
			global $title;
			?>
			<div class="wrap">
				<h1><?php echo $title; ?></h1>
			</div>
			<?php
		}

        /**
         * Register admin submenu page - 'Editor'
         */
		public function admin_submenu() {
			add_submenu_page(
				'micros',
                __( 'Editor', 'micros' ),
                __( 'Editor', 'micros' ),
                'manage_options',
                'editor',
                array(&$this, 'admin_submenu_callback' ));
		}

		/**
		 * Render HTML of admin submenu page - 'Editor'
		 */
		public function admin_submenu_callback() {
		    //admin submenu page title
			global $title;

			// Add codeEditor settings
			$settings = array(
				'codeEditor' => wp_enqueue_code_editor( array( 'type' => 'text/html' ) ),
			);

			// enqueue code editor script
			wp_enqueue_script( 'wp-theme-plugin-editor' );

			// add inline script to initialize to code mirror
			wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { wp.themePluginEditor.init( $( "#sub-template" ), %s ); } )', wp_json_encode( $settings ) ) );

			// add inline script to set plugin name in  'wp.themePluginEditor.themeOrPlugin'
			wp_add_inline_script( 'wp-theme-plugin-editor', 'wp.themePluginEditor.themeOrPlugin = "micros";' );
			?>
			<div class="wrap">
				<div id="icon-tools" class="icon32"></div>
				<h2><?php echo $title; ?></h2>
				<form action="#" id="sub-template">
					<textarea cols="70" rows="30" name="newcontent" id="newcontent"></textarea>
				</form>
			</div>
			<?php
		}

	}

}