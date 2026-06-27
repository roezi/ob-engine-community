<?php
/**
 * OB Engine Community uninstall cleanup.
 *
 * This plugin may store admin/provider settings and private OBE Library items.
 * It does not create custom tables, production content, workflows, queues,
 * cron jobs, routes, or logs.
 *
 * @package OBEngine
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Delete only the admin/provider settings options created by this plugin.
// Do not delete production content, tables, or external data here.
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
