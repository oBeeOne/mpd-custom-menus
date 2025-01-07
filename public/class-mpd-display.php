<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Display {
    public static function init() {
        // On accroche notre logique de remplacement
        add_filter('wp_nav_menu_args', [__CLASS__, 'replace_menu_logic']);
    }

    public static function replace_menu_logic($args) {
        // 1. Ignorer l’admin
        if (is_admin()) {
            return $args;
        }

        // 2. Ne cibler QUE l’emplacement "primary"
        //    (ou "primary-menu" si Divi l’emploie ainsi).
        if (!isset($args['theme_location']) || $args['theme_location'] !== 'primary-menu') {
            return $args;
        }

        /**
         * 3. Logique : si on est sur une page,
         *    on cherche dans nos CPT "mpd_menu" s’il y a un match
         *    (par page ou par auteur).
         */
        if (is_page()) {
            global $post;
            $page_id   = $post->ID;
            $author_id = $post->post_author;

            // Récupérer tous les CPT "mpd_menu"
            $mpd_menus = get_posts([
                'post_type'   => 'mpd_menu',
                'numberposts' => -1
            ]);

            foreach ($mpd_menus as $menu_post) {
                // Pages cibles
                $pages = get_post_meta($menu_post->ID, '_mpd_menu_pages', true);
                // Auteur associé
                $menu_user_id = get_post_meta($menu_post->ID, '_mpd_menu_user', true);

                $has_page_match   = (is_array($pages) && in_array($page_id, $pages));
                $has_author_match = ($menu_user_id && (int)$menu_user_id === (int)$author_id);

                if ($has_page_match || $has_author_match) {
                    // Annulation du menu par défaut
                    $args['menu'] = 0;

                    // On passe l’ID du CPT dans un argument (ou un property statique)
                    $menu_post_id = $menu_post->ID;
                    // On l’injecte dans $args pour que le Walker sache quel CPT il doit lire
                    $args['mpd_menu_post_id'] = $menu_post_id;
                    
                    // On applique un Walker personnalisé
                    $args['walker'] = new MPD_Custom_Menu_Walker();
                    break;
                    
                }
            }
        }

        // 4. Ajuster la classe du conteneur et de la liste
        $args['container_class'] = 'mpd-menu-container et_menu_container';
        $args['menu_class']      = 'mpd-menu-items et_menu nav';

        // 5. Retourner $args pour laisser WP/Divi générer la structure
        return $args;
    }
}
