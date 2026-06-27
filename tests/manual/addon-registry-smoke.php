<?php
/** Manual smoke check for addon registry without external APIs or DB. */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' ); }
$GLOBALS['obe_external_calls'] = 0;
if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return preg_replace( '/[^a-z0-9_\-]/', '', strtolower( (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $value ) { return (string) $value; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return max( 0, (int) $value ); } }
require_once ABSPATH . 'includes/Support/Capabilities.php';
require_once ABSPATH . 'includes/Addons/Addon_Status.php';
require_once ABSPATH . 'includes/Addons/Addon_Scope.php';
require_once ABSPATH . 'includes/Addons/Addon_Definition.php';
require_once ABSPATH . 'includes/Addons/AutoPost/Auto_Post_Addon.php';
require_once ABSPATH . 'includes/Addons/Editorial/Editorial_Humanizer_Addon.php';
require_once ABSPATH . 'includes/Addons/Addon_Registry.php';
use OBEngine\Addons\Addon_Definition; use OBEngine\Addons\Addon_Registry; use OBEngine\Addons\Addon_Scope; use OBEngine\Addons\Addon_Status; use OBEngine\Addons\AutoPost\Auto_Post_Addon;
function obe_addon_assert( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); } }
obe_addon_assert( Addon_Status::is_valid( Addon_Status::ENABLED ), 'enabled status should be valid.' );
obe_addon_assert( ! Addon_Status::is_valid( 'invalid' ), 'invalid status should be invalid.' );
obe_addon_assert( Addon_Scope::is_valid( Addon_Scope::COMMUNITY ), 'community scope should be valid.' );
obe_addon_assert( ! Addon_Scope::is_valid( 'secret' ), 'invalid scope should be invalid.' );
$invalid = new Addon_Definition( array( 'id' => 'Bad ID!', 'status' => 'bad', 'scope' => 'bad' ) );
obe_addon_assert( 'badid' === $invalid->get_id(), 'definition ID should be sanitized.' );
obe_addon_assert( Addon_Status::DISABLED === $invalid->get_status(), 'invalid status should normalize.' );
obe_addon_assert( Addon_Scope::COMMUNITY === $invalid->get_scope(), 'invalid scope should normalize.' );
$registry = new Addon_Registry();
$humanizer = $registry->get( 'editorial_humanizer' );
obe_addon_assert( $humanizer instanceof Addon_Definition, 'Editorial Humanizer should exist.' );
obe_addon_assert( $humanizer->is_enabled(), 'Editorial Humanizer should be enabled.' );
obe_addon_assert( $humanizer->is_community(), 'Editorial Humanizer should be community.' );
obe_addon_assert( 'ob-engine-humanizer' === $humanizer->get_menu_slug(), 'Editorial Humanizer should expose OBE submenu slug.' );
$auto = $registry->get( 'auto_post' );
obe_addon_assert( $auto instanceof Addon_Definition, 'Auto Post should exist.' );
obe_addon_assert( $auto->is_enabled(), 'Auto Post should be enabled.' );
obe_addon_assert( $auto->is_community(), 'Auto Post should be community.' );
foreach ( array( 'seo_review', 'performance_review', 'translation_plan' ) as $id ) { $addon = $registry->get( $id ); obe_addon_assert( $addon instanceof Addon_Definition, "$id should exist." ); obe_addon_assert( Addon_Status::COMING_SOON === $addon->get_status(), "$id should be coming soon." ); obe_addon_assert( Addon_Scope::COMMUNITY === $addon->get_scope(), "$id should be community." ); }
$workflow = $registry->get( 'workflow_pro' ); obe_addon_assert( Addon_Status::PRO === $workflow->get_status(), 'Workflow Pro should be pro status.' ); obe_addon_assert( Addon_Scope::PRO === $workflow->get_scope(), 'Workflow Pro should be pro scope.' );
$private = $registry->get( 'private_project_adapter' ); obe_addon_assert( Addon_Scope::PRIVATE_ADAPTER === $private->get_scope(), 'Private Project Adapter should be private_adapter scope.' );
obe_addon_assert( 'auto_post' === Auto_Post_Addon::definition()->get_id(), 'Auto_Post_Addon definition should return auto_post.' );
obe_addon_assert( 0 === $GLOBALS['obe_external_calls'], 'no external calls should be made.' );
echo "Addon Registry smoke passed without external calls.\n";
