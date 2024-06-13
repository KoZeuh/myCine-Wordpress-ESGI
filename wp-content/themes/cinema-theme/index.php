<?php 
    get_header();
    
    $categories = get_categories();
    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
    $args = array(
        'post_type' => 'post',
        'posts_per_page' => 6,
        'paged' => $paged
    );

    $custom_query = new WP_Query($args);
?>


<main class="container mx-auto p-4 flex flex-col md:flex-row">
    <aside class="w-full md:w-1/4 md:mr-8 mb-8 md:mb-0">
        <div class="w-full max-w-md p-4 bg-white border border-gray-200 rounded-lg shadow sm:p-8 dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h5 class="text-xl font-bold leading-none text-gray-900 dark:text-white">Catégories</h5>
                <a href="#" class="text-sm font-medium text-blue-600 hover:underline dark:text-blue-500">Tout voir</a>
            </div>
            <div class="flow-root">
                <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-700">
                    <?php
                        foreach ($categories as $category) {
                            echo '<li class="py-3 sm:py-4 text-center"><div class="flex items-center"><div class="flex-1 min-w-0 ms-4"><a href="' . get_category_link($category->term_id) . '" class="text-sm font-medium text-gray-900 truncate dark:text-white">' . $category->name . '</a></div></div></li>';
                        }
                    ?>
                </ul>
            </div>
        </div>
    </aside>


    <div class="w-full md:w-3/4">
        <?php

        if ($custom_query->have_posts()) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while ($custom_query->have_posts()) : $custom_query->the_post(); ?>
                    <?php get_template_part('template-parts/card', 'film'); ?>
                <?php endwhile; ?>
            </div>

            <div class="mt-8 flex justify-center">
                <?php
                    echo '<div class="pagination flex justify-center">';
                        echo paginate_links();
                    echo '</div>';
                ?>
            </div>

            <?php wp_reset_postdata(); ?>

        <?php else : ?>
            <p><?php _e('Aucun contenu trouvé'); ?></p>
        <?php endif; ?>
    </div>
</main>

<?php get_footer(); ?>
