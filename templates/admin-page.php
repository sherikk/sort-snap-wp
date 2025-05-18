
<?php
// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get categories
$categories = get_categories(array(
    'hide_empty' => false
));

// Get selected category from URL or default to 'all'
$selected_category = isset($_GET['category']) ? sanitize_text_field($_GET['category']) : 'all';

// Get posts based on selected category
$posts = SortSnap::get_ordered_posts($selected_category === 'all' ? '' : $selected_category);
?>

<div class="wrap sortsnap-admin">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
    
    <div class="sortsnap-header">
        <div class="sortsnap-category-filter">
            <form method="get">
                <input type="hidden" name="page" value="sortsnap">
                <select name="category" id="sortsnap-category">
                    <option value="all" <?php selected($selected_category, 'all'); ?>>
                        <?php esc_html_e('All Categories', 'sortsnap'); ?>
                    </option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo esc_attr($category->slug); ?>" <?php selected($selected_category, $category->slug); ?>>
                            <?php echo esc_html($category->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="button"><?php esc_html_e('Filter', 'sortsnap'); ?></button>
            </form>
        </div>
        
        <div class="sortsnap-actions">
            <label for="sortsnap-autosave">
                <input type="checkbox" id="sortsnap-autosave" checked>
                <?php esc_html_e('Auto Save', 'sortsnap'); ?>
            </label>
            <button id="sortsnap-save" class="button button-primary" style="display: none;">
                <?php esc_html_e('Save Order', 'sortsnap'); ?>
            </button>
        </div>
    </div>
    
    <div class="sortsnap-container">
        <?php if (!empty($posts)): ?>
            <ul id="sortsnap-post-list" class="sortsnap-posts">
                <?php foreach ($posts as $post): ?>
                    <li class="sortsnap-post-item" data-id="<?php echo esc_attr($post->ID); ?>">
                        <div class="sortsnap-post-handle dashicons dashicons-menu"></div>
                        <div class="sortsnap-post-content">
                            <h3>
                                <a href="<?php echo esc_url(get_edit_post_link($post->ID)); ?>" target="_blank">
                                    <?php echo esc_html($post->post_title); ?>
                                </a>
                            </h3>
                            <div class="sortsnap-post-meta">
                                <span class="sortsnap-post-date">
                                    <?php echo esc_html(get_the_date('', $post->ID)); ?>
                                </span>
                                <span class="sortsnap-post-status <?php echo esc_attr($post->post_status); ?>">
                                    <?php echo esc_html(ucfirst($post->post_status)); ?>
                                </span>
                                <?php
                                $post_categories = get_the_category($post->ID);
                                if (!empty($post_categories)):
                                    foreach ($post_categories as $category):
                                ?>
                                    <span class="sortsnap-post-category">
                                        <?php echo esc_html($category->name); ?>
                                    </span>
                                <?php
                                    endforeach;
                                endif;
                                ?>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="sortsnap-no-posts">
                <?php esc_html_e('No posts found in this category', 'sortsnap'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>

