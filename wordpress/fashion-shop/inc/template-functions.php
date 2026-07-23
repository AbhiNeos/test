<?php
/**
 * Template Helper Functions
 *
 * @package FashionShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Get product price display HTML
 */
function fs_get_price_html($post_id) {
    $price = get_post_meta($post_id, '_fs_price', true);
    $discount = get_post_meta($post_id, '_fs_discount_price', true);

    if ($discount && $discount < $price) {
        $percent = round((1 - $discount / $price) * 100);
        return sprintf(
            '<span class="price-current">&#8377;%s</span><span class="price-original">&#8377;%s</span>',
            number_format($discount),
            number_format($price)
        );
    }
    return sprintf('<span class="price-current">&#8377;%s</span>', number_format($price));
}

/**
 * Get discount badge HTML
 */
function fs_get_discount_badge($post_id) {
    $price = get_post_meta($post_id, '_fs_price', true);
    $discount = get_post_meta($post_id, '_fs_discount_price', true);

    if ($discount && $discount < $price) {
        $percent = round((1 - $discount / $price) * 100);
        return '<span class="badge-discount">' . $percent . '% OFF</span>';
    }
    return '';
}

/**
 * Get product sizes display
 */
function fs_get_sizes_html($post_id) {
    $sizes = get_post_meta($post_id, '_fs_sizes', true);
    if (!is_array($sizes) || empty($sizes)) return '';

    $html = '<div class="product-sizes">';
    foreach ($sizes as $size) {
        $html .= '<span class="size-tag">' . esc_html($size) . '</span>';
    }
    $html .= '</div>';
    return $html;
}
