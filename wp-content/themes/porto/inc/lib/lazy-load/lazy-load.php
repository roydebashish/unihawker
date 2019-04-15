<?php

// generate placeholders
if ( !function_exists( 'porto_generate_placeholders' ) ) :
function porto_generate_placeholder($image_size, $placeholder_width = 100) {

    if ( preg_match_all( '/(\d+)x(\d+)/', $image_size, $sizes ) ) {
        $width = isset( $sizes[1][0] ) ? $sizes[1][0] : '1';
        $height = isset( $sizes[2][0] ) ? $sizes[2][0] : '1';
    } else {
        $image_sizes = wp_get_additional_image_sizes();
        if ( in_array( $image_size, $image_sizes ) ) {
            $width = $image_sizes[$image_size]['width'];
            $height = $image_sizes[$image_size]['height'];
        } else {
            $width = '1';
            $height = '1';
        }
    }

    if ( $width === $height || ( '1' === $width && '1' === $height ) ) {
        return array( porto_uri . '/images/lazy.png', $width, $height );
    }

    $upload_dir = wp_upload_dir();
    $placeholder_height = round($height * ( $placeholder_width / $width ));
    $placeholder_path = $upload_dir['basedir'] . '/porto_placeholders/'. $placeholder_width. 'x' . $placeholder_height . '.jpg';
    $placeholder_url = $upload_dir['baseurl'] . '/porto_placeholders/'. $placeholder_width. 'x' . $placeholder_height . '.jpg';
    if ( file_exists( $placeholder_path ) ) {
        return array( $placeholder_url, $width, $height );
    }

    if ( !file_exists( $upload_dir['basedir'] . '/porto_placeholders' ) ) {
        wp_mkdir_p( $upload_dir['basedir'] . '/porto_placeholders' );
    }

    $im  = @imagecreatetruecolor( $placeholder_width, $placeholder_height );
    if ( !$im ) {
        return array( porto_uri . '/images/lazy.png', $width, $height );
    }
    $bgc = @imagecolorallocate( $im, 238, 238, 238 );
    @imagefilledrectangle( $im, 0, 0, $placeholder_width, $placeholder_height, $bgc );
    @imagejpeg( $im, $placeholder_path, 40 );
    @imagedestroy( $im );
    return array( $placeholder_url, $width, $height );
}
endif;

if ( !class_exists( 'Porto_LazyLoad_Images' ) ) :
class Porto_LazyLoad_Images {

    static function init() {
        global $porto_settings;
        if ( !isset( $porto_settings['lazyload-enable'] ) || !$porto_settings['lazyload-enable'] ) {
            return;
        }
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_scripts' ), 99 );
        add_action( 'wp_head', array( __CLASS__, 'setup' ), 99 );
    }
    static function setup() {
        add_filter( 'the_content', array( __CLASS__, 'add_image_placeholders' ), 9999 );
        add_filter( 'post_thumbnail_html', array( __CLASS__, 'add_image_placeholders' ), 11 );
        add_filter( 'get_avatar', array( __CLASS__, 'add_image_placeholders' ), 11 );
        add_filter( 'woocommerce_single_product_image_html', array( __CLASS__, 'add_image_placeholders' ), 9999);
        add_filter( 'porto_lazy_load_images', array( __CLASS__, 'add_image_placeholders' ), 9999);
        add_filter( 'woocommerce_single_product_image_thumbnail_html', array( __CLASS__, 'add_image_placeholders' ), 9999);
    }
    static function add_scripts() {
        
    }
    static function add_image_placeholders( $content ) {

        if( is_feed() || is_preview() ) {
            return $content;
        }

        if ( false !== strpos( $content, 'data-src' ) || false !== strpos( $content, 'data-original' ) ) {
            return $content;
        }

        $matches = array();
        preg_match_all( '/<img[\s\r\n]+.*?>/is', $content, $matches );

        $lazy_image = get_template_directory_uri().'/images/lazy.png';

        $search = array();
        $replace = array();

        $i = 0;
        foreach ( $matches[0] as $imgHTML ) {

            if ( ! preg_match( "/src=['\"]data:image/is", $imgHTML ) ) {
                $i++;
                // replace the src and add the data-src
                $replaceHTML = '';

                if ( preg_match( '/width=["\']/i', $imgHTML ) && preg_match( '/height=["\']/i', $imgHTML ) ) {
                    preg_match( '/width=(["\'])(.*?)["\']/is', $imgHTML, $matchWidth );
                    preg_match( '/height=(["\'])(.*?)["\']/is', $imgHTML, $matchHeight );
                    if ( isset( $matchWidth[2] ) && isset( $matchHeight[2] ) ) {
                        $lazy_image = porto_generate_placeholder( $matchWidth[2]. 'x' .$matchHeight[2] );
                        $lazy_image = $lazy_image[0];
                    }
                }

                $replaceHTML = preg_replace( '/<img(.*?)src=/is', '<img$1src="'.$lazy_image.'" data-original=', $imgHTML );
                $replaceHTML = preg_replace( '/<img(.*?)srcset=/is', '<img$1srcset="" data-srcset=', $replaceHTML );

                if ( preg_match( '/class=["\']/i', $replaceHTML ) ) {
                    $replaceHTML = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1porto-lazyload $2$1', $replaceHTML );
                } else {
                    $replaceHTML = preg_replace( '/<img/is', '<img class="porto-lazyload"', $replaceHTML );
                }

                array_push( $search, $imgHTML );
                array_push( $replace, $replaceHTML );
            }
        }

        $search = array_unique( $search );
        $replace = array_unique( $replace );

        $content = str_replace( $search, $replace, $content );

        return $content;
    }
}

if ( !is_admin() && !is_customize_preview() ) {
    add_action( 'init', array( 'Porto_LazyLoad_Images', 'init' ) );
}
endif;