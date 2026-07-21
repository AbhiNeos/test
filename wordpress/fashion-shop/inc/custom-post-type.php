<?php
/**
 * Register Custom Post Type: Products
 *
 * @package FashionShop
 */

if (!defined('ABSPATH')) exit;

function fashion_shop_register_post_types() {
    // Product Post Type
    $labels = array(
        'name'               => __('Products', 'fashion-shop'),
        'singular_name'      => __('Product', 'fashion-shop'),
        'add_new'            => __('Add New Product', 'fashion-shop'),
        'add_new_item'       => __('Add New Product', 'fashion-shop'),
        'edit_item'          => __('Edit Product', 'fashion-shop'),
        'new_item'           => __('New Product', 'fashion-shop'),
        'view_item'          => __('View Product', 'fashion-shop'),
        'search_items'       => __('Search Products', 'fashion-shop'),
        'not_found'          => __('No products found', 'fashion-shop'),
        'not_found_in_trash' => __('No products found in trash', 'fashion-shop'),
        'menu_name'          => __('Products', 'fashion-shop'),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 5,
        'menu_icon'          => 'dashicons-tag',
        'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
        'rewrite'            => array('slug' => 'product'),
        'show_in_rest'       => true,
    );

    register_post_type('fs_product', $args);

    // Product Category Taxonomy
    $cat_labels = array(
        'name'              => __('Product Categories', 'fashion-shop'),
        'singular_name'     => __('Category', 'fashion-shop'),
        'search_items'      => __('Search Categories', 'fashion-shop'),
        'all_items'         => __('All Categories', 'fashion-shop'),
        'edit_item'         => __('Edit Category', 'fashion-shop'),
        'update_item'       => __('Update Category', 'fashion-shop'),
        'add_new_item'      => __('Add New Category', 'fashion-shop'),
        'new_item_name'     => __('New Category Name', 'fashion-shop'),
        'menu_name'         => __('Categories', 'fashion-shop'),
    );

    register_taxonomy('product_category', 'fs_product', array(
        'hierarchical'      => true,
        'labels'            => $cat_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'rewrite'           => array('slug' => 'product-category'),
        'show_in_rest'      => true,
    ));

    // Register Order Post Type
    $order_labels = array(
        'name'               => __('Orders', 'fashion-shop'),
        'singular_name'      => __('Order', 'fashion-shop'),
        'add_new'            => __('Add New Order', 'fashion-shop'),
        'add_new_item'       => __('Add New Order', 'fashion-shop'),
        'edit_item'          => __('Edit Order', 'fashion-shop'),
        'view_item'          => __('View Order', 'fashion-shop'),
        'search_items'       => __('Search Orders', 'fashion-shop'),
        'not_found'          => __('No orders found', 'fashion-shop'),
        'menu_name'          => __('Orders', 'fashion-shop'),
    );

    register_post_type('fs_order', array(
        'labels'             => $order_labels,
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 6,
        'menu_icon'          => 'dashicons-cart',
        'supports'           => array('title'),
        'capability_type'    => 'post',
    ));
}
add_action('init', 'fashion_shop_register_post_types');

/**
 * Add default product categories on theme activation
 */
function fashion_shop_create_default_categories() {
    $categories = array(
        'Anarkali', 'Straight', 'A-Line', 'Palazzo', 'Sharara',
        'Printed', 'Embroidered', 'Cotton', 'Silk',
        'Party Wear', 'Casual Wear', 'Office Wear'
    );

    foreach ($categories as $cat) {
        if (!term_exists($cat, 'product_category')) {
            wp_insert_term($cat, 'product_category');
        }
    }
}
add_action('after_switch_theme', 'fashion_shop_create_default_categories');

/**
 * Flush rewrite rules on theme activation
 */
function fashion_shop_rewrite_flush() {
    fashion_shop_register_post_types();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'fashion_shop_rewrite_flush');
