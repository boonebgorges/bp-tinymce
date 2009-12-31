=== Plugin Name ===
Contributors: boonebgorges, cuny-academic-commons
Tags: buddypress, tinymce, wysiwyg, rich text, editor
Requires at least: WPMU 2.8, BuddyPress 1.1
Tested up to: WPMU 2.8.6, BuddyPress 1.1.3
Stable tag: trunk

Replaces textareas throughout BuddyPress with the TinyMCE rich text box.

== Description ==

This plugin enables rich text editing for BuddyPress users. It uses the TinyMCE editor that is distributed with Wordpress. 

== Installation ==

* Upload the directory '/bp-tinymce/' to your WP plugins directory and activate from the Dashboard of the main blog.
* Because of some inconsistencies with the way that WP's TinyMCE is integrated, you may want to change the name of the TinyMCE language file you'll be using (for English, `wp-includes/js/tinymce/langs/wp-langs-en.php). The correct format is `en.php`, without the `wp-langs-` prefix.
* If you're not seeing the TinyMCE box in BuddyPress, check to make sure that the path to TinyMCE on line 20 of bp-tinymce.php is correct.

*** IMPORTANT: This plugin allows certain pieces of HTML to be put into BuddyPress, including hrefs. Make sure that you are satisfied with the security of the plugin before activiating it on a production site! ***


== Changelog ==

= 0.1 =
* Initial release