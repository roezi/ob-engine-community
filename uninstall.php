<?php
/**
 * OB Engine Community uninstall cleanup.
 *
 * This skeleton may store admin/provider settings, including a BYOK
 * provider key for future use. It does not create custom tables, production
 * content, workflows, queues, cron jobs, routes, or logs.
 *
 * @package OBEngine
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Delete only the admin/provider settings options created by this skeleton.
// Do not delete production content, tables, or external data here.
delete_option( 'ob_engine_settings' );
delete_option( 'ob_engine_provider_settings' );
