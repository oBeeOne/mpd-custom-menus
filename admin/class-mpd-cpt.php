<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_CPT {
    public static function init() {
        add_action('init', [__CLASS__, 'register_cpt']);
    }

    public static function register_cpt() {
        $labels = array(
            'name'          => __('Menus Personnalisés', 'mpd-textdomain'),
            'singular_name' => __('Menu Personnalisé', 'mpd-textdomain'),
            'menu_name'          => __('Menus Personnalisés', 'mpd-textdomain'),
            'name_admin_bar'     => __('Menu Personnalisé', 'mpd-textdomain'),
            'add_new'            => __('Ajouter un menu', 'mpd-textdomain'),
            'add_new_item'       => __('Ajouter un menu', 'mpd-textdomain'),
            'edit_item'          => __('Éditer le menu', 'mpd-textdomain'),
            'new_item'           => __('Nouveau menu', 'mpd-textdomain'),
            'view_item'          => __('Voir le menu', 'mpd-textdomain'),
            'search_items'       => __('Rechercher un menu', 'mpd-textdomain'),
            'not_found'          => __('Aucun menu trouvé', 'mpd-textdomain'),
            'not_found_in_trash' => __('Aucun menu dans la corbeille', 'mpd-textdomain'),
            // etc...
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'show_ui'            => true,
            'capability_type'    => 'post',
            'supports'           => array('title'),
            'rewrite'            => false,
        );

        register_post_type('mpd_menu', $args);
    }
}
