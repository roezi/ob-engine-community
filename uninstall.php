<?php
/**
 * OB Engine Community uninstall cleanup.
 *
 * This plugin may store admin/provider settings and private OBE Library items.
 * It stores private OBE Library items and may create a private Activity Log table.
 * It does not create production content, workflows, queues, cron jobs, or routes.
 *
 * @package OBEngine
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Delete only the admin/provider settings options created by this plugin.
// Do not delete production content or external data here.
delete_option( 'ob_engine_settings' );
delete_option( 'ob_engine_provider_settings' );

// Remove only private OBE Library CPT items. Deleting these posts also removes
// their related post meta. This intentionally does not delete public posts or
// unrelated post meta.
$obe_library_items = get_posts(
	array(
		'post_type'      => 'obe_library_item',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	)
);

foreach ( $obe_library_items as $obe_library_item_id ) {
	wp_delete_post( (int) $obe_library_item_id, true );
}

// Remove only the private OBE Activity Log table created by this plugin.
// This does not delete public posts, unrelated options, or external data.
global $wpdb;
$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'obe_activity_log' );
