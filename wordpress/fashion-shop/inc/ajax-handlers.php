<?php
/**
 * AJAX Handlers for Cart and Orders
 *
 * @package FashionShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Handle Place Order AJAX
 */
function fashion_shop_place_order() {
    check_ajax_referer('fashion_shop_nonce', 'nonce');

    $customer_name = sanitize_text_field($_POST['customerName']);
    $email = sanitize_email($_POST['email']);
    $phone = sanitize_text_field($_POST['phone']);
    $street = sanitize_text_field($_POST['street']);
    $city = sanitize_text_field($_POST['city']);
    $state = sanitize_text_field($_POST['state']);
    $pincode = sanitize_text_field($_POST['pincode']);
    $items = sanitize_textarea_field($_POST['items']);
    $total = intval($_POST['totalAmount']);
    $payment = sanitize_text_field($_POST['paymentMethod']);

    $address = $street . "\n" . $city . ', ' . $state . ' - ' . $pincode;

    // Create order post
    $order_id = wp_insert_post(array(
        'post_type'   => 'fs_order',
        'post_title'  => 'Order by ' . $customer_name . ' - ₹' . $total,
        'post_status' => 'publish',
    ));

    if ($order_id) {
        update_post_meta($order_id, '_fs_customer_name', $customer_name);
        update_post_meta($order_id, '_fs_email', $email);
        update_post_meta($order_id, '_fs_phone', $phone);
        update_post_meta($order_id, '_fs_address', $address);
        update_post_meta($order_id, '_fs_items', $items);
        update_post_meta($order_id, '_fs_total', $total);
        update_post_meta($order_id, '_fs_payment_method', $payment);
        update_post_meta($order_id, '_fs_order_status', 'Pending');

        wp_send_json_success(array('orderId' => $order_id));
    } else {
        wp_send_json_error(array('message' => 'Failed to create order'));
    }
}
add_action('wp_ajax_fashion_shop_place_order', 'fashion_shop_place_order');
add_action('wp_ajax_nopriv_fashion_shop_place_order', 'fashion_shop_place_order');

/**
 * AJAX: Get filtered products
 */
function fashion_shop_filter_products() {
    $args = array(
        'post_type'      => 'fs_product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
    );

    // Category filter
    if (!empty($_GET['category'])) {
        $args['tax_query'] = array(array(
            'taxonomy' => 'product_category',
            'field'    => 'slug',
            'terms'    => sanitize_text_field($_GET['category']),
        ));
    }

    // Price filter
    if (!empty($_GET['minPrice']) || !empty($_GET['maxPrice'])) {
        $meta_query = array();
        if (!empty($_GET['minPrice'])) {
            $meta_query[] = array('key' => '_fs_price', 'value' => intval($_GET['minPrice']), 'compare' => '>=', 'type' => 'NUMERIC');
        }
        if (!empty($_GET['maxPrice'])) {
            $meta_query[] = array('key' => '_fs_price', 'value' => intval($_GET['maxPrice']), 'compare' => '<=', 'type' => 'NUMERIC');
        }
        $args['meta_query'] = $meta_query;
    }

    // Sort
    if (!empty($_GET['sort'])) {
        switch ($_GET['sort']) {
            case 'price_low':
                $args['meta_key'] = '_fs_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                break;
            case 'price_high':
                $args['meta_key'] = '_fs_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
            case 'name':
                $args['orderby'] = 'title';
                $args['order'] = 'ASC';
                break;
            default:
                $args['orderby'] = 'date';
                $args['order'] = 'DESC';
        }
    }

    $query = new WP_Query($args);
    $products = array();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $id = get_the_ID();
            $products[] = array(
                'id'            => $id,
                'name'          => get_the_title(),
                'permalink'     => get_permalink(),
                'image'         => get_the_post_thumbnail_url($id, 'product-card'),
                'price'         => get_post_meta($id, '_fs_price', true),
                'discountPrice' => get_post_meta($id, '_fs_discount_price', true),
                'category'      => wp_get_post_terms($id, 'product_category', array('fields' => 'names')),
                'sizes'         => get_post_meta($id, '_fs_sizes', true),
                'fabric'        => get_post_meta($id, '_fs_fabric', true),
            );
        }
        wp_reset_postdata();
    }

    wp_send_json_success($products);
}
add_action('wp_ajax_fashion_shop_filter', 'fashion_shop_filter_products');
add_action('wp_ajax_nopriv_fashion_shop_filter', 'fashion_shop_filter_products');
