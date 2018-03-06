<?php

class FileHelper {

    public function test_hook() {
        check_ajax_referer( 'file_get_content' );

        if ( !isset($_POST[ 'url' ]) ) {
            wp_die(422);
        }

        $url = $_POST[ 'url' ];
        echo $this->file_get_content( $url );

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
}
