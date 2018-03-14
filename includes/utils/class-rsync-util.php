<?php
namespace Static_Maker;

class RsyncUtil {
    static function syncWithCurrentOptions() {
    }

    static function sync($host, $user, $ssh_key, $target, $rsync_options = null, $ssh_options = null) {
        $temp = tmpfile();
        $path = stream_get_meta_data( $temp )[ 'uri' ];
        fwrite($temp, $ssh_key);

        $local_src = FileUtil::get_output_path() . '/';
        $rsync_options = $rsync_options ? $rsync_options : '-Pav --delete';
        $ssh_options = $ssh_options ? $ssh_options : "-e 'ssh -i $path -o StrictHostKeyChecking=no'";
        $rsync_command = "rsync $rsync_options $ssh_options $local_src $user@$host:$target 2>&1";

        $output = shell_exec( $rsync_command );
        fclose($temp);

        return $output;
    }
}
