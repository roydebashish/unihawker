<?php

add_action( 'admin_notices', 'porto_theme_options_notices' );
function porto_theme_options_notices() {
    global $pagenow;
    if ( $pagenow == 'themes.php' && isset($_GET['page']) && 'porto_settings' === $_GET['page'] && ( !is_writable( porto_dir. '/scss/' ) || !is_writable( porto_dir. '/less/' ) || !is_writable( porto_dir. '/css/' ) ) ) {
        add_settings_error( 'porto_admin_theme_options', 'porto_admin_theme_options', sprintf( __( '%s directories should be write permitted to generate dynamic css files.', 'porto' ), '"<strong>' . porto_dir . '/scss/</strong>", "<strong>' . porto_dir . '/less/</strong>" and "<strong>' . porto_dir . '/css/</strong>"' ), 'error' );
        settings_errors( 'porto_admin_theme_options' );
    }
}

function porto_compile_css($import = false, $process = null) {
    global $porto_settings;

    if ( current_user_can( 'manage_options' ) && (isset( $_GET['compile_bootstrap'] ) || ($import && $process === 'bootstrap')) ) {
        global $reduxPortoSettings;
        @ini_set('max_execution_time', '10000');

        @ini_set('memory_limit', '256M');
        $reduxFramework = $reduxPortoSettings->ReduxFramework;

        $template_dir = porto_dir;
        // Compile SCSS files
        if (!class_exists('scssc')) {
            require_once( porto_admin . '/scssphp/scss.inc.php' );
        }
        // config file
        ob_start();
        require dirname(__FILE__) . '/config_scss_bootstrap.php';

        $_config_css = ob_get_clean();
        $filename = $template_dir.'/scss/_config_bootstrap.scss';
        if (is_writable(dirname($filename)) == false){
            @chmod(dirname($filename), 0755);
        }
        if (file_exists($filename)) {
            if (is_writable($filename) == false){
                @chmod($filename, 0755);
            }
            @unlink($filename);
        }

        $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $_config_css));
        $scss = new scssc();
        $scss->setImportPaths($template_dir . '/scss');

        if (isset($porto_settings['compress-css']) && $porto_settings['compress-css']) {
            $scss->setFormatter('scss_formatter_crunched');
        } else {
            $scss->setFormatter('scss_formatter');
        }
        try {
            // bootstrap styles
            ob_start();
            $optimize_suffix = '';
            if ( isset( $porto_settings['speed-optimize-bootstrap'] ) && $porto_settings['speed-optimize-bootstrap'] ) {
                $optimize_suffix = '.optimized';
            }
            echo $scss->compile('@import "bootstrap'. $optimize_suffix .'.scss"');

            $_config_css = ob_get_clean();
            $filename = $template_dir.'/css/bootstrap_'.get_current_blog_id().'.css';
            if (is_writable(dirname($filename)) == false){
                @chmod(dirname($filename), 0755);
            }
            if (file_exists($filename)) {
                if (is_writable($filename) == false){
                    @chmod($filename, 0755);
                }
                @unlink($filename);
            }

            $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $_config_css));
            update_option( 'porto_bootstrap_style', true );
        } catch (Exception $e) {
        }
    }
    if ( current_user_can( 'manage_options' ) && (isset( $_GET['compile_bootstrap_rtl'] ) || ($import && $process === 'bootstrap_rtl')) ) {
        global $reduxPortoSettings;
        @ini_set('max_execution_time', '10000');

        @ini_set('memory_limit', '256M');
        $reduxFramework = $reduxPortoSettings->ReduxFramework;

        $template_dir = porto_dir;
        // Compile SCSS files
        if (!class_exists('scssc')) {
            require_once( porto_admin . '/scssphp/scss.inc.php' );
        }
        // config file
        ob_start();
        require dirname(__FILE__) . '/config_scss_bootstrap.php';

        $_config_css = ob_get_clean();
        $filename = $template_dir.'/scss/_config_bootstrap.scss';
        if (is_writable(dirname($filename)) == false){
            @chmod(dirname($filename), 0755);
        }
        if (file_exists($filename)) {
            if (is_writable($filename) == false){
                @chmod($filename, 0755);
            }
            @unlink($filename);
        }

        $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $_config_css));
        $scss = new scssc();

        $scss->setImportPaths($template_dir . '/scss');

        if (isset($porto_settings['compress-css']) && $porto_settings['compress-css']) {
            $scss->setFormatter('scss_formatter_crunched');
        } else {
            $scss->setFormatter('scss_formatter');
        }
        try {
            // bootstrap styles
            ob_start();
            $optimize_suffix = '';
            if ( isset( $porto_settings['speed-optimize-bootstrap'] ) && $porto_settings['speed-optimize-bootstrap'] ) {
                $optimize_suffix = '.optimized';
            }
            echo $scss->compile('@import "bootstrap_rtl'. $optimize_suffix .'.scss"');
            $_config_css = ob_get_clean();
            $filename = $template_dir.'/css/bootstrap_rtl_'.get_current_blog_id().'.css';
            if (is_writable(dirname($filename)) == false){
                @chmod(dirname($filename), 0755);
            }
            if (file_exists($filename)) {
                if (is_writable($filename) == false){
                    @chmod($filename, 0755);
                }
                @unlink($filename);
            }

            $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $_config_css));
            update_option( 'porto_bootstrap_rtl_style', true );
        } catch (Exception $e) {
        }
    }
}
add_action( 'redux/options/porto_settings/saved', 'porto_save_theme_settings', 10, 2 );
add_action( 'redux/options/porto_settings/import', 'porto_save_theme_settings', 10, 2 );
add_action( 'redux/options/porto_settings/reset', 'porto_save_theme_settings' );
add_action( 'redux/options/porto_settings/section/reset', 'porto_save_theme_settings' );
add_action( 'redux/options/porto_settings/import', 'porto_import_theme_settings', 10, 2 );
function porto_config_value( $value ) {
    return isset( $value ) ? $value : 0;
}
function porto_save_theme_settings() {

    global $porto_settings;
    update_option('porto_init_theme', '1');
    global $reduxPortoSettings;
    @ini_set('max_execution_time', '10000');

    @ini_set('memory_limit', '256M');
    $reduxFramework = $reduxPortoSettings->ReduxFramework;

    $template_dir = porto_dir;

    // Compile dynamic styles
    $rtl_arr = array( '', '_rtl' );
    $GLOBALS['porto_save_settings_is_rtl'] = false;
    foreach( $rtl_arr as $rtl_arr_value ) {
        ob_start();
        include porto_dir . '/style.php';
        $css = ob_get_clean();
        // remove comments
        $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );
        // remove whitespace
        $css = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css );

        $filename = $template_dir.'/css/dynamic_style'.$rtl_arr_value.'_'.get_current_blog_id().'.css';
        if (is_writable(dirname($filename)) == false){
            @chmod(dirname($filename), 0755);
        }
        if (file_exists($filename)) {
            if (is_writable($filename) == false){
                @chmod($filename, 0755);
            }
            @unlink($filename);
        }

        $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $css));
        $GLOBALS['porto_save_settings_is_rtl'] = true;
    }
    unset($GLOBALS['porto_save_settings_is_rtl']);
    update_option( 'porto_dynamic_style', true );

    // Compile LESS Files
    if (!class_exists('lessc')) {
        require_once( porto_admin . '/lessphp/lessc.inc.php' );
    }
    // config file
    ob_start();

    include dirname(__FILE__) . '/config_less.php';

    $_config_css = ob_get_clean();
    $filename = $template_dir.'/less/config.less';
    if (is_writable(dirname($filename)) == false){
        @chmod(dirname($filename), 0755);
    }
    if (file_exists($filename)) {
        if (is_writable($filename) == false){
            @chmod($filename, 0755);
        }
        @unlink($filename);
    }

    $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $_config_css));
    try {
        // skin css
        ob_start();
        $less = new lessc;
        if (isset($porto_settings['compress-css']) && $porto_settings['compress-css'])
            $less->setFormatter('compressed');
        echo $less->compileFile($template_dir.'/less/skin.less');
        $_config_css = ob_get_clean();

        $filename = $template_dir.'/css/skin_'.get_current_blog_id().'.css';
        if (is_writable(dirname($filename)) == false){
            @chmod(dirname($filename), 0755);
        }
        if (file_exists($filename)) {
            if (is_writable($filename) == false){
                @chmod($filename, 0755);
            }
            @unlink($filename);

        }

        $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $_config_css));
        update_option( 'porto_skin_style', true );

        // rtl skin css
        ob_start();
        $less = new lessc;
        if (isset($porto_settings['compress-css']) && $porto_settings['compress-css'])
            $less->setFormatter('compressed');
        echo $less->compileFile($template_dir.'/less/skin_rtl.less');
        $_config_css = ob_get_clean();

        $filename = $template_dir.'/css/skin_rtl_'.get_current_blog_id().'.css';
        if (is_writable(dirname($filename)) == false){
            @chmod(dirname($filename), 0755);
        }
        if (file_exists($filename)) {

            if (is_writable($filename) == false){
                @chmod($filename, 0755);
            }
            @unlink($filename);
        }

        $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $_config_css));
        update_option( 'porto_skin_rtl_style', true );


        // visual composer
        if ( isset( $porto_settings['speed-optimize-js-composer'] ) && $porto_settings['speed-optimize-js-composer'] && isset( $porto_settings['speed-optimize-js-composer-shortcodes'] ) && !empty( $porto_settings['speed-optimize-js-composer-shortcodes'] ) ) {
            ob_start();
            include $template_dir . '/less/js_composer/less/lib/front.less.php';
            $_config_css = ob_get_clean();

            $filename = $template_dir.'/less/js_composer/less/lib/front.less';
            if (is_writable(dirname($filename)) == false){
                @chmod(dirname($filename), 0755);
            }
            if (file_exists($filename)) {
                if (is_writable($filename) == false){
                    @chmod($filename, 0755);
                }
                @unlink($filename);
            }

            $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $_config_css));
            ob_start();
            $less = new lessc;
            $less->setFormatter('compressed');
            echo $less->compileFile($template_dir.'/less/js_composer/less/js_composer.less');
            $_config_css = ob_get_clean();

            $filename = $template_dir.'/css/js_composer_'.get_current_blog_id().'.css';
            if (is_writable(dirname($filename)) == false){
                @chmod(dirname($filename), 0755);
            }
            if (file_exists($filename)) {
                if (is_writable($filename) == false){
                    @chmod($filename, 0755);
                }
                @unlink($filename);

            }

            $reduxFramework->filesystem->execute('put_contents', $filename, array('content' => $_config_css));
        } else if ( file_exists( $template_dir.'/css/js_composer_'.get_current_blog_id().'.css' ) ) {
            @unlink( $template_dir.'/css/js_composer_'.get_current_blog_id().'.css' );
        }

        flush_rewrite_rules();

    } catch (Exception $e) {}

}
function porto_import_theme_settings() {
    if (is_rtl()) {
        porto_compile_css(true, 'bootstrap_rtl');
    } else {
        porto_compile_css(true, 'bootstrap');
    }
}

if ( !function_exists( 'porto_generate_scss_after_options_save' ) ) :
    function porto_generate_scss_after_options_save( $options ) {
        porto_compile_css(true, 'bootstrap_rtl');
        porto_compile_css(true, 'bootstrap');
    }
endif;
add_action( 'redux/options/porto_settings/compiler', 'porto_generate_scss_after_options_save', 11, 1 );