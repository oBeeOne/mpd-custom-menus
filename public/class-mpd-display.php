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

            // Vérifier si c’est le menu "primary"
            if ( !isset($args['theme_location']) || $args['theme_location'] !== 'primary-menu' ) {
                // On ne touche pas aux autres menus
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

            // Imaginons qu’on a trouvé $menu_post (un objet WP_Post)
            // On récupère les items
            $menu_items = get_post_meta($menu_post->ID, '_mpd_menu_items', true);

            if (is_array($menu_items) && isset($menu_items['items'])) {
                // On fabrique le HTML
                $html_output = '<div class="mpd-menu-container">';
                
                // (Optionnel) Affichage du logo :
                $html_output .= '<div class="mpd-menu-logo">' . get_custom_logo() . '</div>';

                $html_output .= '<ul class="mpd-menu-items">';
                foreach ($menu_items['items'] as $item) {
                    $title = isset($item['title']) ? esc_html($item['title']) : '';
                    $href  = isset($item['href']) ? esc_url($item['href']) : '#';
                    $class = isset($item['class']) ? esc_attr($item['class']) : '';

                    $html_output .= '<li class="mpd-menu-item">';
                    $html_output .= '<a href="'.$href.'" class="'.$class.'">'.$title.'</a>';
                    $html_output .= '</li>';
                }
                $html_output .= '</ul></div>';

                // On "injecte" ce HTML dans le rendu final
                // Option 1 : on modifie $args['items_wrap'] ou on bypass l'appel normal de wp_nav_menu
                // Option 2 : on renvoie le HTML et on court-circuite le menu WP
                // Par ex. on peut stocker dans un static property 
                //   et l'injecter via un hook plus tard.
                // Pour la démo, on va le faire en "brut".
                add_filter('wp_nav_menu_args', function($orig_args) use ($html_output) {
                    // On remplace tout par un "menu" factice qui ne fera rien
                    $orig_args['items_wrap'] = '<div style="display:none;"></div>';
                    return $orig_args;
                }, 9999);

                // On injecte un hook sur 'wp_nav_menu' pour remplacer la sortie
                add_filter('pre_wp_nav_menu', function() use ($html_output) {
                    return $html_output; 
                }, 9999);
            }

            return $args;
        }
    }
}
