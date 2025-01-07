<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Admin {
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_menu_page']);
    }

    public static function add_menu_page() {
        add_menu_page(
            __('MPD Custom Menu Settings', 'mpd-textdomain'),
            __('MPD Settings', 'mpd-textdomain'),
            'manage_options',
            'mpd_settings',
            [__CLASS__, 'render_settings_page']
        );
    }

    public static function render_settings_page() {
        echo '<div class="wrap"><h1>Paramètres généraux</h1>';
        echo '<p>Ici, vous pouvez gérer des réglages globaux du plugin...</p>';
        echo '</div>';
    }
}
