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
document.addEventListener('DOMContentLoaded', function() { renderCart(); });
</script>

<?php get_footer(); ?>
