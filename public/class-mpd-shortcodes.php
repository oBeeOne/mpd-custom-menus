<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Shortcodes {
    public static function init() {
        add_shortcode('mpd_menu', [__CLASS__, 'mpd_menu_shortcode']);
    }

    public static function mpd_menu_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => ''  // ID du mpd_menu (CPT) ou ID du menu WP
        ), $atts);

        $menu_id = (int)$atts['id'];
        if (!$menu_id) {
            return ''; // Pas d'ID => rien
        }

        // Soit on suppose que c’est un ID du CPT, et on récupère le meta
        // Soit c’est directement l’ID du menu WP
        // Supposons que c’est l’ID du menu WP pour simplifier
        ob_start();
        wp_nav_menu(array(
            'menu'   => $menu_id,
            'walker' => new MPD_Custom_Menu_Walker()
        ));
        return ob_get_clean();
    }
}
