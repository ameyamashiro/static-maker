<?php
namespace Static_Maker;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Page {

    protected static $table_name = 'staticmaker_pages';

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
          post_id int(20) UNIQUE,
          post_type varchar(255) NOT NULL,
          permalink varchar(255) DEFAULT '' NOT NULL,
          active tinyint(1) DEFAULT 1 NOT NULL,
          PRIMARY KEY (id)
        ) $charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function get_page_count() {
        global $wpdb;
        $table_name = self::table_name();
        return $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
    }

    public static function get_pages($page = 1) {
        global $wpdb;
        $each_page = 25;
        $table_name = self::table_name();
        $query = $wpdb->prepare( "SELECT * FROM $table_name LIMIT %d OFFSET %d", $each_page, ($page - 1) * $each_page );
        $pages = $wpdb->get_results( $query, ARRAY_A );

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

    public static function get_page_by_post_id( $id ) {
        global $wpdb;
        $table_name = self::table_name();

        $query = "SELECT * FROM $table_name WHERE post_id = %d LIMIT 1";
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
        $table_name = self::table_name();

        $is_manual_type = $this->data[ 'post_type' ] === 'static-maker-manual';
        $permalink = $this->data[ 'permalink' ];
        $post_id = $this->data[ 'post_id' ];

        if ( $is_manual_type ) {
            $exists_query = $wpdb->prepare( "SELECT EXISTS(SELECT * FROM $table_name WHERE permalink = %s)", $permalink );
        } else {
            $exists_query = $wpdb->prepare( "SELECT EXISTS(SELECT * FROM $table_name WHERE post_id = %s)", $post_id );
        }

        if ( $wpdb->get_var( $exists_query ) === '1' ) { // have

            if ( $is_manual_type ) {
                return $wpdb->update( $table_name, $this->data, array( 'permalink' => $permalink ) );
            } else {
                return $wpdb->update( $table_name, $this->data, array( 'post_id' => $post_id ) );
            }

        } else {
            if ( $is_manual_type ) {
                return $wpdb->insert( $table_name, $this->data );
            } else {
                return $wpdb->insert( $table_name, $this->data );
            }
        }
    }

}
