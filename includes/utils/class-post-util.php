<?php
namespace Static_Maker;

class PostUtil {
    static function get_post_types() {
        $types = get_post_types(array(), 'objects');
        $excludes = array( 'attachment', 'acf-field-group', 'acf-field' );
        $post_types = array();
        foreach ( $types as $key => $type ) {
            if ( in_array( $key, $excludes ) ) { continue; }
            if ( $type->_builtin && !$type->public ) { continue; }
            $post_types[$key] = $type->label;
        }
        return $post_types;
    }

    static function get_posts( $post_type ) {
        $posts = array();

        if ($post_type) {
            $post_types = array( $post_type );
        } else {
            $post_types = self::get_post_types();
        }

        foreach( $post_types as $post_type ) {
            $opts = array(
                'numberposts' => -1,
                'post_type' => $post_type,
            );
            foreach( get_posts( $opts ) as $post ) {
                $info = array(
                    'ID' => $post->ID,
                    'post_title' => $post->post_title,
                    'permalink' => get_permalink( $post->ID ),
                    'post_type' => $post->post_type,
                );
                $posts[] = $info;
            }
        }

        return $posts;
    }

    /**
     * Get ids of all posts
     *
     * @return array
     */
    static function get_all_post_ids() {
        $posts = array();

        $post_types = self::get_post_types();

        foreach( $post_types as $post_type ) {
            $opts = array(
                'nubmerposts' => -1,
                'post_type' => $post_type,
            );
            foreach( get_posts( $opts ) as $post ) {
                $posts[] = $post->ID;
            }
        }

        return $posts;
    }
}
