<?php
namespace Static_Maker;

class OptionsUtil {

    public static function get_accepted_post_types() {
        $options = get_option( PLUGIN_NAME );

        return $options[ 'accepted_post_types' ];
    }

    public static function is_accepted_post_type( $post_type ) {
        $types = explode( ',', static::get_accepted_post_types() );
        return in_array( $post_type, $types );
    }

}
