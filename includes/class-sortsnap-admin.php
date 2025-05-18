
<?php
/**
 * SortSnap Admin class
 */
class SortSnap_Admin {
    /**
     * Constructor
     */
    public function __construct() {
        // Admin menu
        add_action('admin_menu', array($this, 'add_admin_menu'));
        
        // Admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        
        // AJAX handler
        add_action('wp_ajax_sortsnap_save_order', array($this, 'save_post_order'));
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
        // Original SortSnap page scripts
        if ($hook == 'toplevel_page_sortsnap') {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-sortable');
            
            wp_enqueue_style('sortsnap-admin', SORTSNAP_PLUGIN_URL . 'assets/css/admin.css', array(), SORTSNAP_VERSION);
            wp_enqueue_script('sortsnap-admin', SORTSNAP_PLUGIN_URL . 'assets/js/admin.js', array('jquery', 'jquery-ui-sortable'), SORTSNAP_VERSION, true);
            
            wp_localize_script('sortsnap-admin', 'sortsnap', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('sortsnap_nonce')
            ));
        }
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
}
