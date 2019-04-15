<?php
/**
 * Porto Theme Setup Wizard Class
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'Porto_Theme_Setup_Wizard' ) ) {
    /**
     * Porto_Theme_Setup_Wizard class
     */
    class Porto_Theme_Setup_Wizard {

        protected $version = '1.1.0';

        protected $theme_name = '';

        protected $step   = '';

        protected $steps  = array();

        protected $page_slug;

        protected $tgmpa_instance;

        protected $tgmpa_menu_slug = 'tgmpa-install-plugins';

        protected $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

        protected $page_url;

        protected $porto_url = 'http://www.portotheme.com/wordpress/porto/';

        private static $instance = null;

        public static function get_instance() {
            if ( ! self::$instance ) {
                self::$instance = new self;
            }

            return self::$instance;
        }

        public function __construct() {
            $this->init_globals();
            $this->init_actions();
        }

        public function get_header_logo_width(){
            return '200px';
        }

        public function init_globals() {
            $current_theme = wp_get_theme();
            $this->theme_name = strtolower( preg_replace( '#[^a-zA-Z]#', '', $current_theme->get( 'Name' ) ) );
            $this->page_slug = 'porto-setup-wizard';
            $this->page_url = 'admin.php?page='.$this->page_slug;
        }

        public function init_actions() {
            if ( apply_filters( $this->theme_name . '_enable_setup_wizard', true ) && current_user_can( 'manage_options' )  ) {

                if ( !is_child_theme() ){
                    add_action( 'after_switch_theme', array( $this, 'switch_theme' ) );
                }

                if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
                    add_action( 'init', array( $this, 'get_tgmpa_instanse' ), 30 );
                    add_action( 'init', array( $this, 'set_tgmpa_url' ), 40 );
                }

                add_action( 'admin_menu', array( $this, 'admin_menus' ) );
                add_action( 'admin_init', array( $this, 'admin_redirects' ), 30 );

                add_action( 'admin_init', array( $this, 'init_wizard_steps' ), 30 );
                add_action( 'admin_init', array( $this, 'setup_wizard' ), 30 );
                add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
                add_action( 'wp_ajax_porto_setup_wizard_plugins', array( $this, 'ajax_plugins' ) );
            }

            add_action( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 2 );
        }

        public function upgrader_post_install( $return, $theme ) {
            if ( is_wp_error( $return ) ) {
                return $return;
            }
            if ( $theme != get_stylesheet() ) {
                return $return;
            }
            update_option( 'porto_setup_complete', false );

            return $return;
        }

        public function tgmpa_load( $status ) {
            return is_admin() || current_user_can( 'install_themes' );
        }

        public function switch_theme() {
            set_transient( '_'.$this->theme_name.'_activation_redirect', 1 );
        }

        public function admin_redirects() {
            ob_start();

            if ( ! get_transient( '_'.$this->theme_name.'_activation_redirect' ) || get_option( 'porto_setup_complete', false ) ) {
                return;
            }
            delete_transient( '_'.$this->theme_name.'_activation_redirect' );
            wp_safe_redirect( admin_url( $this->page_url ) );
            exit;
        }

        public function get_tgmpa_instanse(){
            $this->tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
        }

        public function set_tgmpa_url(){

            $this->tgmpa_menu_slug = ( property_exists($this->tgmpa_instance, 'menu') ) ? $this->tgmpa_instance->menu : $this->tgmpa_menu_slug;
            $this->tgmpa_menu_slug = apply_filters($this->theme_name . '_theme_setup_wizard_tgmpa_menu_slug', $this->tgmpa_menu_slug);

            $tgmpa_parent_slug = ( property_exists($this->tgmpa_instance, 'parent_slug') && $this->tgmpa_instance->parent_slug !== 'themes.php' ) ? 'admin.php' : 'themes.php';

            $this->tgmpa_url = apply_filters($this->theme_name . '_theme_setup_wizard_tgmpa_url', $tgmpa_parent_slug.'?page='.$this->tgmpa_menu_slug);

        }

        public function admin_menus() {
            add_submenu_page('porto', esc_html__( 'Setup Wizard','porto' ), esc_html__( 'Setup Wizard','porto' ), 'manage_options', $this->page_slug,  array( $this, $this->page_slug) );
        }

        public function init_wizard_steps() {

            $this->steps = array(
                'introduction' => array(
                    'name'    => esc_html__( 'Welcome', 'porto' ),
                    'view'    => array( $this, 'porto_setup_wizard_welcome' ),
                    'handler' => array( $this, 'porto_setup_wizard_welcome_save' ),
                ),
            );

            $this->steps['updates'] = array(
                'name'    => esc_html__( 'Activate', 'porto' ),
                'view'    => array( $this, 'porto_setup_wizard_updates' ),
                'handler' => array( $this, 'porto_setup_wizard_updates_save' ),
            );

            $this->steps['status'] = array(
                'name'    => esc_html__( 'Status', 'porto' ),
                'view'    => array( $this, 'porto_setup_wizard_status' ),
                'handler' => array( $this, 'porto_setup_wizard_status_save' ),
            );

            $this->steps['customize'] = array(
                'name'    => esc_html__( 'Child Theme', 'porto' ),
                'view'    => array( $this, 'porto_setup_wizard_customize' ),
                'handler' => '',
            );

            if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
                $this->steps['default_plugins'] = array(
                    'name' => esc_html__( 'Plugins', 'porto' ),
                    'view' => array( $this, 'porto_setup_wizard_default_plugins' ),
                    'handler' => '',
                );
            }
            $this->steps['demo_content'] = array(
                'name'    => esc_html__( 'Demo Content', 'porto' ),
                'view'    => array( $this, 'porto_setup_wizard_demo_content' ),
                'handler' => array( $this, 'porto_setup_wizard_demo_content_save' ),
            );
            $this->steps['help_support'] = array(
                'name'    => esc_html__( 'Support', 'porto' ),
                'view'    => array( $this, 'porto_setup_wizard_help_support' ),
                'handler' => '',
            );
            $this->steps['next_steps'] = array(
                'name'    => esc_html__( 'Ready!', 'porto' ),
                'view'    => array( $this, 'porto_setup_wizard_ready' ),
                'handler' => '',
            );


            $this->steps = apply_filters(  $this->theme_name . '_theme_setup_wizard_steps', $this->steps );

        }

        /**
         * Display the setup wizard
         */
        public function setup_wizard() {
            if ( empty( $_GET['page'] ) || $this->page_slug !== $_GET['page'] ) {
                return;
            }
            ob_end_clean();

            $this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

            wp_register_script( 'jquery-blockui', porto_uri . '/inc/admin/setup_wizard/assets/js/jquery.blockUI.js', array( 'jquery' ), '2.70', true );
            wp_register_script( 'porto-admin-isotope', porto_js.'/jquery.isotope.min.js', array( 'jquery' ), $this->version, true );
            wp_register_script( 'porto-magnific-popup', porto_js.'/jquery.magnific-popup.min.js', array( 'jquery' ), $this->version, true );
            wp_register_script( 'porto-admin', porto_js.'/admin.js', array( 'jquery', 'porto-magnific-popup' ), $this->version, true );
            wp_register_script( 'porto-setup', porto_uri . '/inc/admin/setup_wizard/assets/js/setup-wizard.js', array( 'jquery', 'porto-admin-isotope', 'porto-admin', 'jquery-blockui' ), $this->version );
            wp_localize_script( 'porto-setup', 'porto_setup_wizard_params', array(
                'tgm_plugin_nonce' => array(
                    'update' => wp_create_nonce( 'tgmpa-update' ),
                    'install' => wp_create_nonce( 'tgmpa-install' ),
                ),
                'tgm_bulk_url' => admin_url( $this->tgmpa_url ),
                'wpnonce' => wp_create_nonce( 'porto_setup_wizard_nonce' ),
            ) );


            wp_enqueue_style( 'porto-magnific-popup', porto_css.'/magnific-popup.min.css', false, $this->version, 'all' );
            wp_enqueue_style( 'porto-setup', porto_uri . '/inc/admin/setup_wizard/assets/css/style.css', array( 'wp-admin', 'dashicons', 'install', 'porto-magnific-popup' ), $this->version );

            wp_enqueue_style( 'wp-admin' );
            wp_enqueue_media();
            wp_enqueue_script( 'media' );

            ob_start();
            $this->setup_wizard_header();
            $this->setup_wizard_steps();
            $show_content = true;
            echo '<div class="porto-setup-content">';
            if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
                $show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
            }
            if ( $show_content ) {
                $this->setup_wizard_content();
            }
            echo '</div>';
            $this->setup_wizard_footer();
            exit;
        }

        public function get_step_link( $step ) {
            return  add_query_arg( 'step', $step, admin_url( 'admin.php?page=' .$this->page_slug ) );
        }
        public function get_next_step_link() {
            $keys = array_keys( $this->steps );
            return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ], remove_query_arg( 'translation_updated' ) );
        }

        /**
         * Setup Wizard Header
         */
        public function setup_wizard_header() {
        ?>
            <!DOCTYPE html>
            <html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
            <head>
                <meta name="viewport" content="width=device-width" />
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <title><?php esc_html_e( 'Theme &rsaquo; Setup Wizard', 'porto' ); ?></title>
                <script type="text/javascript">
                    var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
                </script>
                <?php wp_print_scripts( 'porto-setup' ); ?>
                <?php do_action( 'admin_print_styles' ); ?>
                <?php do_action( 'admin_print_scripts' ); ?>
            </head>
            <body class="porto-setup wp-core-ui">
            <h1 id="porto-logo">
                <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?> - <?php bloginfo( 'description' ); ?>" class="overlay-logo">
                    <img class="img-responsive" src="<?php echo porto_uri ?>/images/logo/logo.png" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" width="111" height="54" />
                </a>
            </h1>
        <?php
        }

        /**
         * Setup Wizard Footer
         */
        public function setup_wizard_footer() {
        ?>
            <a class="wc-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Return to the WordPress Dashboard', 'porto' ); ?></a>
            <?php
            @do_action( 'admin_footer' );
            do_action( 'admin_print_footer_scripts' );
            ?>
            </body>
            </html>
        <?php
    }

        /**
         * Output the steps
         */
        public function setup_wizard_steps() {
            $ouput_steps = $this->steps;
            array_shift( $ouput_steps );
            ?>
            <ol class="porto-setup-steps">
            <?php foreach ( $ouput_steps as $step_key => $step ) : ?>
                <li class="<?php
                $show_link = true;
                if ( $step_key === $this->step ) {
                    echo 'active';
                } elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
                    echo 'done';
                }
                if ( $step_key === $this->step || $step_key == 'next_steps' ) {
                    $show_link = false;
                }
                ?>"><?php
                    if ( $show_link ) {
                        ?>
                        <a href="<?php echo esc_url( $this->get_step_link( $step_key ) );?>"><?php echo esc_html( $step['name'] );?></a>
                        <?php
                    } else {
                        echo esc_html( $step['name'] );
                    }
                    ?></li>
            <?php endforeach; ?>
            </ol>
            <?php
        }

        /**
         * Output the content for the current step
         */
        public function setup_wizard_content() {
            isset( $this->steps[ $this->step ] ) ? call_user_func( $this->steps[ $this->step ]['view'] ) : false;
        }

        /**
         * Welcome step
         */
        public function porto_setup_wizard_welcome() {
            if( get_option('porto_setup_complete', false) ){
                ?>
                <h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.', 'porto' ), wp_get_theme()); ?></h1>
                <p class="lead success"><?php esc_html_e( 'It looks like you already have setup Porto.', 'porto' );?></p>

                <p class="porto-setup-actions step">
                    <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                           class="button-primary button button-next button-large"><?php esc_html_e( 'Run Setup Wizard Again', 'porto' ); ?></a>
                    <a href="<?php echo admin_url( 'admin.php?page=porto' ); ?>"
                       class="button button-large"><?php esc_html_e( 'Exit to Porto Panel', 'porto' ); ?></a>
                </p>
                <?php
            } else {
                ?>
                <h1><?php printf( esc_html__( 'Welcome to the setup wizard for %s.' ), wp_get_theme()); ?></h1>
                <p class="lead"><?php printf( esc_html__( 'Thank you for choosing the %s theme. This quick setup wizard will help you configure your new website. This wizard will install the required WordPress plugins, demo content, logo, etc.', 'porto' ), wp_get_theme() ); ?></p>
                <p><?php esc_html_e( "No time right now? If you don't want to go through the wizard, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!", 'porto' ); ?></p>
                <p class="porto-setup-actions step">
                    <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
                       class="button-primary button button-large button-next"><?php esc_html_e( "Let's Go!", 'porto' ); ?></a>
                    <a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(),'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>"
                       class="button button-large"><?php esc_html_e( 'Not right now' ); ?></a>
                </p>
                <?php
            }
        }

        public function porto_setup_wizard_welcome_save(){

            check_admin_referer( 'porto-setup' );
            return false;
        }

        public function porto_setup_wizard_status() {
        ?>
            <h1><?php esc_html_e( 'System Status', 'porto' ); ?></h1>
            <?php include_once porto_admin . '/admin_pages/mini-status.php'; ?>
            <p class="porto-setup-actions step">
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next" data-callback="install_plugins"><?php esc_html_e( 'Continue', 'porto' ); ?></a>
            </p>
        <?php
        }

        public function porto_setup_wizard_status_save(){

            check_admin_referer( 'porto-setup' );
        }

        private function _wp_get_attachment_id_by_post_name( $post_name ) {
            global $wpdb;
            $str = $post_name;
            $posts = $wpdb->get_results( "SELECT * FROM $wpdb->posts WHERE post_title = '$str' ", OBJECT );
            if($posts) return $posts[0]->ID;
        }

        private function _get_plugins() {
            $instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
            $plugins = array(
                'all'      => array(), // Meaning: all plugins which still have open actions.
                'install'  => array(),
                'update'   => array(),
                'activate' => array(),
            );

            foreach ( $instance->plugins as $slug => $plugin ) {
                if ( $instance->is_plugin_active( $slug ) && false === $instance->does_plugin_have_update( $slug ) ) {
                    continue;
                } else {
                    $plugins['all'][ $slug ] = $plugin;

                    if ( ! $instance->is_plugin_installed( $slug ) ) {
                        $plugins['install'][ $slug ] = $plugin;
                    } else {
                        if ( false !== $instance->does_plugin_have_update( $slug ) ) {
                            $plugins['update'][ $slug ] = $plugin;
                        }

                        if ( $instance->can_plugin_activate( $slug ) ) {
                            $plugins['activate'][ $slug ] = $plugin;
                        }
                    }
                }
            }
            return $plugins;
        }

        /**
         * Page setup
         */
        public function porto_setup_wizard_default_plugins() {

            tgmpa_load_bulk_installer();
            if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
                die( 'Failed to find TGM' );
            }
            $url = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'porto-setup' );
            $plugins = $this->_get_plugins();

            $method = '';
            $fields = array_keys( $_POST );

            if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
                return true;
            }

            if ( ! WP_Filesystem( $creds ) ) {
                request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
                return true;
            }

            ?>
            <h1><?php esc_html_e( 'Default Plugins', 'porto' ); ?></h1>
            <form method="post">

                <?php
                $plugins = $this->_get_plugins();
                if ( count( $plugins['all'] ) ) {
                    ?>
                    <p class="lead"><?php esc_html_e( 'This will install the default plugins which is used in Porto.', 'porto' ); ?></p>
                    <p><?php esc_html_e( 'Please check the plugins to install.', 'porto' ); ?></p>
                    <ul class="porto-setup-wizard-plugins">
                        <?php foreach ( $plugins['all'] as $slug => $plugin ) {  ?>
                            <?php if ( 'wysija-newsletters' === $plugin['slug'] ) : ?>
                                <li class="separator">
                                    <a href="#" class="button-load-plugins"><?php esc_html_e( 'Load more plugins fully compatible with Porto', 'porto' ); ?></a>
                                </li>
                            <?php endif; ?>
                            <li data-slug="<?php echo esc_attr( $slug );?>"<?php echo isset( $plugin['visibility'] ) && 'hidden' === $plugin['visibility'] ? ' class="hidden"' : ''; ?>>
                                <label class="checkbox checkbox-inline">
                                    <input type="checkbox" name="setup-plugin"<?php echo $plugin['required'] ? ' checked="checked"' : ''; ?>>
                                    <?php echo esc_html( $plugin['name'] );?>
                                    <span>
                                    <?php
                                        $key = '';
                                        if ( isset( $plugins['install'][ $slug ] ) ) {
                                            $key = esc_html__( 'Installation', 'porto' );
                                        } else if ( isset( $plugins['update'][ $slug ] ) ) {
                                            $key = esc_html__( 'Update', 'porto' );
                                        } else if ( isset( $plugins['activate'][ $slug ] ) ) {
                                            $key = esc_html__( 'Activation', 'porto' );
                                        }
                                        if ( $key ) {
                                            if ( $plugin['required'] ) {
                                                printf( esc_html__( '%s required', 'porto' ), $key );
                                            } else {
                                                printf( esc_html__( '%s recommended for certain demos', 'porto' ), $key );
                                            }
                                        }
                                    ?>
                                    </span>
                                </label>
                                <div class="spinner"></div>
                            </li>
                            <?php if ( 'porto-functionality' === $plugin['slug'] ) : ?>
                                <li class="separator"></li>
                            <?php endif; ?>
                        <?php } ?>
                    </ul>
                    <?php
                } else {
                    echo '<p class="lead">'.esc_html__( 'Good news! All plugins are already installed and up to date. Please continue.', 'porto' ).'</p>';
                } ?>

                <p class="porto-setup-actions step">
                    <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-primary button button-large button-next" data-callback="install_plugins"><?php esc_html_e( 'Continue', 'porto' ); ?></a>
                    <?php wp_nonce_field( 'porto-setup' ); ?>
                </p>
            </form>
            <?php
        }


        public function ajax_plugins() {
            if ( ! check_ajax_referer( 'porto_setup_wizard_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
                wp_send_json_error( array( 'error' => 1, 'message' => esc_html__( 'No Slug Found', 'porto' ) ) );
            }
            $json = array();
            // send back some json we use to hit up TGM
            $plugins = $this->_get_plugins();
            // what are we doing with this plugin?
            foreach ( $plugins['activate'] as $slug => $plugin ) {
                if ( $_POST['slug'] == $slug ) {
                    $json = array(
                        'url' => admin_url( $this->tgmpa_url ),
                        'plugin' => array( $slug ),
                        'tgmpa-page' => $this->tgmpa_menu_slug,
                        'plugin_status' => 'all',
                        '_wpnonce' => wp_create_nonce( 'bulk-plugins' ),
                        'action' => 'tgmpa-bulk-activate',
                        'action2' => -1,
                        'message' => esc_html__( 'Activating Plugin', 'porto' ),
                    );
                    break;
                }
            }
            foreach ( $plugins['update'] as $slug => $plugin ) {
                if ( $_POST['slug'] == $slug ) {
                    $json = array(
                        'url' => admin_url( $this->tgmpa_url ),
                        'plugin' => array( $slug ),
                        'tgmpa-page' => $this->tgmpa_menu_slug,
                        'plugin_status' => 'all',
                        '_wpnonce' => wp_create_nonce( 'bulk-plugins' ),
                        'action' => 'tgmpa-bulk-update',
                        'action2' => -1,
                        'message' => esc_html__( 'Updating Plugin', 'porto' ),
                    );
                    break;
                }
            }
            foreach ( $plugins['install'] as $slug => $plugin ) {
                if ( $_POST['slug'] == $slug ) {
                    $json = array(
                        'url' => admin_url( $this->tgmpa_url ),
                        'plugin' => array( $slug ),
                        'tgmpa-page' => $this->tgmpa_menu_slug,
                        'plugin_status' => 'all',
                        '_wpnonce' => wp_create_nonce( 'bulk-plugins' ),
                        'action' => 'tgmpa-bulk-install',
                        'action2' => -1,
                        'message' => esc_html__( 'Installing Plugin', 'porto' ),
                    );
                    break;
                }
            }

            if ( $json ) {
                $json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
                wp_send_json( $json );
            } else {
                wp_send_json( array( 'done' => 1, 'message' => esc_html__( 'Success','porto' ) ) );
            }
            exit;
        }

        private function _make_child_theme( $new_theme_title ) {

            $parent_theme_title = 'Porto';
            $parent_theme_template = 'porto';
            $parent_theme_name = get_stylesheet();
            $parent_theme_dir = get_stylesheet_directory();

            $new_theme_name = sanitize_title( $new_theme_title );
            $theme_root = get_theme_root();

            $new_theme_path = $theme_root.'/'.$new_theme_name;
            if ( !file_exists( $new_theme_path ) ) {
                mkdir( $new_theme_path );

                $plugin_folder = get_template_directory().'/inc/admin/setup_wizard/porto-child/';

                ob_start();
                require $plugin_folder.'style.css.php';
                $css = ob_get_clean();
                file_put_contents( $new_theme_path.'/style.css', $css );

                // Copy functions.php
                copy( $plugin_folder.'functions.php', $new_theme_path.'/functions.php' );

                // Copy screenshot
                copy( $plugin_folder.'screenshot.png', $new_theme_path.'/screenshot.png' );

                // Copy style rtl
                copy( $plugin_folder.'style_rtl.css', $new_theme_path.'/style_rtl.css' );

                // Make child theme an allowed theme (network enable theme)
                $allowed_themes = get_site_option( 'allowedthemes' );
                $allowed_themes[ $new_theme_name ] = true;
                update_site_option( 'allowedthemes', $allowed_themes );
            }

            // Switch to theme
            if($parent_theme_template !== $new_theme_name){
                echo '<p class="lead success">Child Theme <strong>'.$new_theme_title.'</strong> created and activated!<br />Folder is located in wp-content/themes/<strong>'.$new_theme_name.'</strong></p>';
                switch_theme( $new_theme_name, $new_theme_name );
            }
        }

        /**
         * Logo & Design
         */
        public function porto_setup_wizard_demo_content() {
            ?>
            <h1><?php esc_html_e( 'Demo Install', 'porto' ); ?></h1>
            <h3><?php esc_html_e( 'Upload Logo', 'porto' ); ?></h3>
            <form method="post" class="porto-install-demos">
                <input type="hidden" id="current_site_url" value="<?php echo esc_url( site_url() ); ?>">
                <table>
                    <tr>
                        <td>
                            <div id="current-logo">
                            <?php
                                global $porto_settings;
                                if ( !$porto_settings['logo-type'] ) {
                                    $image_url = $porto_settings['logo'] && $porto_settings['logo']['url'] ? $porto_settings['logo']['url'] : porto_uri . '/images/logo/logo.png';
                                    $logo_width = $porto_settings['logo-overlay-width'] ? $porto_settings['logo-overlay-width'] : 250;
                                    if ( $image_url ) {
                                        $image = '<img class="site-logo" src="%s" alt="%s" style="max-width:%spx; height:auto" />';
                                        printf(
                                            $image,
                                            $image_url,
                                            get_bloginfo( 'name' ),
                                            $logo_width
                                        );
                                    }
                                } else {
                            ?>
                                <input type="text" name="new_logo_text" id="new_logo_text" value="<?php echo esc_attr( $porto_settings['logo-text'] ); ?>" style="padding: 7px 10px; width: 300px;">
                            <?php
                                }
                            ?>
                            </div>
                        </td>
                        <td>
                            <?php if ( !$porto_settings['logo-type'] ) : ?>
                            <a href="#" class="button button-upload"><?php esc_html_e( 'Upload New Logo', 'porto' ); ?></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
                <p>You can upload and customize this in Theme Options later.</p>

                <hr/>

                <h3 style="margin-top: 30px;"><?php esc_html_e( 'Select Demo', 'porto' ); ?></h3>
                <?php
                    $demos = porto_demo_types();
                    $demo_filters = porto_demo_filters();
                    $memory_limit = wp_convert_hr_to_bytes( @ini_get( 'memory_limit' ) );
                    $required_plugins = porto_get_required_plugins_list();
                    $uninstalled_plugins = array();
                    $all_plugins = array();
                    foreach( $required_plugins as $plugin ) {
                        if ( $plugin['required'] && is_plugin_inactive( $plugin['url'] ) ) {
                            $uninstalled_plugins[$plugin['slug']] = $plugin;
                        }
                        $all_plugins[$plugin['slug']] = $plugin;
                    }
                    $time_limit = ini_get( 'max_execution_time' );
                    $server_status = $memory_limit >= 268435456 && ( $time_limit >= 600 || $time_limit == 0 );
                ?>

                <div class="porto-install-demo mfp-hide">
                    <div class="theme-img"></div>
                    <div id="import-status"></div>
                    <div id="porto-install-options">
                        <h3>
                            <span class="theme-name"></span> <?php esc_html_e('Demo', 'porto') ?>
                            <?php if ( Porto()->is_registered() ) : ?>
                                <span class="more-options"><?php esc_html_e( 'Details', 'porto' ); ?></span>
                            <?php endif; ?>
                        </h3>
                        <div class="porto-install-section" style="margin-bottom: 10px;">
                            <?php if ( Porto()->is_registered() ) : ?>
                                <div class="porto-install-options-section" style="display: none;">
                                    <label for="porto-import-options"><input type="checkbox" id="porto-import-options" value="1" checked="checked"/> <?php esc_html_e('Import theme options', 'porto') ?></label>
                                    <input type="hidden" id="porto-install-demo-type" value="landing"/>
                                    <label for="porto-reset-menus"><input type="checkbox" id="porto-reset-menus" value="1" checked="checked"/> <?php esc_html_e('Reset menus', 'porto') ?></label>
                                    <label for="porto-reset-widgets"><input type="checkbox" id="porto-reset-widgets" value="1" checked="checked"/> <?php esc_html_e('Reset widgets', 'porto') ?></label>
                                    <label for="porto-import-dummy"><input type="checkbox" id="porto-import-dummy" value="1" checked="checked"/> <?php esc_html_e('Import dummy content', 'porto') ?></label>
                                    <label for="porto-import-widgets"><input type="checkbox" id="porto-import-widgets" value="1" checked="checked"/> <?php esc_html_e('Import widgets', 'porto') ?></label>
                                    <label for="porto-import-icons"><input type="checkbox" id="porto-import-icons" value="1" checked="checked"/> <?php esc_html_e('Import icons for ultimate addons plugin', 'porto') ?></label>
                                    <label for="porto-import-shortcodes"><input type="checkbox" id="porto-import-shortcodes" value="1"/> <?php esc_html_e('Import shortcode pages', 'porto') ?></label>
                                    <label for="porto-override-contents"><input type="checkbox" id="porto-override-contents" value="1" checked="checked" /> <?php esc_html_e('Override existing contents', 'porto') ?></label>
                                </div>
                                <p><?php esc_html_e('Do you want to install demo? It can also take a minute to complete.', 'porto') ?></p>
                                <button class="btn <?php echo $server_status ? 'btn-primary' : 'btn-quaternary'; ?> porto-import-yes"<?php echo !$server_status ? ' disabled="disabled"' : ''; ?>><?php esc_html_e('Standard Import', 'porto') ?></button>
                                <?php if ( !$server_status ) : ?>
                                <p><?php esc_html_e( 'Your server performance does not satisfy Porto demo importer engine\'s requirement. We recommend you to use alternative method to perform demo import without any issues but it may take much time than standard import.', 'porto' ); ?></p>
                                <?php else: ?>
                                <p><?php esc_html_e( 'If you have any issues with standard import, please use Alternative mode. But it may take much time than standard import.', 'porto' ); ?></p>
                                <?php endif; ?>
                                <button class="btn btn-primary porto-import-yes alternative"><?php esc_html_e('Alternative Mode', 'porto') ?></button>
                            <?php endif; ?>
                        </div>
                        <?php if ( !Porto()->is_registered() ) : ?>
                            <a href="<?php echo esc_url( $this->get_step_link( 'updates' ) ); ?>" class="btn btn-quaternary" style="display: inline-block; box-sizing: border-box; text-decoration: none; text-align: center; margin-bottom: 20px;"><?php esc_html_e( 'Activate Theme', 'porto' ); ?></a>
                        <?php endif; ?>
                        <a href="#" class="live-site" target="_blank"><?php esc_html_e( 'Live Preview', 'porto' ); ?></a>
                    </div>
                </div>
                <div class="demo-sort-filters">
                    <ul data-sort-id="theme-install-demos" class="sort-source">
                    <?php foreach ( $demo_filters as $filter_class => $filter_name) : ?>
                        <li data-filter-by="<?php echo esc_attr($filter_class) ?>" data-active="<?php echo ($filter_class=='all' ? 'true' : 'false') ?>"><a href="#"><?php echo $filter_name ?></a></li>
                    <?php endforeach; ?>
                    </ul>
                    <div class="clear"></div>
                </div>
                <div id="theme-install-demos">
                    <?php foreach ( $demos as $demo => $demo_details) : ?>
                        <?php
                            $uninstalled_demo_plugins = $uninstalled_plugins;
                            if ( isset( $demo_details['revslider'] ) && !empty( $demo_details['revslider'] ) && is_plugin_inactive( 'revslider/revslider.php' ) ) {
                                $uninstalled_demo_plugins['revslider'] = $all_plugins['revslider'];
                            }
                            if ( !empty( $demo_details['plugins'] ) ) {
                                foreach( $demo_details['plugins'] as $plugin ) {
                                    if ( is_plugin_inactive( $all_plugins[$plugin]['url'] ) ) {
                                        $uninstalled_demo_plugins[$plugin] = $all_plugins[$plugin];
                                    }
                                }
                            }
                        ?>
                        <div class="theme <?php echo $demo_details['filter'] ?>">
                            <div class="theme-wrapper">
                                <div class="theme-screenshot">
                                    <img src="<?php echo $demo_details['img']; ?>" alt="" />
                                </div>
                                <h3 class="theme-name" id="<?php echo $demo; ?>" data-live-url="<?php echo ( $demo != 'landing' ) ? $this->porto_url .  $demo : $this->porto_url; ?>"><?php echo $demo_details['alt']; ?></h3>
                                <?php if ( !empty( $uninstalled_demo_plugins ) ) : ?>
                                    <ul class="plugins-used">
                                        <?php foreach( $uninstalled_demo_plugins as $plugin ) : ?>
                                            <li>
                                                <div class="thumb">
                                                    <img src="<?php echo $plugin['image_url']; ?>" alt="" />
                                                </div>
                                                <div>
                                                    <h5><?php echo $plugin['name']; ?></h5>
                                                    <?php if ( $plugin['slug'] == 'revslider' ) : ?>
                                                        <p><?php _e( 'Demo sliders <u>will not</u> be installed if Revolution Slider is not active.', 'porto' ); ?></p>
                                                    <?php endif; ?>
                                                </div>
                                            </li>
                                        <?php endforeach; ?>
                                        <li>
                                            <p><?php printf( __( 'Please go to <a href="%s">Plugins step</a> and install required plugins.', 'porto' ), esc_url( $this->get_step_link( 'default_plugins' ) ) ); ?></p>
                                        </li>
                                    </ul>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <br />
                <p><?php esc_html_e( 'Installing a demo provides pages, posts, menus, images, theme options, widgets and more.', 'porto' ); ?>
                <br /><strong><?php esc_html_e( 'IMPORTANT: The included plugins need to be installed and activated before you install a demo.', 'porto' ); ?> </strong>
                <br /><?php printf( __( 'Please check the <a href="%s">Status</a> step to ensure your server meets all requirements for a successful import. Settings that need attention will be listed in red.', 'porto' ), esc_url( $this->get_step_link( 'status' ) ) ); ?></p>
                <p class="lead"><?php esc_html_e( 'If you want to install demo later or don\'t want it, you can skip this step', 'porto' ) ?></p>

                <input type="hidden" name="new_logo_id" id="new_logo_id" value="">

                <p class="porto-setup-actions step">
                    <input type="submit" class="button-primary button button-large button-next" value="<?php esc_attr_e( 'Continue', 'porto' ); ?>" name="save_step" />
                    <?php wp_nonce_field( 'porto-setup' ); ?>
                </p>
            </form>
            <?php
        }

        /**
         * Save logo & design options
         */
        public function porto_setup_wizard_demo_content_save() {
            check_admin_referer( 'porto-setup' );

            $new_logo_id = (int) $_POST['new_logo_id'];
            $new_logo_text = $_POST['new_logo_text'];

            if ( ( $new_logo_id || $new_logo_text ) && class_exists( 'ReduxFrameworkInstances' ) ) {
                $redux = ReduxFrameworkInstances::get_instance( 'porto_settings' );
                global $porto_settings;
                if ( $new_logo_id ) {
                    $attr = wp_get_attachment_image_src( $new_logo_id, 'full' );
                    if ( $attr && ! empty( $attr[1] ) && ! empty( $attr[2] ) ) {
                        $porto_settings['logo']['url'] = $attr[0];
                        $porto_settings['logo']['id'] = $new_logo_id;
                        $porto_settings['logo']['width'] = $attr[1];
                        $porto_settings['logo']['height'] = $attr[2];
                    }
                }
                if ( $new_logo_text ) {
                    $porto_settings['logo-text'] = esc_html( $new_logo_text );
                }
                $redux->set_options( $porto_settings );
            }

            wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
            exit;
        }

        /**
         * Payments Step
         */
        public function porto_setup_wizard_updates() {
            ?>
            <h1><?php esc_html_e( 'Activate Theme', 'porto' ); ?></h1>
            <?php if ( Porto()->is_envato_hosted() ) :?>
                <p class="lead" style="margin-bottom:40px">
                <?php esc_html_e( 'You are using Envato Hosted.', 'porto' ); ?>
                </p>
                <p class="porto-setup-actions step">
                  <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-primary button-large button-next"><?php esc_html_e( 'Continue', 'porto' ); ?></a>
                </p>
            <?php else: ?>
                <p class="lead">Enter your Purchase Code.</p>
                    <?php
                        $output = '';

                        $errors = get_option( 'porto_register_error_msg' );
                        delete_option( 'porto_register_error_msg' );
                        $purchase_code = Porto()->get_purchase_code_asterisk();

                        if ( ! empty( $errors ) ) {
                            echo '<div class="notice-error notice-alt"><p>' . $errors . '</p></div>';
                        }

                        if ( ! empty( $purchase_code ) ) {
                          if ( ! empty( $errors ) ) {
                            echo '<div class="notice-warning notice-alt"><p>' . esc_html__( 'Purchase code not updated. We will keep the existing one.', 'porto' ) . '</p></div>';
                          } else {
                            echo '<div class="notice-success notice-alt notice-large" style="margin-bottom:15px!important">' . __( 'Your <strong>purchase code is valid</strong>. Thank you! Enjoy Porto Theme and automatic updates.', 'porto' ) . '</div>';
                          }
                        }

                        if ( !Porto()->is_registered() ) {
                        echo '<form action="" method="post">';
                        ?>
                            <p style="margin-bottom: 0;"><?php esc_html_e( 'Where can I find my purchase code?', 'porto' ); ?></p>
                            <ol>
                                <li><?php _e( 'Please go to <a target="_blank" href="https://themeforest.net/downloads">ThemeForest.net/downloads</a>', 'porto' ); ?></li>
                                <li><?php _e( 'Click the <strong>Download</strong> button in Porto row', 'porto' ); ?></li>
                                <li><?php _e( 'Select <strong>License Certificate &amp; Purchase code</strong>', 'porto' ); ?></li>
                                <li><?php _e( 'Copy <strong>Item Purchase Code</strong>', 'porto' ); ?></li>
                            </ol>
                        <?php
                            echo '<input type="hidden" name="porto_registration" /><input type="hidden" name="action" value="register" />' .
                                 '<input type="text" id="porto_purchase_code" name="code"
                                  value="' . $purchase_code . '" placeholder="Purchase code" style="width:100%; padding:10px;"/><br/><br/>' .
                                 '<p class="porto-setup-actions step">' .
                                  '<input type="submit" class="button button-large button-next button-primary" value="'. esc_html__( 'Activate', 'porto' ) .'" />' .
                                  '<a href="'.esc_url( $this->get_next_step_link() ).'" class="button button-large button-next">'. esc_html__( 'Skip this step', 'porto' ).'</a>'.
                                 '</p>
                          </form>';
                        } else {
                            echo '<form action="" method="post"><input type="hidden" name="porto_registration" /><input type="hidden" name="action" value="unregister" />' .
                                     '<input type="text" id="porto_purchase_code" name="code"
                                      value="' . $purchase_code . '" placeholder="Purchase code" style="width:100%; padding:10px;"/><br/><br/>' .
                                      '<p class="porto-setup-actions step">' .
                                      '<a href="'.esc_url( $this->get_next_step_link() ).'" class="button button-large button-next" style="margin-right: 0;">'. esc_html__( 'Next Step', 'porto' ).'</a>' .
                                      '<input type="submit" class="button button-large button-next button-primary" value="'. esc_html__( 'Deactivate', 'porto' ) .'" style="margin-right: 0.5em;" />' .
                                     '</p>
                              </form>';
                        }
                    ?>
                    <?php wp_nonce_field( 'porto-setup' ); ?>

                <?php
            endif;
        }

        public function porto_setup_wizard_updates_save() {
            check_admin_referer( 'porto-setup' );

            $url = $this->get_oauth_login_url( $this->get_step_link( 'updates' ) );

            wp_redirect( esc_url_raw( $url ) );
            exit;
        }


        public function porto_setup_wizard_customize() {
        ?>

            <h1><?php esc_html_e( 'Setup Porto Child Theme (Optional)', 'porto' ); ?></h1>

            <p>
                <?php _e( 'If you are going to make changes to the theme source code please use a <a href="https://codex.wordpress.org/Child_Themes" target="_blank">Child Theme</a> rather than modifying the main theme HTML/CSS/PHP code. This allows the parent theme to receive updates without overwriting your source code changes. Use the form below to create and activate the Child Theme.', 'porto' ); ?>
            </p>

            <?php if(!isset($_REQUEST['theme_name'])){ ?>
            <p class="lead"><?php esc_html_e( 'If you\'re not sure what a Child Theme is just click the "Skip this step" button.', 'porto' ); ?></p>
            <?php } ?>

            <?php
                // Create Child Theme
                if ( isset( $_REQUEST['theme_name'] ) && current_user_can( 'manage_options' ) ) {
                    echo $this->_make_child_theme(esc_html($_REQUEST['theme_name']));
                }
                $theme = 'Porto Child';
             ?>

            <?php if( !isset( $_REQUEST['theme_name'] ) ) { ?>

            <form action="<?php $_PHP_SELF ?>" method="POST">
             <div class="child-theme-input" style="margin-bottom: 20px;">
             <label style="font-weight: bold;margin-bottom: 5px; display: block;"><?php esc_html_e( 'Child Theme Title', 'porto' ); ?></label>
             <input type="text" style="padding:10px; width: 100%;" name="theme_name" value="<?php echo $theme; ?>" />
             </div>
            <p class="porto-setup-actions step">
                <button type="submit" id= type="submit"  class="button button-primary button-next button-next">
                 <?php esc_html_e( 'Create and Use Child Theme', 'porto' ); ?>
                </button>
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-next"><?php esc_html_e( 'Skip this step', 'porto' ); ?></a>

            </p>
            </form>
            <?php } else { ?>
            <p class="porto-setup-actions step">
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-primary button-large button-next"><?php esc_html_e( 'Continue', 'porto' ); ?></a>
            </p>
            <?php } ?>
            <?php
        }
        public function porto_setup_wizard_help_support() {
            ?>
            <h1><?php esc_html_e( 'Help and Support', 'porto' ); ?></h1>
            <p class="lead">This theme comes with 6 months item support from purchase date (with the option to extend this period). This license allows you to use this theme on a single website. Please purchase an additional license to use this theme on another website.</p>

            <p class="success">Item Support <strong>DOES</strong> Include:</p>

            <ul>
                <li>Availability of the author to answer questions</li>
                <li>Answering technical questions about item features</li>
                <li>Assistance with reported bugs and issues</li>
                <li>Help with bundled 3rd party plugins</li>
            </ul>

            <p class="error">Item Support <strong>DOES NOT</strong> Include:</p>
            <ul>
                <li>Customization services (this is available through <a href="mailto:nicework125@gmail.com">nicework125@gmail.com</a>)</li>
                <li>Installation services (this is available through <a href="mailto:nicework125@gmail.com">nicework125@gmail.com</a>)</li>
                <li>Help and Support for non-bundled 3rd party plugins (i.e. plugins you install yourself later on)</li>
            </ul>
            <p>More details about item support can be found in the ThemeForest <a href="http://themeforest.net/page/item_support_policy" target="_blank">Item Support Policy</a>. </p>
            <p class="porto-setup-actions step">
                <a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-primary button-large button-next"><?php esc_html_e( 'Agree and Continue', 'porto' ); ?></a>
                <?php wp_nonce_field( 'porto-setup' ); ?>
            </p>
            <?php
        }

        /**
         * Final step
         */
        public function porto_setup_wizard_ready() {

            update_option('porto_setup_complete',time());
            ?>

            <h1><?php esc_html_e( 'Your Website is Ready!', 'porto' ); ?></h1>

            <p class="lead success">Congratulations! The theme has been activated and your website is ready. Please go to your WordPress dashboard to make changes and modify the content for you needs.</p>
            <p>Please come back and <a href="http://themeforest.net/downloads" target="_blank">leave a 5-star rating</a> if you are happy with this theme. Thanks! </p>

            <div class="porto-setup-next-steps">
                <div class="porto-setup-next-steps-first">
                    <h2><?php esc_html_e( 'Next Steps', 'porto' ); ?></h2>
                    <ul>
                        <?php if(class_exists('woocommerce')) { ?><li class="setup-product"><a class="button  button-primary button-large woocommerce-button" href="<?php echo admin_url().'index.php?page=wc-setup';?>"><?php esc_html_e( 'Setup WooCommerce (optional)', 'porto' ); ?></a></li><?php } ?>
                        <li class="setup-product"><a class="button button-primary button-large" href="https://www.facebook.com/groups/porto/" target="_blank"><?php esc_html_e( 'Join Facebook Group', 'porto' ); ?></a></li>
                        <li class="setup-product"><a class="button button-large" href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'View your new website!', 'porto' ); ?></a></li>
                    </ul>
                </div>
                <div class="porto-setup-next-steps-last">
                    <h2><?php esc_html_e( 'More Resources', 'porto' ); ?></h2>
                    <ul>
                        <li class="documentation"><a href="http://www.portotheme.com/wordpress/porto/documentation"><?php esc_html_e( 'Porto Documentation', 'porto' ); ?></a></li>
                        <li class="woocommerce documentation"><a href="https://docs.woocommerce.com/document/woocommerce-101-video-series/"><?php esc_html_e( 'Learn how to use WooCommerce', 'porto' ); ?></a></li>
                        <li class="howto"><a href="https://wordpress.org/support/"><?php esc_html_e( 'Learn how to use WordPress', 'porto' ); ?></a></li>
                        <li class="rating"><a href="http://themeforest.net/downloads"><?php esc_html_e( 'Leave an Item Rating', 'porto' ); ?></a></li>
                    </ul>
                </div>
            </div>
            <?php
        }

    }
}

add_action( 'after_setup_theme', 'porto_theme_setup_wizard', 10 );

if ( ! function_exists( 'porto_theme_setup_wizard' ) ) :
    function porto_theme_setup_wizard() {
        Porto_Theme_Setup_Wizard::get_instance();
    }
endif;