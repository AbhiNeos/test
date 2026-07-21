<?php
/**
 * Template Name: Checkout Page
 *
 * @package FashionShop
 */

get_header();
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
                <form id="checkoutForm" onsubmit="placeOrder(event)">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="customerName">Full Name *</label>
                            <input type="text" id="customerName" name="customerName" required>
                        </div>
                    </div>
                    <div class="form-row two-cols">
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" required>
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

                    <h3>Payment Method</h3>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="paymentMethod" value="COD" checked>
                            <span><i class="fas fa-money-bill"></i> Cash on Delivery</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="paymentMethod" value="Online">
                            <span><i class="fas fa-credit-card"></i> Online Payment (Coming Soon)</span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-block">Place Order</button>
                </form>
            </div>

            <div class="checkout-summary">
                <h3>Order Summary</h3>
                <div id="checkoutItems"></div>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="checkoutSubtotal">&#8377;0</span>
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
</script>

<?php get_footer(); ?>
