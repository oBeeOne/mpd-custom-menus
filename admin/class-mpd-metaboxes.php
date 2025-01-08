<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Metaboxes {
    public static function init() {
        add_action('add_meta_boxes', [__CLASS__, 'add_meta_boxes']);
        add_action('save_post_mpd_menu', [__CLASS__, 'save_mpd_menu_data']);

        // Charger le JS/CSS en admin
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_assets']);
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
    /* public static function render_pages_metabox($post) {
        // Récupérer l'ID de l'utilisateur connecté
        $current_user_id = get_current_user_id();
    
        // Récupérer uniquement les pages de l'utilisateur connecté
        $user_pages = get_posts([
            'post_type'      => 'page',
            'post_status'    => ['publish', 'draft'],
            'posts_per_page' => -1,
            'author'         => $current_user_id,
        ]);
    
        // Récupérer les pages déjà assignées au menu
        $saved_pages = get_post_meta($post->ID, '_mpd_menu_pages', true);
        $saved_pages = is_array($saved_pages) ? $saved_pages : [];
    
        echo '<ul>';
    
        // Afficher uniquement les pages de l'utilisateur connecté en version checkbox
        foreach ($user_pages as $page) {
            $checked = in_array($page->ID, $saved_pages) ? 'checked' : '';
            echo '<li>';
            echo '<label>';
            echo '<input type="checkbox" name="mpd_menu_pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . '>';
            echo esc_html($page->post_title);
            echo '</label>';
            echo '</li>';
        }
    
        echo '</ul>';
    } */

    public static function render_pages_metabox($post) {
        $current_user_id = get_current_user_id();

        // Ajouter un nonce pour la sécurité
        wp_nonce_field('save_mpd_menu', 'mpd_menu_nonce');
    
        // Récupérer uniquement les pages de l'utilisateur connecté
        $user_pages = get_posts([
            'post_type'      => 'page',
            'post_status'    => ['publish', 'draft'],
            'posts_per_page' => -1,
            'author'         => $current_user_id,
        ]);
    
        // Récupérer les pages déjà assignées au menu
        $saved_pages = get_post_meta($post->ID, '_mpd_menu_pages', true);
        $saved_pages = is_array($saved_pages) ? $saved_pages : [];
    
        echo '<label for="mpd_menu_pages">' . __('Sélectionnez les pages pour ce menu :', 'mpd-textdomain') . '</label>';
        echo '<div id="mpd_menu_pages">';
    
        // Afficher uniquement les pages de l'utilisateur connecté sous forme de checkbox
        foreach ($user_pages as $page) {
            $checked = in_array($page->ID, $saved_pages) ? 'checked' : '';
            echo '<label>';
            echo '<input type="checkbox" name="mpd_menu_pages[]" value="' . esc_attr($page->ID) . '" ' . $checked . ' />';
            echo esc_html($page->post_title);
            echo '</label><br />';
        }
    
        echo '</div>';
    }
    
    

    // --- Menu items Metabox ---
    public static function enqueue_admin_assets($hook) {
        global $post;
        // Vérifier qu’on édite bien un CPT mpd_menu
        if (!isset($post->post_type) || $post->post_type !== 'mpd_menu') {
            return;
        }

        // Charger jQuery UI Sortable (inclus dans WP, on s’assure juste de l’appeler)
        wp_enqueue_script('jquery-ui-sortable');

        // Charger notre script JS externe
        wp_enqueue_script(
            'menu-items-script',
            MPD_PLUGIN_URL . 'assets/js/menu-items-mgr.js', // Chemin vers le fichier
            ['jquery', 'jquery-ui-sortable'],           // Dépendances
            '1.0',
            true                                        // in_footer = true
        );
    }

    public static function render_menu_items_metabox($post) {
        // Récupère la meta '_mpd_menu_items'
        $items_data = get_post_meta($post->ID, '_mpd_menu_items', true);
        if (!is_array($items_data)) {
            $items_data = [];
        }

        // Soit on stocke directement un array, soit $items_data['items'] = [...]
        // Adapter selon ta structure
        $items = (isset($items_data['items']) && is_array($items_data['items']))
            ? $items_data['items']
            : ( (is_array($items_data)) ? $items_data : [] );

        echo '<p>'.__('Ajoutez/modifiez les éléments de votre menu. Glissez-déposez les lignes pour changer l’ordre.', 'mpd-textdomain').'</p>';

        echo '<table class="widefat" id="mpd_menu_items_table">';
        echo '<thead>
                <tr>
                  <th style="width:30px;"></th>
                  <th>'.__('Titre', 'mpd-textdomain').'</th>
                  <th>'.__('Lien', 'mpd-textdomain').'</th>
                  <th>'.__('Classe CSS', 'mpd-textdomain').'</th>
                  <th></th>
                </tr>
              </thead>';
        echo '<tbody id="mpd_menu_items_tbody">';

        if (!empty($items)) {
            foreach ($items as $item) {
                $title = isset($item['title']) ? esc_attr($item['title']) : '';
                $href  = isset($item['href'])  ? esc_attr($item['href'])  : '';
                $class = isset($item['class']) ? esc_attr($item['class']) : '';

                echo '<tr class="mpd-menu-item-row">';
                echo '  <td class="mpd-drag-handle" style="cursor:move;">&#x2630;</td>';
                echo '  <td><input type="text" name="mpd_item_title[]" value="'.$title.'" placeholder="Titre" style="width:100%;" /></td>';
                echo '  <td><input type="text" name="mpd_item_href[]" value="'.$href.'" placeholder="/lien-relatif" style="width:100%;" /></td>';
                echo '  <td><input type="text" name="mpd_item_class[]" value="'.$class.'" placeholder="Classe CSS" style="width:100%;" /></td>';
                echo '  <td><button type="button" class="button-link-delete mpd-remove-item" style="color:red;">X</button></td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table>';

        echo '<br><button type="button" class="button" id="mpd_add_menu_item">'.__('Ajouter un élément', 'mpd-textdomain').'</button>';
    }
    

    // --- User Metabox ---
    public static function render_user_metabox($post) {
        // Récupérer l'utilisateur enregistré pour ce menu
        $current_user_id = get_current_user_id();
        $saved_user_id = get_post_meta($post->ID, '_mpd_menu_user', true);

        // Priorité : afficher l'utilisateur enregistré ou l'utilisateur connecté
        $user_id_to_display = $saved_user_id ?: $current_user_id;

        // Récupérer les informations de l'utilisateur
        $user_data = get_userdata($user_id_to_display);
        $user_name = $user_data ? $user_data->display_name : __('Utilisateur inconnu', 'mpd-textdomain');

        echo '<label for="mpd_menu_user">' . __('Utilisateur assigné au menu :', 'mpd-textdomain') . '</label>';
        echo '<input type="text" id="mpd_menu_user" name="mpd_menu_user" value="' . esc_attr($user_name) . '" readonly />';
    }

    // --- Save data ---
    public static function save_mpd_menu_data($post_id) {
        // Éviter les autosaves et autres
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!isset($_POST['mpd_menu_nonce']) || !wp_verify_nonce($_POST['mpd_menu_nonce'], 'save_mpd_menu')) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Attribuer automatiquement l'utilisateur connecté comme auteur
        $current_user_id = get_current_user_id();
        if ($current_user_id) {
            update_post_meta($post_id, '_mpd_menu_user', $current_user_id);
        }

        
        // Pages sélectionnées
        if (isset($_POST['mpd_menu_pages']) && is_array($_POST['mpd_menu_pages'])) {
            $pages = array_map('intval', $_POST['mpd_menu_pages']);
            update_post_meta($post_id, '_mpd_menu_pages', $pages);
        } else {
            delete_post_meta($post_id, '_mpd_menu_pages');
        }

        // Récupérer les tableaux envoyés
        $titles = isset($_POST['mpd_item_title']) ? (array) $_POST['mpd_item_title'] : [];
        $hrefs  = isset($_POST['mpd_item_href'])  ? (array) $_POST['mpd_item_href']  : [];
        $classes= isset($_POST['mpd_item_class']) ? (array) $_POST['mpd_item_class'] : [];

        $items  = [];
        $count  = count($titles);

        for ($i=0; $i<$count; $i++) {
            $t = sanitize_text_field($titles[$i]);
            $h = sanitize_text_field($hrefs[$i]);
            $c = sanitize_text_field($classes[$i]);

            $items[] = [
                'title' => $t,
                'href'  => $h,
                'class' => $c,
            ];
        }

        // Stockage en JSON (ou array) dans la meta
        $data_to_save = [
            'items' => $items,
        ];
        update_post_meta($post_id, '_mpd_menu_items', $data_to_save);

        // User
        if (isset($_POST['mpd_menu_user'])) {
            update_post_meta($post_id, '_mpd_menu_user', sanitize_text_field($_POST['mpd_menu_user']));
        } else {
            delete_post_meta($post_id, '_mpd_menu_user');
        }
    }
}
