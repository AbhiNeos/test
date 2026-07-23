<?php
/**
 * Coupons Management Page
 * @package FashionShop
 */

$coupons = get_posts(array('post_type' => 'fs_coupon', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC'));
?>

<!-- Add New Coupon Form -->
<div class="card">
    <div class="card-header"><h2><i class="fas fa-plus-circle" style="color:#a67c52;"></i> Create New Coupon</h2></div>
    <form method="post" class="product-form">
        <?php wp_nonce_field('fs_save_coupon', '_fs_nonce'); ?>
        <div class="form-row two-col">
            <div class="form-group">
                <label>Coupon Code *</label>
                <input type="text" name="coupon_code" required placeholder="e.g., SAVE30" style="text-transform:uppercase;">
                <p style="font-size:12px; color:#999; margin-top:4px;">Customers will enter this code at checkout</p>
            </div>
            <div class="form-group">
                <label>Discount Percentage (%) *</label>
                <input type="number" name="coupon_percent" required min="1" max="100" placeholder="e.g., 30">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Minimum Order Amount (&#8377;)</label>
                <input type="number" name="coupon_min_amount" min="0" value="0" placeholder="0 = no minimum">
                <p style="font-size:12px; color:#999; margin-top:4px;">Set to 0 for no minimum order requirement</p>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="fs_save_coupon" class="btn-primary">
                <i class="fas fa-ticket-alt"></i> Create Coupon
            </button>
        </div>
    </form>
</div>

<!-- Existing Coupons -->
<div class="card" style="margin-top:24px;">
    <div class="card-header"><h2>All Coupons (<?php echo count($coupons); ?>)</h2></div>
    <?php if (empty($coupons)) : ?>
        <div class="empty-state">
            <i class="fas fa-ticket-alt"></i>
            <h3>No coupons yet</h3>
            <p>Create your first coupon above</p>
        </div>
    <?php else : ?>
        <table class="data-table">
            <thead><tr><th>Code</th><th>Discount</th><th>Min. Order</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach ($coupons as $c) :
                    $percent = get_post_meta($c->ID, '_fs_coupon_percent', true);
                    $min = get_post_meta($c->ID, '_fs_coupon_min_amount', true);
                    $active = get_post_meta($c->ID, '_fs_coupon_active', true);
                ?>
                    <tr>
                        <td><strong style="font-size:15px; letter-spacing:1px;"><?php echo esc_html($c->post_title); ?></strong></td>
                        <td><span style="background:rgba(39,174,96,0.1); color:#27ae60; padding:4px 12px; border-radius:12px; font-weight:600;"><?php echo esc_html($percent); ?>% OFF</span></td>
                        <td><?php echo $min > 0 ? '&#8377;' . esc_html($min) : 'No minimum'; ?></td>
                        <td>
                            <?php if ($active === '1') : ?>
                                <span class="status-pill" style="background:rgba(39,174,96,0.15); color:#27ae60;">Active</span>
                            <?php else : ?>
                                <span class="status-pill" style="background:rgba(231,76,60,0.15); color:#e74c3c;">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="?page=coupons&toggle_coupon=<?php echo esc_attr($c->ID); ?>&_nonce=<?php echo wp_create_nonce('fs_toggle_coupon_' . $c->ID); ?>" class="btn-sm btn-edit" title="<?php echo $active === '1' ? 'Deactivate' : 'Activate'; ?>">
                                    <i class="fas fa-<?php echo $active === '1' ? 'pause' : 'play'; ?>"></i>
                                </a>
                                <a href="?page=coupons&delete_coupon=<?php echo esc_attr($c->ID); ?>&_nonce=<?php echo wp_create_nonce('fs_delete_coupon_' . $c->ID); ?>" class="btn-sm btn-delete" title="Delete" onclick="return confirm('Delete this coupon?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
