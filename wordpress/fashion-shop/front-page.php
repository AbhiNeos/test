<?php
/**
 * Home Page Template
 *
 * @package FashionShop
 */

get_header();
?>

<!-- Hero Section -->
<section class="hero" style="background-image: url('<?php echo esc_url(FASHION_SHOP_URI . '/assets/images/hero-bg.svg'); ?>'); background-size: cover; background-position: center;">
    <div class="hero-content">
        <span class="hero-badge">New Collection 2024</span>
        <h1>Discover Beautiful Kurtis</h1>
        <p>Handcrafted ethnic wear for the modern woman — from everyday elegance to festive glamour</p>
        <div class="hero-buttons">
            <a href="<?php echo esc_url(get_post_type_archive_link('fs_product')); ?>" class="btn btn-primary btn-lg">Shop Now</a>
            <a href="https://wa.me/<?php echo esc_attr(get_option('fashion_shop_whatsapp', '919876543210')); ?>?text=Hi%2C%20I%27m%20interested%20in%20your%20kurtis%20collection" target="_blank" class="btn btn-whatsapp-hero btn-lg"><i class="fab fa-whatsapp"></i> Connect on WhatsApp</a>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="section categories-section">
    <div class="container">
        <h2 class="section-title">Shop by Category</h2>
        <div class="categories-grid">
            <?php
            $icons = array(
                'Anarkali' => 'fa-star', 'Straight' => 'fa-ruler-vertical',
                'A-Line' => 'fa-chevron-down', 'Party Wear' => 'fa-glass-cheers',
                'Cotton' => 'fa-leaf', 'Silk' => 'fa-crown',
            );
            $home_cats = get_terms(array(
                'taxonomy' => 'product_category',
                'slug' => array('anarkali','straight','a-line','party-wear','cotton','silk'),
                'hide_empty' => false,
            ));
            if (!is_wp_error($home_cats)) :
                foreach ($home_cats as $cat) :
                    $icon = isset($icons[$cat->name]) ? $icons[$cat->name] : 'fa-tag';
            ?>
                <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="category-card">
                    <div class="category-icon"><i class="fas <?php echo esc_attr($icon); ?>"></i></div>
                    <h3><?php echo esc_html($cat->name); ?></h3>
                </a>
            <?php
                endforeach;
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="section">
    <div class="container">
        <h2 class="section-title">Featured Collection</h2>
        <div class="products-grid">
            <?php
            $featured = new WP_Query(array(
                'post_type' => 'fs_product',
                'posts_per_page' => 8,
                'meta_key' => '_fs_featured',
                'meta_value' => '1',
            ));
            if ($featured->have_posts()) :
                while ($featured->have_posts()) : $featured->the_post();
                    get_template_part('template-parts/product-card');
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <p class="text-center">No featured products yet. Check back soon!</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- New Arrivals -->
<section class="section section-gray">
    <div class="container">
        <h2 class="section-title">New Arrivals</h2>
        <div class="products-grid">
            <?php
            $new_arrivals = new WP_Query(array(
                'post_type' => 'fs_product',
                'posts_per_page' => 8,
                'orderby' => 'date',
                'order' => 'DESC',
            ));
            if ($new_arrivals->have_posts()) :
                while ($new_arrivals->have_posts()) : $new_arrivals->the_post();
                    get_template_part('template-parts/product-card');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section features-section">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-truck"></i>
                <h4>Free Delivery</h4>
                <p>On orders above &#8377;<?php echo esc_html(get_option('fashion_shop_free_delivery_amount', 999)); ?></p>
            </div>
            <div class="feature-card">
                <i class="fas fa-undo"></i>
                <h4>Easy Returns</h4>
                <p>7-day return policy</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h4>Secure Payment</h4>
                <p>100% secure checkout</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-headset"></i>
                <h4>24/7 Support</h4>
                <p>Dedicated customer support</p>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
