<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Admin {

    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu_page']);
        add_action('admin_init', [__CLASS__, 'register_design_settings']);
    }

    // 1. Création de la page d’options principale
    public static function add_menu_page() {
        add_menu_page(
            __('MPD Custom Menu Settings', 'mpd-textdomain'),
            __('MPD Settings', 'mpd-textdomain'),
            'manage_options',
            'mpd_settings',
            [__CLASS__, 'render_settings_page']
        );
    }

    // 2. Enregistrer le nouveau champ "CSS personnalisé"
    public static function register_design_settings() {
        // On suppose que le groupe d’options n’existait pas avant :
        register_setting('mpd_settings_group', 'mpd_custom_css');

        // Section "Design" (on peut en faire d’autres si besoin)
        add_settings_section(
            'mpd_design_section',
            __('Design Settings', 'mpd-textdomain'),
            function () {
                echo '<p>'.__('Définissez ici votre CSS personnalisé pour le menu.', 'mpd-textdomain').'</p>';
            },
            'mpd_settings'
        );

        // Champ "CSS personnalisé"
        add_settings_field(
            'mpd_custom_css_field',
            __('CSS personnalisé', 'mpd-textdomain'),
            [__CLASS__, 'mpd_custom_css_field_cb'],
            'mpd_settings',
            'mpd_design_section'
        );
    }

    // 3. Callback qui affiche le <textarea> pour le CSS
    public static function mpd_custom_css_field_cb() {
        $custom_css = get_option('mpd_custom_css', '');
        echo '<textarea name="mpd_custom_css" rows="10" cols="70" style="width:100%;">'
             . esc_textarea($custom_css) . '</textarea>';
    }

    // 4. Rendu HTML global de la page d’options
    public static function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('MPD Custom Menu - Paramètres', 'mpd-textdomain'); ?></h1>
            <form method="post" action="options.php">
                <?php
                    settings_fields('mpd_settings_group');    // Sécurité WP
                    do_settings_sections('mpd_settings');     // Affiche sections & champs
                    submit_button();                          // Bouton "Enregistrer"
                ?>
            </form>
        </div>
        <?php
    }

}
