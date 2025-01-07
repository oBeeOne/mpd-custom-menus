<?php
/**
 * Uninstall script for MPD Custom Menu
 * 
 * Ce script est exécuté lorsque l’utilisateur désinstalle
 * totalement le plug-in depuis l’interface d’administration.
 */

// Sécurité : vérifier que le script est bien appelé par WordPress
if ( ! defined('WP_UNINSTALL_PLUGIN') ) {
    exit;
}

/**
 * 1. Supprimer tous les CPT "mpd_menu"
 *    - Pour purger complètement vos contenus, vous pouvez utiliser `get_posts()`
 *      et un `wp_delete_post()`.
 */
$args = array(
    'post_type'   => 'mpd_menu',
    'numberposts' => -1,
);

$mpd_menus = get_posts($args);

if ( ! empty($mpd_menus) ) {
    foreach ( $mpd_menus as $menu ) {
        // Force la suppression (true) sans passer par la corbeille
        wp_delete_post($menu->ID, true);
    }
}

/**
 * 2. Supprimer les options éventuelles créées par le plug-in
 *    - Par exemple, si vous avez des options stockées via update_option('mpd_menu_bg_color'), etc.
 *    - Ici, on les supprime toutes si on en a créé.
 */
delete_option('mpd_menu_sticky');
delete_option('mpd_menu_bg_color');
delete_option('mpd_plugin_options'); // Exemple générique si on avait un tableau d’options globales

/**
 * 3. Supprimer éventuellement les rôles ou capabilities créées
 *    - Si vous avez créé un rôle "menu_manager" lors de l'activation, vous pouvez le retirer ici.
 */
$role = get_role('menu_manager');
if ( null !== $role ) {
    // Supprime le rôle "menu_manager" entièrement
    remove_role('menu_manager');
}

/**
 * 4. Nettoyage de réécriture (optionnel)
 *    - En théorie, WordPress gère la plupart du temps le flush tout seul,
 *      mais si vous voulez vous assurer qu'aucune réécriture ne reste...
 *
 * Attention, flush_rewrite_rules() nécessite un environnement WordPress complet.
 * Dans uninstall.php, vous n’avez pas toujours accès à tous les hooks et fonctions
 * dans les mêmes conditions qu’un plugin normal. 
 * Il est souvent recommandé de faire ce flush via l’activation/désactivation,
 * pas forcément ici.
 */
// flush_rewrite_rules(); // A utiliser avec précaution dans uninstall.php

/**
 * Votre plugin devrait maintenant être complètement purgé :
 * - CPT supprimés
 * - Metas associées à ces CPT supprimées
 * - Options supprimées
 * - Rôle/capabilities supprimées
 */
