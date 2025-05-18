
=== SortSnap ===
Contributors: sortsnap
Tags: posts, sorting, drag and drop, post order
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A simple plugin for drag and drop post sorting with the order saved as post meta.

== Description ==

SortSnap is a lightweight WordPress plugin that allows you to easily reorder your posts using drag and drop functionality. The plugin saves the post order as post meta, making it easy to maintain custom post ordering across your site.

**Features:**

* Intuitive drag and drop interface directly on post type admin screens
* Sort order column displayed in post listing tables
* Automatic sorting of posts by custom order in admin
* Filter posts by category in the dedicated SortSnap page
* Auto-save option or manual save button
* Custom post order stored as post meta
* Easy to use admin interface

== Installation ==

1. Upload the `sortsnap` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to any post listing page and start sorting by dragging and dropping
4. You can also use the 'SortSnap' menu in your admin panel for additional sorting options

== Frequently Asked Questions ==

= How do I display posts in my custom order? =

Use the SortSnap::get_ordered_posts() function in your theme or plugin:

`
<?php
$ordered_posts = SortSnap::get_ordered_posts('category-slug', 'post', 10);
foreach ($ordered_posts as $post) {
    // Display posts
}
?>
`

= Does this plugin work with custom post types? =

Currently, the plugin works with standard WordPress posts. Support for custom post types will be added in a future update.

= How do I know the current order of my posts? =

The plugin adds an "Order" column to your post listing tables that displays the current sort order number for each post.

== Screenshots ==

1. The SortSnap admin interface showing drag and drop post sorting.
2. Post list table with sortable rows and order column.

== Changelog ==

= 1.0.0 =
* Initial release
* Added drag and drop sorting directly on post admin screens
* Added order column to post listing tables
* Added automatic sorting of posts by custom order

== Upgrade Notice ==

= 1.0.0 =
Initial release of the SortSnap plugin.
