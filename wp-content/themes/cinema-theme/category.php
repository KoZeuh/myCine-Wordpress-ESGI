<?php 
    get_header(); 
    
    $category = get_queried_object();
    $background_image = get_term_meta($category->term_id, 'category_header_image', true);
    $header_name = get_term_meta($category->term_id, 'category_header_name', true) ?: single_cat_title('', false);
?>

<main class="container mx-auto p-4">
    <?php if ($background_image) : ?>
        <header class="bg-cover bg-center h-64 flex items-center justify-center" style="background-image: url('<?php echo esc_url($background_image); ?>');">
            <h1 class="text-4xl font-bold text-white"><?php echo esc_html($header_name); ?></h1>
        </header>
    <?php else : ?>
        <header class="h-64 flex items-center justify-center bg-gray-800">
            <h1 class="text-4xl font-bold text-white"><?php echo esc_html($header_name); ?></h1>
        </header>
    <?php endif; ?>

    <div class="mt-8">
        <?php if (have_posts()) : ?>
            <header class="mb-4">
                <?php the_archive_description('<div class="text-gray-600 mb-4">', '</div>'); ?>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('template-parts/card', 'film'); ?>
                <?php endwhile; ?>
            </div>

            <?php
                echo '<div class="pagination flex justify-center">';
                    echo paginate_links();
                echo '</div>';
            ?>
        <?php else : ?>
            <p><?php _e('Cette catégorie ne contient aucun contenu !'); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
