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
          id int(20) NOT NULL AUTO_INCREMENT,
          post_id int(20),
          post_type varchar(255) NOT NULL,
          type varchar(20) NOT NULL,
          url varchar(255) DEFAULT '' NOT NULL,
          status varchar(20) DEFAULT 'waiting' NOT NULL,
          created datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          process_started datetime,
          process_ended datetime,
          PRIMARY KEY (id)
        ) $charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function receive_unprocessed_queues() {
        global $wpdb;
        $table_name = self::table_name();

        $options = get_option( PLUGIN_NAME );
        $limit = isset( $options[ 'queue_limit' ] ) && !empty( $options[ 'queue_limit' ] ) ? $options[ 'queue_limit' ] : '10';

        $sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE status = 'waiting' LIMIT %d", $limit );
        $queues = $wpdb->get_results($sql);
        $instances = array();

        foreach( $queues as $queue ) {
            $instances[] = new self( $queue );
        }

        return $instances;
    }

    // TODO: 自身をインスタンス化
    public static function get_queues( $args = array() ) {
        global $wpdb;
        $table_name = self::table_name();

        $query = "SELECT * FROM $table_name";

        if ( isset($args[ 'desc' ]) && $args[ 'desc' ] ) {
            $query .= ' ORDER BY id DESC';
        }

        $queues = $wpdb->get_results( $query );

        if ( isset( $args[ 'output' ] ) && $args[ 'output' ] === 'original' ) {
            return $queues;
        }

        $instances = array();
        foreach ( $queues as $queue ) {
            $instances[] = new self( $queue );
        }

        return $instances;
    }

    public static function enqueue_by_id( $id, $action = 'add' ) {
        global $wpdb;
        $table_name = self::table_name();

        $page = Page::get_page( $id );

        return $wpdb->insert(
            $table_name,
            array(
                'created' => current_time( 'mysql' ),
                'type' => $action,
                'post_type' => $page->post_type,
                'url' => preg_replace('/__trashed$/', '', $page->permalink)
            )
        );
    }

    public static function enqueue_by_post_id( $post_id, $action = 'add' ) {
        global $wpdb;
        $table_name = self::table_name();

        $post = get_post( $post_id );
        $url = get_permalink( $post_id );

        return $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'created' => current_time( 'mysql' ),
                'type' => $action,
                'post_type' => $post->post_type,
                'url' => preg_replace('/__trashed$/', '', $url),
            )
        );
    }

    public static function enqueue_by_link( $link, $action = 'add', $post_type = '' ) {
        global $wpdb;
        $table_name = self::table_name();

        return $wpdb->insert(
            $table_name,
            array(
                'post_id' => '',
                'created' => current_time( 'mysql' ),
                'type' => $action,
                'post_type' => $post_type,
                'url' => preg_replace('/__trashed$/', '', $link),
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
