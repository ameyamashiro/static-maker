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
        $dir = $url_parsed[ 'path' ];

        return self::file_put_content( $content, 'index.html', $dir );
    }

    private static function file_get_content( $url ) {
        $context = stream_context_create(array(
            'http' => array(
                'method' => 'GET',
            ),
        ));

        return file_get_contents( $url, false, $context );
    }

    private static function file_put_content( $content, $file_name = 'index.html', $subdir = '' ) {
        $export_path = wp_upload_dir()[ 'path' ];

        if (!empty($subdir) && !is_dir( $export_path . $subdir )) {
            if (!mkdir( $export_path . $subdir, 0700, true )) {
                return false;
            }
        }

        return file_put_contents( $export_path . $subdir . $file_name, $content );
    }

}
