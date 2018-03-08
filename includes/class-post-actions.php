<?php
namespace Static_Maker;

class Post_Actions {
    function every_post_update( $post_id, $post ) {
        if ( wp_is_post_revision( $post_id ) )
            return;

        if ( $post->post_status !== 'publish' ) {
            return;
        }

        Queue::queue_by_post_id( $post_id );
    }
}
