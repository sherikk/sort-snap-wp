
<?php
/**
 * SortSnap Post List class
 */
class SortSnap_Post_List {
    /**
     * Constructor
     */
    public function __construct() {
        // Add column to post type admin pages
        add_filter('manage_posts_columns', array($this, 'add_order_column'));
        add_action('manage_posts_custom_column', array($this, 'display_order_column'), 10, 2);
        
        // Add sorting capability to admin columns
        add_filter('manage_edit-post_sortable_columns', array($this, 'make_order_column_sortable'));
        
        // Apply sortable to post list tables
        add_action('admin_footer', array($this, 'add_sortable_script_to_list_tables'));
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
     * Add sortable script to WP post list tables
     */
    public function add_sortable_script_to_list_tables() {
        // Only run in admin area and if function exists
        if (!is_admin() || !function_exists('get_current_screen')) {
            return;
        }
        
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
