<?php
namespace Static_Maker;

class FileUtil {

    public static function export_single_file( $url, $replace_domain = true ) {
        if ( !$url || !filter_var( $url, FILTER_VALIDATE_URL ) ) { return false; }
        $options = get_option( PLUGIN_NAME );
        $alter_url = isset( $options[ 'host' ] ) ? $options[ 'host' ] : '';
        $url_parsed = parse_url( $url );

        if ( $replace_domain && !empty( $alter_url ) ) {
            $target_host = $url_parsed[ 'scheme' ] . '://' . $url_parsed[ 'host' ];
            $url = str_replace( $target_host, $alter_url, $url );
        }

        $content = self::file_get_content( $url );

        if ( $content === false ) {
            return false;
        }

        $dir = $url_parsed[ 'path' ];
        return self::file_put_content( $content, 'index.html', $dir );
    }

    public static function remove_single_file( $url ) {
        $url_parsed = parse_url( $url );
        $path = rawurldecode($url_parsed[ 'path' ]);
        $file = $path;

        if ( substr( $path, -1 ) === '/' ) {
            $file .= 'index.html';
        }

        $result = unlink( static::get_output_path() . $file );

        // attempt to remove parent directory to avoid generating garbage files.
        if ( $result ) {
            rmdir( static::get_output_path() . $path );
        }

        return $result;
    }

    private static function file_get_content( $url ) {
        $options = get_option( PLUGIN_NAME );
        $basic_enable = isset( $options[ 'basic_enable' ]) && $options[ 'basic_enable' ];
        $req_opts = array(
            'http' => array(
                'method' => 'GET',
            ),
        );

        if ( $basic_enable ) {
            $auth = base64_encode( $options[ 'basic_auth_user' ] . ':' . $options[ 'basic_auth_pass' ] );
            $req_opts[ 'http' ][ 'header' ] = 'Authorization: Basic ' . $auth;
        }

        $context = stream_context_create($req_opts);
        $resp = file_get_contents( $url, false, $context );

        if ($resp === false) {
            // explode( ' ', $http_response_header[ 0 ])[ 1 ]  // status code;
            return false;
        }

        return $resp;
    }

    private static function file_put_content( $content, $file_name = 'index.html', $subdir = '' ) {
        $options = get_option( PLUGIN_NAME );
        $export_path = static::get_output_path();

        $subdir = rawurldecode($subdir);

        if ( !static::create_dir( $export_path . $subdir ) ) {
            return false;
        }

        if ( $options['replaces'] && mb_strlen($options['replaces'][0]) !== 0 ) {
            $replaces = $options['replaces'];

            foreach ($replaces as $replace) {
                $content = str_replace($replace[ 'from' ], $replace[ 'to' ], $content);
            }
        }

        return file_put_contents( $export_path . $subdir . $file_name, $content );
    }

    public static function get_output_path() {
        $export_path = wp_upload_dir()[ 'basedir' ] . '/static-maker';
        $options = get_option( PLUGIN_NAME );
        $output_path = isset( $options[ 'output_path' ] ) ? $options[ 'output_path' ] : '';

        if ( !empty( $output_path ) ) {
            $export_path = get_home_path() . $output_path;
        }

        if ( !static::create_dir( $export_path ) ) {
            return false;
        }

        return $export_path;
    }

    private static function create_dir( $export_path ) {
        if (!is_dir( $export_path )) {
            if (!mkdir( $export_path, 0755, true )) {
                return false;
            }
        }
        return true;
    }

}
