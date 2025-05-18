
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

* Intuitive drag and drop interface
* Filter posts by category
* Auto-save option or manual save button
* Custom post order stored as post meta
* Easy to use admin interface

== Installation ==

1. Upload the `sortsnap` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Navigate to the 'SortSnap' menu in your admin panel to start sorting posts

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

== Screenshots ==

1. The SortSnap admin interface showing drag and drop post sorting.

== Changelog ==

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of the SortSnap plugin.

