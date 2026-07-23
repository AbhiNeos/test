<?php
/**
 * Coupon Code System
 *
 * @package FashionShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Register Coupon Post Type
 */
function fs_register_coupon_post_type() {
    register_post_type('fs_coupon', array(
        'labels' => array(
            'name'          => 'Coupons',
            'singular_name' => 'Coupon',
            'add_new'       => 'Add Coupon',
            'add_new_item'  => 'Add New Coupon',
            'edit_item'     => 'Edit Coupon',
        ),
        'public'       => false,
        'show_ui'      => false,
        'show_in_menu' => false,
        'supports'     => array('title'),
    ));
}
add_action('init', 'fs_register_coupon_post_type');

/**
 * AJAX: Validate coupon code
 */
function fs_validate_coupon() {
    $code = strtoupper(sanitize_text_field($_POST['coupon_code']));

    if (empty($code)) {
        wp_send_json_error(array('message' => 'Please enter a coupon code'));
    }

    // Find coupon by code
    $coupons = get_posts(array(
        'post_type'      => 'fs_coupon',
        'posts_per_page' => 1,
        'title'          => $code,
        'post_status'    => 'publish',
    ));

    // WordPress doesn't search by exact title, so filter manually
    $found = null;
    if (!empty($coupons)) {
        foreach ($coupons as $c) {
            if (strtoupper($c->post_title) === $code) {
                $found = $c;
                break;
            }
        }
    }

    // Also try meta-based search as backup
    if (!$found) {
        $all_coupons = get_posts(array(
            'post_type'      => 'fs_coupon',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ));
        foreach ($all_coupons as $c) {
            if (strtoupper($c->post_title) === $code) {
                $found = $c;
                break;
            }
        }
    }

    if (!$found) {
        wp_send_json_error(array('message' => 'Invalid coupon code'));
    }

    $discount = intval(get_post_meta($found->ID, '_fs_coupon_percent', true));
    $min_amount = intval(get_post_meta($found->ID, '_fs_coupon_min_amount', true));
    $active = get_post_meta($found->ID, '_fs_coupon_active', true);

    if ($active !== '1') {
        wp_send_json_error(array('message' => 'This coupon has expired'));
    }

    if ($discount < 1 || $discount > 100) {
        wp_send_json_error(array('message' => 'Invalid coupon'));
    }

    wp_send_json_success(array(
        'code'       => $code,
        'discount'   => $discount,
        'min_amount' => $min_amount,
        'message'    => $discount . '% discount applied!',
    ));
}
add_action('wp_ajax_fs_validate_coupon', 'fs_validate_coupon');
add_action('wp_ajax_nopriv_fs_validate_coupon', 'fs_validate_coupon');
