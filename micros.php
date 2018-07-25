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

//directory constant
define( 'Micros_PLUGIN_DIR', plugin_dir_path( __FILE ) );

//register Micro page hook to admin menu
add_action('admin_menu', 'micros_page_register');

if(!function_exists('micros_page_register')){
    function micros_page_register(){
        add_menu_page(
            __('Micros', 'micros'),
            __('Micros', 'micros'),
            'manage_options',
            'micros',
            'micro_page_render_callback',
            'dashicons-editor-expand',
            70
        );
    }
}
// add_menu_page callback
if(!function_exists('micro_page_render_callback')){
    function micro_page_render_callback(){
        global $title;
        ?>
        <div class="wrap">
            <h1><?php echo $title; ?></h1>
        </div>
        <?php
    }
}