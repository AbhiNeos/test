<?php
/**
 * Fashion Shop Theme Functions
 *
 * @package FashionShop
 */

if (!defined('ABSPATH')) exit;

define('FASHION_SHOP_VERSION', '1.0.0');
define('FASHION_SHOP_DIR', get_template_directory());
define('FASHION_SHOP_URI', get_template_directory_uri());

/**
 * Theme Setup
 */
function fashion_shop_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'gallery', 'caption'));
    add_image_size('product-card', 400, 500, true);
    add_image_size('product-detail', 800, 1000, true);
    add_image_size('product-thumb', 100, 100, true);
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'fashion-shop'),
        'footer'  => __('Footer Menu', 'fashion-shop'),
    ));
}
add_action('after_setup_theme', 'fashion_shop_setup');

/**
 * Enqueue Scripts and Styles
 */
function fashion_shop_scripts() {
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap', array(), null);
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0');
    wp_enqueue_style('fashion-shop-main', FASHION_SHOP_URI . '/assets/css/main.css', array(), FASHION_SHOP_VERSION);
    wp_enqueue_script('fashion-shop-main', FASHION_SHOP_URI . '/assets/js/main.js', array(), FASHION_SHOP_VERSION, true);
    wp_localize_script('fashion-shop-main', 'fashionShop', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('fashion_shop_nonce'),
        'homeUrl' => home_url('/'),
    ));
}
add_action('wp_enqueue_scripts', 'fashion_shop_scripts');

/**
 * Safely include files - won't crash if file missing
 */
$inc_files = array(
    '/inc/custom-post-type.php',
    '/inc/meta-boxes.php',
    '/inc/ajax-handlers.php',
    '/inc/template-functions.php',
    '/inc/admin-columns.php',
    '/inc/admin-dashboard.php',
);
foreach ($inc_files as $file) {
    $filepath = FASHION_SHOP_DIR . $file;
    if (file_exists($filepath)) {
        require_once $filepath;
    }
}

/**
 * Register Widgets
 */
function fashion_shop_widgets() {
    register_sidebar(array(
        'name'          => __('Shop Sidebar', 'fashion-shop'),
        'id'            => 'shop-sidebar',
        'before_widget' => '<div class="filter-group">',
        'after_widget'  => '</div>',
        'before_title'  => '<h4>',
        'after_title'   => '</h4>',
    ));
}
add_action('widgets_init', 'fashion_shop_widgets');

/**
 * Custom excerpt length
 */
function fashion_shop_excerpt_length($length) {
    return 20;
}
add_filter('excerpt_length', 'fashion_shop_excerpt_length');

/**
 * Theme Settings Page
 */
function fashion_shop_admin_menu() {
    add_menu_page(
        'Fashion Shop Settings',
        'Fashion Shop',
        'manage_options',
        'fashion-shop-settings',
        'fashion_shop_settings_page',
        'dashicons-store',
        30
    );
}
add_action('admin_menu', 'fashion_shop_admin_menu');

function fashion_shop_settings_page() {
    if (isset($_POST['fashion_shop_save']) && check_admin_referer('fs_settings_save')) {
        update_option('fashion_shop_phone', sanitize_text_field($_POST['phone']));
        update_option('fashion_shop_email', sanitize_email($_POST['email']));
        update_option('fashion_shop_address', sanitize_textarea_field($_POST['address']));
        update_option('fashion_shop_free_delivery_amount', intval($_POST['free_delivery_amount']));
        update_option('fashion_shop_facebook', esc_url_raw($_POST['facebook']));
        update_option('fashion_shop_instagram', esc_url_raw($_POST['instagram']));
        echo '<div class="updated"><p>Settings saved!</p></div>';
    }
    ?>
    <div class="wrap">
        <h1>Fashion Shop Settings</h1>
        <form method="post">
            <?php wp_nonce_field('fs_settings_save'); ?>
            <table class="form-table">
                <tr><th>Phone</th><td><input type="text" name="phone" value="<?php echo esc_attr(get_option('fashion_shop_phone', '+91 98765 43210')); ?>" class="regular-text"></td></tr>
                <tr><th>Email</th><td><input type="email" name="email" value="<?php echo esc_attr(get_option('fashion_shop_email', 'info@fashionshop.com')); ?>" class="regular-text"></td></tr>
                <tr><th>Address</th><td><textarea name="address" class="large-text" rows="3"><?php echo esc_textarea(get_option('fashion_shop_address', '123 Fashion Street, New Delhi')); ?></textarea></td></tr>
                <tr><th>Free Delivery Above (&#8377;)</th><td><input type="number" name="free_delivery_amount" value="<?php echo esc_attr(get_option('fashion_shop_free_delivery_amount', 999)); ?>"></td></tr>
                <tr><th>Facebook URL</th><td><input type="url" name="facebook" value="<?php echo esc_attr(get_option('fashion_shop_facebook', '#')); ?>" class="regular-text"></td></tr>
                <tr><th>Instagram URL</th><td><input type="url" name="instagram" value="<?php echo esc_attr(get_option('fashion_shop_instagram', '#')); ?>" class="regular-text"></td></tr>
            </table>
            <p class="submit"><input type="submit" name="fashion_shop_save" class="button-primary" value="Save Settings"></p>
        </form>
    </div>
    <?php
}
