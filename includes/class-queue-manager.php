<?php

class QueueManager {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'staticmaker';
    }

    public function createTable() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
          type varchar(20) NOT NULL,
          url varchar(55) DEFAULT '' NOT NULL,
          PRIMARY KEY (id)
        ) $charset_collate";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public function get_all_queues() {

    }

    public function queue_all() {
    }

    public function queue_by_post_id( $post_id ) {
        global $wpdb;

        $url = get_permalink( $post_id );

        $wpdb->insert(
            $this->table_name,
            array(
                'time' => current_time( 'mysql' ),
                'type' => 'individual',
                'url' => $url,
            )
        );
    }

}