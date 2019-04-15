<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 * @class      YITH_Vendor_Request_Quote
 * @package    Yithemes
 * @since      Version 1.7
 * @author     Your Inspiration Themes
 *
 */
if ( ! class_exists( 'YITH_Vendor_Request_Quote' ) && function_exists( 'YITH_Request_Quote' ) ) {

    /**
     * YITH_Vendor_Request_Quote Class
     */
    class YITH_Vendor_Request_Quote {

        /**
         * Main instance
         */
        private static $_instance = null;

        /**
         * Order quote status
         */
        public $quote_status = array();

        /**
         * Construct
         */
        public function __construct(){
            $this->quote_status = $this->get_quote_status();

            //Check if quote have commissions
            add_filter( 'ywraq_order_cart_item_data',                   array( $this, 'order_cart_item_data' ), 10, 3 );
            add_action( 'woocommerce_checkout_create_order_line_item',  array( $this, 'checkout_create_order_line_item_object' ), 10, 4 );
	        add_action( 'woocommerce_saved_order_items',                array( $this, 'manage_order_changing' ), 10, 2 );
	        add_filter( 'woocommerce_order_get_total',                  array( $this, 'quote_get_total' ), 10, 3 );
        }

        public function checkout_create_order_line_item_object( $item, $cart_item_key, $values, $order   ){
        	/** @var WC_Order_Item_Product $item */
	        $item_meta_to_add = apply_filters( 'yith_wcmv_order_cart_item_data_for_quote', array(
			        '_parent_line_item_id',
			        '_commission_id',
			        '_commission_included_tax',
			        '_commission_included_coupon'
		        )
	        );

	        foreach( $item_meta_to_add as $meta_key ){
	        	if( ! empty( $values[ $meta_key ] ) ){
			        $item->add_meta_data( $meta_key, $values[ $meta_key ] );
			        $to_add[ $meta_key ] = $values[ $meta_key ];
		        }
	        }
        }

        public function order_cart_item_data( $cart_item_data, $item, $order ){

	        $to_retreive = apply_filters( 'yith_wcmv_order_cart_item_data_for_quote', array(
			        '_parent_line_item_id',
			        '_commission_id',
			        '_commission_included_tax',
			        '_commission_included_coupon'
		        )
	        );

	        foreach ( $to_retreive as $key ){
		        $value = wc_get_order_item_meta( $item->get_id(), $key, true );
		        if( ! empty( $value ) ){
			        $cart_item_data[ $key ] = $value;
		        }
	        }

        	return $cart_item_data;
        }

        public function get_quote_status( $filtered = true ){
            $raq_status = YITH_YWRAQ_Order_Request()->raq_order_status;
            if( $filtered ){
                array_walk( $raq_status, 'self::filter_status', 'wc-' );
            }
            return $raq_status;
        }

        public function is_quote( $order ) {
            $is_quote = false;

            if( ! is_object( $order ) ){
                $order_id = $order;
                $order    = wc_get_order( $order_id );
            }

            if( $order instanceof WC_Order && $order->has_status( $this->quote_status ) ) {
                $is_quote = true;
            }

            elseif( $order instanceof WP_POST && 'shop_order' == $order->post_type ) {
                $_post = $order;
                $order = wc_get_order( $_post->ID );
                $is_quote = in_array( $order->get_status(), $this->quote_status ) ? true : false;
            }

            return $is_quote;
        }

        /**
         * Main plugin Instance
         *
         * @static
         * @return YITH_Vendor_Request_Quote Main instance
         *
         * @since  1.7
         * @author Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        public static function filter_status( &$status, $key, $prefix ){
            $status = 'wc-' === substr( $status, 0, 3 ) ? substr( $status, 3 ) : $status;
        }

        public function check_if_quote_have_commissions(){
            if( ! empty( $_POST['order_id'] ) ){
                $order_id = $_POST['order_id'];
                if( $this->is_quote( $order_id ) && wp_get_post_parent_id( $order_id ) != 0 ){
                    $quote = wc_get_order( $order_id );
                    $items = $quote->get_items();
                    if( ! empty( $items ) ){
                        foreach ( $items as $item_id => $item ){
                            $commission_id = wc_get_order_item_meta( $item_id, '_commission_id', true );
                            if( empty( $commission_id ) ){
                                delete_post_meta( $order_id, '_commissions_processed' );
                                YITH_Commissions()->register_commissions( $order_id);
                            }
                        }
                    }
                }
            }
        }

	    /**
	     * When order is saved, removes old commissions and update them
	     *
	     * @param $order_id string|int the order id
	     *
	     * @return void
	     * @author Andrea Grilo <andrea.grillo@yithemes.com>
	     * @since 3.6.0
	     */
	    public function manage_order_changing( $order_id ) {
	    	if( $this->is_quote( $order_id ) ){
			    $processed = get_post_meta( $order_id, '_commissions_processed', true );
			    $order = wc_get_order( $order_id );
			    $items = $order->get_items();
			    if( ! $order ){
				    return;
			    }

			    if( 'yes' == $processed ){

				    if( ! empty( $items ) ){
					    foreach( $items as $item_id => $item ){
						    $commission_id = wc_get_order_item_meta( $item_id, '_commission_id', true );
						    error_log( $commission_id );
						    if( $commission_id ){
							    YITH_Commission( $commission_id )->remove();
						    }
					    }
				    }

				    update_post_meta( $order_id, '_commissions_processed', 'no' );
			    }

			    YITH_Commissions()->register_commissions( $order_id );
		    }
	    }

	    /**
	     * Get the correct amount for vendors quote in orders list table
	     *
	     * @param $value float original value fro main quote
	     * @param $order WC_Order
	     *
	     * @return mixed
	     */
	    public function quote_get_total( $value, $order ){
		    global $pagenow;
		    $quote_total = 0;

		    if( 'edit.php' == $pagenow && ! empty( $_GET['post_type'] ) && 'shop_order' == $_GET['post_type'] && function_exists( 'YITH_YWRAQ_Order_Request' ) && YITH_YWRAQ_Order_Request()->is_quote( $order->get_id() )){
			    $suborder_ids = YITH_Orders::get_suborder( $order->get_id() );
			    if( ! empty( $suborder_ids ) ){
				    foreach( $suborder_ids as $suborder_id ){
					    $suborder = wc_get_order( $suborder_id );
					    if( $suborder instanceof WC_Order ){
						    $quote_total = $quote_total + $suborder->get_total();
					    }
				    }
			    }
		    }

		    return ( $quote_total + $value );
	    }
    }
}

/**
 * Main instance of plugin
 *
 * @return /YITH_Vendor_Request_Quote
 * @since  1.9
 * @author Andrea Grillo <andrea.grillo@yithemes.com>
 */
if ( ! function_exists( 'YITH_Vendor_Request_Quote' ) ) {
    function YITH_Vendor_Request_Quote() {
        return YITH_Vendor_Request_Quote::instance();
    }
}

if( empty( YITH_Vendors()->quote ) ){
	YITH_Vendors()->quote = YITH_Vendor_Request_Quote();
}