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
    error_log( print_r( $micro_editable_files, true ), 3, WP_CONTENT_DIR.'/debug.log' );
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
    global $file, $micro;
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
                'micro' => rawurlencode($micro),
            ),
            self_admin_url('micro-editor.php')
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