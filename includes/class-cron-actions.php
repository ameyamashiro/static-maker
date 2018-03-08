<?php
namespace Static_Maker;

class Cron_Actions {
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
}