<?php
namespace Static_Maker;

class RsyncUtil {
    static function syncWithCurrentOptions() {
        $options = get_option( PLUGIN_NAME );
        $logs = '';

        if ( isset( $options['rsync'] ) ) {
            foreach ($options['rsync'] as $rsync) {
                $command =  $rsync[ 'before_command' ];
                $command = preg_replace( '/\{\{ROOT\}\}/', get_home_path(), $command );
                $command = preg_replace( '/\{\{WP_ROOT\}\}/', ABSPATH, $command );
                $command = preg_replace( '/\{\{OUTPUT_DIR\}\}/', FileUtil::get_output_path() . '/', $command );
                $command = preg_replace('#/+#','/', $command);

                if ( !empty( $command ) ) {
                    $logs .= "\nBefore Command:\n";
                    $logs .= shell_exec( $command . ' 2>&1' );
                }

                $key = CryptoUtil::decrypt( $rsync['ssh_key'], true );
                $logs .= static::sync($rsync['host'], $rsync['user'], $key, $rsync['dir'], '-Pav --delete ' . $rsync['rsync_options']);
            }
        }

        return $logs;
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
