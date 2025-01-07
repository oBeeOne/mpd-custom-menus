<?php
if (!defined('ABSPATH')) {
    exit;
}

class MPD_Deactivator {
    public static function deactivate() {
        // Nettoyage éventuel des réécritures
        flush_rewrite_rules();
    }
}
