<?php
namespace Static_Maker;

class Post_Actions {
    function change_post_status( $new_status, $old_status, $post ) {
        switch ( $new_status ) {
            case 'publish':
                if ( OptionsUtil::is_accepted_post_type( $post->post_type ) ) {
                    if ( !Page::get_page_by_post_id( $post->ID ) ) {
                        Page::create( $post->ID, $post->post_type, get_permalink( $post->ID ) );
                    }
                    Queue::enqueue_by_post_id( $post->ID, 'add', true );
                }
                break;
            case 'trash':
                Queue::enqueue_by_post_id( $post->ID, 'remove' );
                break;
            default:
                break;
        }
    }
}
