<?php
/**
 * Theme Footer
 *
 * @package FashionShop
 */

$phone = get_option('fashion_shop_phone', '+91 98765 43210');
$email = get_option('fashion_shop_email', 'info@fashionshop.com');
$address = get_option('fashion_shop_address', '123 Fashion Street, New Delhi');
$facebook = get_option('fashion_shop_facebook', '#');
$instagram = get_option('fashion_shop_instagram', '#');
?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <h3><?php bloginfo('name'); ?></h3>
                <p>Your one-stop destination for beautiful women's kurtis. We bring you the latest designs in ethnic wear at affordable prices.</p>
                <div class="social-links">
                    <a href="<?php echo esc_url($facebook); ?>"><i class="fab fa-facebook"></i></a>
                    <a href="<?php echo esc_url($instagram); ?>"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-pinterest"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url(get_post_type_archive_link('fs_product')); ?>">Shop All</a></li>
                    <li><a href="<?php echo esc_url(home_url('/about/')); ?>">About Us</a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Categories</h4>
                <ul>
                    <?php
                    $cats = get_terms(array('taxonomy' => 'product_category', 'number' => 6, 'hide_empty' => false));
                    if (!is_wp_error($cats)) :
                        foreach ($cats as $cat) :
                    ?>
                        <li><a href="<?php echo esc_url(get_term_link($cat)); ?>"><?php echo esc_html($cat->name); ?></a></li>
                    <?php
                        endforeach;
                    endif;
                    ?>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact Info</h4>
                <ul class="contact-info">
                    <li><i class="fas fa-map-marker-alt"></i> <?php echo esc_html($address); ?></li>
                    <li><i class="fas fa-phone"></i> <?php echo esc_html($phone); ?></li>
                    <li><i class="fas fa-envelope"></i> <?php echo esc_html($email); ?></li>
                    <li><i class="fas fa-clock"></i> Mon-Sat: 10AM - 8PM</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
