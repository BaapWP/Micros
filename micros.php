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

// register Micro sub page - Editor
add_action('admin_menu', 'register_micros_submenu_page');

function register_micros_submenu_page() {
    add_submenu_page(
        'micros',
        'Editor',
        'Editor',
        'manage_options',
        'editor',
        'micros_submenu_page_callback' );
}

if(!function_exists('micros_submenu_page_callback')){
    function micros_submenu_page_callback() {
        global $title;
        $settings = array(
            'codeEditor' => wp_enqueue_code_editor( array('type' => 'text/html') ),
        );
        wp_enqueue_script( 'wp-theme-plugin-editor' );
        wp_add_inline_script( 'wp-theme-plugin-editor', sprintf( 'jQuery( function( $ ) { wp.themePluginEditor.init( $( "#sub-template" ), %s ); } )', wp_json_encode( $settings ) ) );
        wp_add_inline_script( 'wp-theme-plugin-editor', 'wp.themePluginEditor.themeOrPlugin = "plugin";' );
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