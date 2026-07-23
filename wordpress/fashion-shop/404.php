<?php
/**
 * 404 Page Template
 *
 * @package FashionShop
 */

get_header();
?>

<section class="section error-page">
    <div class="container text-center">
        <h1 class="error-code">404</h1>
        <h2>Page Not Found</h2>
        <p>The page you're looking for doesn't exist or has been moved.</p>
        <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Back to Home</a>
    </div>
</section>

<?php get_footer(); ?>
