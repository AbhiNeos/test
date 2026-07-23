<?php
/**
 * Template Name: About Page
 *
 * @package FashionShop
 */

get_header();
?>

<section class="section page-header">
    <div class="container">
        <h1>About Us</h1>
        <p>Know more about Fashion Shop</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="about-content">
            <div class="about-text">
                <h2>Welcome to <?php bloginfo('name'); ?></h2>
                <p>Fashion Shop is your premier destination for beautiful women's kurtis. We started with a simple mission: to make stylish, high-quality ethnic wear accessible to every woman.</p>
                <p>Our collection features a wide range of kurtis from everyday cotton pieces to exquisite party wear. Each piece is carefully curated to ensure quality craftsmanship and contemporary design.</p>
                <p>We believe that ethnic wear should be comfortable, affordable, and beautiful. That's why we work directly with talented artisans and manufacturers across India to bring you the best kurtis at competitive prices.</p>

                <h3>Why Choose Us?</h3>
                <ul class="about-list">
                    <li><i class="fas fa-check"></i> Handpicked collection of premium kurtis</li>
                    <li><i class="fas fa-check"></i> Sizes from XS to 3XL for all body types</li>
                    <li><i class="fas fa-check"></i> Quality fabrics - Cotton, Silk, Georgette, Rayon</li>
                    <li><i class="fas fa-check"></i> Free delivery on orders above &#8377;<?php echo esc_html(get_option('fashion_shop_free_delivery_amount', 999)); ?></li>
                    <li><i class="fas fa-check"></i> Easy 7-day return policy</li>
                    <li><i class="fas fa-check"></i> Secure payment options</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
