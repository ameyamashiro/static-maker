<?php
namespace Static_Maker;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LogUtil
{
    private static $instance = null;
    public $log;

    private function __construct()
    {
        $this->log = new Logger('StaticMaker');
        if (WP_DEBUG) {
            $stream = new RotatingFileHandler(self::get_log_path(), 0, Logger::DEBUG);
        } else {
            $stream = new RotatingFileHandler(self::get_log_path(), 0, Logger::NOTICE);
        }
        $this->log->pushHandler($stream);
    }

    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new LogUtil();
        }

        return self::$instance;
    }

    public static function get_log_path()
    {
        // $path = wp_upload_dir()['basedir'] . '/static-maker.log';
        $path = get_home_path() . '/sm-logs/static-maker.log';
        $realpath = realpath($path);
        if ($realpath) {
            return $realpath;
        }
        return $path;
    }

    public static function get_log_url()
    {
        return str_replace('//', '/', str_replace(untrailingslashit(get_home_path()), '', self::get_log_path()));
    }
}
