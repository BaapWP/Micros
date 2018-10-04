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

            if ( !current_user_can('edit_plugins') )
                wp_die( __('Sorry, you are not allowed to edit plugins for this site.') );

            $title = __("Edit Micros");

            $micros = get_micros();
            if ( empty( $micros ) ) {
                include( ABSPATH . 'wp-admin/admin-header.php' );
                ?>
                <div class="wrap">
                    <h1><?php echo esc_html( $title ); ?></h1>
                    <div id="message" class="error"><p><?php _e( 'You do not appear to have any micros available at this time.' ); ?></p></div>
                </div>
                <?php
                include( ABSPATH . 'wp-admin/admin-footer.php' );
                exit;
            }

            $file = '';
            $micro = '';

            if ( isset( $_REQUEST['file'] ) ) {
                $file = wp_unslash( $_REQUEST['file'] );

            }

            if ( isset( $_REQUEST['micro'] ) ) {
                $micro = wp_unslash( $_REQUEST['micro'] );
            }

            if ( empty( $micro ) ) {
                if ( $file ) {

                    // Locate the micro for a given micro file being edited.
                    $file_dirname = dirname( $file );
                    foreach ( array_keys( $micros ) as $micro_candidate ) {
                        if ( $micro_candidate === $file || ( '.' !== $file_dirname && dirname( $micro_candidate ) === $file_dirname ) ) {
                            $micro = $micro_candidate;
                            break;
                        }
                    }

                    // Fallback to the file as the micro.
                    if ( empty( $micro ) ) {
                        $micro = $file;
                    }
                } else {
                    $micro = array_keys( $micros );
                    $micro = $micro[0];
                }
            }

            $micro_files = get_micro_files($micro);

            if ( empty( $file ) ) {
                $file = $micro_files[0];
            }

            $file = validate_file_to_edit( $file, $micro_files );
            $real_file = MICROS_UPLOAD_DIR. '/' . $file;

            // Handle fallback editing of file when JavaScript is not available.
            $edit_error = null;
            $posted_content = null;
            if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
                $r = wp_edit_theme_plugin_file( wp_unslash( $_POST ) );
                if ( is_wp_error( $r ) ) {
                    $edit_error = $r;
                    if ( check_ajax_referer( 'edit-micro_' . $file, 'nonce', false ) && isset( $_POST['newcontent'] ) ) {
                        $posted_content = wp_unslash( $_POST['newcontent'] );
                    }
                } else {
                    wp_redirect( add_query_arg(
                        array(
                            'a' => 1, // This means "success" for some reason.
                            'micro' => $micro,
                            'file' => $file,
                        ),
                        admin_url( 'admin.php' )
                    ) );
                    exit;
                }
            }

            // List of allowable extensions
            $editable_extensions = wp_get_plugin_file_editable_extensions( $micro );

            if ( ! is_file($real_file) ) {
                wp_die(sprintf('<p>%s</p>', __('No such file exists! Double check the name and try again.')));
            } else {
                // Get the extension of the file
                if ( preg_match('/\.([^.]+)$/', $real_file, $matches) ) {
                    $ext = strtolower($matches[1]);
                    // If extension is not in the acceptable list, skip it
                    if ( !in_array( $ext, $editable_extensions) )
                        wp_die(sprintf('<p>%s</p>', __('Files of this type are not editable.')));
                }
            }

			// Adds codeEditor settings
			$settings = array(
				'codeEditor' => wp_enqueue_code_editor( array( 'file' => $real_file ) ),
			);

			// Enqueues code editor script
			wp_enqueue_script( 'wp-theme-plugin-editor' );

			// Adds inline script to initialize to code mirror
			wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { wp.themePluginEditor.init( $( "#sub-template" ), %s ); } )', wp_json_encode( $settings ) ) );

			// Adds inline script to set plugin name in  'wp.themePluginEditor.themeOrPlugin'
			wp_add_inline_script( 'wp-theme-plugin-editor', 'wp.themePluginEditor.themeOrPlugin = "micros";' );

            require_once(ABSPATH . 'wp-admin/admin-header.php');

            if ( ! empty( $posted_content ) ) {
                $content = $posted_content;
            } else {
                $content = file_get_contents( $real_file );
            }

            $content = esc_textarea( $content );

            // List of allowable extensions
            //$editable_extensions = wp_get_plugin_file_editable_extensions( $micro );

            //var_dump( $micro_files );

            //error_log( print_r( $micro_files, true ), 3, WP_CONTENT_DIR.'/debug.log' );
			?>
            <div class="wrap">
                <h1>Edit Micros</h1>

                <?php if ( isset( $_GET['a'] ) ) : ?>
                    <div id="message" class="updated notice is-dismissible">
                        <p><?php _e( 'File edited successfully.' ); ?></p>
                    </div>
                <?php elseif ( is_wp_error( $edit_error ) ) : ?>
                    <div id="message" class="notice notice-error">
                        <p><?php _e( 'There was an error while trying to update the file. You may need to fix something and try updating again.' ); ?></p>
                        <pre><?php echo esc_html( $edit_error->get_error_message() ? $edit_error->get_error_message() : $edit_error->get_error_code() ); ?></pre>
                    </div>
                <?php endif; ?>

                <div class="fileedit-sub">
                    <div class="alignright">
                        <form action="admin.php" method="get">
                            <strong><label for="plugin"><?php _e('Select micro to edit:'); ?> </label></strong>
                            <input type="hidden" name="page" value="editor">
                            <select name="micro" id="micro">
                                <?php
                                foreach ( $micros as $micro_key => $a_micro ) {
                                    $micro_name = $a_micro['title'];
//                                    if ( $micro_key == $micro )
//                                        $selected = " selected='selected'";
//                                    else
//                                        $selected = '';
                                    $micro_name = esc_attr($micro_name);
                                    $micro_key = esc_attr($micro_key);
                                    echo "\n\t<option value=\"$micro_key\" $selected>$micro_name</option>";
                                }
                                ?>
                            </select>
                            <?php submit_button( __( 'Select' ), '', 'Submit', false ); ?>
                        </form>
                    </div>
                    <br class="clear" />
                </div>
                <div id="templateside">
                    <h2 id="micro-files-label"><?php _e( 'Micro Files' ); ?></h2>
                    <?php
                    $micro_editable_files = array();
                    //var_dump($micro_files);
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
                    <?php wp_nonce_field( 'edit-micro_' . $file, 'nonce' ); ?>
                    <div>
                        <label for="newcontent" id="theme-plugin-editor-label"><?php _e( 'Selected file content:' ); ?></label>
                        <textarea cols="70" rows="30" name="newcontent" id="newcontent"><?php echo $content; ?></textarea>
                        <input type="hidden" name="file" value="<?php echo esc_attr( $file ); ?>" />
                        <input type="hidden" name="micros" value="<?php echo esc_attr( $micro ); ?>" />
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