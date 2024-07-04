<?php
/**
 * Plugin Name: Cinema Schedule Manager
 * Description: Plugin pour gérer les horaires de diffusion de films avec une page d'administration.
 * Version: 1.0
 * Author: DE PRIESTER Maxime
 * Text Domain: cinema-schedule
*/


function enqueue_cinema_schedule_styles() {
    wp_enqueue_style('cinema-schedule-css', plugins_url('css/style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'enqueue_cinema_schedule_styles');

function enqueue_cinema_schedule_scripts() {
    wp_enqueue_script('cinema-schedule-js', plugins_url('js/script.js', __FILE__), array('jquery'), null, true);
}
add_action('admin_enqueue_scripts', 'enqueue_cinema_schedule_scripts');


function change_post_labels_to_films() {
    global $wp_post_types;
    if (isset($wp_post_types['post'])) {
        $labels = &$wp_post_types['post']->labels;
        $labels->name = 'Films';
        $labels->singular_name = 'Film';
        $labels->add_new = 'Ajouter un film';
        $labels->add_new_item = 'Ajouter un nouveau film';
        $labels->edit_item = 'Modifier le film';
        $labels->new_item = 'Nouveau film';
        $labels->view_item = 'Voir le film';
        $labels->search_items = 'Rechercher des films';
        $labels->not_found = 'Aucun film trouvé';
        $labels->not_found_in_trash = 'Aucun film trouvé dans la corbeille';
        $labels->all_items = 'Tous les films';
        $labels->menu_name = 'Films';
        $labels->name_admin_bar = 'Film';
    }
}
add_action('init', 'change_post_labels_to_films');


function film_showtimes_meta_box() {
    add_meta_box(
        'film_showtimes',
        __('Horaires de Diffusion'),
        'display_film_showtimes_meta_box',
        'post',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'film_showtimes_meta_box');

function display_film_showtimes_meta_box($post) {
    $showtimes = get_post_meta($post->ID, '_film_showtimes', true);
    wp_nonce_field(basename(__FILE__), 'film_showtimes_nonce');
    ?>
        <div id="showtimes-container">
            <?php if (!empty($showtimes)) : ?>
                <?php foreach ($showtimes as $showtime) : ?>
                    <div class="showtime">
                        <input type="text" name="showtime_day[]" value="<?php echo esc_attr($showtime['day']); ?>" placeholder="Jour" />
                        <input type="time" name="showtime_time[]" value="<?php echo esc_attr($showtime['time']); ?>" placeholder="Heure" />
                        <button type="button" class="remove-showtime">Supprimer</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <button type="button" id="add-showtime">Ajouter horaires</button>
    <?php
}

function save_film_showtimes_meta($post_id) {
    if (!isset($_POST['film_showtimes_nonce']) || !wp_verify_nonce($_POST['film_showtimes_nonce'], basename(__FILE__))) {
        return $post_id;
    }

    $new_showtimes = array();
    if (isset($_POST['showtime_day']) && isset($_POST['showtime_time'])) {
        $days = $_POST['showtime_day'];
        $times = $_POST['showtime_time'];

        for ($i = 0; $i < count($days); $i++) {
            if (!empty($days[$i]) && !empty($times[$i])) {
                $new_showtimes[] = array(
                    'day' => sanitize_text_field($days[$i]),
                    'time' => sanitize_text_field($times[$i]),
                );
            }
        }
    }

    update_post_meta($post_id, '_film_showtimes', $new_showtimes);
}
add_action('save_post', 'save_film_showtimes_meta');


function display_film_showtimes($atts) {
    $atts = shortcode_atts(array(
        'id' => null,
    ), $atts, 'film_showtimes');

    if (!$atts['id']) {
        return '';
    }

    $post = get_post($atts['id']);
    if (!$post) {
        return '';
    }

    $showtimes = get_post_meta($post->ID, '_film_showtimes', true);
    if (empty($showtimes)) {
        return '<p>Aucun horaire de diffusion disponible pour ce film.</p>';
    }

    $output = '<h3>Horaires de diffusion pour ' . esc_html(get_the_title($post)) . '</h3>';
    $output .= '<ul>';
    foreach ($showtimes as $showtime) {
        $output .= '<li>' . esc_html($showtime['day']) . ' à ' . esc_html($showtime['time']) . '</li>';
    }
    $output .= '</ul>';

    return $output;
}
add_shortcode('film_showtimes', 'display_film_showtimes');


function cinema_schedule_menu() {
    add_menu_page(
        __('Gestion des horaires de films', 'cinema-schedule'),
        __('Horaires de Films', 'cinema-schedule'),
        'manage_options',
        'cinema-schedule',
        'cinema_schedule_page',
        'dashicons-video-alt2',
        6
    );
}
add_action('admin_menu', 'cinema_schedule_menu');

function cinema_schedule_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['submit'])) {
        $post_id = intval($_POST['film_id']);
        $showtimes = array();
        if (isset($_POST['showtime_day']) && isset($_POST['showtime_time'])) {
            $days = $_POST['showtime_day'];
            $times = $_POST['showtime_time'];

            for ($i = 0; $i < count($days); $i++) {
                if (!empty($days[$i]) && !empty($times[$i])) {
                    $showtimes[] = array(
                        'day' => sanitize_text_field($days[$i]),
                        'time' => sanitize_text_field($times[$i]),
                    );
                }
            }
        }

        update_post_meta($post_id, '_film_showtimes', $showtimes);
        echo '<div class="updated"><p>Les horaires ont été mis à jour.</p></div>';
    }

    if (isset($_POST['delete'])) {
        $post_id = intval($_POST['film_id']);
        delete_post_meta($post_id, '_film_showtimes');
        echo '<div class="updated"><p>Les horaires ont été supprimés.</p></div>';
    }

    $edit_film_id = null;
    $edit_showtimes = array();
    if (isset($_POST['edit'])) {
        $edit_film_id = intval($_POST['film_id']);
        $edit_showtimes = get_post_meta($edit_film_id, '_film_showtimes', true);
    }

    $films = get_posts(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
    ));

    $films_with_showtimes = array_filter($films, function($film) {
        return get_post_meta($film->ID, '_film_showtimes', true);
    });

    ?>
    <div class="wrap">
        <h1><?php _e('Gestion des horaires de films', 'cinema-schedule'); ?></h1>
        <form method="post">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Choisir un film', 'cinema-schedule'); ?></th>
                    <td>
                        <select name="film_id">
                            <?php foreach ($films as $film) : ?>
                                <option value="<?php echo esc_attr($film->ID); ?>" <?php selected($edit_film_id, $film->ID); ?>><?php echo esc_html($film->post_title); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Horaires de diffusion', 'cinema-schedule'); ?></th>
                    <td>
                        <div id="admin-showtimes-container">
                            <?php if ($edit_film_id && $edit_showtimes) : ?>
                                <?php foreach ($edit_showtimes as $showtime) : ?>
                                    <div class="showtime">
                                        <input type="text" name="showtime_day[]" value="<?php echo esc_attr($showtime['day']); ?>" placeholder="Jour" />
                                        <input type="time" name="showtime_time[]" value="<?php echo esc_attr($showtime['time']); ?>" placeholder="Heure" />
                                        <button type="button" class="remove-showtime">Supprimer</button>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="showtime">
                                    <input type="text" name="showtime_day[]" placeholder="Jour" />
                                    <input type="time" name="showtime_time[]" placeholder="Heure" />
                                    <button type="button" class="remove-showtime">Supprimer</button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <button type="button" id="admin-add-showtime">Ajouter horaires</button>
                    </td>
                </tr>
            </table>
            <input type="submit" name="submit" value="Enregistrer" class="button button-primary" />
        </form>

        <h2>Films avec des horaires définis</h2>
        <div class="film-schedule">
            <?php if (!empty($films_with_showtimes)) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>Film</th>
                            <th>Horaires</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($films_with_showtimes as $film) : 
                            $showtimes = get_post_meta($film->ID, '_film_showtimes', true); ?>
                            <tr>
                                <td><?php echo esc_html($film->post_title); ?></td>
                                <td>
                                    <?php foreach ($showtimes as $showtime) : ?>
                                        <?php echo esc_html($showtime['day'] . ' à ' . $showtime['time']); ?><br>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="film_id" value="<?php echo esc_attr($film->ID); ?>">
                                        <input type="submit" name="delete" value="Supprimer" class="button button-secondary" />
                                    </form>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="film_id" value="<?php echo esc_attr($film->ID); ?>">
                                        <input type="submit" name="edit" value="Editer" class="button button-secondary" />
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p>Aucun film avec des horaires définis.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}
