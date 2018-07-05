<?php
namespace Static_Maker;

class Post_Actions {
    function change_post_status( $new_status, $old_status, $post ) {
        switch ( $new_status ) {
            case 'publish':
                if ( OptionsUtil::is_accepted_post_type( $post->post_type ) ) {
                    if ( !Page::get_page_by_post_id( $post->ID ) ) {
                        Page::create( $post->ID, $post->post_type, get_permalink( $post->ID ) );
                    } else {
                        // Change status to enabled
                        $page = Page::get_page_by_post_id($post->ID);
                        $page->active = 1;
                        $page->save();
                    }

                    Queue::enqueue_by_post_id( $post->ID, 'add', true );
                }
                break;
            case 'trash':
                Queue::enqueue_by_post_id( $post->ID, 'remove', true );
                break;
            default:
                $page = Page::get_page_by_post_id($post->ID);
                if ($page) {
                    // Change status to disabled
                    $page->active = 0;
                    $page->save();
                }
                break;
        }
    }
}
