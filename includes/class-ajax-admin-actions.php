<?php
namespace Static_Maker;

class Ajax_Admin_Actions {

    static public function fetch_single_html() {
        check_ajax_referer( 'single_file_get_content' );

        if ( !isset($_POST[ 'url' ]) ) {
            wp_die('', '', 422);
        }

        $url = $_POST[ 'url' ];
        $content = FileUtil::file_get_content( $url ) or wp_die('', '', 500);
        $dir = str_replace(get_home_url(), '', $url);

        FileUtil::file_put_content( $content, 'index.html', $dir ) or wp_die('', '', 500);

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

            if (!$post->save()) {
                wp_die('', '', 500);
                break;
            }
        }

        wp_die();
    }
}
