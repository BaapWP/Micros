<?php
/**
 *
 */
defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'Micros_Admin' ) ) {

	/**
	 *
	 */
	class Micros_Admin {

		/**
		 *
		 */
		public function hook() {

			//register Micro page hook to admin menu
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );

			// register Micro sub page - Editor
			add_action( 'admin_menu', array( $this, 'admin_submenu' ) );
		}

		/**
		 *
		 */
		public function admin_menu() {
			add_menu_page(
				__( 'Micros', 'micros' ), __( 'Micros', 'micros' ), 'manage_options', 'micros', 'micro_page_render_callback', 'dashicons-editor-expand', 70
			);
		}

// add_menu_page callback
		/**
		 *
		 * @global type $title
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
		 *
		 */
		public function admin_submenu() {
			add_submenu_page(
				'micros', 'Editor', 'Editor', 'manage_options', 'editor', 'micros_submenu_page_callback' );
		}

		/**
		 *
		 * @global type $title
		 */
		public function admin_submenu_callback() {
			global $title;
			$settings = array(
				'codeEditor' => wp_enqueue_code_editor( array( 'type' => 'text/html' ) ),
			);
			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { wp.themePluginEditor.init( $( "#sub-template" ), %s ); } )', wp_json_encode( $settings ) ) );
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