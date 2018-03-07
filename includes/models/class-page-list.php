<?php
namespace Static_Maker;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PageList {

    protected static $table_name = 'static_makerpage_pages';

    protected static $columns = array();
    protected $data = array();

    public static function table_name() {
        global $wpdb;
        return $wpdb->prefix . self::$table_name;
    }

    public static function create_table() {
        global $wpdb;

        $table_name = self::table_name();
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
          id int(20) NOT NULL AUTO_INCREMENT,
          post_id int(20) NOT NULL,
          post_type varchar(20) NOT NULL,
          permalink varchar(255) DEFAULT '' NOT NULL,
          active tinyint(1) DEFAULT 1 NOT NULL,
          PRIMARY KEY (id)
        ) $charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_pages() {
        global $wpdb;
        $table_name = self::table_name();
        $pages = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A );

        $instances = array();

        foreach ( $pages as $page ) {
            $ins = new self( $page );
            $instances[] = $ins;
        }

        return $instances;
    }

    public static function get_page( $id ) {
        global $wpdb;
        $table_name = self::table_name();

        $query = "SELECT * FROM $table_name WHERE id = %d LIMIT 1";
        $row = $wpdb->get_results($wpdb->prepare( $query, $id ), ARRAY_A)[0];
        return new self( $row );
    }

    public function __construct( $columns ) {
        foreach ( $columns as $key => $value ) {
            $this->$key = $value;
        }
    }

    public function __get( $field_name ) {
        if ( ! array_key_exists( $field_name, $this->data ) ) {
            throw new \Exception( 'Undefined variable for ' . get_called_class() );
        } else {
            return $this->data[ $field_name ];
        }
    }

    public function __set( $field_name, $field_value ) {
        return $this->data[ $field_name ] = $field_value;
    }

    public function save() {
        global $wpdb;
        $wpdb->replace( self::table_name(), $this->data );
    }

    public function exists() {

    }
}
