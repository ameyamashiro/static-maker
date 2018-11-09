<?php
namespace Static_Maker;

class LogUtil
{
    public static function get_log_path()
    {
        $path = wp_upload_dir()['basedir'] . '/static-maker.log';
        $realpath = realpath($path);
        if ($realpath) {
            return $realpath;
        }
        return $path;
    }

    public static function get_log_url()
    {
        return str_replace(untrailingslashit(get_home_path()), '', self::get_log_path());
    }

    public static function write_with_trace($content)
    {
        $path = self::get_log_path();
        $trace = debug_backtrace()[1];
        $prepend = $trace['class'] . $trace['type'] . $trace['function'];
        return file_put_contents($path, $prepend . ' : ' . $content . "\n", FILE_APPEND);
    }
}
