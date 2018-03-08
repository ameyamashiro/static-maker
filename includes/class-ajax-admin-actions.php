<?php
namespace Static_Maker;

class Ajax_Admin_Actions {

    static public function process_queue_all() {
        check_ajax_referer( 'process_queue_all' );

        Queue_Actions::dequeue_all();

        wp_die();
    }

    static public function enqueue_single_by_id() {
        check_ajax_referer( 'enqueue_single_by_id' );

        if ( !isset( $_POST[ 'post_id' ] ) && !isset( $_POST[ 'id' ] ) ) {
            wp_die('ID を指定してください', '', 422);
        }

        if ( isset( $_POST[ 'post_id' ] ) ) {
            $result = Queue::enqueue_by_post_id( $_POST[ 'post_id' ] );
        }

        if ( isset( $_POST[ 'id' ] ) ) {
            $result = Queue::enqueue_by_id( $_POST[ 'id' ] );
        }

        if ( $result === false ) {
            wp_die('', '', 500);
        }

        wp_die();
    }

    /**
     * Export html immediately
     */
    static public function fetch_single_html() {
        check_ajax_referer( 'single_file_get_content' );

        if ( !isset($_POST[ 'url' ]) || !filter_var( $_POST[ 'url' ], FILTER_VALIDATE_URL ) ) {
            wp_die('URL を正しく入力してください', '', 422);
        }

        FileUtil::export_single_file( $_POST[ 'url' ] ) or wp_die('', '', 500);

        wp_die();
    }

    static public function add_pages_by_post_type() {
        check_ajax_referer( 'add_pages_by_post_type' );

        if ( !isset($_POST[ 'post_type' ]) ) {
            wp_die('', '', 422);
        }

        $post_type = $_POST[ 'post_type' ];

        $posts = PostUtil::get_posts( $post_type );

        foreach( $posts as $post_data ) {
            $post = new Page( array(
//                'post_title' => $post_type->post_title,
                'post_id' => $post_data[ 'ID' ],
                'post_type' => $post_data[ 'post_type' ],
                'permalink' => $post_data[ 'permalink' ],
                'active' => 1,
            ) );

            if ($post->save() === false) {
                wp_die('', '', 500);
                break;
            }
        }

        wp_die();
    }

    static public function add_page_by_url() {
        check_ajax_referer( 'add_page_by_url' );

        if ( !isset($_POST[ 'url' ]) || !filter_var( $_POST[ 'url' ], FILTER_VALIDATE_URL ) ) {
            wp_die('URL を正しく入力してください', '', 422);
        }

        $post = new Page( array(
            'post_type' => 'static-maker-manual',
            'permalink' => $_POST[ 'url' ],
            'active' => 1,
        ) );

        if ($post->save() === false) {
            wp_die('', '', 500);
            return;
        }

        wp_die();
    }
}