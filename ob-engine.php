<?php
/**
 * Plugin Name: OB Engine Community
 * Plugin URI: https://github.com/roezi/ob-engine-community
 * Description: Safety-first WordPress automation foundation for OB Engine Community.
 * Version: 0.4.0
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

define( 'OB_ENGINE_VERSION', '0.4.0' );
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
require_once OB_ENGINE_PATH . 'includes/Providers/Provider_Interface.php';
require_once OB_ENGINE_PATH . 'includes/Providers/Provider_Result.php';
require_once OB_ENGINE_PATH . 'includes/Providers/Provider_Settings.php';
require_once OB_ENGINE_PATH . 'includes/Admin/Dashboard_Page.php';
require_once OB_ENGINE_PATH . 'includes/Admin/Settings_Page.php';
require_once OB_ENGINE_PATH . 'includes/Admin/Admin_Menu.php';
require_once OB_ENGINE_PATH . 'includes/Core/Plugin.php';

add_action(
	'plugins_loaded',
	static function () {
		\OBEngine\Core\Plugin::instance()->register();
	}
);
