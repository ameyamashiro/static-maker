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
            $queue->mark_as_processing();
        }

        foreach ( $queues as $queue ) {
            $queue->dequeue();
        }
    }

}
