<?php
/**
 * The template for displaying product widget entries
 *
 * @version 3.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product, $porto_settings; ?>

<li>
    <?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

	<a class="product-image" href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
		<?php porto_widget_product_thumbnail(); ?>
	</a>

    <div class="product-details">
        <a href="<?php echo esc_url( get_permalink( $product->get_id() ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>">
            <?php echo $product->get_title(); ?>
        </a>

        <?php if ( ! empty( $show_rating ) || $porto_settings['woo-show-rating'] ) : ?>
            <?php echo porto_get_rating_html($product); ?>
        <?php endif; ?>
        <?php echo $product->get_price_html(); ?>
    </div>

    <?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>