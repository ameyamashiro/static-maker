<?php

class Cron_Actions {
    public static function set_cron_schedule() {
        add_filter( 'cron_schedules', 'example_add_cron_interval' );

        function example_add_cron_interval( $schedules ) {
            $schedules['every_minutes'] = array(
                'interval' => 60,
                'display'  => esc_html__( 'Every 1 Minutes' ),
            );

            return $schedules;
        }

        if ( !wp_next_scheduled( 'static_maker_dequeue' ) ) {
            wp_schedule_event( time(), 'every_minutes', 'static_maker_dequeue' );
        }
    }
}
