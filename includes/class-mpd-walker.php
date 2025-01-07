<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Custom_Menu_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {
        $indent  = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"mpd-sub-menu\">\n";
    }

    public function end_lvl(&$output, $depth = 0, $args = null) {
        $indent  = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent    = ($depth) ? str_repeat("\t", $depth) : '';
        $classes   = implode(' ', $item->classes);
        $output   .= $indent . '<li class="mpd-menu-item ' . esc_attr($classes) . '">';

        $attributes  = !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';
        $item_output = '<a class="mpd-link"' . $attributes . '>';
        $item_output .= apply_filters('the_title', $item->title, $item->ID);
        $item_output .= '</a>';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}
