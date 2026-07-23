<?php
/**
 * Add/Edit Product Page
 * @package FashionShop
 */

$editing = false;
$product = null;
if (isset($_GET['edit'])) {
    $editing = true;
    $product = get_post(intval($_GET['edit']));
}
$categories = get_terms(array('taxonomy' => 'product_category', 'hide_empty' => false));
$current_cat = $editing ? wp_get_post_terms($product->ID, 'product_category', array('fields' => 'ids')) : array();
$current_cat_id = !empty($current_cat) ? $current_cat[0] : 0;
$saved_sizes = $editing ? (array) get_post_meta($product->ID, '_fs_sizes', true) : array();
$gallery_ids = $editing ? (array) get_post_meta($product->ID, '_fs_gallery', true) : array();
$gallery_ids = array_filter($gallery_ids);
?>

<div class="card">
    <div class="card-header">
        <h2><?php echo $editing ? 'Edit Product' : 'Add New Product'; ?></h2>
    </div>
    <form method="post" enctype="multipart/form-data" class="product-form">
        <?php wp_nonce_field('fs_save_product', '_fs_nonce'); ?>
        <?php if ($editing) : ?>
            <input type="hidden" name="product_id" value="<?php echo esc_attr($product->ID); ?>">
        <?php endif; ?>

        <div class="form-row">
            <div class="form-group">
                <label>Product Name *</label>
                <input type="text" name="product_name" required
                    placeholder="e.g., Elegant Anarkali Kurti"
                    value="<?php echo $editing ? esc_attr($product->post_title) : ''; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Description</label>
                <textarea name="product_description" rows="4"
                    placeholder="Describe the kurti..."><?php echo $editing ? esc_textarea($product->post_content) : ''; ?></textarea>
            </div>
        </div>

        <div class="form-row two-col">
            <div class="form-group">
                <label>Price (&#8377;) *</label>
                <input type="number" name="price" required min="0" placeholder="1499"
                    value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_price', true)) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Discount Price (&#8377;)</label>
                <input type="number" name="discount_price" min="0" placeholder="999"
                    value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_discount_price', true)) : ''; ?>">
            </div>
        </div>

        <div class="form-row two-col">
            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <?php if (!is_wp_error($categories)) : foreach ($categories as $cat) : ?>
                        <option value="<?php echo esc_attr($cat->term_id); ?>" <?php selected($current_cat_id, $cat->term_id); ?>><?php echo esc_html($cat->name); ?></option>
                    <?php endforeach; endif; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Fabric *</label>
                <input type="text" name="fabric" required
                    placeholder="Cotton, Silk, Georgette..."
                    value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_fabric', true)) : ''; ?>">
            </div>
        </div>

        <div class="form-row two-col">
            <div class="form-group">
                <label>Stock Quantity *</label>
                <input type="number" name="stock" required min="0" placeholder="50"
                    value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_stock', true)) : ''; ?>">
            </div>
            <div class="form-group">
                <label>Colors (comma separated)</label>
                <input type="text" name="colors" placeholder="Red, Blue, Green"
                    value="<?php echo $editing ? esc_attr(get_post_meta($product->ID, '_fs_colors', true)) : ''; ?>">
            </div>
        </div>

        <div class="form-group">
            <label>Available Sizes</label>
            <div class="sizes-grid">
                <?php foreach (array('XS','S','M','L','XL','XXL','3XL') as $sz) : ?>
                    <label class="size-checkbox">
                        <input type="checkbox" name="sizes[]" value="<?php echo esc_attr($sz); ?>" <?php checked(in_array($sz, $saved_sizes)); ?>>
                        <span><?php echo esc_html($sz); ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label>Product Images (select multiple)</label>

            <?php if (!empty($gallery_ids)) : ?>
                <div class="gallery-preview">
                    <?php foreach ($gallery_ids as $img_id) :
                        $img_url = wp_get_attachment_image_url($img_id, 'thumbnail');
                        if (!$img_url) continue;
                    ?>
                        <div class="gallery-thumb">
                            <img src="<?php echo esc_url($img_url); ?>">
                            <label class="remove-check">
                                <input type="checkbox" name="remove_gallery_images[]" value="<?php echo esc_attr($img_id); ?>">
                                <span title="Remove"><i class="fas fa-times"></i></span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p style="font-size:12px; color:#999; margin-bottom:12px;">Check the X on images you want to remove.</p>
            <?php endif; ?>

            <input type="file" name="product_images[]" multiple
                accept="image/jpeg,image/png,image/webp,image/gif" class="file-input">
            <p style="font-size:12px; color:#999; margin-top:6px;">
                Select multiple images at once (hold Ctrl/Cmd). First image becomes the main product image. JPG, PNG, WebP accepted.
            </p>
        </div>

        <div class="form-group">
            <label class="checkbox-inline">
                <input type="checkbox" name="featured" value="1"
                    <?php echo ($editing && get_post_meta($product->ID, '_fs_featured', true) === '1') ? 'checked' : ''; ?>>
                <span>Mark as Featured (shows on homepage)</span>
            </label>
        </div>

        <div class="form-actions">
            <button type="submit" name="fs_save_product" class="btn-primary btn-lg">
                <i class="fas fa-save"></i> <?php echo $editing ? 'Update Product' : 'Add Product'; ?>
            </button>
            <a href="?page=products" class="btn-secondary">Cancel</a>
        </div>
    </form>
</div>
