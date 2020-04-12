<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

$json_file = \plugin_dir_path(__FILE__) . 'extension.json';

if (file_exists($json_file)) {
    $options_json = file_get_contents($json_file);
    $options = json_decode($options_json);
    if (isset($options->short_name)) {
        $short = $options->short_name;
        $option = "cf7_$short";
        $opts = wp_load_alloptions();

        $self_opts = array_filter($opts, function ($k) use ($option) {
            return strpos($k, $option) !== false;
        }, ARRAY_FILTER_USE_KEY);

        if (!empty($self_opts)) {
            foreach ($self_opts as $k => $v) {
                delete_option($k);
            }
        }
    }
}
