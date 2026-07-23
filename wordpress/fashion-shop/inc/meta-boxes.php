<?php
/**
 * Product Meta Boxes
 *
 * @package FashionShop
 */

if (!defined('ABSPATH')) exit;

/**
 * Register Product Meta Boxes
 */
function fashion_shop_product_meta_boxes() {
    add_meta_box(
        'fs_product_details',
        __('Product Details', 'fashion-shop'),
        'fashion_shop_product_details_callback',
        'fs_product',
        'normal',
        'high'
    );

    add_meta_box(
        'fs_product_gallery',
        __('Product Gallery', 'fashion-shop'),
        'fashion_shop_product_gallery_callback',
        'fs_product',
        'side',
        'default'
    );

    // Order Meta Box
    add_meta_box(
        'fs_order_details',
        __('Order Details', 'fashion-shop'),
        'fashion_shop_order_details_callback',
        'fs_order',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'fashion_shop_product_meta_boxes');

/**
 * Product Details Meta Box Callback
 */
function fashion_shop_product_details_callback($post) {
    wp_nonce_field('fs_product_meta', 'fs_product_nonce');

    $price = get_post_meta($post->ID, '_fs_price', true);
    $discount_price = get_post_meta($post->ID, '_fs_discount_price', true);
    $fabric = get_post_meta($post->ID, '_fs_fabric', true);
    $stock = get_post_meta($post->ID, '_fs_stock', true);
    $sizes = get_post_meta($post->ID, '_fs_sizes', true);
    $colors = get_post_meta($post->ID, '_fs_colors', true);
    $featured = get_post_meta($post->ID, '_fs_featured', true);

    if (!is_array($sizes)) $sizes = array();
    ?>
    <table class="form-table">
        <tr>
            <th><label for="fs_price">Price (₹) *</label></th>
            <td><input type="number" id="fs_price" name="fs_price" value="<?php echo esc_attr($price); ?>" min="0" step="1" class="regular-text" required></td>
        </tr>
        <tr>
            <th><label for="fs_discount_price">Discount Price (₹)</label></th>
            <td><input type="number" id="fs_discount_price" name="fs_discount_price" value="<?php echo esc_attr($discount_price); ?>" min="0" step="1" class="regular-text">
            <p class="description">Leave empty if no discount</p></td>
        </tr>
        <tr>
            <th><label for="fs_fabric">Fabric *</label></th>
            <td><input type="text" id="fs_fabric" name="fs_fabric" value="<?php echo esc_attr($fabric); ?>" class="regular-text" placeholder="e.g., Cotton, Silk, Georgette, Rayon"></td>
        </tr>
        <tr>
            <th><label for="fs_stock">Stock *</label></th>
            <td><input type="number" id="fs_stock" name="fs_stock" value="<?php echo esc_attr($stock); ?>" min="0" class="small-text"></td>
        </tr>
        <tr>
            <th><label>Available Sizes</label></th>
            <td>
                <?php
                $all_sizes = array('XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL');
                foreach ($all_sizes as $size) :
                ?>
                    <label style="margin-right: 15px; display: inline-block;">
                        <input type="checkbox" name="fs_sizes[]" value="<?php echo esc_attr($size); ?>" <?php checked(in_array($size, $sizes)); ?>>
                        <?php echo esc_html($size); ?>
                    </label>
                <?php endforeach; ?>
            </td>
        </tr>
        <tr>
            <th><label for="fs_colors">Colors (comma separated)</label></th>
            <td><input type="text" id="fs_colors" name="fs_colors" value="<?php echo esc_attr($colors); ?>" class="large-text" placeholder="e.g., Red, Blue, Green, Black"></td>
        </tr>
        <tr>
            <th><label for="fs_featured">Featured Product</label></th>
            <td><label><input type="checkbox" id="fs_featured" name="fs_featured" value="1" <?php checked($featured, '1'); ?>> Display on homepage featured section</label></td>
        </tr>
    </table>
    <?php
}

/**
 * Product Gallery Meta Box Callback
 */
function fashion_shop_product_gallery_callback($post) {
    $gallery = get_post_meta($post->ID, '_fs_gallery', true);
    if (!is_array($gallery)) $gallery = array();
    ?>
    <div id="fs-gallery-container">
        <div id="fs-gallery-images">
            <?php foreach ($gallery as $img_id) :
                $img_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                if ($img_url) : ?>
                    <div class="fs-gallery-item" style="display:inline-block; margin:5px; position:relative;">
                        <img src="<?php echo esc_url($img_url); ?>" style="width:80px; height:80px; object-fit:cover; border-radius:4px;">
                        <input type="hidden" name="fs_gallery[]" value="<?php echo esc_attr($img_id); ?>">
                        <button type="button" class="fs-remove-img" style="position:absolute; top:-5px; right:-5px; background:#e74c3c; color:#fff; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; font-size:12px;">&times;</button>
                    </div>
                <?php endif;
            endforeach; ?>
        </div>
        <p><button type="button" id="fs-add-gallery" class="button">Add Gallery Images</button></p>
        <p class="description">Additional product images (Featured Image is the main image)</p>
    </div>
    <script>
    jQuery(document).ready(function($) {
        $('#fs-add-gallery').on('click', function(e) {
            e.preventDefault();
            var frame = wp.media({title: 'Select Gallery Images', multiple: true, library: {type: 'image'}});
            frame.on('select', function() {
                var selection = frame.state().get('selection');
                selection.each(function(attachment) {
                    var img = attachment.toJSON();
                    var thumb = img.sizes.thumbnail ? img.sizes.thumbnail.url : img.url;
                    $('#fs-gallery-images').append('<div class="fs-gallery-item" style="display:inline-block; margin:5px; position:relative;"><img src="' + thumb + '" style="width:80px; height:80px; object-fit:cover; border-radius:4px;"><input type="hidden" name="fs_gallery[]" value="' + img.id + '"><button type="button" class="fs-remove-img" style="position:absolute; top:-5px; right:-5px; background:#e74c3c; color:#fff; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; font-size:12px;">&times;</button></div>');
                });
            });
            frame.open();
        });
        $(document).on('click', '.fs-remove-img', function() { $(this).parent().remove(); });
    });
    </script>
    <?php
}

/**
 * Order Details Meta Box Callback
 */
function fashion_shop_order_details_callback($post) {
    wp_nonce_field('fs_order_meta', 'fs_order_nonce');

    $customer_name = get_post_meta($post->ID, '_fs_customer_name', true);
    $email = get_post_meta($post->ID, '_fs_email', true);
    $phone = get_post_meta($post->ID, '_fs_phone', true);
    $address = get_post_meta($post->ID, '_fs_address', true);
    $items = get_post_meta($post->ID, '_fs_items', true);
    $total = get_post_meta($post->ID, '_fs_total', true);
    $payment = get_post_meta($post->ID, '_fs_payment_method', true);
    $status = get_post_meta($post->ID, '_fs_order_status', true);
    if (!$status) $status = 'Pending';
    ?>
    <table class="form-table">
        <tr><th>Customer</th><td><strong><?php echo esc_html($customer_name); ?></strong></td></tr>
        <tr><th>Email</th><td><?php echo esc_html($email); ?></td></tr>
        <tr><th>Phone</th><td><?php echo esc_html($phone); ?></td></tr>
        <tr><th>Address</th><td><?php echo nl2br(esc_html($address)); ?></td></tr>
        <tr><th>Total Amount</th><td><strong>₹<?php echo esc_html($total); ?></strong></td></tr>
        <tr><th>Payment</th><td><?php echo esc_html($payment); ?></td></tr>
        <tr><th>Items</th><td><pre style="background:#f5f5f5; padding:10px; border-radius:4px;"><?php echo esc_html($items); ?></pre></td></tr>
        <tr>
            <th><label for="fs_order_status">Order Status</label></th>
            <td>
                <select id="fs_order_status" name="fs_order_status">
                    <?php foreach (array('Pending', 'Confirmed', 'Shipped', 'Delivered', 'Cancelled') as $s) : ?>
                        <option value="<?php echo esc_attr($s); ?>" <?php selected($status, $s); ?>><?php echo esc_html($s); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

/**
 * Save Product Meta
 */
function fashion_shop_save_product_meta($post_id) {
    if (!isset($_POST['fs_product_nonce']) || !wp_verify_nonce($_POST['fs_product_nonce'], 'fs_product_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $fields = array(
        'fs_price'          => '_fs_price',
        'fs_discount_price' => '_fs_discount_price',
        'fs_fabric'         => '_fs_fabric',
        'fs_stock'          => '_fs_stock',
        'fs_colors'         => '_fs_colors',
    );

    foreach ($fields as $field => $meta_key) {
        if (isset($_POST[$field])) {
            update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$field]));
        }
    }

    // Sizes (array)
    $sizes = isset($_POST['fs_sizes']) ? array_map('sanitize_text_field', $_POST['fs_sizes']) : array();
    update_post_meta($post_id, '_fs_sizes', $sizes);

    // Featured
    $featured = isset($_POST['fs_featured']) ? '1' : '0';
    update_post_meta($post_id, '_fs_featured', $featured);

    // Gallery
    $gallery = isset($_POST['fs_gallery']) ? array_map('intval', $_POST['fs_gallery']) : array();
    update_post_meta($post_id, '_fs_gallery', $gallery);
}
add_action('save_post_fs_product', 'fashion_shop_save_product_meta');

/**
 * Save Order Meta
 */
function fashion_shop_save_order_meta($post_id) {
    if (!isset($_POST['fs_order_nonce']) || !wp_verify_nonce($_POST['fs_order_nonce'], 'fs_order_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['fs_order_status'])) {
        update_post_meta($post_id, '_fs_order_status', sanitize_text_field($_POST['fs_order_status']));
    }
}
add_action('save_post_fs_order', 'fashion_shop_save_order_meta');
