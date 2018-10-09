<?php
namespace Static_Maker;

class Queue_Actions
{

    public function dequeue_task()
    {
        static::dequeue_all();
    }

    public static function dequeue_all()
    {
        $queues = Queue::receive_unprocessed_queues();

        // mark all queues as processing
        foreach ($queues as $queue) {
            $queue->mark_as_processing();
        }

        foreach ($queues as $queue) {
            $queue->dequeue();
        }

        $options = get_option(PLUGIN_NAME);

        if ($options['copy_directories']) {
            $directories = OptionsUtil::get_copy_directories();

            foreach ($directories as $directory) {
                RsyncUtil::sync_local($directory, FileUtil::get_output_path());
            }
        }

        RsyncUtil::sync_with_current_options();
    }

}
