<?php
namespace Static_Maker;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PageList {

    protected static $table_name = 'static_makerpage_pages';

    public static function create_table() {
        global $wpdb;

        $table_name = $wpdb->prefix . self::$table_name;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
          id int(20) NOT NULL AUTO_INCREMENT,
          post_id int(20) NOT NULL,
          post_type varchar(20) NOT NULL,
          permalink varchar(255) DEFAULT '' NOT NULL,
          active tinyint(1)
          PRIMARY KEY (id)
        ) $charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

}
