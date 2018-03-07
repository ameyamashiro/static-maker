<?php
namespace Static_Maker;

class FileHelper {

    /**
     * called from wp_ajax action
     */
    public function fetch_html_action() {
        check_ajax_referer( 'file_get_content' );

        if ( !isset($_POST[ 'url' ]) ) {
            wp_die(422);
        }

        $url = $_POST[ 'url' ];
        $content = $this->file_get_content( $url ) or wp_die(500);
        $dir = str_replace(get_home_url(), '', $url);

        $this->file_put_content( $content, 'index.html', $dir ) or wp_die(500);

        wp_die();
    }

    public function file_get_content( $url ) {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
            ),
        ));

        return file_get_contents( $url, false, $context );
    }

    public function file_put_content( $content, $file_name = 'index.html', $subdir = '' ) {
        $export_path = wp_upload_dir()[ 'path' ];

        if (!empty($subdir) && !is_dir( $export_path . $subdir )) {
            if (!mkdir( $export_path . $subdir, 0700, true )) {
                wp_die(500);
            }
        }

        return file_put_contents( $export_path . $subdir . $file_name, $content );
    }

}
