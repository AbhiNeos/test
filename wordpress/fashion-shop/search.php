<?php
/**
 * Search Results Template
 *
 * @package FashionShop
 */

get_header();
?>

<section class="section page-header">
    <div class="container">
        <h1>Search Results</h1>
        <p>Results for: "<?php echo esc_html(get_search_query()); ?>"</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (have_posts()) : ?>
            <div class="products-grid">
                <?php while (have_posts()) : the_post();
                    if (get_post_type() === 'fs_product') :
                        get_template_part('template-parts/product-card');
                    endif;
                endwhile; ?>
            </div>
        <?php else : ?>
            <div class="no-products">
                <i class="fas fa-search"></i>
                <h3>No products found</h3>
                <p>Try different search terms</p>
                <a href="<?php echo esc_url(get_post_type_archive_link('fs_product')); ?>" class="btn btn-primary">View All Products</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
