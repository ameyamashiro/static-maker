<?php
namespace Static_Maker;

class Post_Actions {
    function change_post_status( $new_status, $old_status, $post ) {
        switch ( $new_status ) {
            case 'publish':
                Queue::enqueue_by_post_id( $post->ID );
                break;
            case 'trash':
                Queue::enqueue_by_post_id( $post->ID, 'remove' );
                break;
            default:
                break;
        }
    }
}
