<?php
namespace Static_Maker;

class Queue_Actions {

    function dequeue_task() {
    }

    static public function dequeue_all() {
        $queues = Queue::receive_unprocessed_queues();
        var_dump( $queues );

        foreach ( $queues as $queue ) {
            $queue->status = 'processing';
            $queue->process_started = current_time( 'mysql' );
            $queue->save();

            if (FileUtil::export_single_file( $queue->url ) !== false) {
                $queue->status = 'completed';
                $queue->process_ended = current_time( 'mysql' );
                $queue->save();
            } else {
                $queue->status = 'failed';
                $queue->process_ended = current_time( 'mysql' );
                $queue->save();
            }
        }
    }

}
