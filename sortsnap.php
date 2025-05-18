
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
        
        // Add column to post type admin pages
        add_filter('manage_posts_columns', array($this, 'add_order_column'));
        add_action('manage_posts_custom_column', array($this, 'display_order_column'), 10, 2);
        
        // Add sorting capability to admin columns
        add_filter('manage_edit-post_sortable_columns', array($this, 'make_order_column_sortable'));
        
        // Apply custom sorting to admin screen
        add_action('pre_get_posts', array($this, 'apply_custom_order_to_admin'));
        
        // Apply sortable to post list tables
        add_action('admin_footer', array($this, 'add_sortable_script_to_list_tables'));
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
    
    /**
     * Add order column to post list tables
     */
    public function add_order_column($columns) {
        $new_columns = array();
        
        // Insert order column after checkbox but before title
        foreach ($columns as $key => $value) {
            if ($key === 'cb') {
                $new_columns[$key] = $value;
                $new_columns['sortsnap_order'] = __('Order', 'sortsnap');
            } else {
                $new_columns[$key] = $value;
            }
        }
        
        return $new_columns;
    }
    
    /**
     * Display order value in the column
     */
    public function display_order_column($column, $post_id) {
        if ($column === 'sortsnap_order') {
            $order = get_post_meta($post_id, '_sortsnap_order', true);
            echo !empty($order) ? intval($order) : '-';
        }
    }
    
    /**
     * Make order column sortable
     */
    public function make_order_column_sortable($columns) {
        $columns['sortsnap_order'] = 'sortsnap_order';
        return $columns;
    }
    
    /**
     * Apply custom sorting to admin screen
     */
    public function apply_custom_order_to_admin($query) {
        if (!is_admin()) {
            return;
        }
        
        $screen = get_current_screen();
        
        // Only apply to post type list screens
        if ($screen && $screen->base === 'edit') {
            // If sorting by our column or no specific sorting selected
            if (!isset($_GET['orderby']) || $_GET['orderby'] === 'sortsnap_order') {
                $query->set('meta_key', '_sortsnap_order');
                $query->set('orderby', 'meta_value_num');
                $query->set('order', 'ASC');
            }
        }
    }
    
    /**
     * Add sortable script to WP post list tables
     */
    public function add_sortable_script_to_list_tables() {
        $screen = get_current_screen();
        
        // Only load on post list screens
        if ($screen && $screen->base === 'edit' && $screen->post_type) {
            ?>
            <style>
                .wp-list-table tbody {
                    cursor: move;
                }
                .wp-list-table tr.ui-sortable-helper {
                    background: #e8f0fe !important;
                    border: 1px solid #c3c4c7 !important;
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
                }
                .wp-list-table .ui-sortable-placeholder {
                    visibility: visible !important;
                    background: #f7f7f7;
                    height: 50px;
                    border: 1px dashed #c3c4c7;
                }
                .sortsnap-loading-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.7);
                    z-index: 999999;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .sortsnap-loading-spinner {
                    width: 50px;
                    height: 50px;
                    border: 5px solid #f3f3f3;
                    border-top: 5px solid #0073aa;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
            <script>
            jQuery(document).ready(function($) {
                // Make the table body sortable
                $('.wp-list-table tbody').sortable({
                    items: 'tr',
                    cursor: 'move',
                    placeholder: 'ui-sortable-placeholder',
                    update: function(event, ui) {
                        // Show loading overlay
                        $('body').append('<div class="sortsnap-loading-overlay"><div class="sortsnap-loading-spinner"></div></div>');
                        
                        // Collect all post IDs in their new order
                        const postIds = [];
                        $(this).find('tr').each(function() {
                            const postId = $(this).find('input[name="post[]"]').val();
                            if (postId) {
                                postIds.push(postId);
                            }
                        });
                        
                        // Save the order via AJAX
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'sortsnap_save_order',
                                post_ids: postIds,
                                nonce: '<?php echo wp_create_nonce('sortsnap_nonce'); ?>'
                            },
                            success: function(response) {
                                $('.sortsnap-loading-overlay').remove();
                                
                                if (response.success) {
                                    // Add success notice
                                    const $notice = $('<div class="notice notice-success is-dismissible"><p>Post order saved successfully.</p></div>');
                                    $('#wpbody-content > .wrap > .wp-header-end').after($notice);
                                    
                                    // Remove notice after delay
                                    setTimeout(function() {
                                        $notice.fadeOut(300, function() { $(this).remove(); });
                                    }, 3000);
                                } else {
                                    // Show error
                                    const $notice = $('<div class="notice notice-error is-dismissible"><p>Error: ' + (response.data || 'Failed to save order.') + '</p></div>');
                                    $('#wpbody-content > .wrap > .wp-header-end').after($notice);
                                }
                            },
                            error: function() {
                                $('.sortsnap-loading-overlay').remove();
                                
                                // Show error notice
                                const $notice = $('<div class="notice notice-error is-dismissible"><p>Error: Could not connect to the server.</p></div>');
                                $('#wpbody-content > .wrap > .wp-header-end').after($notice);
                            }
                        });
                    }
                });
            });
            </script>
            <?php
        }
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
