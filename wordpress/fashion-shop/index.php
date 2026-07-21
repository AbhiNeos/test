<?php
/**
 * Main template file (fallback)
 *
 * @package FashionShop
 */

get_header();
?>

<section class="section page-header">
    <div class="container">
        <h1><?php the_title(); ?></h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article>
                    <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                    <?php the_excerpt(); ?>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <p><?php esc_html_e('No content found.', 'fashion-shop'); ?></p>
        <?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
