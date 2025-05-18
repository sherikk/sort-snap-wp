
<?php
/**
 * Plugin Name: SortSnap
 * Plugin URI: https://example.com/sortsnap
 * Description: A simple plugin for drag and drop post sorting with the order saved as post meta.
 * Version: 1.0.0
 * Author: SortSnap
 * Author URI: https://example.com
 * Text Domain: sortsnap
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('SORTSNAP_VERSION', '1.0.0');
define('SORTSNAP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('SORTSNAP_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once SORTSNAP_PLUGIN_DIR . 'includes/class-sortsnap.php';
require_once SORTSNAP_PLUGIN_DIR . 'includes/class-sortsnap-admin.php';
require_once SORTSNAP_PLUGIN_DIR . 'includes/class-sortsnap-post-list.php';

/**
 * Initialize the plugin
 */
function sortsnap_init() {
    $sortsnap = new SortSnap();
    $sortsnap->init();
}
add_action('plugins_loaded', 'sortsnap_init');

/**
 * Register activation hook
 */
register_activation_hook(__FILE__, 'sortsnap_activate');

function sortsnap_activate() {
    // Add initial post order to existing posts if it doesn't exist
    $posts = get_posts(array(
        'post_type' => 'post',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_sortsnap_order',
                'compare' => 'NOT EXISTS'
            )
        )
    ));

    $order = 0;
    foreach ($posts as $post) {
        update_post_meta($post->ID, '_sortsnap_order', $order);
        $order++;
    }
}
