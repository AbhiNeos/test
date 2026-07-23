<?php
/**
 * Orders Page
 * @package FashionShop
 */

$orders = get_posts(array('post_type' => 'fs_order', 'posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC'));
$status_colors = array('Pending'=>'#f39c12','Confirmed'=>'#3498db','Shipped'=>'#9b59b6','Delivered'=>'#27ae60','Cancelled'=>'#e74c3c');
?>

<div class="card">
    <div class="card-header"><h2>All Orders (<?php echo count($orders); ?>)</h2></div>

    <?php if (empty($orders)) : ?>
        <div class="empty-state">
            <i class="fas fa-clipboard-list"></i>
            <h3>No orders yet</h3>
            <p>Orders will appear here when customers place them</p>
        </div>
    <?php else : ?>
        <table class="data-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Phone</th><th>Amount</th><th>Payment</th><th>Status</th><th>Date</th><th>Update</th></tr></thead>
            <tbody>
                <?php foreach ($orders as $o) :
                    $status = get_post_meta($o->ID, '_fs_order_status', true);
                    $clr = isset($status_colors[$status]) ? $status_colors[$status] : '#999';
                ?>
                    <tr>
                        <td><strong>#<?php echo esc_html($o->ID); ?></strong></td>
                        <td><?php echo esc_html(get_post_meta($o->ID, '_fs_customer_name', true)); ?><br><small style="color:#999;"><?php echo esc_html(get_post_meta($o->ID, '_fs_email', true)); ?></small></td>
                        <td><?php echo esc_html(get_post_meta($o->ID, '_fs_phone', true)); ?></td>
                        <td><strong>&#8377;<?php echo esc_html(get_post_meta($o->ID, '_fs_total', true)); ?></strong></td>
                        <td><?php echo esc_html(get_post_meta($o->ID, '_fs_payment_method', true)); ?></td>
                        <td><span class="status-pill" style="background:<?php echo $clr; ?>20; color:<?php echo $clr; ?>;"><?php echo esc_html($status); ?></span></td>
                        <td><?php echo get_the_date('d M Y', $o->ID); ?></td>
                        <td>
                            <form method="post" style="display:inline;">
                                <?php wp_nonce_field('fs_update_order', '_fs_nonce'); ?>
                                <input type="hidden" name="order_id" value="<?php echo esc_attr($o->ID); ?>">
                                <select name="new_status" class="status-select" onchange="this.form.submit()">
                                    <?php foreach (array('Pending','Confirmed','Shipped','Delivered','Cancelled') as $s) : ?>
                                        <option value="<?php echo esc_attr($s); ?>" <?php selected($status, $s); ?>><?php echo esc_html($s); ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
