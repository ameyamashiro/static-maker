<?php
namespace Static_Maker;

class LogUtil
{
    public static function get_log_path()
    {
        return wp_upload_dir()['basedir'] . '/static-maker.log';
    }

    public static function write_with_trace($content)
    {
        $path = self::get_log_path();
        $trace = debug_backtrace()[1];
        $prepend = $trace['class'] . $trace['type'] . $trace['function'];
        return file_put_contents($path, $prepend . ' : ' . $content . "\n", FILE_APPEND);
    }
}
