<?php
if ( ! defined( 'ABSPATH' ) ){
    exit;
}

class Porto_Importer_API {

    protected $demo = '';

    protected $path_tmp = '';
    protected $path_demo = '';

    protected $url = array(
        'revslider' => 'http://sw-themes.com/porto_dummy/revslider/',
    );

    public function __construct( $demo = false ){
        if( ! $demo ){
            return false;
        }
        $this->demo = $demo;
        $upload_dir = wp_upload_dir();
        $this->path_tmp = wp_normalize_path( $upload_dir['basedir'] . '/porto_tmp_dir' );
        $this->mkdir();
    }
    
    /**
     * Create directories
     */
    protected function mkdir(){

        if( ! file_exists( $this->path_tmp ) ){
            wp_mkdir_p( $this->path_tmp );
        }
        
        $this->path_demo = wp_normalize_path( $this->path_tmp .'/'. $this->demo );
        if( ! file_exists( $this->path_demo ) ){
            wp_mkdir_p( $this->path_demo );
        }
    }
    
    /**
     * Delete temporary directory
     */
    public function delete_temp_dir(){

        // filesystem
        global $wp_filesystem;
        // Initialize the Wordpress filesystem, no more using file_put_contents function
        if ( empty( $wp_filesystem ) ) {
            require_once( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }
        
        // director is located outside wp uploads dir
        $upload_dir = wp_upload_dir();
        if( false === strpos( $this->path_demo, $upload_dir['basedir'] ) ){
            return false;
        }
        
        $wp_filesystem->delete( $this->path_demo, true );
    }

    /**
     * Get remote demo files
     */
    public function get_remote_demo($file_type = 'revslider', $file = '') {

        if ( !$file ) {
            return false;
        }
        $url = $this->url[$file_type] . $file;
        $args = array(
            'user-agent'    => 'WordPress/'. get_bloginfo( 'version' ) .'; '. network_site_url(),
            'timeout'       => 60,
        );

        $response = wp_remote_get( $url, $args );

        if( is_wp_error( $response ) ){
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );

        // remote get fallback
        if( empty( $body ) ) {
            if( function_exists( 'ini_get' ) && ini_get( 'allow_url_fopen' ) ){
                $body = @file_get_contents( $url );
            }
        }

        if( empty( $body ) ){
            return new WP_Error( 'error_download', __( 'The package could not be downloaded.', 'porto' ) );
        }

        // filesystem
        global $wp_filesystem;
        // Initialize the Wordpress filesystem, no more using file_put_contents function
        if ( empty( $wp_filesystem ) ) {
            require_once( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
        }

        $path_package = wp_normalize_path( $this->path_demo .'/'. $file );

        if( ! $wp_filesystem->put_contents( $path_package, $body, FS_CHMOD_FILE ) ){
            // put_contents fallback
            @unlink( $path_package );
            $fp = @fopen( $path_package, 'w' );
            $fwrite = @fwrite( $fp, $body );
            @fclose( $fp );
            if( false === $fwrite ){
                return new WP_Error( 'error_fs', __( 'WordPress filesystem error.', 'porto' ) );
            }
            
        }

        return $path_package;
    }
}
