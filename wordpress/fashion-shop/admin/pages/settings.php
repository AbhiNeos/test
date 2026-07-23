<?php
/**
 * Settings Page
 * @package FashionShop
 */

$whatsapp = get_option('fashion_shop_whatsapp', '919876543210');
$phone = get_option('fashion_shop_phone', '+91 98765 43210');
$email = get_option('fashion_shop_email', 'info@fashionshop.com');
$free_delivery = get_option('fashion_shop_free_delivery_amount', 999);
?>

<div class="card">
    <div class="card-header">
        <h2><i class="fab fa-whatsapp" style="color:#25d366;"></i> WhatsApp & Store Settings</h2>
    </div>
    <form method="post" class="product-form">
        <?php wp_nonce_field('fs_save_settings', '_fs_nonce'); ?>

        <div class="form-row">
            <div class="form-group">
                <label><i class="fab fa-whatsapp" style="color:#25d366;"></i> WhatsApp Number *</label>
                <input type="text" name="whatsapp_number" value="<?php echo esc_attr($whatsapp); ?>" required
                    placeholder="919876543210">
                <p style="font-size:12px; color:#999; margin-top:6px;">
                    Enter country code + number without + or spaces (e.g., 919876543210 for India).
                    All orders from the checkout page will be sent to this number.
                </p>
            </div>
        </div>

        <div class="form-row two-col">
            <div class="form-group">
                <label>Store Phone (displayed on site)</label>
                <input type="text" name="store_phone" value="<?php echo esc_attr($phone); ?>"
                    placeholder="+91 98765 43210">
            </div>
            <div class="form-group">
                <label>Store Email</label>
                <input type="email" name="store_email" value="<?php echo esc_attr($email); ?>"
                    placeholder="info@fashionshop.com">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Free Delivery Above (&#8377;)</label>
                <input type="number" name="free_delivery" value="<?php echo esc_attr($free_delivery); ?>"
                    min="0" placeholder="999">
                <p style="font-size:12px; color:#999; margin-top:6px;">
                    Orders above this amount get free delivery. Set to 0 for always free.
                </p>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="fs_save_settings" class="btn-primary btn-lg">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>

<div class="card" style="margin-top:24px;">
    <div class="card-header"><h2>How It Works</h2></div>
    <div style="padding:24px; color:#666; line-height:1.8;">
        <p><strong>1.</strong> Customer adds products to cart and goes to checkout</p>
        <p><strong>2.</strong> They fill in their name, phone, and address</p>
        <p><strong>3.</strong> Click "Order via WhatsApp" button</p>
        <p><strong>4.</strong> A pre-formatted message with all order details opens in WhatsApp</p>
        <p><strong>5.</strong> The message is sent to your configured WhatsApp number</p>
        <p><strong>6.</strong> You confirm the order directly on WhatsApp</p>
        <br>
        <p style="background:#d4edda; padding:12px 16px; border-radius:8px; color:#155724;">
            <i class="fas fa-info-circle"></i> <strong>Tip:</strong> Use WhatsApp Business for better order management with labels and quick replies.
        </p>
    </div>
</div>
