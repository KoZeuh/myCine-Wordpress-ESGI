<?php get_header(); ?>

<main class="container mx-auto p-4">
    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
        <article class="bg-white p-6 rounded shadow-lg">
            <div class="px-6 py-4">
                <h1 class="text-3xl font-bold mb-4 text-center"><?php the_title(); ?> - <?php echo get_film_star_rating(get_post_meta(get_the_ID(), 'film_rating', true)); ?></h1>
                <p class="text-gray-600 text-center"><strong>Date de sortie :</strong> <?php echo get_post_meta(get_the_ID(), 'film_year_of_release', true); ?></p>
                <?php if ($trailer_url = get_post_meta(get_the_ID(), 'film_trailer_video', true)) : ?>
                    <div class="mt-4 mb-5">
                        <iframe width="100%" height="315" src="<?php echo esc_url($trailer_url); ?>" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen class="w-full rounded shadow-lg"></iframe>
                    </div>
                <?php endif; ?>

                <div class="text-gray-700 mb-4"><?php the_content(); ?></div>

                <?php
                $showtimes = get_post_meta(get_the_ID(), '_film_showtimes', true);
                if (!empty($showtimes)) :
                    usort($showtimes, function ($a, $b) {
                        $dayComparison = strcmp($a['day'], $b['day']);
                        if ($dayComparison !== 0) {
                            return $dayComparison;
                        }
                        return strcmp($a['time'], $b['time']);
                    });

                    $grouped_showtimes = [];
                    foreach ($showtimes as $showtime) {
                        $grouped_showtimes[$showtime['day']][] = $showtime['time'];
                    }
                ?>
                    <div class="mt-6">
                        <h3 class="text-xl font-bold mb-2">Horaires de diffusion</h3>
                        <table class="w-full border-collapse border border-gray-200">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-200 px-4 py-2">Jour</th>
                                    <th class="border border-gray-200 px-4 py-2">Horaires</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grouped_showtimes as $day => $times) : ?>
                                    <tr>
                                        <td class="border border-gray-200 px-4 py-2"><?php echo esc_html($day); ?></td>
                                        <td class="border border-gray-200 px-4 py-2"><?php echo esc_html(implode(' - ', $times)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p>Aucun horaire de diffusion disponible pour ce film.</p>
                <?php endif; ?>
            </div>
        </article>
    <?php endwhile; endif; ?>
</main>

<?php get_footer(); ?>
