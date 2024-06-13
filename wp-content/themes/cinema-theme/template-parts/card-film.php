<div class="max-w-sm rounded overflow-hidden shadow-lg bg-white flex flex-col h-full bg-white dark:bg-gray-800 text-white">
    <div class="px-3 py-4 flex-1">
        <div class="font-bold text-xl mb-2 flex items-center">
            <a href="<?php the_permalink(); ?>" class="text-blue-600 hover:underline"><?php the_title(); ?></a>
        </div>
        <div class="flex items-center mb-3">
            <?php echo get_film_star_rating(get_post_meta(get_the_ID(), 'film_rating', true)); ?>
        </div>
        <p class="mb-4 flex-1"><?php echo wp_trim_words(get_post_meta(get_the_ID(), 'film_description', true), 20); ?></p>
        <p><strong>Date de sortie :</strong> <?php echo get_post_meta(get_the_ID(), 'film_year_of_release', true); ?></p>
    </div>
    <?php if ($trailer_url = get_post_meta(get_the_ID(), 'film_trailer_video', true)) : ?>
        <div class="mt-4 px-3">
            <iframe width="100%" height="315" src="<?php echo esc_url($trailer_url); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full rounded shadow-lg mb-3"></iframe>
        </div>
    <?php endif; ?>
</div>
