<?php
namespace Static_Maker;

class OptionsUtil
{

    public static function get_option()
    {
        return get_option(PLUGIN_NAME);
    }

    public static function get_accepted_post_types($format = 'array')
    {
        $options = get_option(PLUGIN_NAME)['accepted_post_types'];

        switch ($format) {
            case 'string':
                $types = $options;
                break;
            case 'array':
            default:
                $types = explode(',', $options);

        }
        return $types;
    }

    public static function is_accepted_post_type($post_type)
    {
        $types = static::get_accepted_post_types();
        return in_array($post_type, $types);
    }

    public static function add_accepted_post_type($post_type)
    {
        if (empty($post_type)) {return;}

        $option = static::get_option();

        $current_types = static::get_accepted_post_types();
        array_push($current_types, $post_type);
        $current_types = array_unique($current_types);
        $option['accepted_post_types'] = implode(',', $current_types);

        update_option(PLUGIN_NAME, $option);
    }

}
