<?php
/** Auto Post addon identity. */
namespace OBEngine\Addons\AutoPost;
use OBEngine\Addons\Addon_Definition;
use OBEngine\Addons\Addon_Scope;
use OBEngine\Addons\Addon_Status;
use OBEngine\Support\Capabilities;
defined( 'ABSPATH' ) || exit;
final class Auto_Post_Addon {
	public static function definition(): Addon_Definition {
		return Addon_Definition::from_array( array( 'id' => 'auto_post', 'name' => __( 'Auto Post / Import', 'ob-engine' ), 'description' => __( 'Converts source previews and mappings into reviewed Library plans and drafts.', 'ob-engine' ), 'status' => Addon_Status::ENABLED, 'scope' => Addon_Scope::COMMUNITY, 'menu_slug' => 'ob-engine-auto-post', 'page_title' => __( 'Auto Post / Import', 'ob-engine' ), 'menu_title' => __( 'Auto Post / Import', 'ob-engine' ), 'capability' => Capabilities::MANAGE, 'callback_class' => 'OBEngine\\Admin\\Auto_Post_Import_Page', 'callback_method' => 'render', 'primary_action' => __( 'Generate plan', 'ob-engine' ), 'secondary_action' => __( 'Run dry-run', 'ob-engine' ), 'order' => 10 ) );
	}
}
