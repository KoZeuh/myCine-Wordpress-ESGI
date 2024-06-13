<?php
    function cinema_theme_setup() {
        add_theme_support('post-thumbnails');

        register_nav_menus(array(
            'main-menu' => __('Main Menu', 'cinema-theme'),
            'categories-menu' => __('Categories Menu', 'cinema-theme'),
        ));
    }
    
    add_action('after_setup_theme', 'cinema_theme_setup');

    function cinema_theme_scripts() {
        wp_enqueue_style('style', get_stylesheet_uri());
    }

    add_action('wp_enqueue_scripts', 'cinema_theme_scripts');

    function add_category_fields() {
        ?>
            <div class="form-field">
                <label for="category_header_image"><?php _e('Image de fond'); ?></label>
                <input type="text" name="category_header_image" id="category_header_image" value="" />
                <p class="description"><?php _e('URL de l\'image de fond pour cette catégorie.'); ?></p>
            </div>
        <?php
    }
    add_action('category_add_form_fields', 'add_category_fields', 10, 2);

    function edit_category_fields($term) {
        $header_image = get_term_meta($term->term_id, 'category_header_image', true);
        ?>
        <tr class="form-field">
            <th scope="row" valign="top"><label for="category_header_image"><?php _e('Image de fond'); ?></label></th>
            <td>
                <input type="text" name="category_header_image" id="category_header_image" value="<?php echo esc_attr($header_image) ? esc_attr($header_image) : ''; ?>" />
                <p class="description"><?php _e('URL de l\'image de fond pour cette catégorie.'); ?></p>
            </td>
        </tr>
        <?php
    }
    add_action('category_edit_form_fields', 'edit_category_fields', 10, 2);

    function save_category_fields($term_id) {
        if (isset($_POST['category_header_image'])) {
            update_term_meta($term_id, 'category_header_image', sanitize_text_field($_POST['category_header_image']));
        }
    }
    add_action('edited_category', 'save_category_fields', 10, 2);
    add_action('create_category', 'save_category_fields', 10, 2);


    function add_film_metabox() {
        add_meta_box(
            'film_metabox',
            __('Détails du film'),
            'film_metabox_callback', 
            'post',
            'normal',
            'high'
        );
    }
    add_action('add_meta_boxes', 'add_film_metabox');

    
    function film_metabox_callback($post) {
        wp_nonce_field('save_film_details', 'film_metabox_nonce');

        $year = get_post_meta($post->ID, 'film_year_of_release', true);
        $rating = get_post_meta($post->ID, 'film_rating', true);
        $trailer = get_post_meta($post->ID, 'film_trailer_video', true);
        $description = get_post_meta($post->ID, 'film_description', true);

        ?>
            <p>
                <label for="film_year_of_release"><?php _e('Année de sortie'); ?></label><br>
                <input type="text" id="film_year_of_release" name="film_year_of_release" value="<?php echo esc_attr($year); ?>" />
            </p>
            <p>
                <label for="film_rating"><?php _e('Note'); ?></label><br>
                <input type="number" id="film_rating" min="0" max="5" step="0.1" name="film_rating" value="<?php echo esc_attr($rating); ?>" />
            </p>
            <p>
                <label for="film_trailer_video"><?php _e('Trailer vidéo (URL)'); ?></label><br>
                <input type="text" id="film_trailer_video" name="film_trailer_video" value="<?php echo esc_url($trailer); ?>" />
            </p>
            <p>
                <label for="film_description"><?php _e('Description'); ?></label><br>
                <textarea id="film_description" name="film_description"><?php echo esc_textarea($description); ?></textarea>
            </p>
        <?php
    }

    function save_film_details($post_id) {
        if (!isset($_POST['film_metabox_nonce']) || !wp_verify_nonce($_POST['film_metabox_nonce'], 'save_film_details')) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (isset($_POST['film_year_of_release'])) {
            update_post_meta($post_id, 'film_year_of_release', sanitize_text_field($_POST['film_year_of_release']));
        }
        if (isset($_POST['film_rating'])) {
            update_post_meta($post_id, 'film_rating', sanitize_text_field($_POST['film_rating']));
        }
        if (isset($_POST['film_trailer_video'])) {
            update_post_meta($post_id, 'film_trailer_video', esc_url_raw($_POST['film_trailer_video']));
        }
        if (isset($_POST['film_description'])) {
            update_post_meta($post_id, 'film_description', sanitize_textarea_field($_POST['film_description']));
        }
    }
    add_action('save_post', 'save_film_details');

    function get_film_star_rating($rating) {
        $rating = floatval($rating);
        $output = '';
    
        for ($i = 1; $i <= floor($rating); $i++) {
            $output .= '<span class="text-yellow-500">&#9733;</span>';
        }
    
        if ($rating - floor($rating) >= 0.5) {
            $output .= '<span class="text-yellow-500">&#9733;</span>';
        }
    
        while (strlen($output) / 24 < 5) {
            $output .= '<span class="text-gray-300">&#9733;</span>';
        }
    
        return $output;
    }
    

?>

