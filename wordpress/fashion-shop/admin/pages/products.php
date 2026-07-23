<?php
/**
 * Products List Page
 * @package FashionShop
 */

$products = get_posts(array('post_type' => 'fs_product', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC'));
?>

<div class="card">
    <div class="card-header">
        <h2>All Products (<?php echo count($products); ?>)</h2>
        <a href="?page=add-product" class="btn-primary"><i class="fas fa-plus"></i> Add New</a>
    </div>

    <?php if (empty($products)) : ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <h3>No products yet</h3>
            <p>Start by adding your first product</p>
            <a href="?page=add-product" class="btn-primary">Add Product</a>
        </div>
    <?php else : ?>
        <table class="data-table">
            <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Featured</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($products as $p) :
                    $price = get_post_meta($p->ID, '_fs_price', true);
                    $discount = get_post_meta($p->ID, '_fs_discount_price', true);
                    $stock = intval(get_post_meta($p->ID, '_fs_stock', true));
                    $featured = get_post_meta($p->ID, '_fs_featured', true);
                    $cats = wp_get_post_terms($p->ID, 'product_category', array('fields' => 'names'));
                    $thumb = get_the_post_thumbnail_url($p->ID, 'thumbnail');
                    $stock_class = $stock > 5 ? 'stock-ok' : ($stock > 0 ? 'stock-low' : 'stock-out');
                ?>
                    <tr>
                        <td>
                            <?php if ($thumb) : ?>
                                <img src="<?php echo esc_url($thumb); ?>" class="table-thumb">
                            <?php else : ?>
                                <div class="table-thumb-placeholder"><i class="fas fa-image"></i></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo esc_html($p->post_title); ?></strong></td>
                        <td><span class="category-pill"><?php echo esc_html(implode(', ', $cats)); ?></span></td>
                        <td>
                            <?php if ($discount && $discount < $price) : ?>
                                <del style="color:#999;">&#8377;<?php echo esc_html($price); ?></del><br>
                                <strong style="color:#27ae60;">&#8377;<?php echo esc_html($discount); ?></strong>
                            <?php else : ?>
                                <strong>&#8377;<?php echo esc_html($price); ?></strong>
                            <?php endif; ?>
                        </td>
                        <td><span class="stock-badge <?php echo $stock_class; ?>"><?php echo esc_html($stock); ?></span></td>
                        <td><?php echo $featured === '1' ? '<span style="color:#a67c52;">&#9733; Yes</span>' : '—'; ?></td>
                        <td>
                            <div class="action-btns">
                                <a href="?page=add-product&edit=<?php echo esc_attr($p->ID); ?>" class="btn-sm btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="?page=products&delete=<?php echo esc_attr($p->ID); ?>&_nonce=<?php echo wp_create_nonce('fs_delete_' . $p->ID); ?>" class="btn-sm btn-delete" title="Delete" onclick="return confirm('Delete this product?')"><i class="fas fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
