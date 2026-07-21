<?php
/**
 * Single Product Template
 *
 * @package FashionShop
 */

get_header();

while (have_posts()) : the_post();
    $product_id = get_the_ID();
    $price = get_post_meta($product_id, '_fs_price', true);
    $discount = get_post_meta($product_id, '_fs_discount_price', true);
    $fabric = get_post_meta($product_id, '_fs_fabric', true);
    $stock = get_post_meta($product_id, '_fs_stock', true);
    $sizes = get_post_meta($product_id, '_fs_sizes', true);
    $colors = get_post_meta($product_id, '_fs_colors', true);
    $gallery = get_post_meta($product_id, '_fs_gallery', true);
    $categories = wp_get_post_terms($product_id, 'product_category', array('fields' => 'names'));
    $cat_name = !empty($categories) ? $categories[0] : '';
    $display_price = ($discount && $discount < $price) ? $discount : $price;

    if (!is_array($sizes)) $sizes = array();
    if (!is_array($gallery)) $gallery = array();
    $colors_arr = $colors ? array_map('trim', explode(',', $colors)) : array();

    // Main image
    $main_img = get_the_post_thumbnail_url($product_id, 'product-detail');
    if (!$main_img) $main_img = FASHION_SHOP_URI . '/assets/images/placeholder.svg';
?>

<section class="section">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>">Home</a> /
            <a href="<?php echo esc_url(get_post_type_archive_link('fs_product')); ?>">Shop</a> /
            <span><?php the_title(); ?></span>
        </div>

        <div class="product-detail">
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?php echo esc_url($main_img); ?>" alt="<?php the_title_attribute(); ?>" id="mainImage">
                </div>
                <?php if (!empty($gallery)) : ?>
                    <div class="thumbnail-list">
                        <?php if (has_post_thumbnail()) : ?>
                            <img src="<?php echo esc_url(get_the_post_thumbnail_url($product_id, 'product-thumb')); ?>" class="thumbnail active" onclick="changeImage(this, '<?php echo esc_url($main_img); ?>')">
                        <?php endif; ?>
                        <?php foreach ($gallery as $img_id) :
                            $thumb = wp_get_attachment_image_url($img_id, 'product-thumb');
                            $full = wp_get_attachment_image_url($img_id, 'product-detail');
                            if ($thumb) :
                        ?>
                            <img src="<?php echo esc_url($thumb); ?>" class="thumbnail" onclick="changeImage(this, '<?php echo esc_url($full); ?>')">
                        <?php endif; endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-details">
                <?php if ($cat_name) : ?>
                    <span class="product-category-badge"><?php echo esc_html($cat_name); ?></span>
                <?php endif; ?>

                <h1 class="product-title"><?php the_title(); ?></h1>

                <div class="product-price-detail">
                    <?php if ($discount && $discount < $price) : ?>
                        <span class="price-current-lg">&#8377;<?php echo number_format($discount); ?></span>
                        <span class="price-original-lg">&#8377;<?php echo number_format($price); ?></span>
                        <span class="discount-badge"><?php echo round((1 - $discount/$price) * 100); ?>% OFF</span>
                    <?php else : ?>
                        <span class="price-current-lg">&#8377;<?php echo number_format($price); ?></span>
                    <?php endif; ?>
                </div>

                <div class="product-description"><?php the_content(); ?></div>

                <div class="product-meta">
                    <div class="meta-item"><strong>Fabric:</strong> <span><?php echo esc_html($fabric); ?></span></div>
                    <div class="meta-item">
                        <strong>Availability:</strong>
                        <?php if (intval($stock) > 0) : ?>
                            <span class="in-stock">In Stock (<?php echo esc_html($stock); ?> available)</span>
                        <?php else : ?>
                            <span class="out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (!empty($colors_arr)) : ?>
                    <div class="option-group">
                        <label>Color:</label>
                        <div class="color-options" id="colorOptions">
                            <?php foreach ($colors_arr as $i => $color) : ?>
                                <button type="button" class="color-btn <?php echo $i === 0 ? 'selected' : ''; ?>" data-color="<?php echo esc_attr($color); ?>" onclick="selectColor(this)"><?php echo esc_html($color); ?></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($sizes)) : ?>
                    <div class="option-group">
                        <label>Size:</label>
                        <div class="size-options" id="sizeOptions">
                            <?php foreach ($sizes as $size) : ?>
                                <button type="button" class="size-btn" data-size="<?php echo esc_attr($size); ?>" onclick="selectSize(this)"><?php echo esc_html($size); ?></button>
                            <?php endforeach; ?>
                        </div>
                        <a href="#" class="size-guide-link">Size Guide</a>
                    </div>
                <?php endif; ?>

                <div class="option-group">
                    <label>Quantity:</label>
                    <div class="quantity-selector">
                        <button type="button" onclick="changeQuantity(-1)">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="<?php echo esc_attr($stock); ?>" readonly>
                        <button type="button" onclick="changeQuantity(1)">+</button>
                    </div>
                </div>

                <?php if (intval($stock) > 0) : ?>
                    <div class="product-buttons">
                        <button class="btn btn-primary btn-lg" onclick="addToCart('<?php echo esc_js($product_id); ?>', '<?php echo esc_js(get_the_title()); ?>', <?php echo esc_js($display_price); ?>)">
                            <i class="fas fa-shopping-cart"></i> Add to Cart
                        </button>
                        <button class="btn btn-secondary btn-lg" onclick="buyNow('<?php echo esc_js($product_id); ?>', '<?php echo esc_js(get_the_title()); ?>', <?php echo esc_js($display_price); ?>)">
                            Buy Now
                        </button>
                    </div>
                <?php else : ?>
                    <button class="btn btn-disabled btn-lg" disabled>Out of Stock</button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php
        $cat_terms = wp_get_post_terms($product_id, 'product_category', array('fields' => 'ids'));
        if (!empty($cat_terms)) :
            $related = new WP_Query(array(
                'post_type'      => 'fs_product',
                'posts_per_page' => 4,
                'post__not_in'   => array($product_id),
                'tax_query'      => array(array(
                    'taxonomy' => 'product_category',
                    'field'    => 'term_id',
                    'terms'    => $cat_terms,
                )),
            ));
            if ($related->have_posts()) :
        ?>
            <div class="related-products">
                <h2 class="section-title">You May Also Like</h2>
                <div class="products-grid">
                    <?php while ($related->have_posts()) : $related->the_post();
                        get_template_part('template-parts/product-card');
                    endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        <?php endif; endif; ?>
    </div>
</section>

<script>
function changeImage(el, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}
function selectSize(el) {
    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
}
function selectColor(el) {
    document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('selected'));
    el.classList.add('selected');
}
function changeQuantity(delta) {
    var input = document.getElementById('quantity');
    var val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > parseInt(input.max)) val = parseInt(input.max);
    input.value = val;
}
</script>

<?php endwhile; get_footer(); ?>
