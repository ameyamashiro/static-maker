<?php
namespace Static_Maker;

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

    public function get_queues() {
        global $wpdb;
        return $wpdb->get_results( "SELECT * FROM $this->table_name" );
    }

    public function queue_all() {
    }

    public function queue_by_post_id( $post_id ) {
        global $wpdb;

        $url = get_permalink( $post_id );

        $wpdb->insert(
            $this->table_name,
            array(
                'post_id' => $post_id,
                'time' => current_time( 'mysql' ),
                'type' => 'individual',
                'url' => $url,
            )
        );
    }

    public function dequeue_all() {
        global $wpdb;

//        $queues = $wpdb->get_results( "SELECT * FROM $this->table_name WHERE status = 'waiting'" );
        $queues = $wpdb->get_results( "SELECT * FROM $this->table_name" );
        foreach ( $queues as $queue ) {
            $this->dequeue( $queue );
        }
    }

    public function dequeue( $queue ) {
        $this->markQueueAsCompleted( $queue->id );
    }

    public function set_cron_schedule() {
        add_filter( 'cron_schedules', 'example_add_cron_interval' );

        function example_add_cron_interval( $schedules ) {
            $schedules['five_seconds'] = array(
                'interval' => 5,
                'display'  => esc_html__( 'Every Five Seconds' ),
            );

            return $schedules;
        }

        if ( !wp_next_scheduled( 'static_maker_dequeue' ) ) {
            wp_schedule_event( time(), 'five_seconds', 'static_maker_dequeue' );
        }
    }

    public function dequeue_task() {
    }


    private function markQueueAsCompleted( $id ) {
        global $wpdb;

        $wpdb->update(
            $this->table_name,
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

    private function markQueueAsFailed( $id ) {
        global $wpdb;

        $wpdb->update(
            $this->table_name,
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
}

function get_queues() {
    $queueManager = new QueueManager();
    return $queueManager->get_queues();
}
