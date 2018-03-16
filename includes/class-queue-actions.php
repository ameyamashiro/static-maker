<?php
namespace Static_Maker;

class Queue_Actions {

    function dequeue_task() {
        static::dequeue_all();
    }

    static public function dequeue_all() {
        $queues = Queue::receive_unprocessed_queues();
        var_dump( $queues );

        // mark all queues as processing
        foreach ( $queues as $queue ) {
            $queue->status = 'processing';
            $queue->process_started = current_time( 'mysql' );
            $queue->save();
        }

        foreach ( $queues as $queue ) {

            if ( $queue->type === 'add' ) {
                if (FileUtil::export_single_file( $queue->url ) !== false) {
                    $queue->status = 'completed';
                    $queue->process_ended = current_time( 'mysql' );
                    $queue->save();
                } else {
                    $queue->status = 'failed';
                    $queue->process_ended = current_time( 'mysql' );
                    $queue->save();
                }
            } else if ( $queue->type === 'remove' ) {
                if (FileUtil::remove_single_file( $queue->url ) !== false) {
                    $queue->status = 'completed';
                    $queue->process_ended = current_time( 'mysql' );
                    $queue->save();
                } else {
                    $queue->status = 'failed';
                    $queue->process_ended = current_time( 'mysql' );
                    $queue->save();
                }
            } else {
                $queue->status = 'skipped (unknown)';
                $queue->process_ended = current_time( 'mysql' );
                $queue->save();
            }

        }
    }

}
