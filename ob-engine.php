<?php
/**
 * Plugin Name: OB Engine Community
 * Plugin URI: https://github.com/roezi/ob-engine-community
 * Description: Safety-first WordPress automation foundation for OB Engine Community.
 * Version: 0.14.0
 * Author: Optimized Builder
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ob-engine
 * Requires at least: 6.4
 * Requires PHP: 7.4
 *
 * @package OBEngine
 */

defined( 'ABSPATH' ) || exit;

define( 'OB_ENGINE_VERSION', '0.14.0' );
define( 'OB_ENGINE_FILE', __FILE__ );
define( 'OB_ENGINE_PATH', plugin_dir_path( __FILE__ ) );
define( 'OB_ENGINE_URL', plugin_dir_url( __FILE__ ) );

require_once OB_ENGINE_PATH . 'includes/Support/Capabilities.php';
require_once OB_ENGINE_PATH . 'includes/Support/Secret_Masker.php';
require_once OB_ENGINE_PATH . 'includes/Support/View.php';
require_once OB_ENGINE_PATH . 'includes/AI/Model_Profile.php';
require_once OB_ENGINE_PATH . 'includes/AI/AI_Task_Type.php';
require_once OB_ENGINE_PATH . 'includes/AI/AI_Error.php';
require_once OB_ENGINE_PATH . 'includes/AI/Usage_Record.php';
require_once OB_ENGINE_PATH . 'includes/AI/AI_Request.php';
require_once OB_ENGINE_PATH . 'includes/AI/AI_Response.php';
require_once OB_ENGINE_PATH . 'includes/AI/Structured_Output.php';
require_once OB_ENGINE_PATH . 'includes/AI/AI_Task_Profile_Resolver.php';
require_once OB_ENGINE_PATH . 'includes/AI/AI_Response_Normalizer.php';
require_once OB_ENGINE_PATH . 'includes/Providers/Provider_Interface.php';
require_once OB_ENGINE_PATH . 'includes/Providers/Provider_Result.php';
require_once OB_ENGINE_PATH . 'includes/Providers/OpenAI/OpenAI_Error_Mapper.php';
require_once OB_ENGINE_PATH . 'includes/Providers/OpenAI/OpenAI_Responses_Client.php';
require_once OB_ENGINE_PATH . 'includes/Providers/OpenAI/OpenAI_Provider.php';
require_once OB_ENGINE_PATH . 'includes/Providers/Provider_Settings.php';
require_once OB_ENGINE_PATH . 'includes/Providers/Provider_Resolver.php';
require_once OB_ENGINE_PATH . 'includes/AI/AI_Request_Builder.php';
require_once OB_ENGINE_PATH . 'includes/AI/AI_Engine.php';
require_once OB_ENGINE_PATH . 'includes/AI/Manual_Generate_Service.php';
require_once OB_ENGINE_PATH . 'includes/Activity/Activity_Action.php';
require_once OB_ENGINE_PATH . 'includes/Activity/Activity_Status.php';
require_once OB_ENGINE_PATH . 'includes/Activity/Activity_Object_Type.php';
require_once OB_ENGINE_PATH . 'includes/Activity/Activity_Item.php';
require_once OB_ENGINE_PATH . 'includes/Activity/Activity_Redactor.php';
require_once OB_ENGINE_PATH . 'includes/Activity/Activity_Repository.php';
require_once OB_ENGINE_PATH . 'includes/Activity/Activity_Logger.php';
require_once OB_ENGINE_PATH . 'includes/Activity/Activity_Admin_Page.php';
require_once OB_ENGINE_PATH . 'includes/Library/Library_Type.php';
require_once OB_ENGINE_PATH . 'includes/Library/Library_Status.php';
require_once OB_ENGINE_PATH . 'includes/Library/Library_Repository.php';
require_once OB_ENGINE_PATH . 'includes/Library/Library_Item.php';
require_once OB_ENGINE_PATH . 'includes/Approval/Approval_Status.php';
require_once OB_ENGINE_PATH . 'includes/Approval/Approval_Record.php';
require_once OB_ENGINE_PATH . 'includes/Approval/Approval_Repository.php';
require_once OB_ENGINE_PATH . 'includes/Approval/Approval_Service.php';
require_once OB_ENGINE_PATH . 'includes/Safety/Safety_Status.php';
require_once OB_ENGINE_PATH . 'includes/Safety/Safety_Result.php';
require_once OB_ENGINE_PATH . 'includes/Safety/Write_Target.php';
require_once OB_ENGINE_PATH . 'includes/Safety/Dry_Run_Result.php';
require_once OB_ENGINE_PATH . 'includes/Safety/Dry_Run_Repository.php';
require_once OB_ENGINE_PATH . 'includes/Safety/Dry_Run_Service.php';
require_once OB_ENGINE_PATH . 'includes/Writing/Write_Status.php';
require_once OB_ENGINE_PATH . 'includes/Writing/Write_Result.php';
require_once OB_ENGINE_PATH . 'includes/Writing/Write_Record.php';
require_once OB_ENGINE_PATH . 'includes/Writing/Write_Repository.php';
require_once OB_ENGINE_PATH . 'includes/Writing/WordPress_Draft_Writer.php';
require_once OB_ENGINE_PATH . 'includes/Writing/Write_Draft_Service.php';
require_once OB_ENGINE_PATH . 'includes/Library/Library_Admin_Page.php';
require_once OB_ENGINE_PATH . 'includes/Sources/Source_Type.php';
require_once OB_ENGINE_PATH . 'includes/Sources/Source_Preview.php';
require_once OB_ENGINE_PATH . 'includes/Sources/Source_Preview_Redactor.php';
require_once OB_ENGINE_PATH . 'includes/Sources/Source_Preview_Parser.php';
require_once OB_ENGINE_PATH . 'includes/Sources/Source_Intake_Service.php';
require_once OB_ENGINE_PATH . 'includes/Admin/Dashboard_Page.php';
require_once OB_ENGINE_PATH . 'includes/Admin/Settings_Page.php';
require_once OB_ENGINE_PATH . 'includes/Admin/Manual_Generate_Page.php';
require_once OB_ENGINE_PATH . 'includes/Admin/Auto_Post_Import_Page.php';
require_once OB_ENGINE_PATH . 'includes/Admin/Admin_Menu.php';
require_once OB_ENGINE_PATH . 'includes/Core/Plugin.php';

register_activation_hook(
	__FILE__,
	static function () {
		( new \OBEngine\Activity\Activity_Repository() )->create_table();
	}
);

add_action(
	'plugins_loaded',
	static function () {
		\OBEngine\Core\Plugin::instance()->register();
	}
);
