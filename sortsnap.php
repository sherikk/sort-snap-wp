
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

class SortSnap {
    /**
     * Constructor
     */
    public function __construct() {
        // Define constants
        $this->define_constants();

        // Hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_sortsnap_save_order', array($this, 'save_post_order'));
    }

    /**
     * Define constants
     */
    private function define_constants() {
        define('SORTSNAP_VERSION', '1.0.0');
        define('SORTSNAP_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('SORTSNAP_PLUGIN_URL', plugin_dir_url(__FILE__));
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            'SortSnap',
            'SortSnap',
            'manage_options',
            'sortsnap',
            array($this, 'admin_page'),
            'dashicons-sort',
            20
        );
    }

    /**
     * Admin page
     */
    public function admin_page() {
        include_once SORTSNAP_PLUGIN_DIR . 'templates/admin-page.php';
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our plugin page
        if ($hook != 'toplevel_page_sortsnap') {
            return;
        }

        // jQuery UI
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-sortable');
        
        // CSS
        wp_enqueue_style('sortsnap-admin', SORTSNAP_PLUGIN_URL . 'assets/css/admin.css', array(), SORTSNAP_VERSION);
        
        // JS
        wp_enqueue_script('sortsnap-admin', SORTSNAP_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), SORTSNAP_VERSION, true);
        
        // Localize script
        wp_localize_script('sortsnap-admin', 'sortsnap', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sortsnap_nonce')
        ));
    }

    /**
     * Save post order via AJAX
     */
    public function save_post_order() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'sortsnap_nonce')) {
            wp_send_json_error('Invalid nonce');
            exit;
        }

        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Permission denied');
            exit;
        }

        // Get post IDs in order
        $post_ids = isset($_POST['post_ids']) ? array_map('intval', $_POST['post_ids']) : array();
        
        if (empty($post_ids)) {
            wp_send_json_error('No posts to sort');
            exit;
        }

        // Save order as post meta for each post
        $order = 0;
        foreach ($post_ids as $post_id) {
            update_post_meta($post_id, '_sortsnap_order', $order);
            $order++;
        }

        // Send success response
        wp_send_json_success('Post order saved successfully');
        exit;
    }

    /**
     * Get posts by category with custom order
     */
    public static function get_ordered_posts($category = '', $post_type = 'post', $limit = -1) {
        $args = array(
            'post_type' => $post_type,
            'posts_per_page' => $limit,
            'meta_key' => '_sortsnap_order',
            'orderby' => 'meta_value_num',
            'order' => 'ASC',
        );

        // Add category if specified
        if (!empty($category) && $category !== 'all') {
            $args['category_name'] = $category;
        }

        return get_posts($args);
    }
}

// Initialize the plugin
$sortsnap = new SortSnap();

// Register activation hook
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

