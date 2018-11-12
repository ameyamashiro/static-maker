<?php
namespace Static_Maker;

class RsyncUtil
{
    public static function sync_with_current_options()
    {
        $options = get_option(PLUGIN_NAME);
        $logs = '';

        if (isset($options['rsync'])) {
            foreach ($options['rsync'] as $rsync) {
                $command = OptionsUtil::replace_vars($rsync['before_command']);

                if (!empty($command)) {
                    $logs .= "\nBefore Command:\n";
                    $logs .= shell_exec($command . ' 2>&1');
                }

                $key = CryptoUtil::decrypt($rsync['ssh_key'], true);
                $logs .= static::sync_remote($rsync['host'], $rsync['user'], $rsync['auth_method'], $key, $rsync['dir'], '-Pav --delete ' . $rsync['rsync_options']);
            }
        }

        return $logs;
    }

    public static function sync_remote($host, $user, $auth_method, $credential, $target, $rsync_options = null, $ssh_options = null)
    {
        $temp = tmpfile();
        $path = stream_get_meta_data($temp)['uri'];
        fwrite($temp, $credential);

        $local_src = FileUtil::get_output_path() . '/';
        $rsync_options = $rsync_options ? $rsync_options : '-Pav --delete';

        $output = "\nRsync Command:\n";

        $pass_options = $auth_options = '';
        if ($auth_method === 'ssh') {
            $auth_options = $ssh_options ? $ssh_options : "-e 'ssh -i $path -o StrictHostKeyChecking=no'";
        } else if ($auth_method === 'pass') {
            $pass_options = "--password-file=$path";
        }

        $rsync_command = "rsync $rsync_options $auth_options $pass_options $local_src $user@$host:$target 2>&1";

        $output .= shell_exec($rsync_command);
        fclose($temp);

        return $output;
    }

    public static function sync_local($from, $to)
    {
        $from = substr($from, -1) === '/' ?: "$from/**";
        $from = str_replace(get_home_path(), '', $from);

        $output = "\nRsync Command:\n";
        $options = '';
        $last = '';
        foreach (explode('/', $from) as $path) {
            $last .= $last ? "/$path" : $path;
            $options .= "--include '$last' ";
        }

        $output .= shell_exec("rsync -Pa $options --exclude '*' " . get_home_path() . ' ' . FileUtil::get_output_path() . ' 2>&1');
        return $output;
    }
}
