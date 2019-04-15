<?php
/**
 * Order details
 *
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $order = wc_get_order( $order_id ) ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$porto_woo_version = porto_get_woo_version_number();

if ( version_compare( WC_VERSION, '3.2', '>=' ) ) {
	$downloads             = $order->get_downloadable_items();
	$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();


	if ( $show_downloads ) {
		wc_get_template( 'order/order-downloads.php', array( 'downloads' => $downloads, 'show_title' => true ) );
	}
}
?>

<?php if (version_compare($porto_woo_version, '2.6', '<')) : ?>
<div class="featured-box featured-box-primary align-left">
    <div class="box-content">
<?php endif; ?>

<?php if (version_compare($porto_woo_version, '3.0', '>=')) : ?>
<section class="woocommerce-order-details">
<?php endif; ?>
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<h2 class="woocommerce-order-details__title"><?php _e( 'Order Details', 'porto' ); ?></h2>
	
	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">
	
		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php _e( 'Product', 'porto' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php _e( 'Total', 'porto' ); ?></th>
			</tr>
		</thead>
		
		<tbody>
			<?php
				do_action( 'woocommerce_order_details_before_order_table_items', $order );

				foreach( $order_items as $item_id => $item ) {
					$product = apply_filters( 'woocommerce_order_item_product', $item->get_product(), $item );

					wc_get_template( 'order/order-details-item.php', array(
						'order'					=> $order,
						'item_id'				=> $item_id,
						'item'					=> $item,
						'show_purchase_note'	=> $show_purchase_note,
						'purchase_note'			=> $product ? $product->get_purchase_note() : '',
						'product'				=> $product,
					) );
				}

				do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>
		<tfoot>
			<?php
				foreach ( $order->get_order_item_totals() as $key => $total ) {
					?>
					<tr>
						<th scope="row"><?php echo $total['label']; ?></th>
						<td><?php echo $total['value']; ?></td>
					</tr>
					<?php
				}
			?>
		</tfoot>
	</table>
	<br />
	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

	<?php if ( $show_customer_details ) : ?>
		<?php wc_get_template( 'order/order-details-customer.php', array( 'order' =>  $order ) ); ?>
	<?php endif; ?>

<?php if (version_compare($porto_woo_version, '2.6', '<')) : ?>
    </div>
</div>
<?php endif; ?>

<?php if (version_compare($porto_woo_version, '3.0', '>=')) : ?>
</section>
<?php endif; ?>
