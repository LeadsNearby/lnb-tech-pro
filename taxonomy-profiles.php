<?php
/**
 *
 * The Tech Profiles Page
 *
 */

get_header();
$options = get_option('lnb-tech-pro-options', true);
$sprite = isset($options['sprite']) ? $options['sprite'] : false;
$raw_categories = get_categories(['taxonomy' => 'profiles_category']);
$categories = array();
foreach ($raw_categories as $raw_category) {
    $categories[$raw_category->slug] = $raw_category->name;
}
?>
<div class="tech-profile content-area tech-profiles-category profile-category <?php echo $sprite ? 'tech-profiles-sprites' : ''; ?>">
<?php
if (!empty($categories)) {
    foreach ($categories as $term_slug => $term_name) {
        $query = new WP_Query([
            'post_type' => 'profiles',
            'posts_per_page' => -1,
            'order' => 'ASC',
            'tax_query' => array(
                array(
                    'taxonomy' => 'profiles_category',
                    'field' => 'slug',
                    'terms' => $term_slug,
                ),
            ),
        ]
        );
        if ($query->have_posts()):
            echo "<h3 class='tech-profile-category-title' style='grid-column: 1 / -1'>{$term_name}</h3>";
            while ($query->have_posts()): $query->the_post();
                require 'lib/templates/profile-card.php';
            endwhile;
        endif;
        wp_reset_postdata();
    }
    $uncategorized_query = new WP_Query([
        'post_type' => 'profiles',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'tax_query' => array(
            array(
                'taxonomy' => 'profiles_category',
                'field' => 'slug',
                'terms' => array_keys($categories),
                'operator' => 'NOT EXISTS',
            ),
        ),
    ]);
    if ($uncategorized_query->have_posts()):
        echo "<h3 class='tech-profile-category-title' style='grid-column: 1 / -1'>Uncategorized</h3>";
        while ($uncategorized_query->have_posts()): $uncategorized_query->the_post();
            require 'lib/templates/profile-card.php';
        endwhile;
    endif;
    wp_reset_postdata();
} else {
    $query = new WP_Query(['post_type' => 'profiles', 'posts_per_page' => -1, 'order' => 'ASC']);
    if ($query->have_posts()): while ($query->have_posts()): $query->the_post();
            require 'lib/templates/profile-card.php';
        endwhile;
    endif;
    wp_reset_postdata();
}

?>
</div><!-- #tech-profiles -->
<?php get_footer();?>