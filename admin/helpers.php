<?php
/**
 * Micros helper functions
 */


/**
* Get a list of a micro's files.
 *
 * @param string $micro Path to the main micro file from micros-micros directory.
 * @return array List of files relative to the micros-micros root.
 */
function get_micro_files( ) {
	$dir = WP_CONTENT_DIR . '/micro-micro';

	if ( is_dir( $dir ) && WP_CONTENT_DIR !== $dir ) {

        $list_files = list_files( $dir );

		$micro_files = array_values( array_unique( $list_files ) );
	}
    //error_log( print_r( $micro_files, true ), 3, WP_CONTENT_DIR.'/debug.log' );
	return $micro_files;
}

/**
 * Makes a tree structure for the micro editor's file list.
 *
 * @since 4.9.0
 * @access private
 *
 * @param string $micro_editable_files List of micro file paths.
 * @return array Tree structure for listing micro files.
 */
function wp_make_micro_file_tree( $micro_editable_files ) {
    $tree_list = array();
    //error_log( print_r( $micro_editable_files, true ), 3, WP_CONTENT_DIR.'/debug.log' );
    foreach ( $micro_editable_files as $micro_file ) {
        $list     = explode( '/', preg_replace( '#^.+?/#', '', $micro_file ) );
        $last_dir = &$tree_list;
        //error_log( print_r( $list, true ), 3, WP_CONTENT_DIR.'/debug.log' );
        foreach ( $list as $dir ) {
            $last_dir =& $last_dir[ $dir ];
        }
            $last_dir = $micro_file;
    }
        //error_log( print_r( $tree_list, true ), 3, WP_CONTENT_DIR.'/debug.log' );
    return $tree_list['wp-content']['micro-micro'];
}
/**
 * Outputs the formatted file list for the micro editor.
 *
 * @since 4.9.0
 * @access private
 *
 * @param array|string $tree  List of file/folder paths, or filename.
 * @param string       $label Name of file or folder to print.
 * @param int          $level The aria-level for the current iteration.
 * @param int          $size  The aria-setsize for the current iteration.
 * @param int          $index The aria-posinset for the current iteration.
 */
function wp_print_micro_file_tree( $tree, $label = '', $level = 2, $size = 1, $index = 1 )
{
    //global $file, $micro;
    global $file;
    if (is_array($tree)) {
        $index = 0;
        $size = count($tree);
        foreach ($tree as $label => $micro_file) :
            $index++;
            if (!is_array($micro_file)) {
                wp_print_micro_file_tree($micro_file, $label, $level, $index, $size);
                continue;
            }
            ?>
            <li role="treeitem" aria-expanded="true" tabindex="-1"
                aria-level="<?php echo esc_attr($level); ?>"
                aria-setsize="<?php echo esc_attr($size); ?>"
                aria-posinset="<?php echo esc_attr($index); ?>">
                <span class="folder-label"><?php echo esc_html($label); ?> <span
                        class="screen-reader-text"><?php _e('folder'); ?></span><span aria-hidden="true"
                                                                                      class="icon"></span></span>
                <ul role="group" class="tree-folder"><?php wp_print_micro_file_tree($micro_file, '', $level + 1, $index, $size); ?></ul>
            </li>
            <?php
        endforeach;
    } else {
        $url = add_query_arg(
            array(
                'file' => rawurlencode($tree),
                //'micro' => rawurlencode($micro),
            ),
            self_admin_url('admin.php?page=editor')
        );
        ?>
        <li role="none" class="<?php echo esc_attr($file === $tree ? 'current-file' : ''); ?>">
            <a role="treeitem" tabindex="<?php echo esc_attr($file === $tree ? '0' : '-1'); ?>"
               href="<?php echo esc_url($url); ?>"
               aria-level="<?php echo esc_attr($level); ?>"
               aria-setsize="<?php echo esc_attr($size); ?>"
               aria-posinset="<?php echo esc_attr($index); ?>">
                <?php
                if ($file === $tree) {
                    echo '<span class="notice notice-info">' . esc_html($label) . '</span>';
                } else {
                    echo esc_html($label);
                }
                ?>
            </a>
        </li>
        <?php
    }
}

if( !function_exists('get_micros') ):
    /**
     * Check the micros directory and retrieve all micro files with all meta data.
     *
     * The file with the micro meta data is the file that will be included and therefore
     * needs to have the main execution for the micro. This does not mean
     * everything must be contained in the file and it is recommended that the file
     * be split for maintainability. Keep everything in one file for extreme
     * optimization purposes.
     *
     * @since 1.5.0
     *
     * @param string $micro_folder Optional. Relative path to single micro folder.
     * @return array Key is the plugin file path and the value is an array of the plugin data.
     */
    function get_micros($micros_folder = '') {

        if ( ! $cache_micros = wp_cache_get('micros', 'micros') )
            $cache_micros = array();

        if ( isset($cache_micros[ $micros_folder ]) )
            return $cache_micros[ $micros_folder ];

        $wp_micros = array ();
        $micro_root = MICROS_UPLOAD_DIR;
        if ( !empty($micros_folder) )
            $micro_root .= $micros_folder;

        // Files in wp-content/micros directory
        $micros_dir = @ opendir( $micro_root);

        $micro_files = array();
        if ( $micros_dir ) {
            while (($file = readdir( $micros_dir ) ) !== false ) {
                if ( substr($file, 0, 1) == '.' )
                    continue;
                if ( is_dir( $micro_root.'/'.$file ) ) {
                    $micros_subdir = @ opendir( $micro_root.'/'.$file );
                    if ( $micros_subdir ) {
                        while (($subfile = readdir( $micros_subdir ) ) !== false ) {
                            if ( substr($subfile, 0, 1) == '.' )
                                continue;
                            if ( $subfile == 'micros.json' )
                                $micro_files[] = "$micro_root/$file/$subfile";
                        }
                        closedir( $micros_subdir );
                    }
                } else {
                    if ( $file == 'micros.json' )
                        $micro_files[] = $file;
                }
            }
            closedir( $micros_dir );
        }

        if ( empty($micro_files) )
            return $wp_micros;

        foreach ( $micro_files as $micro_file ) {

            if ( !is_readable( "$micro_file" ) )
                continue;

            $get_micro_data = file_get_contents( $micro_file );
            $decode_micro_data = json_decode( preg_replace( '/,(?!\s*?[\{\[\"\'\w])/i', '', $get_micro_data ), true );

            if ( empty ( $decode_micro_data['name'] ) )
                continue;

            $wp_micros[ $micro_file ] = $decode_micro_data;
        }

        $cache_micros[ $micros_folder ] = $wp_micros;
        wp_cache_set('micros', $cache_micros, 'micros');
        return $wp_micros;
    }
endif;