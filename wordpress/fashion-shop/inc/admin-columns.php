<?php
/**
 * Custom Admin Columns for Products and Orders
 *
 * @package FashionShop
 */

if (!defined('ABSPATH')) exit;

// Product columns
function fs_product_columns($columns) {
    $new = array();
    $new['cb'] = $columns['cb'];
    $new['thumbnail'] = __('Image', 'fashion-shop');
    $new['title'] = $columns['title'];
    $new['price'] = __('Price', 'fashion-shop');
    $new['stock'] = __('Stock', 'fashion-shop');
    $new['featured'] = __('Featured', 'fashion-shop');
    $new['date'] = $columns['date'];
    return $new;
}
add_filter('manage_fs_product_posts_columns', 'fs_product_columns');

function fs_product_column_data($column, $post_id) {
    switch ($column) {
        case 'thumbnail':
            echo get_the_post_thumbnail($post_id, array(50, 50));
            break;
        case 'price':
            $price = get_post_meta($post_id, '_fs_price', true);
            $discount = get_post_meta($post_id, '_fs_discount_price', true);
            if ($discount) {
                echo '<del>₹' . esc_html($price) . '</del> <strong>₹' . esc_html($discount) . '</strong>';
            } else {
                echo '₹' . esc_html($price);
            }
            break;
        case 'stock':
            $stock = get_post_meta($post_id, '_fs_stock', true);
            $class = intval($stock) < 5 ? 'color:#e74c3c' : 'color:#27ae60';
            echo '<span style="' . $class . '; font-weight:600;">' . esc_html($stock) . '</span>';
            break;
        case 'featured':
            $featured = get_post_meta($post_id, '_fs_featured', true);
            echo $featured === '1' ? '<span style="color:#d4a574;">★ Yes</span>' : '—';
            break;
    }
}
add_action('manage_fs_product_posts_custom_column', 'fs_product_column_data', 10, 2);

// Order columns
function fs_order_columns($columns) {
    return array(
        'cb'       => $columns['cb'],
        'title'    => __('Order', 'fashion-shop'),
        'customer' => __('Customer', 'fashion-shop'),
        'total'    => __('Total', 'fashion-shop'),
        'payment'  => __('Payment', 'fashion-shop'),
        'status'   => __('Status', 'fashion-shop'),
        'date'     => $columns['date'],
    );
}
add_filter('manage_fs_order_posts_columns', 'fs_order_columns');

function fs_order_column_data($column, $post_id) {
    switch ($column) {
        case 'customer':
            echo esc_html(get_post_meta($post_id, '_fs_customer_name', true));
            echo '<br><small>' . esc_html(get_post_meta($post_id, '_fs_phone', true)) . '</small>';
            break;
        case 'total':
            echo '<strong>₹' . esc_html(get_post_meta($post_id, '_fs_total', true)) . '</strong>';
            break;
        case 'payment':
            echo esc_html(get_post_meta($post_id, '_fs_payment_method', true));
            break;
        case 'status':
            $status = get_post_meta($post_id, '_fs_order_status', true);
            $colors = array('Pending' => '#f39c12', 'Confirmed' => '#3498db', 'Shipped' => '#9b59b6', 'Delivered' => '#27ae60', 'Cancelled' => '#e74c3c');
            $color = isset($colors[$status]) ? $colors[$status] : '#999';
            echo '<span style="background:' . $color . '20; color:' . $color . '; padding:4px 10px; border-radius:12px; font-size:12px; font-weight:600;">' . esc_html($status) . '</span>';
            break;
    }
}
add_action('manage_fs_order_posts_custom_column', 'fs_order_column_data', 10, 2);
