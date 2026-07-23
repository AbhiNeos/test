<?php
/**
 * Admin Dashboard Page
 * @package FashionShop
 */

$total_products = wp_count_posts('fs_product')->publish;
$total_orders = wp_count_posts('fs_order')->publish;
$pending = count(get_posts(array('post_type' => 'fs_order', 'posts_per_page' => -1, 'meta_key' => '_fs_order_status', 'meta_value' => 'Pending')));
$featured = count(get_posts(array('post_type' => 'fs_product', 'posts_per_page' => -1, 'meta_key' => '_fs_featured', 'meta_value' => '1')));
$recent_orders = get_posts(array('post_type' => 'fs_order', 'posts_per_page' => 5, 'orderby' => 'date', 'order' => 'DESC'));
?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(166,124,82,0.1); color:#a67c52;"><i class="fas fa-box"></i></div>
        <div class="stat-info"><h3><?php echo esc_html($total_products); ?></h3><p>Total Products</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(39,174,96,0.1); color:#27ae60;"><i class="fas fa-shopping-bag"></i></div>
        <div class="stat-info"><h3><?php echo esc_html($total_orders); ?></h3><p>Total Orders</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(243,156,18,0.1); color:#f39c12;"><i class="fas fa-clock"></i></div>
        <div class="stat-info"><h3><?php echo esc_html($pending); ?></h3><p>Pending Orders</p></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(142,68,173,0.1); color:#8e44ad;"><i class="fas fa-star"></i></div>
        <div class="stat-info"><h3><?php echo esc_html($featured); ?></h3><p>Featured Products</p></div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h2>Quick Actions</h2>
    </div>
    <div class="quick-actions">
        <a href="?page=add-product" class="action-btn"><i class="fas fa-plus-circle"></i> Add New Product</a>
        <a href="?page=products" class="action-btn"><i class="fas fa-list"></i> View All Products</a>
        <a href="?page=orders" class="action-btn"><i class="fas fa-clipboard-list"></i> Manage Orders</a>
        <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" class="action-btn"><i class="fas fa-external-link-alt"></i> View Store</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><h2>Recent Orders</h2></div>
    <?php if (empty($recent_orders)) : ?>
        <p style="padding:20px; color:#999;">No orders yet.</p>
    <?php else : ?>
        <table class="data-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                <?php foreach ($recent_orders as $o) :
                    $status = get_post_meta($o->ID, '_fs_order_status', true);
                    $colors = array('Pending'=>'#f39c12','Confirmed'=>'#3498db','Shipped'=>'#9b59b6','Delivered'=>'#27ae60','Cancelled'=>'#e74c3c');
                    $clr = isset($colors[$status]) ? $colors[$status] : '#999';
                ?>
                    <tr>
                        <td><strong>#<?php echo esc_html($o->ID); ?></strong></td>
                        <td><?php echo esc_html(get_post_meta($o->ID, '_fs_customer_name', true)); ?></td>
                        <td>&#8377;<?php echo esc_html(get_post_meta($o->ID, '_fs_total', true)); ?></td>
                        <td><span class="status-pill" style="background:<?php echo $clr; ?>20; color:<?php echo $clr; ?>;"><?php echo esc_html($status); ?></span></td>
                        <td><?php echo get_the_date('d M Y', $o->ID); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
