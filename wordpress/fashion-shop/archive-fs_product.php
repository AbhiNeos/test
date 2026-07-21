<?php
/**
 * Products Archive / Shop Page
 *
 * @package FashionShop
 */

get_header();

// Get filter params
$current_category = get_query_var('product_category') ? get_query_var('product_category') : (isset($_GET['category']) ? sanitize_text_field($_GET['category']) : '');
$current_size = isset($_GET['size']) ? sanitize_text_field($_GET['size']) : '';
$current_sort = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'newest';
$current_min = isset($_GET['minPrice']) ? intval($_GET['minPrice']) : '';
$current_max = isset($_GET['maxPrice']) ? intval($_GET['maxPrice']) : '';
$current_search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';

// Build query
$args = array(
    'post_type'      => 'fs_product',
    'posts_per_page' => -1,
    'post_status'    => 'publish',
);

// Category filter
if ($current_category) {
    $args['tax_query'] = array(array(
        'taxonomy' => 'product_category',
        'field'    => 'slug',
        'terms'    => $current_category,
    ));
}

// Search
if ($current_search) {
    $args['s'] = $current_search;
}

// Price filter
$meta_query = array();
if ($current_min) {
    $meta_query[] = array('key' => '_fs_price', 'value' => $current_min, 'compare' => '>=', 'type' => 'NUMERIC');
}
if ($current_max) {
    $meta_query[] = array('key' => '_fs_price', 'value' => $current_max, 'compare' => '<=', 'type' => 'NUMERIC');
}
if (!empty($meta_query)) {
    $args['meta_query'] = $meta_query;
}

// Sort
switch ($current_sort) {
    case 'price_low':
        $args['meta_key'] = '_fs_price';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'ASC';
        break;
    case 'price_high':
        $args['meta_key'] = '_fs_price';
        $args['orderby'] = 'meta_value_num';
        $args['order'] = 'DESC';
        break;
    case 'name':
        $args['orderby'] = 'title';
        $args['order'] = 'ASC';
        break;
    default:
        $args['orderby'] = 'date';
        $args['order'] = 'DESC';
}

$products = new WP_Query($args);
$categories = get_terms(array('taxonomy' => 'product_category', 'hide_empty' => false));
$base_url = get_post_type_archive_link('fs_product');
?>

<section class="section page-header">
    <div class="container">
        <h1>Our Collection</h1>
        <p>Browse our beautiful range of women's kurtis</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="shop-layout">
            <!-- Sidebar Filters -->
            <aside class="shop-sidebar">
                <div class="filter-group">
                    <h4>Categories</h4>
                    <ul class="filter-list">
                        <li><a href="<?php echo esc_url($base_url); ?>" class="<?php echo !$current_category ? 'active' : ''; ?>">All Kurtis</a></li>
                        <?php if (!is_wp_error($categories)) : foreach ($categories as $cat) : ?>
                            <li><a href="<?php echo esc_url(add_query_arg('category', $cat->slug, $base_url)); ?>" class="<?php echo $current_category === $cat->slug ? 'active' : ''; ?>"><?php echo esc_html($cat->name); ?></a></li>
                        <?php endforeach; endif; ?>
                    </ul>
                </div>

                <div class="filter-group">
                    <h4>Size</h4>
                    <div class="size-filters">
                        <?php foreach (array('XS','S','M','L','XL','XXL','3XL') as $size) :
                            $size_url = add_query_arg('size', $size, $base_url);
                            if ($current_category) $size_url = add_query_arg('category', $current_category, $size_url);
                        ?>
                            <a href="<?php echo esc_url($size_url); ?>" class="size-filter-btn <?php echo $current_size === $size ? 'active' : ''; ?>"><?php echo esc_html($size); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-group">
                    <h4>Price Range</h4>
                    <div class="price-filters">
                        <?php
                        $price_ranges = array(
                            array('max' => 999, 'label' => 'Under &#8377;999'),
                            array('min' => 999, 'max' => 1999, 'label' => '&#8377;999 - &#8377;1999'),
                            array('min' => 1999, 'max' => 2999, 'label' => '&#8377;1999 - &#8377;2999'),
                            array('min' => 2999, 'label' => 'Above &#8377;2999'),
                        );
                        foreach ($price_ranges as $range) :
                            $purl = $base_url;
                            if (isset($range['min'])) $purl = add_query_arg('minPrice', $range['min'], $purl);
                            if (isset($range['max'])) $purl = add_query_arg('maxPrice', $range['max'], $purl);
                            if ($current_category) $purl = add_query_arg('category', $current_category, $purl);
                        ?>
                            <a href="<?php echo esc_url($purl); ?>" class="price-filter-link"><?php echo $range['label']; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="filter-group">
                    <a href="<?php echo esc_url($base_url); ?>" class="btn btn-outline btn-block">Clear All Filters</a>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="shop-content">
                <div class="shop-toolbar">
                    <p class="results-count"><?php echo esc_html($products->found_posts); ?> products found</p>
                    <div class="sort-options">
                        <label>Sort by:</label>
                        <select onchange="window.location.href=this.value">
                            <?php
                            $sort_options = array('newest' => 'Newest', 'price_low' => 'Price: Low to High', 'price_high' => 'Price: High to Low', 'name' => 'Name: A-Z');
                            foreach ($sort_options as $val => $label) :
                                $sort_url = add_query_arg('sort', $val, $base_url);
                                if ($current_category) $sort_url = add_query_arg('category', $current_category, $sort_url);
                            ?>
                                <option value="<?php echo esc_url($sort_url); ?>" <?php selected($current_sort, $val); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="products-grid">
                    <?php if ($products->have_posts()) :
                        while ($products->have_posts()) : $products->the_post();
                            get_template_part('template-parts/product-card');
                        endwhile;
                        wp_reset_postdata();
                    endif; ?>
                </div>

                <?php if (!$products->have_posts()) : ?>
                    <div class="no-products">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your filters or search terms</p>
                        <a href="<?php echo esc_url($base_url); ?>" class="btn btn-primary">View All Products</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
