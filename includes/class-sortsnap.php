
<?php
/**
 * Main SortSnap class
 */
class SortSnap {
    /**
     * Admin class instance
     */
    private $admin;
    
    /**
     * Post List class instance
     */
    private $post_list;
    
    /**
     * Initialize the plugin
     */
    public function init() {
        $this->load_dependencies();
        $this->setup_hooks();
    }
    
    /**
     * Load dependent classes
     */
    private function load_dependencies() {
        $this->admin = new SortSnap_Admin();
        $this->post_list = new SortSnap_Post_List();
    }
    
    /**
     * Set up hooks
     */
    private function setup_hooks() {
        // Apply custom sorting to admin screen
        add_action('pre_get_posts', array($this, 'apply_custom_order_to_admin'));
    }
    
    /**
     * Apply custom sorting to admin screen
     */
    public function apply_custom_order_to_admin($query) {
        // Only apply in admin area
        if (!is_admin()) {
            return;
        }
        
        // Check if we're in a WP_Query which will be affected
        if (!$query->is_main_query()) {
            return;
        }
        
        // Only apply to post type admin screens by checking if function exists
        if (function_exists('get_current_screen')) {
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
