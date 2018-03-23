<?php
namespace Static_Maker;

class Ajax_Admin_Actions {

    static public function process_queue_all() {
        check_ajax_referer( 'process_queue_all' );

        Queue_Actions::dequeue_all();

        echo RsyncUtil::syncWithCurrentOptions();

        wp_die();
    }

    static public function enqueue_single_by_id() {
        global $wpdb;
        check_ajax_referer( 'enqueue_single_by_id' );
        $results = array();

        if ( !isset( $_POST[ 'post_id' ] ) && !isset( $_POST[ 'id' ] ) ) {
            wp_die('ID を指定してください', '', 422);
        }

        $actionType = isset( $_POST[ 'action-type' ] ) && $_POST[ 'action-type' ] === 'remove' ? 'remove' : 'add';

        if ( isset( $_POST[ 'post_id' ] ) ) {
            $results = Queue::enqueue_by_post_id( $_POST[ 'post_id' ], $actionType, true );
        }

        if ( isset( $_POST[ 'id' ] ) ) {
            $results = Queue::enqueue_by_id( $_POST[ 'id' ], $actionType, true );
        }

        foreach ( $results as $result ) {
            if ( $result === false ) {
                return wp_die($wpdb->print_error(), '', 500);
            }
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

            // remove posts which status is not publish
            if ( $post_data[ 'post_status' ] !== 'publish' ) {
                continue;
            }

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

        OptionsUtil::add_accepted_post_type( $post_type );

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

    static public function enqueue_all_pages() {
        check_ajax_referer( 'enqueue_all_pages' );

        $pages = Page::get_pages( array( 'numberposts' => -1 ) );

        $results = array();

        foreach ( $pages as $page ) {
            if ( $page->post_type === 'static-maker-manual' ) {
                $results[] = Queue::enqueue_by_id( $page->id );
            } else {
                $results[] = Queue::enqueue_by_post_id( $page->post_id );
            }
        }

        if ( count( array_filter( $results, array( __CLASS__, 'filter_remove_true' ) ) ) !== 0 ) {
            wp_die('全て、もしくは一部ファイルが登録できていない可能性があります。', '', 500);
        }

        wp_die();
    }

    static public function filter_remove_true($r) {
        return !$r;
    }

    static public function remove_page_from_list() {
        check_ajax_referer( 'remove_page_from_list' );

        $id = $_POST[ 'id' ];

        $page = Page::get_page( $id );

        if ( $page->delete() === false) {
            wp_die('', '', 500);
            return;
        }

        wp_die();
    }

    static public function change_page_status() {
        check_ajax_referer( 'change_page_status' );

        $status = $_POST[ 'action-type' ] === 'disable' ? 0 : 1;

        $id = $_POST[ 'id' ];
        $page = Page::get_page( $id );

        $page->active = $status;

        if ( $page->save() === false) {
            wp_die('', '', 500);
            return;
        }

        wp_die();

    }
}
