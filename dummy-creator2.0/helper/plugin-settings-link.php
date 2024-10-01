<?php
// Settings link hozzáadása a bővítmény listához
if (!function_exists('dummy_creator_add_settings_link')) {
    function dummy_creator_add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('admin.php?page=dummy-creator') . '">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

// Filter hozzáadása a beállítások linkhez
add_filter('plugin_action_links_' . plugin_basename(dirname(__DIR__) . '/plugin.php'), 'dummy_creator_add_settings_link');
