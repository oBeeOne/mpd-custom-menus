<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Activator {
    public static function activate() {
        // Exemple : Vérifier la présence de Divi ou la version de PHP
        $theme = wp_get_theme();
        if (strpos($theme->get('Name'), 'Divi') === false && strpos($theme->get('Template'), 'Divi') === false) {
            deactivate_plugins(plugin_basename(__FILE__)); // Attention : __FILE__ est ici le fichier actuel
            wp_die(
                __('Le plugin MPD Custom Menu nécessite le thème Divi. Activation annulée.', 'mpd-textdomain'),
                __('Thème manquant', 'mpd-textdomain'),
                ['back_link' => true]
            );
        }
        
        // Pour être sûr que le CPT s’enregistre correctement à l’activation
        MPD_CPT::register_cpt();
        flush_rewrite_rules();
    }
}
