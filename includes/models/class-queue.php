<?php
namespace Static_Maker;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Queue {

    protected static $table_name = 'staticmaker_queues';

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
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          post_id mediumint(9) NOT NULL,
          time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          type varchar(20) NOT NULL,
          url varchar(55) DEFAULT '' NOT NULL,
          status varchar(20) DEFAULT 'waiting' NOT NULL,
          PRIMARY KEY (id)
        ) $charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function receive_unprocessed_queues() {
        global $wpdb;
        $table_name = self::table_name();

        $queues = $wpdb->get_results( "SELECT * FROM $table_name WHERE status = 'waiting'" );
        $instances = array();

        foreach( $queues as $queue ) {
            $instances[] = new self( $queue );
        }

        return $instances;
    }

    // TODO: 自身をインスタンス化
    public static function get_queues() {
        global $wpdb;
        $table_name = self::table_name();

        return $wpdb->get_results( "SELECT * FROM $table_name" );
    }

    public static function enqueue_by_id( $id ) {
        global $wpdb;
        $table_name = self::table_name();

        $url = Page::get_page( $id )->permalink;

        return $wpdb->insert(
            $table_name,
            array(
                'time' => current_time( 'mysql' ),
                'type' => 'individual',
                'url' => $url,
            )
        );
    }

    public static function enqueue_by_post_id( $post_id ) {
        global $wpdb;
        $table_name = self::table_name();

        $url = get_permalink( $post_id );

        return $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'time' => current_time( 'mysql' ),
                'type' => 'individual',
                'url' => $url,
            )
        );
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

    public function dequeue() {
        static::markQueueAsCompleted( $this->id );
    }

    private static function markQueueAsCompleted( $id ) {
        global $wpdb;
        $table_name = self::table_name();

        $wpdb->update(
            $table_name,
            array(
                'status' => 'completed',
            ),
            array( 'id' => $id ),
            array(
                '%s',
            ),
            array( '%d' )
        );
    }

    private static function markQueueAsFailed( $id ) {
        global $wpdb;
        $table_name = self::table_name();

        $wpdb->update(
            $table_name,
            array(
                'status' => 'failed',
            ),
            array( 'id' => $id ),
            array(
                '%s',
            ),
            array( '%d' )
        );
    }

    public function save() {
        global $wpdb;
        $table_name = self::table_name();
        $id = $this->data[ 'id' ];

        $exists_query = $wpdb->prepare( "SELECT EXISTS(SELECT * FROM $table_name WHERE id = %d)", $id );

        if ( $wpdb->get_var( $exists_query ) === '1' ) { // have
            return $wpdb->update( $table_name, $this->data, array( 'id' => $id ) );
        } else {
            return $wpdb->insert( $table_name, $this->data );
        }
    }
}
