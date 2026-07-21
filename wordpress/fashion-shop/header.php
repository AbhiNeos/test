<?php
/**
 * Theme Header
 *
 * @package FashionShop
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Top Bar -->
<div class="top-bar">
    <div class="container">
        <span><i class="fas fa-phone"></i> <?php echo esc_html(get_option('fashion_shop_phone', '+91 98765 43210')); ?></span>
        <span><i class="fas fa-envelope"></i> <?php echo esc_html(get_option('fashion_shop_email', 'info@fashionshop.com')); ?></span>
        <span><i class="fas fa-truck"></i> Free Delivery on orders above &#8377;<?php echo esc_html(get_option('fashion_shop_free_delivery_amount', 999)); ?></span>
    </div>
</div>

<!-- Navigation -->
<nav class="navbar">
    <div class="container nav-container">
        <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
            <span class="logo-text"><?php bloginfo('name'); ?></span>
            <small>Women's Kurtis</small>
        </a>

        <div class="nav-search">
            <form action="<?php echo esc_url(home_url('/')); ?>" method="GET">
                <input type="text" name="s" placeholder="Search for kurtis..." value="<?php echo esc_attr(get_search_query()); ?>">
                <input type="hidden" name="post_type" value="fs_product">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>

        <div class="nav-links">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="nav-link">Home</a>
            <a href="<?php echo esc_url(get_post_type_archive_link('fs_product')); ?>" class="nav-link">Shop</a>
            <a href="<?php echo esc_url(home_url('/about/')); ?>" class="nav-link">About</a>
            <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="nav-link">Contact</a>
            <a href="<?php echo esc_url(home_url('/cart/')); ?>" class="nav-link cart-link">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count" id="cartCount">0</span>
            </a>
        </div>

        <button class="mobile-toggle" id="mobileToggle">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
        <a href="<?php echo esc_url(get_post_type_archive_link('fs_product')); ?>">Shop</a>
        <a href="<?php echo esc_url(home_url('/about/')); ?>">About</a>
        <a href="<?php echo esc_url(home_url('/contact/')); ?>">Contact</a>
        <a href="<?php echo esc_url(home_url('/cart/')); ?>"><i class="fas fa-shopping-cart"></i> Cart (<span class="mobile-cart-count">0</span>)</a>
    </div>
</nav>
