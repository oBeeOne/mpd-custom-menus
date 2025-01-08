<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_CPT_Walker extends Walker_Nav_Menu {

    // On va récupérer l’ID du CPT via $args dans la fonction "walk()"
    public function walk($elements, $max_depth) {
        // $elements est normalement la liste d’items WP, 
        // mais ici on s’en fiche, on ira lire $args->mpd_menu_post_id
        $args = func_get_arg(2); // 3e argument
        // Walker_Nav_Menu::walk($elements, $max_depth, ... ) → le 3e arg c’est $args

        if (!empty($args->mpd_menu_post_id)) {
            $menu_post_id = $args->mpd_menu_post_id;
        } else {
            // S’il n’est pas défini, on retourne le menu par défaut
            return parent::walk($elements, $max_depth, $args);;
        }

        // Récupérer les items du CPT
        $items_data = get_post_meta($menu_post_id, '_mpd_menu_items', true);
        if (!is_array($items_data) || empty($items_data['items'])) {
            // Pas d’items → rien à afficher, on affiche le menu par défaut

            return parent::walk($elements, $max_depth, $args);;
        }

        $items = $items_data['items']; // ex : array of [title, href, class]

        // On va construire le HTML final.
        // Normalement, "walk()" renvoie un string contenant les <li>.
        // On doit manuellement appeler start_lvl, start_el, end_el, end_lvl, etc. 
        // ou tout générer nous-même.

        $output = '';

        // Ouvrir le <ul> principal (si on veut la cohérence Walker)
        // Mais attention, WP l'ouvre déjà via "items_wrap".
        // Dans un Walker natif, start_lvl() est appelé pour les sous-niveaux. 
        // Pour le menu racine, WP fait l'enrobage si "items_wrap" = "<ul>%3$s</ul>".
        // ICI : on va tout faire manuellement, ou partiellement.
        // Mieux vaut laisser WP gérer <ul> (via $args['items_wrap']), 
        // et nous on va juste générer les <li>.

        // On va boucler sur nos items
        foreach ($items as $i => $item) {
            $output .= $this->start_el_custom($item, 0, $args, $i);
            // pas de sous-menu pour cet exemple, on en reste au niveau 0
            $output .= $this->end_el_custom();
        }

        return $output;
    }

    // Custom method pour générer un <li> + <a>
    protected function start_el_custom($item_data, $depth, $args, $index) {
        // On peut créer nos classes comme on veut
        $li_class  = 'menu-item menu-item-type-custom mpd-menu-item';
        // On récupère ce qu'on a dans $item_data
        $title = isset($item_data['title']) ? esc_html($item_data['title']) : 'Untitled';
        $href  = isset($item_data['href'])  ? esc_url($item_data['href'])   : '#';
        $class = isset($item_data['class']) ? esc_attr($item_data['class']) : '';

        // Divi aime bien <li><a>...
        $output  = '<li class="'.$li_class.'">';
        $output .= '<a href="'.$href.'" class="mpd-link '.$class.'">';
        $output .= $title;
        $output .= '</a>';

        return $output;
    }

    protected function end_el_custom() {
        return "</li>\n";
    }
}
