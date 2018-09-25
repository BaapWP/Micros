<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package micros
 * @subpackage micros/admin
 * @since 0.0.1
 */

/**
 * Require helper functions
 */
require_once( MICROS_PLUGIN_DIR.'/admin/helpers.php' );

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

            /** WordPress Administration Bootstrap */
            require_once( dirname( __FILE__ ) . '/admin.php' );

            if ( !current_user_can('edit_plugins') )
                wp_die( __('Sorry, you are not allowed to edit plugins for this site.') );

			global $title;

            global $file;

            if ( isset( $_REQUEST['file'] ) ) {
                $file = wp_unslash( $_REQUEST['file'] );

            }else{
                $micro_dir = WP_CONTENT_DIR.'/micro-micro';
                $get_first_file_or_dir = scandir( $micro_dir );
                if( is_dir( $micro_dir."/{$get_first_file_or_dir[2]}" ) ){
                    $get_file = scandir( $micro_dir."/{$get_first_file_or_dir[2]}" );
                    $file = $micro_dir."/{$get_first_file_or_dir[2]}"."/{$get_file[2]}";

                }else{
                    $file = $micro_dir."/{$get_first_file_or_dir[2]}";
                }
            }
            //var_dump($file);
            $file_content = esc_textarea( file_get_contents( $file ) );

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

            // List of allowable extensions
            $editable_extensions = wp_get_plugin_file_editable_extensions( $micro );

            $micro_files = get_micro_files();
            //error_log( print_r( $micros, true ), 3, WP_CONTENT_DIR.'/debug.log' );
			?>
            <div class="wrap">
                <div id="templateside">
                    <h2 id="micro-files-label"><?php _e( 'Micro Files' ); ?></h2>
                    <?php
                    $micro_editable_files = array();
                    foreach ( $micro_files as $micro_file ) {
                        if ( preg_match('/\.([^.]+)$/', $micro_file, $matches ) && in_array( $matches[1], $editable_extensions ) ) {
                            $micro_editable_files[] = $micro_file;
                        }
                    }
                    ?>
                    <ul role="tree" aria-labelledby="plugin-files-label">
                        <li role="treeitem" tabindex="-1" aria-expanded="true" aria-level="1" aria-posinset="1" aria-setsize="1">
                            <ul role="group">
                                <?php wp_print_micro_file_tree( wp_make_micro_file_tree( $micro_editable_files ) ); ?>
                            </ul>
                    </ul>
                </div>

                <form action="#" id="sub-template">
                    <?php wp_nonce_field( 'edit-plugin_' . $file, 'nonce' ); ?>
                    <div>
                        <label for="newcontent" id="theme-plugin-editor-label"><?php _e( 'Selected file content:' ); ?></label>
                        <textarea cols="70" rows="30" name="newcontent" id="newcontent"><?php echo $file_content; ?></textarea>
                        <input type="hidden" name="file" value="<?php //echo esc_attr( $file ); ?>" />
                        <input type="hidden" name="micros" value="<?php //echo esc_attr( $micros ); ?>" />
                    </div>
                    <p class="submit">
                        <?php submit_button( __( 'Update File' ), 'primary', 'submit', false ); ?>
                        <span class="spinner"></span>
                    </p>
                </form>
                <br class="clear" />
            </div>
			<?php
		}

	}

}