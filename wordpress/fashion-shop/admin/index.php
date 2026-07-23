<?php
/**
 * External Admin Panel - Fashion Shop
 * Standalone admin panel separate from WordPress admin
 *
 * @package FashionShop
 */

// Load WordPress
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/wp-load.php');

// Start session for login
if (!session_id()) session_start();

// Check if logged in
$is_logged_in = isset($_SESSION['fs_admin_logged_in']) && $_SESSION['fs_admin_logged_in'] === true;

// If session says logged in, also set WP current user for media handling
if ($is_logged_in && isset($_SESSION['fs_admin_id'])) {
    wp_set_current_user($_SESSION['fs_admin_id']);
}

// Handle login
if (isset($_POST['fs_login'])) {
    $username = sanitize_text_field($_POST['username']);
    $password = $_POST['password'];
    $user = wp_authenticate($username, $password);
    if (!is_wp_error($user) && user_can($user, 'manage_options')) {
        $_SESSION['fs_admin_logged_in'] = true;
        $_SESSION['fs_admin_user'] = $user->display_name;
        $_SESSION['fs_admin_id'] = $user->ID;
        // Also set WordPress auth cookie so media uploads work
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true);
        $is_logged_in = true;
    } else {
        $login_error = 'Invalid username or password';
    }
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: ' . get_template_directory_uri() . '/admin/');
    exit;
}

// If not logged in, show login page
if (!$is_logged_in) {
    include(get_template_directory() . '/admin/login.php');
    exit;
}

// Get current page
$page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'dashboard';
$admin_url = get_template_directory_uri() . '/admin/';

