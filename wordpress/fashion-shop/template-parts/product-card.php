<?php
/**
 * Product Card Template Part
 *
 * @package FashionShop
 */

$product_id = get_the_ID();
$price = get_post_meta($product_id, '_fs_price', true);
$discount = get_post_meta($product_id, '_fs_discount_price', true);
$fabric = get_post_meta($product_id, '_fs_fabric', true);
$stock = get_post_meta($product_id, '_fs_stock', true);
$sizes = get_post_meta($product_id, '_fs_sizes', true);
$categories = wp_get_post_terms($product_id, 'product_category', array('fields' => 'names'));
$cat_name = !empty($categories) ? $categories[0] : '';
?>

<div class="product-card">
    <div class="product-image">
        <?php if (has_post_thumbnail()) : ?>
            <img src="<?php echo esc_url(get_the_post_thumbnail_url($product_id, 'product-card')); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
        <?php else : ?>
            <img src="<?php echo esc_url(FASHION_SHOP_URI . '/assets/images/placeholder.svg'); ?>" alt="<?php the_title_attribute(); ?>" loading="lazy">
        <?php endif; ?>

        <?php echo fs_get_discount_badge($product_id); ?>

        <?php if ($stock && intval($stock) < 5 && intval($stock) > 0) : ?>
            <span class="badge-limited">Few Left</span>
        <?php endif; ?>

        <div class="product-actions">
            <a href="<?php the_permalink(); ?>" class="btn btn-sm">View Details</a>
        </div>
    </div>
    <div class="product-info">
        <?php if ($cat_name) : ?>
            <span class="product-category"><?php echo esc_html($cat_name); ?></span>
        <?php endif; ?>
        <h3 class="product-name"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <?php if ($fabric) : ?>
            <p class="product-fabric"><?php echo esc_html($fabric); ?></p>
        <?php endif; ?>
        <div class="product-price">
            <?php echo fs_get_price_html($product_id); ?>
        </div>
        <?php echo fs_get_sizes_html($product_id); ?>
    </div>
</div>
