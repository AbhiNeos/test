<?php
/**
 * Template Name: Checkout Page
 *
 * @package FashionShop
 */

get_header();
$whatsapp_number = get_option('fashion_shop_whatsapp', '919876543210');
?>

<section class="section page-header">
    <div class="container">
        <h1>Checkout</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="checkout-layout">
            <div class="checkout-form">
                <h3>Shipping Details</h3>
                <form id="checkoutForm" onsubmit="sendToWhatsApp(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="customerName">Full Name *</label>
                            <input type="text" id="customerName" name="customerName" required>
                        </div>
                    </div>
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email">
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone *</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="street">Street Address *</label>
                            <input type="text" id="street" name="street" required>
                        </div>
                    </div>
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label for="city">City *</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="state">State *</label>
                            <input type="text" id="state" name="state" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="pincode">Pincode *</label>
                            <input type="text" id="pincode" name="pincode" required>
                        </div>
                    </div>

                    <button type="submit" class="btn-whatsapp btn-block">
                        <i class="fab fa-whatsapp"></i> Order via WhatsApp
                    </button>
                    <p class="whatsapp-note">Your order details will be sent to us on WhatsApp for confirmation</p>
                </form>
            </div>

            <div class="checkout-summary">
                <h3>Order Summary</h3>
                <div id="checkoutItems"></div>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="checkoutSubtotal">&#8377;0</span>
                </div>
                <div class="summary-row discount" id="checkoutDiscountRow" style="display:none;">
                    <span id="checkoutDiscountLabel">Discount:</span>
                    <span id="checkoutDiscount">-&#8377;0</span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span id="checkoutShipping">Free</span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span id="checkoutTotal">&#8377;0</span>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() { renderCheckoutSummary(); });

function sendToWhatsApp(e) {
    e.preventDefault();
    var cart = JSON.parse(localStorage.getItem('fashionShopCart')) || [];
    if (cart.length === 0) { showToast('Cart is empty!', 'error'); return; }

    var form = e.target;
    var name = form.customerName.value;
    var phone = form.phone.value;
    var street = form.street.value;
    var city = form.city.value;
    var state = form.state.value;
    var pincode = form.pincode.value;

    var subtotal = cart.reduce(function(s, i) { return s + (i.price * i.quantity); }, 0);
    var shipping = subtotal >= 999 ? 0 : 99;
    var coupon = JSON.parse(localStorage.getItem('fs_coupon') || 'null');
    var discountAmt = 0;
    if (coupon && coupon.discount) {
        discountAmt = Math.round(subtotal * coupon.discount / 100);
    }
    var total = subtotal - discountAmt + shipping;

    // Build WhatsApp message
    var msg = '*New Order - Fashion Shop*\n\n';
    msg += '*Customer:* ' + name + '\n';
    msg += '*Phone:* ' + phone + '\n';
    msg += '*Address:* ' + street + ', ' + city + ', ' + state + ' - ' + pincode + '\n\n';
    msg += '*Order Items:*\n';
    cart.forEach(function(item, i) {
        msg += (i+1) + '. ' + item.name;
        if (item.size) msg += ' (Size: ' + item.size + ')';
        if (item.color) msg += ' [' + item.color + ']';
        msg += ' x' + item.quantity + ' = Rs.' + (item.price * item.quantity) + '\n';
    });
    msg += '\n*Subtotal:* Rs.' + subtotal;
    if (discountAmt > 0) {
        msg += '\n*Coupon:* ' + coupon.code + ' (-' + coupon.discount + '% = -Rs.' + discountAmt + ')';
    }
    msg += '\n*Shipping:* ' + (shipping === 0 ? 'Free' : 'Rs.' + shipping);
    msg += '\n*Total:* Rs.' + total;
    msg += '\n\n_Sent from Fashion Shop Website_';

    var whatsappNumber = '<?php echo esc_js($whatsapp_number); ?>';
    var url = 'https://wa.me/' + whatsappNumber + '?text=' + encodeURIComponent(msg);

    // Clear cart and coupon after sending
    localStorage.removeItem('fashionShopCart');
    localStorage.removeItem('fs_coupon');

    // Open WhatsApp
    window.open(url, '_blank');

    // Redirect home
    setTimeout(function() {
        window.location.href = '<?php echo esc_url(home_url('/')); ?>';
    }, 1000);
}
</script>

<?php get_footer(); ?>
