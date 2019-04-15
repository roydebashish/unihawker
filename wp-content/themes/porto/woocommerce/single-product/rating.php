<?php
/**
 * Single Product Rating
 *
 * @version     3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
    return;
}

$rating_count = $product->get_rating_count();
$review_count = $product->get_review_count();
$average = $product->get_average_rating();

?>

<div class="woocommerce-product-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
	<div class="star-rating" title="<?php echo $average ?>">
		<span style="width:<?php echo ( ( $average / 5 ) * 100 ); ?>%">
			<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average ); ?></strong> <?php printf( __( 'out of %s5%s', 'woocommerce' ), '', '' ); ?>
		</span>
	</div>
	<meta content="<?php echo $rating_count; ?>" itemprop="ratingCount" />
	<meta content="5" itemprop="bestRating" />
	<?php if ( comments_open() ) : ?>
		<?php if ( $rating_count > 0 ) : ?>
			<div class="review-link"><a href="<?php if (porto_is_ajax()) the_permalink() ?>#reviews" class="woocommerce-review-link" rel="nofollow"><?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'woocommerce' ), '<span itemprop="reviewCount" class="count">' . $review_count . '</span>' ); ?></a>|<a href="<?php if (porto_is_ajax()) the_permalink() ?>#review_form" class="woocommerce-write-review-link" rel="nofollow"><?php echo __( 'Add a review', 'woocommerce' ); ?></a></div>
		<?php else : ?>
			<div class="review-link noreview">
				<a href="<?php if (porto_is_ajax()) the_permalink() ?>#review_form" class="woocommerce-write-review-link" rel="nofollow">( <?php echo __( 'There are no reviews yet.', 'woocommerce' ); ?> )</a>
			</div>
		<?php endif; ?>
	<?php endif; ?>
</div>