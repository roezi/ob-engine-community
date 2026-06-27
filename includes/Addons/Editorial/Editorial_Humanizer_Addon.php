<?php
/** Editorial Humanizer addon identity. */
namespace OBEngine\Addons\Editorial;
use OBEngine\Addons\Addon_Definition; use OBEngine\Addons\Addon_Scope; use OBEngine\Addons\Addon_Status; use OBEngine\Support\Capabilities;
defined( 'ABSPATH' ) || exit;
final class Editorial_Humanizer_Addon {
	public static function definition(): Addon_Definition { return Addon_Definition::from_array( array( 'id'=>'editorial_humanizer', 'name'=>__( 'Editorial Humanizer / Readability', 'ob-engine' ), 'description'=>__( 'Improves readability, tone, Bahasa Indonesia naturalness, and editorial clarity for Library draft candidates.', 'ob-engine' ), 'status'=>Addon_Status::ENABLED, 'scope'=>Addon_Scope::COMMUNITY, 'menu_slug'=>'ob-engine-humanizer', 'page_title'=>__( 'Humanizer', 'ob-engine' ), 'menu_title'=>__( 'Humanizer', 'ob-engine' ), 'capability'=>Capabilities::MANAGE, 'callback_class'=>'OBEngine\\Admin\\Editorial_Humanizer_Page', 'callback_method'=>'render', 'primary_action'=>__( 'Review draft', 'ob-engine' ), 'secondary_action'=>__( 'Save to Library', 'ob-engine' ), 'order'=>15 ) ); }
}
