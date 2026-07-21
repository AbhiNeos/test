<?php
/**
 * Simple Admin Dashboard for Fashion Shop
 * A user-friendly panel to manage products without WordPress complexity
 *
 * @package FashionShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Add the simple dashboard page
 */
function fs_admin_dashboard_menu() {
    add_menu_page(
        'Product Manager',
        'Product Manager',
        'manage_options',
        'fs-product-manager',
        'fs_product_manager_page',
        'dashicons-grid-view',
        4
    );
    add_submenu_page(
        'fs-product-manager',
        'Add Product',
        'Add Product',
        'manage_options',
        'fs-add-product',
        'fs_add_product_page'
    );
    add_submenu_page(
        'fs-product-manager',
        'View Orders',
        'View Orders',
        'manage_options',
        'fs-view-orders',
        'fs_view_orders_page'
    );
}
add_action('admin_menu', 'fs_admin_dashboard_menu');

/**
 * Enqueue admin styles
 */
function fs_admin_dashboard_styles($hook) {
    if (strpos($hook, 'fs-product-manager') === false && strpos($hook, 'fs-add-product') === false && strpos($hook, 'fs-view-orders') === false) return;
    wp_enqueue_media();
    echo '<style>
        .fs-admin-wrap { max-width: 1200px; }
        .fs-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 24px; margin-bottom: 20px; }
        .fs-card h2 { margin-top: 0; color: #3d2b1f; border-bottom: 2px solid #a67c52; padding-bottom: 10px; }
        .fs-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .fs-stat { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; text-align: center; border-top: 3px solid #a67c52; }
        .fs-stat h3 { font-size: 2em; margin: 0; color: #3d2b1f; }
        .fs-stat p { margin: 5px 0 0; color: #666; }
        .fs-products-table { width: 100%; border-collapse: collapse; }
        .fs-products-table th, .fs-products-table td { padding: 12px; text-align: left; border-bottom: 1px solid #f0f0f0; }
        .fs-products-table th { background: #faf6f1; font-weight: 600; font-size: 12px; text-transform: uppercase; color: #666; }
        .fs-products-table tr:hover { background: #faf6f1; }
        .fs-products-table img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; }
        .fs-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        .fs-badge-active { background: #d4edda; color: #155724; }
        .fs-badge-low { background: #fff3cd; color: #856404; }
        .fs-badge-out { background: #f8d7da; color: #721c24; }
        .fs-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 16px; }
        .fs-form-row.full { grid-template-columns: 1fr; }
        .fs-form-group { margin-bottom: 16px; }
        .fs-form-group label { display: block; font-weight: 600; margin-bottom: 6px; color: #333; }
        .fs-form-group input, .fs-form-group textarea, .fs-form-group select { width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; }
        .fs-form-group textarea { min-height: 100px; }
        .fs-form-group input:focus, .fs-form-group textarea:focus { border-color: #a67c52; outline: none; box-shadow: 0 0 0 2px rgba(166,124,82,0.1); }
        .fs-sizes-wrap { display: flex; gap: 8px; flex-wrap: wrap; }
        .fs-sizes-wrap label { padding: 6px 14px; border: 1px solid #ddd; border-radius: 6px; cursor: pointer; font-size: 13px; }
        .fs-sizes-wrap input:checked + span { background: #a67c52; color: #fff; border-radius: 4px; padding: 2px 6px; }
        .fs-btn { display: inline-block; padding: 12px 24px; background: #a67c52; color: #fff; border: none; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; text-decoration: none; }
        .fs-btn:hover { background: #8b5e3c; color: #fff; }
        .fs-btn-sm { padding: 6px 14px; font-size: 13px; }
        .fs-btn-danger { background: #dc3545; }
        .fs-btn-danger:hover { background: #c82333; color: #fff; }
        .fs-img-preview { display: inline-block; margin: 5px; position: relative; }
        .fs-img-preview img { width: 80px; height: 80px; object-fit: cover; border-radius: 6px; border: 1px solid #ddd; }
        .fs-quick-actions { display: flex; gap: 8px; }
        .fs-order-status { padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
        @media (max-width: 768px) { .fs-stats { grid-template-columns: 1fr 1fr; } .fs-form-row { grid-template-columns: 1fr; } }
    </style>';
}
add_action('admin_head', 'fs_admin_dashboard_styles');

/**
 * Product Manager Main Page
 */
function fs_product_manager_page() {
    // Handle delete
    if (isset($_GET['delete_product']) && wp_verify_nonce($_GET['_wpnonce'], 'fs_delete_product')) {
        wp_delete_post(intval($_GET['delete_product']), true);
        echo '<div class="updated"><p>Product deleted!</p></div>';
    }

    $total_products = wp_count_posts('fs_product')->publish;
    $total_orders = wp_count_posts('fs_order')->publish;
    $pending_orders = 0;
    $orders_q = get_posts(array('post_type' => 'fs_order', 'posts_per_page' => -1, 'meta_key' => '_fs_order_status', 'meta_value' => 'Pending'));
    $pending_orders = count($orders_q);

    $products = get_posts(array('post_type' => 'fs_product', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC'));
    ?>
    <div class="wrap fs-admin-wrap">
        <h1>Product Manager</h1>

        <div class="fs-stats">
            <div class="fs-stat"><h3><?php echo esc_html($total_products); ?></h3><p>Total Products</p></div>
            <div class="fs-stat"><h3><?php echo esc_html($total_orders); ?></h3><p>Total Orders</p></div>
            <div class="fs-stat"><h3><?php echo esc_html($pending_orders); ?></h3><p>Pending Orders</p></div>
            <div class="fs-stat"><h3><a href="<?php echo esc_url(admin_url('admin.php?page=fs-add-product')); ?>" class="fs-btn fs-btn-sm">+ Add New</a></h3><p>Quick Action</p></div>
        </div>

        <div class="fs-card">
            <h2>All Products</h2>
            <?php if (empty($products)) : ?>
                <p>No products yet. <a href="<?php echo esc_url(admin_url('admin.php?page=fs-add-product')); ?>">Add your first product</a></p>
            <?php else : ?>
                <table class="fs-products-table">
                    <thead>
                        <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Featured</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p) :
                            $price = get_post_meta($p->ID, '_fs_price', true);
                            $discount = get_post_meta($p->ID, '_fs_discount_price', true);
                            $stock = get_post_meta($p->ID, '_fs_stock', true);
                            $featured = get_post_meta($p->ID, '_fs_featured', true);
                            $cats = wp_get_post_terms($p->ID, 'product_category', array('fields' => 'names'));
                            $thumb = get_the_post_thumbnail_url($p->ID, 'thumbnail');
                            $stock_class = intval($stock) > 5 ? 'fs-badge-active' : (intval($stock) > 0 ? 'fs-badge-low' : 'fs-badge-out');
                        ?>
                            <tr>
                                <td><?php echo $thumb ? '<img src="' . esc_url($thumb) . '">' : '—'; ?></td>
                                <td><strong><?php echo esc_html($p->post_title); ?></strong></td>
                                <td><?php echo esc_html(implode(', ', $cats)); ?></td>
                                <td>
                                    <?php if ($discount) : ?>
                                        <del>&#8377;<?php echo esc_html($price); ?></del> <strong>&#8377;<?php echo esc_html($discount); ?></strong>
                                    <?php else : ?>
                                        &#8377;<?php echo esc_html($price); ?>
                                    <?php endif; ?>
                                </td>
                                <td><span class="fs-badge <?php echo $stock_class; ?>"><?php echo esc_html($stock); ?></span></td>
                                <td><?php echo $featured === '1' ? '⭐' : '—'; ?></td>
                                <td class="fs-quick-actions">
                                    <a href="<?php echo esc_url(admin_url('admin.php?page=fs-add-product&edit=' . $p->ID)); ?>" class="fs-btn fs-btn-sm">Edit</a>
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('admin.php?page=fs-product-manager&delete_product=' . $p->ID), 'fs_delete_product')); ?>" class="fs-btn fs-btn-sm fs-btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Add/Edit Product Page
 */
function fs_add_product_page() {
    $editing = false;
    $product = null;

    // Handle form submission
    if (isset($_POST['fs_save_product']) && check_admin_referer('fs_product_save')) {
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

        if ($post_id) {
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

            echo '<div class="updated"><p>Product saved! <a href="' . esc_url(admin_url('admin.php?page=fs-product-manager')) . '">View all products</a></p></div>';
        }
    }

    // Load product for editing
    if (isset($_GET['edit'])) {
        $editing = true;
        $product = get_post(intval($_GET['edit']));
    }

    $categories = get_terms(array('taxonomy' => 'product_category', 'hide_empty' => false));
    $current_cat = $editing ? wp_get_post_terms($product->ID, 'product_category', array('fields' => 'ids')) : array();
    $current_cat_id = !empty($current_cat) ? $current_cat[0] : 0;
    ?>
    <div class="wrap fs-admin-wrap">
        <h1><?php echo $editing ? 'Edit Product' : 'Add New Product'; ?></h1>

        <div class="fs-card">
            <form method="post" enctype="multipart/form-data">
                <?php wp_nonce_field('fs_product_save'); ?>
                <?php if ($editing) : ?>
                    <input type="hidden" name="product_id" value="<?php echo esc_attr($product->ID); ?>">
                <?php endif; ?>

                <div class="fs-form-row full">
                    <div class="fs-form-group">
                        <label>Product Name *</label>
                        <input type="text" name="product_name" value="<?php echo $editing ? esc_attr($product->post_title) : ''; ?>" required placeholder="e.g., Elegant Anarkali Kurti">
                    </div>
                </div>

                <div class="fs-form-row full">
                    <div class="fs-form-group">
                        <label>Description</label>
                        <textarea name="product_description" placeholder="Describe the kurti - fabric, design, occasion..."><?php echo $editing ? esc_textarea($product->post_content) : ''; ?></textarea>
                    </div>
                </div>

                <div class="fs-form-row">
                    <div class="fs-form-group">
                        <label>Price (&#8377;) *</label>
                        <input type="number" name="price" value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_price', true)) : ''; ?>" required min="0" placeholder="1499">
                    </div>
                    <div class="fs-form-group">
                        <label>Discount Price (&#8377;)</label>
                        <input type="number" name="discount_price" value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_discount_price', true)) : ''; ?>" min="0" placeholder="999">
                    </div>
                </div>

                <div class="fs-form-row">
                    <div class="fs-form-group">
                        <label>Category *</label>
                        <select name="category" required>
                            <option value="">Select Category</option>
                            <?php if (!is_wp_error($categories)) : foreach ($categories as $cat) : ?>
                                <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($current_cat_id, $cat->term_id); ?>><?php echo esc_html($cat->name); ?></option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                    <div class="fs-form-group">
                        <label>Fabric *</label>
                        <input type="text" name="fabric" value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_fabric', true)) : ''; ?>" required placeholder="Cotton, Silk, Georgette...">
                    </div>
                </div>

                <div class="fs-form-row">
                    <div class="fs-form-group">
                        <label>Stock Quantity *</label>
                        <input type="number" name="stock" value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_stock', true)) : ''; ?>" required min="0" placeholder="50">
                    </div>
                    <div class="fs-form-group">
                        <label>Colors (comma separated)</label>
                        <input type="text" name="colors" value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_colors', true)) : ''; ?>" placeholder="Red, Blue, Green">
                    </div>
                </div>

                <div class="fs-form-group">
                    <label>Available Sizes</label>
                    <div class="fs-sizes-wrap">
                        <?php
                        $all_sizes = array('XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL');
                        $saved_sizes = $editing ? (array) get_post_meta($product->ID, '_fs_sizes', true) : array();
                        foreach ($all_sizes as $sz) : ?>
                            <label><input type="checkbox" name="sizes[]" value="<?php echo esc_attr($sz); ?>" <?php checked(in_array($sz, $saved_sizes)); ?>> <span><?php echo esc_html($sz); ?></span></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="fs-form-group">
                    <label>Product Image</label>
                    <input type="hidden" name="product_image_id" id="product_image_id" value="<?php echo $editing ? get_post_thumbnail_id($product->ID) : ''; ?>">
                    <div id="fs-image-preview">
                        <?php if ($editing && has_post_thumbnail($product->ID)) : ?>
                            <div class="fs-img-preview"><img src="<?php echo esc_url(get_the_post_thumbnail_url($product->ID, 'thumbnail')); ?>"></div>
                        <?php endif; ?>
                    </div>
                    <button type="button" id="fs-upload-btn" class="button">Select Image</button>
                </div>

                <div class="fs-form-group">
                    <label><input type="checkbox" name="featured" value="1" <?php echo ($editing && get_post_meta($product->ID, '_fs_featured', true) === '1') ? 'checked' : ''; ?>> Mark as Featured (shows on homepage)</label>
                </div>

                <p><button type="submit" name="fs_save_product" class="fs-btn"><?php echo $editing ? 'Update Product' : 'Add Product'; ?></button>
                <a href="<?php echo esc_url(admin_url('admin.php?page=fs-product-manager')); ?>" style="margin-left:12px;">Cancel</a></p>
            </form>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        $('#fs-upload-btn').on('click', function(e) {
            e.preventDefault();
            var frame = wp.media({ title: 'Select Product Image', multiple: false, library: { type: 'image' } });
            frame.on('select', function() {
                var img = frame.state().get('selection').first().toJSON();
                var thumb = img.sizes && img.sizes.thumbnail ? img.sizes.thumbnail.url : img.url;
                $('#product_image_id').val(img.id);
                $('#fs-image-preview').html('<div class="fs-img-preview"><img src="' + thumb + '"></div>');
            });
            frame.open();
        });
    });
    </script>
    <?php
}

/**
 * View Orders Page
 */
function fs_view_orders_page() {
    // Handle status update
    if (isset($_POST['update_order_status']) && check_admin_referer('fs_order_update')) {
        update_post_meta(intval($_POST['order_id']), '_fs_order_status', sanitize_text_field($_POST['new_status']));
        echo '<div class="updated"><p>Order status updated!</p></div>';
    }

    $orders = get_posts(array('post_type' => 'fs_order', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC'));
    $status_colors = array('Pending' => '#f39c12', 'Confirmed' => '#3498db', 'Shipped' => '#9b59b6', 'Delivered' => '#27ae60', 'Cancelled' => '#e74c3c');
    ?>
    <div class="wrap fs-admin-wrap">
        <h1>Orders</h1>
        <div class="fs-card">
            <?php if (empty($orders)) : ?>
                <p>No orders yet.</p>
            <?php else : ?>
                <table class="fs-products-table">
                    <thead><tr><th>Order</th><th>Customer</th><th>Phone</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th>Update</th></tr></thead>
                    <tbody>
                        <?php foreach ($orders as $o) :
                            $status = get_post_meta($o->ID, '_fs_order_status', true);
                            $color = isset($status_colors[$status]) ? $status_colors[$status] : '#999';
                        ?>
                            <tr>
                                <td><strong>#<?php echo esc_html($o->ID); ?></strong></td>
                                <td><?php echo esc_html(get_post_meta($o->ID, '_fs_customer_name', true)); ?></td>
                                <td><?php echo esc_html(get_post_meta($o->ID, '_fs_phone', true)); ?></td>
                                <td><strong>&#8377;<?php echo esc_html(get_post_meta($o->ID, '_fs_total', true)); ?></strong></td>
                                <td><?php echo esc_html(get_post_meta($o->ID, '_fs_payment_method', true)); ?></td>
                                <td><span class="fs-order-status" style="background:<?php echo $color; ?>20; color:<?php echo $color; ?>;"><?php echo esc_html($status); ?></span></td>
                                <td><?php echo get_the_date('d M Y', $o->ID); ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <?php wp_nonce_field('fs_order_update'); ?>
                                        <input type="hidden" name="order_id" value="<?php echo esc_attr($o->ID); ?>">
                                        <select name="new_status" onchange="this.form.submit()" style="padding:4px 8px; border-radius:4px; border:1px solid #ddd;">
                                            <?php foreach (array('Pending','Confirmed','Shipped','Delivered','Cancelled') as $s) : ?>
                                                <option value="<?php echo esc_attr($s); ?>" <?php selected($status, $s); ?>><?php echo esc_html($s); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" name="update_order_status" value="1">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