// Handle product save
if (isset($_POST['fs_save_product']) && wp_verify_nonce($_POST['_fs_nonce'], 'fs_save_product')) {
    $title = sanitize_text_field($_POST['product_name']);
    $content = wp_kses_post($_POST['product_description']);
    $post_data = array(
        'post_type'    => 'fs_product',
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',
    );
    if (!empty($_POST['product_id'])) {
        $post_data['ID'] = intval($_POST['product_id']);
        wp_update_post($post_data);
        $post_id = intval($_POST['product_id']);
    } else {
        $post_id = wp_insert_post($post_data);
    }
    if ($post_id && !is_wp_error($post_id)) {
        update_post_meta($post_id, '_fs_price', sanitize_text_field($_POST['price']));
        update_post_meta($post_id, '_fs_discount_price', sanitize_text_field($_POST['discount_price']));
        update_post_meta($post_id, '_fs_fabric', sanitize_text_field($_POST['fabric']));
        update_post_meta($post_id, '_fs_stock', intval($_POST['stock']));
        update_post_meta($post_id, '_fs_colors', sanitize_text_field($_POST['colors']));
        update_post_meta($post_id, '_fs_featured', isset($_POST['featured']) ? '1' : '0');
        $sizes = isset($_POST['sizes']) ? array_map('sanitize_text_field', $_POST['sizes']) : array();
        update_post_meta($post_id, '_fs_sizes', $sizes);
        if (!empty($_POST['category'])) {
            wp_set_post_terms($post_id, array(intval($_POST['category'])), 'product_category');
        }
        if (!empty($_POST['product_image_id'])) {
            set_post_thumbnail($post_id, intval($_POST['product_image_id']));
        }

        // Handle multiple file uploads
        if (!empty($_FILES['product_images']['name'][0])) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $gallery_ids = get_post_meta($post_id, '_fs_gallery', true);
            if (!is_array($gallery_ids)) $gallery_ids = array();

            $file_count = count($_FILES['product_images']['name']);
            for ($i = 0; $i < $file_count; $i++) {
                if ($_FILES['product_images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                // Restructure for media_handle_upload
                $_FILES['upload_image'] = array(
                    'name'     => $_FILES['product_images']['name'][$i],
                    'type'     => $_FILES['product_images']['type'][$i],
                    'tmp_name' => $_FILES['product_images']['tmp_name'][$i],
                    'error'    => $_FILES['product_images']['error'][$i],
                    'size'     => $_FILES['product_images']['size'][$i],
                );
                $attachment_id = media_handle_upload('upload_image', $post_id);
                if (!is_wp_error($attachment_id)) {
                    $gallery_ids[] = $attachment_id;
                    // Set first image as featured if no featured image exists
                    if (!has_post_thumbnail($post_id)) {
                        set_post_thumbnail($post_id, $attachment_id);
                    }
                }
            }
            update_post_meta($post_id, '_fs_gallery', $gallery_ids);
        }

        // Handle gallery image removal
        if (!empty($_POST['remove_gallery_images'])) {
            $gallery_ids = get_post_meta($post_id, '_fs_gallery', true);
            if (is_array($gallery_ids)) {
                $remove_ids = array_map('intval', $_POST['remove_gallery_images']);
                $gallery_ids = array_diff($gallery_ids, $remove_ids);
                update_post_meta($post_id, '_fs_gallery', array_values($gallery_ids));
            }
        }

        $success_msg = !empty($_POST['product_id']) ? 'Product updated!' : 'Product added!';
    }
}

// Handle product delete
if (isset($_GET['delete']) && isset($_GET['_nonce']) && wp_verify_nonce($_GET['_nonce'], 'fs_delete_' . intval($_GET['delete']))) {
    wp_delete_post(intval($_GET['delete']), true);
    $success_msg = 'Product deleted!';
}

// Handle order status update
if (isset($_POST['update_status']) && wp_verify_nonce($_POST['_fs_nonce'], 'fs_update_order')) {
    update_post_meta(intval($_POST['order_id']), '_fs_order_status', sanitize_text_field($_POST['new_status']));
    $success_msg = 'Order status updated!';
}

// Handle settings save
if (isset($_POST['fs_save_settings']) && wp_verify_nonce($_POST['_fs_nonce'], 'fs_save_settings')) {
    update_option('fashion_shop_whatsapp', sanitize_text_field($_POST['whatsapp_number']));
    update_option('fashion_shop_phone', sanitize_text_field($_POST['store_phone']));
    update_option('fashion_shop_email', sanitize_email($_POST['store_email']));
    update_option('fashion_shop_free_delivery_amount', intval($_POST['free_delivery']));
    $success_msg = 'Settings saved!';
}

// Handle coupon save
if (isset($_POST['fs_save_coupon']) && wp_verify_nonce($_POST['_fs_nonce'], 'fs_save_coupon')) {
    $coupon_code = strtoupper(sanitize_text_field($_POST['coupon_code']));
    $coupon_percent = intval($_POST['coupon_percent']);
    $coupon_min = intval($_POST['coupon_min_amount']);

    if ($coupon_code && $coupon_percent > 0 && $coupon_percent <= 100) {
        $coupon_id = wp_insert_post(array(
            'post_type'   => 'fs_coupon',
            'post_title'  => $coupon_code,
            'post_status' => 'publish',
        ));
        if ($coupon_id && !is_wp_error($coupon_id)) {
            update_post_meta($coupon_id, '_fs_coupon_percent', $coupon_percent);
            update_post_meta($coupon_id, '_fs_coupon_min_amount', $coupon_min);
            update_post_meta($coupon_id, '_fs_coupon_active', '1');
            $success_msg = 'Coupon "' . $coupon_code . '" created! (' . $coupon_percent . '% off)';
        }
    }
}

// Handle coupon delete
if (isset($_GET['delete_coupon']) && isset($_GET['_nonce']) && wp_verify_nonce($_GET['_nonce'], 'fs_delete_coupon_' . intval($_GET['delete_coupon']))) {
    wp_delete_post(intval($_GET['delete_coupon']), true);
    $success_msg = 'Coupon deleted!';
}

// Handle coupon toggle
if (isset($_GET['toggle_coupon']) && isset($_GET['_nonce']) && wp_verify_nonce($_GET['_nonce'], 'fs_toggle_coupon_' . intval($_GET['toggle_coupon']))) {
    $cid = intval($_GET['toggle_coupon']);
    $current = get_post_meta($cid, '_fs_coupon_active', true);
    update_post_meta($cid, '_fs_coupon_active', $current === '1' ? '0' : '1');
    $success_msg = 'Coupon status updated!';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Fashion Shop</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo esc_url(get_template_directory_uri() . '/admin/admin-style.css'); ?>">
</head>
<body>
<div class="admin-layout">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-store"></i>
            <div>
                <h2>Fashion Shop</h2>
                <small>Admin Panel</small>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="?page=dashboard" class="<?php echo $page === 'dashboard' ? 'active' : ''; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="?page=products" class="<?php echo $page === 'products' ? 'active' : ''; ?>"><i class="fas fa-box"></i> Products</a>
            <a href="?page=add-product" class="<?php echo $page === 'add-product' ? 'active' : ''; ?>"><i class="fas fa-plus-circle"></i> Add Product</a>
            <a href="?page=orders" class="<?php echo $page === 'orders' ? 'active' : ''; ?>"><i class="fas fa-shopping-bag"></i> Orders</a>
            <a href="?page=coupons" class="<?php echo $page === 'coupons' ? 'active' : ''; ?>"><i class="fas fa-ticket-alt"></i> Coupons</a>
            <a href="?page=settings" class="<?php echo $page === 'settings' ? 'active' : ''; ?>"><i class="fas fa-cog"></i> Settings</a>
            <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank"><i class="fas fa-external-link-alt"></i> View Store</a>
            <a href="?action=logout" class="logout-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <!-- Main -->
    <main class="main-content">
        <header class="top-header">
            <h1><?php
                switch($page) {
                    case 'dashboard': echo 'Dashboard'; break;
                    case 'products': echo 'All Products'; break;
                    case 'add-product': echo isset($_GET['edit']) ? 'Edit Product' : 'Add Product'; break;
                    case 'orders': echo 'Orders'; break;
                    case 'coupons': echo 'Coupons'; break;
                    case 'settings': echo 'Settings'; break;
                    default: echo 'Dashboard';
                }
            ?></h1>
            <div class="header-right">
                <span class="admin-user"><i class="fas fa-user-circle"></i> <?php echo esc_html($_SESSION['fs_admin_user']); ?></span>
            </div>
        </header>

        <?php if (isset($success_msg)) : ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo esc_html($success_msg); ?></div>
        <?php endif; ?>

        <div class="content-area">
            <?php
            switch ($page) {
                case 'dashboard':
                    include(get_template_directory() . '/admin/pages/dashboard.php');
                    break;
                case 'products':
                    include(get_template_directory() . '/admin/pages/products.php');
                    break;
                case 'add-product':
                    include(get_template_directory() . '/admin/pages/add-product.php');
                    break;
                case 'orders':
                    include(get_template_directory() . '/admin/pages/orders.php');
                    break;
                case 'coupons':
                    include(get_template_directory() . '/admin/pages/coupons.php');
                    break;
                case 'settings':
                    include(get_template_directory() . '/admin/pages/settings.php');
                    break;
                default:
                    include(get_template_directory() . '/admin/pages/dashboard.php');
            }
            ?>
        </div>
    </main>
</div>
</body>
</html>
