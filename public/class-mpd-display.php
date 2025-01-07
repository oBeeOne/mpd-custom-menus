<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Display {
    public static function init() {
        add_filter('wp_nav_menu_args', [__CLASS__, 'replace_menu_logic']);
    }

    public static function replace_menu_logic($args) {
        if (is_admin()) {
            return $args; 
        }

        // Ex : on teste si on est sur une page
        if (is_page()) {
            global $post;
            $page_id = $post->ID;
            $author_id = $post->post_author;

            // Récupérer tous les mpd_menu
            $mpd_menus = get_posts([
                'post_type' => 'mpd_menu',
                'numberposts' => -1
            ]);

            foreach ($mpd_menus as $menu_post) {
                $pages = get_post_meta($menu_post->ID, '_mpd_menu_pages', true);
                $menu_user_id = get_post_meta($menu_post->ID, '_mpd_menu_user', true);

                $has_page_match   = (is_array($pages) && in_array($page_id, $pages));
                $has_author_match = ($menu_user_id && (int)$menu_user_id === (int)$author_id);

                if ($has_page_match || $has_author_match) {
                    // Trouver l'ID du menu WordPress à afficher pour ce CPT
                    // Option 1 : vous stockez l'ID du menu WP dans un meta, par ex. '_mpd_nav_menu_id'
                    $nav_menu_id = get_post_meta($menu_post->ID, '_mpd_nav_menu_id', true);

                    if ($nav_menu_id) {
                        // Remplacement
                        $args['menu'] = (int)$nav_menu_id;
                        $args['walker'] = new MPD_Custom_Menu_Walker();
                        break; // On sort de la boucle si on a trouvé un menu
                    }
                }
            }
        }

        return $args;
    }
}
