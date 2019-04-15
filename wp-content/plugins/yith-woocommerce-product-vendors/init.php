<?php
/*
* Plugin Name: YITH WooCommerce Multi Vendor
* Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-product-vendors/
* Description: <code><strong>YITH WooCommerce Multi Vendor</strong></code> turns your website into a real marketplace, where it's your partners who will add new products independently and you earn a percentage commission on every sale. Take advantage of this great opportunity to increase your earnings in a simple way and for good. <a href="https://yithemes.com/" target="_blank">Find new awesome plugins by <strong>YITH</strong></a>
* Author: YITH
* Text Domain: yith-woocommerce-product-vendors
* Version: 3.2.7
* Author URI: https://yithemes.com/
*
* WC requires at least: 3.0
* WC tested up to: 3.5
*/

/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if( ! function_exists( 'install_premium_woocommerce_admin_notice' ) ) {
    /**
     * Print an admin notice if woocommerce is deactivated
     *
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     * @since 1.0
     * @return void
     * @use admin_notices hooks
     */
    function install_premium_woocommerce_admin_notice() { ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Multi Vendor is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-product-vendors' ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( 'WC' ) ) {
    add_action( 'admin_notices', 'install_premium_woocommerce_admin_notice' );
    return;
}

if ( ! defined( 'YITH_WPV_FREE_INIT' ) ) {
	define( 'YITH_WPV_FREE_INIT', plugin_basename( __FILE__ ) );
}

//  Stop activation if the premium version of the same plugin is still active
if ( defined( 'YITH_WPV_VERSION' ) ) {
	if( ! function_exists( 'yith_wcmv_install_free_admin_notice' ) ){
		function yith_wcmv_install_free_admin_notice() {
			?>
            <div class="error">
                <p><?php _e( 'You can\'t activate the free version of YITH WooCommerce Multi Vendor while you are using the premium one.', 'yith-woocommerce-product-vendors' ); ?></p>
            </div>
			<?php
		}
	}

	add_action( 'admin_notices', 'yith_wcmv_install_free_admin_notice' );

	deactivate_plugins( YITH_WPV_FREE_INIT );
	return;
} else {
    define( 'YITH_WPV_VERSION', '3.2.7' );
}

if ( ! defined( 'YITH_WPV_DB_VERSION' ) ) {
    define( 'YITH_WPV_DB_VERSION', '1.1.6' );
}

/* Load YWCM text domain */
load_plugin_textdomain( 'yith-woocommerce-product-vendors', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if ( ! defined( 'YITH_WPV_SLUG' ) ) {
    define( 'YITH_WPV_SLUG', 'yith-woocommerce-product-vendors' );
}

if ( ! defined( 'YITH_WPV_FILE' ) ) {
    define( 'YITH_WPV_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WPV_PATH' ) ) {
    define( 'YITH_WPV_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_WPV_URL' ) ) {
    define( 'YITH_WPV_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_WPV_ASSETS_URL' ) ) {
    define( 'YITH_WPV_ASSETS_URL', YITH_WPV_URL . 'assets/' );
}

if ( ! defined( 'YITH_WPV_TEMPLATE_PATH' ) ) {
    define( 'YITH_WPV_TEMPLATE_PATH', YITH_WPV_PATH . 'templates/' );
}

/**
 * Init default plugin settings
 */
if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

if ( ! function_exists( 'YITH_Vendors' ) ) {
	/**
	 * Unique access to instance of YITH_Vendors class
	 *
	 * @return YITH_Vendors|YITH_Vendors_Premium
	 * @since 1.0.0
	 */
	function YITH_Vendors() {
		// Load required classes and functions
		require_once( YITH_WPV_PATH . 'includes/class.yith-vendors.php' );

		if ( defined( 'YITH_WPV_PREMIUM' ) && file_exists( YITH_WPV_PATH . 'includes/class.yith-vendors-premium.php' ) ) {
			require_once( YITH_WPV_PATH . 'includes/class.yith-vendors-premium.php' );
			return YITH_Vendors_Premium::instance();
		}

		return YITH_Vendors::instance();
	}
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WPV_PATH . 'plugin-fw/init.php' ) ) {
    require_once( YITH_WPV_PATH . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WPV_PATH  );

/**
 * Instance main plugin class
 */
YITH_Vendors();

register_activation_hook( YITH_WPV_FILE, array( 'YITH_Commissions', 'create_commissions_table' ) );
register_activation_hook( YITH_WPV_FILE, 'YITH_Vendors::add_vendor_role' );
register_deactivation_hook( YITH_WPV_FILE, 'YITH_Vendors::setup' );
register_deactivation_hook( YITH_WPV_FILE, 'YITH_Vendors::remove_vendor_role' );