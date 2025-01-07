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
        // Meta box "Eléments de menu"
        add_meta_box(
            'mpd_menu_items',
            __('Éléments du menu', 'mpd-textdomain'),
            [__CLASS__, 'render_menu_items_metabox'],
            'mpd_menu',
            'normal',
            'default'
        );
        
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

    // --- Menu items Metabox ---
    public static function render_menu_items_metabox($post) {
        // Récupère l'array stocké en JSON
        $items_data = get_post_meta($post->ID, '_mpd_menu_items', true);
        if (!is_array($items_data)) {
            $items_data = array();
        }
    
        // On convertit l'array en JSON pour l'afficher dans le champ
        $json_value = json_encode($items_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    
        echo '<p>'.__('Saisissez la liste des éléments du menu au format JSON.', 'mpd-textdomain').'</p>';
        echo '<textarea name="mpd_menu_items_json" rows="10" style="width:100%;">'
             . esc_textarea($json_value)
             . '</textarea>';
    
        // Exemple de structure attendue
        echo '<p><em>'.__('Exemple', 'mpd-textdomain').':</em></p>';
        echo '<pre>{
      "items": [
        {
          "title": "Accueil",
          "href": "/accueil",
          "class": "lien-accueil"
        },
        {
          "title": "Contact",
          "href": "/contact",
          "class": "lien-contact"
        }
      ]
    }</pre>';
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

        // Menu items
        // Récupération du JSON
        if (isset($_POST['mpd_menu_items_json'])) {
            $json_str = wp_unslash($_POST['mpd_menu_items_json']); // Nettoyer les slashes
            $decoded  = json_decode($json_str, true);

            // S'assurer que c’est un tableau
            if (is_array($decoded)) {
                update_post_meta($post_id, '_mpd_menu_items', $decoded);
            } else {
                // Erreur de parsing => on peut décider de ne rien enregistrer ou vider
                // update_post_meta($post_id, '_mpd_menu_items', array());
            }
        } else {
            delete_post_meta($post_id, '_mpd_menu_items');
        }

        // User
        if (isset($_POST['mpd_menu_user'])) {
            update_post_meta($post_id, '_mpd_menu_user', sanitize_text_field($_POST['mpd_menu_user']));
        } else {
            delete_post_meta($post_id, '_mpd_menu_user');
        }
    }
}
