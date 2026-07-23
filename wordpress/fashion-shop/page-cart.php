<?php
/**
 * Template Name: Cart Page
 *
 * @package FashionShop
 */

get_header();
?>

<section class="section page-header">
    <div class="container">
        <h1>Shopping Cart</h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="cart-container" id="cartContainer">
            <div class="cart-empty" id="cartEmpty">
                <i class="fas fa-shopping-cart"></i>
                <h3>Your cart is empty</h3>
                <p>Looks like you haven't added anything yet</p>
                <a href="<?php echo esc_url(get_post_type_archive_link('fs_product')); ?>" class="btn btn-primary">Continue Shopping</a>
            </div>

            <div class="cart-content" id="cartContent" style="display:none;">
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="cartItems"></tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span id="subtotal">&#8377;0</span>
                    </div>

                    <!-- Coupon Code -->
                    <div class="coupon-section">
                        <div class="coupon-input-row" id="couponInputRow">
                            <input type="text" id="couponCode" placeholder="Enter coupon code" style="text-transform:uppercase;">
                            <button type="button" onclick="applyCoupon()">Apply</button>
                        </div>
                        <div id="couponApplied" style="display:none;"></div>
                        <div id="couponError" class="coupon-error" style="display:none;"></div>
                    </div>

                    <div class="summary-row discount" id="discountRow" style="display:none;">
                        <span>Discount:</span>
                        <span id="discountAmount">-&#8377;0</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span id="shipping">&#8377;0</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span id="total">&#8377;0</span>
                    </div>
                    <a href="<?php echo esc_url(home_url('/checkout/')); ?>" class="btn btn-primary btn-block">Proceed to Checkout</a>
                    <a href="<?php echo esc_url(get_post_type_archive_link('fs_product')); ?>" class="btn btn-outline btn-block">Continue Shopping</a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() { renderCart(); updateCartTotals(); });

var appliedCoupon = JSON.parse(localStorage.getItem('fs_coupon') || 'null');

function applyCoupon() {
    var code = document.getElementById('couponCode').value.trim().toUpperCase();
    if (!code) { showCouponError('Please enter a coupon code'); return; }

    var formData = new FormData();
    formData.append('action', 'fs_validate_coupon');
    formData.append('coupon_code', code);

    fetch(fashionShop.ajaxurl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                var cart = getCart();
                var subtotal = cart.reduce(function(s, i) { return s + (i.price * i.quantity); }, 0);
                if (data.data.min_amount > 0 && subtotal < data.data.min_amount) {
                    showCouponError('Minimum order of \u20B9' + data.data.min_amount + ' required for this coupon');
                    return;
                }
                appliedCoupon = { code: data.data.code, discount: data.data.discount };
                localStorage.setItem('fs_coupon', JSON.stringify(appliedCoupon));
                showCouponApplied();
                // Immediately update totals with discount
                updateCartTotals();
            } else {
                showCouponError(data.data.message);
            }
        })
        .catch(function() { showCouponError('Something went wrong'); });
}

function removeCoupon() {
    appliedCoupon = null;
    localStorage.removeItem('fs_coupon');
    document.getElementById('couponApplied').style.display = 'none';
    document.getElementById('couponInputRow').style.display = 'flex';
    document.getElementById('discountRow').style.display = 'none';
    document.getElementById('couponCode').value = '';
    updateCartTotals();
}

function showCouponApplied() {
    document.getElementById('couponInputRow').style.display = 'none';
    document.getElementById('couponError').style.display = 'none';
    document.getElementById('couponApplied').style.display = 'flex';
    document.getElementById('couponApplied').innerHTML = '<span><i class="fas fa-check-circle"></i> ' + appliedCoupon.code + ' (' + appliedCoupon.discount + '% off)</span><button onclick="removeCoupon()" title="Remove">&times;</button>';
    document.getElementById('couponApplied').className = 'coupon-applied';
}

function showCouponError(msg) {
    var el = document.getElementById('couponError');
    el.textContent = msg;
    el.style.display = 'block';
    setTimeout(function() { el.style.display = 'none'; }, 3000);
}

// Show coupon if already applied
if (appliedCoupon) { showCouponApplied(); }

function updateCartTotals() {
    var cart = JSON.parse(localStorage.getItem('fashionShopCart')) || [];
    if (cart.length === 0) return;

    var subtotal = 0;
    for (var i = 0; i < cart.length; i++) {
        subtotal += cart[i].price * cart[i].quantity;
    }

    var coupon = JSON.parse(localStorage.getItem('fs_coupon') || 'null');
    var discountAmt = 0;
    var discountRow = document.getElementById('discountRow');

    if (coupon && coupon.discount > 0) {
        discountAmt = Math.round(subtotal * coupon.discount / 100);
        document.getElementById('discountAmount').textContent = '-\u20B9' + discountAmt;
        if (discountRow) discountRow.style.display = 'flex';
    } else {
        if (discountRow) discountRow.style.display = 'none';
    }

    var shipping = subtotal >= 999 ? 0 : 99;
    var total = subtotal - discountAmt + shipping;

    document.getElementById('subtotal').textContent = '\u20B9' + subtotal;
    document.getElementById('shipping').textContent = shipping === 0 ? 'Free' : '\u20B9' + shipping;
    document.getElementById('total').textContent = '\u20B9' + total;
}
</script>

<?php get_footer(); ?>
