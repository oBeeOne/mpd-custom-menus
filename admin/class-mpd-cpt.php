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
            // ...
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
