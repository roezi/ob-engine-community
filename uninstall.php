<?php
/**
 * OB Engine Community uninstall cleanup.
 *
 * The minimal skeleton stores only harmless placeholder settings in the
 * ob_engine_settings option. It does not create custom tables, production
 * content, provider credentials, workflows, queues, cron jobs, routes, or logs.
 *
 * @package OBEngine
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

// Delete only the harmless placeholder settings option created by this skeleton.
// Do not delete production content, provider credentials, tables, or external data here.
delete_option( 'ob_engine_settings' );
