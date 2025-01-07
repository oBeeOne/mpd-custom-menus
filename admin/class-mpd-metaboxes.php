<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Metaboxes {
    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post_mpd_menu', [__CLASS__, 'save_mpd_menu_data']);
    }

    public static function add_meta_boxes() {
        // Meta box "Pages Cibles"
        add_meta_box(
            'mpd_menu_pages',
            __('Pages Cibles', 'mpd-textdomain'),
            [__CLASS__, 'render_pages_metabox'],
            'mpd_menu',
            'normal',
            'default'
        );

        // Meta box "Utilisateur associé"
        add_meta_box(
            'mpd_menu_user',
            __('Utilisateur associé', 'mpd-textdomain'),
            [__CLASS__, 'render_user_metabox'],
            'mpd_menu',
            'side',
            'default'
        );
    }

    // --- Pages Metabox ---
    public static function render_pages_metabox($post) {
        $selected_pages = get_post_meta($post->ID, '_mpd_menu_pages', true);
        if (!is_array($selected_pages)) {
            $selected_pages = array();
        }

        $pages = get_pages();

        echo '<ul>';
        foreach ($pages as $page) {
            $checked = in_array($page->ID, $selected_pages) ? 'checked' : '';
            echo '<li>';
            echo '<label>';
            echo '<input type="checkbox" name="mpd_menu_pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . '>';
            echo esc_html($page->post_title);
            echo '</label>';
            echo '</li>';
        }
        echo '</ul>';
    }

    // --- User Metabox ---
    public static function render_user_metabox($post) {
        $selected_user_id = get_post_meta($post->ID, '_mpd_menu_user', true);
        
        $users = get_users([
            // Ajustez si besoin (par ex. exclure admin)
        ]);

        echo '<select name="mpd_menu_user" style="width:100%;">';
        echo '<option value="">— Aucun —</option>';
        foreach ($users as $user) {
            $selected = selected($selected_user_id, $user->ID, false);
            echo '<option value="' . esc_attr($user->ID) . '"' . $selected . '>';
            echo esc_html($user->display_name);
            echo '</option>';
        }
        echo '</select>';
    }

    // --- Save data ---
    public static function save_mpd_menu_data($post_id) {
        // Pages
        if (isset($_POST['mpd_menu_pages'])) {
            $pages = array_map('intval', $_POST['mpd_menu_pages']);
            update_post_meta($post_id, '_mpd_menu_pages', $pages);
        } else {
            delete_post_meta($post_id, '_mpd_menu_pages');
        }

        // User
        if (isset($_POST['mpd_menu_user'])) {
            update_post_meta($post_id, '_mpd_menu_user', sanitize_text_field($_POST['mpd_menu_user']));
        } else {
            delete_post_meta($post_id, '_mpd_menu_user');
        }
    }
}
