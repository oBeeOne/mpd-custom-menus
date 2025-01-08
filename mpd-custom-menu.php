<?php
/**
 * Plugin Name: MPD Custom Menu
 * Description: Crée et gère plusieurs menus personnalisés basés sur un Custom Post Type, avec règles d’affichage par pages et par auteur, adapté pour Divi.
 * Version: 0.2.1
 * Author: oBeeOne
 * Text Domain: mpd-textdomain
 */

// Sécurité
if (!defined('ABSPATH')) {
    exit;
}

// Chemins
define('MPD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('MPD_PLUGIN_URL', plugin_dir_url(__FILE__));

// ========== INCLUSIONS DES FICHIERS ==========

// Fichiers d'activation / désactivation
require_once MPD_PLUGIN_DIR . 'includes/class-mpd-activator.php';
require_once MPD_PLUGIN_DIR . 'includes/class-mpd-deactivator.php';

// Walker
require_once MPD_PLUGIN_DIR . 'includes/class-mpd-walker.php';

// Partie Admin
require_once MPD_PLUGIN_DIR . 'admin/class-mpd-cpt.php';
require_once MPD_PLUGIN_DIR . 'admin/class-mpd-metaboxes.php';
require_once MPD_PLUGIN_DIR . 'admin/class-mpd-admin.php'; // si nécessaire

// Partie Public
require_once MPD_PLUGIN_DIR . 'public/class-mpd-display.php';
require_once MPD_PLUGIN_DIR . 'public/class-mpd-shortcodes.php'; // si vous gérez des shortcodes

// Check auto de nouvelle version à mettre à jour depuis Github
require_once MPD_PLUGIN_DIR . 'includes/plugin-update-checker-master/plugin-update-checker.php';

// ========== HOOKS D'ACTIVATION / DESACTIVATION ==========
register_activation_hook(__FILE__, ['MPD_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['MPD_Deactivator', 'deactivate']);

// ========== INITIALISATION ==========
function mpd_custom_menu_init() {
    // Init du CPT
    MPD_CPT::init();
    
    // Init des meta boxes
    MPD_Metaboxes::init();

    // Init admin (page de réglages éventuelle)
    MPD_Admin::init();
    
    // Init front (remplacement du menu, etc.)
    MPD_Display::init();
    MPD_Shortcodes::init();
}
add_action('plugins_loaded', 'mpd_custom_menu_init');

// === Chargement des scripts et styles ===
function mpd_enqueue_scripts_styles() {
    // 1. Charger le fichier CSS de base
    wp_enqueue_style(
        'mpd-styles',
        MPD_PLUGIN_URL . 'assets/css/style.css',
        [],
        '1.0',
        'all'
    );

    // 2. Si l’admin a saisi du CSS personnalisé, on l’injecte
    $custom_css = get_option('mpd_custom_css', '');
    if (!empty($custom_css)) {
        wp_add_inline_style('mpd-styles', $custom_css);
    }

    // (Si besoin) Charger un script JS
    // wp_enqueue_script('mpd-script', MPD_PLUGIN_URL . 'assets/js/script.js', ['jquery'], '1.0', true);
}
add_action('wp_enqueue_scripts', 'mpd_enqueue_scripts_styles');

// Activation des màj via Github
use YahnisElsts\PluginUpdateChecker\v5\PucFactory; 
// (Selon la version du plugin-update-checker que tu as, l’espace de nom peut varier.)

function mpd_init_update_checker() {
    // 1. Chemin vers le fichier principal du plug-in
    $pluginFile = __FILE__; 
    // Ou plugin_basename(__FILE__) selon la config

    // 2. URL du repo GitHub. Le “slug” du plugin doit correspondre à ton folder
    $updateChecker = PucFactory::buildUpdateChecker(
        'https://github.com/oBeeOne/mpd-custom-menus',
        $pluginFile,
        'mpd-custom-menu' // slug du plug-in
    );

    // 3. (Optionnel) définir la branche, si tu ne veux pas “main” / “master”
    $updateChecker->setBranch('main');

    // 4. (Si ton repo est privé) authentification :
    // $updateChecker->setAuthentication('TonGitHubAccessToken');

    // Ça y est, les updates seront vérifiées.
}
add_action('plugins_loaded', 'mpd_init_update_checker');
