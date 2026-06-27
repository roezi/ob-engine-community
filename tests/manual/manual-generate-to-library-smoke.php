<?php
/**
 * Manual smoke check for Manual Generate to Library without external calls.
 *
 * Run from repository root:
 * php tests/manual/manual-generate-to-library-smoke.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}

$GLOBALS['obe_posts'] = array();
$GLOBALS['obe_post_meta'] = array();
$GLOBALS['obe_next_post_id'] = 9001;
$GLOBALS['obe_provider_settings_option'] = array();

if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'wp_strip_all_tags' ) ) { function wp_strip_all_tags( $value ) { return strip_tags( (string) $value ); } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $value ) { return (string) $value; } }
if ( ! function_exists( 'wp_json_encode' ) ) { function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'get_current_user_id' ) ) { function get_current_user_id() { return 7; } }
if ( ! function_exists( 'current_time' ) ) { function current_time() { return gmdate( 'Y-m-d H:i:s' ); } }
if ( ! function_exists( 'wp_generate_uuid4' ) ) { function wp_generate_uuid4() { return '00000000-0000-4000-8000-000000000000'; } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $name, $default = false ) { return isset( $GLOBALS['obe_provider_settings_option'][ $name ] ) ? $GLOBALS['obe_provider_settings_option'][ $name ] : $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $name, $value, $autoload = null ) { $GLOBALS['obe_provider_settings_option'][ $name ] = $value; return true; } }
if ( ! function_exists( 'is_wp_error' ) ) { function is_wp_error( $value ) { return $value instanceof WP_Error; } }
if ( ! class_exists( 'WP_Error' ) ) { class WP_Error { public function __construct( $code = '', $message = '' ) {} } }
if ( ! function_exists( 'wp_insert_post' ) ) { function wp_insert_post( $data, $wp_error = false ) { $id = $GLOBALS['obe_next_post_id']++; $data['ID'] = $id; $GLOBALS['obe_posts'][ $id ] = (object) $data; return $id; } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['obe_post_meta'][ $id ][ $key ] = $value; return true; } }

class OBE_Manual_Generate_Smoke_WPDB {
	public $prefix = 'wp_';
	public $insert_id = 1;
	public function insert( $table, $data, $formats = array() ) { $this->insert_id++; return true; }
	public function prepare( $query ) { return $query; }
	public function get_var( $query = null ) { return null; }
	public function get_row( $query = null ) { return null; }
	public function get_results( $query = null ) { return array(); }
	public function esc_like( $text ) { return addcslashes( $text, '_%' ); }
}
$GLOBALS['wpdb'] = new OBE_Manual_Generate_Smoke_WPDB();

require_once ABSPATH . 'includes/Support/Secret_Masker.php';
require_once ABSPATH . 'includes/Support/Capabilities.php';
require_once ABSPATH . 'includes/AI/Model_Profile.php';
require_once ABSPATH . 'includes/AI/AI_Task_Type.php';
require_once ABSPATH . 'includes/AI/AI_Error.php';
require_once ABSPATH . 'includes/AI/Usage_Record.php';
require_once ABSPATH . 'includes/AI/AI_Request.php';
require_once ABSPATH . 'includes/AI/AI_Response.php';
require_once ABSPATH . 'includes/AI/Structured_Output.php';
require_once ABSPATH . 'includes/AI/AI_Task_Profile_Resolver.php';
require_once ABSPATH . 'includes/AI/AI_Response_Normalizer.php';
require_once ABSPATH . 'includes/Providers/Provider_Interface.php';
require_once ABSPATH . 'includes/Providers/OpenAI/OpenAI_Error_Mapper.php';
require_once ABSPATH . 'includes/Providers/OpenAI/OpenAI_Responses_Client.php';
require_once ABSPATH . 'includes/Providers/OpenAI/OpenAI_Provider.php';
require_once ABSPATH . 'includes/Providers/Provider_Settings.php';
require_once ABSPATH . 'includes/Providers/Provider_Resolver.php';
require_once ABSPATH . 'includes/AI/AI_Request_Builder.php';
require_once ABSPATH . 'includes/Activity/Activity_Action.php';
require_once ABSPATH . 'includes/Activity/Activity_Status.php';
require_once ABSPATH . 'includes/Activity/Activity_Object_Type.php';
require_once ABSPATH . 'includes/Activity/Activity_Redactor.php';
require_once ABSPATH . 'includes/Activity/Activity_Item.php';
require_once ABSPATH . 'includes/Activity/Activity_Repository.php';
require_once ABSPATH . 'includes/Activity/Activity_Logger.php';
require_once ABSPATH . 'includes/AI/AI_Engine.php';
require_once ABSPATH . 'includes/Library/Library_Type.php';
require_once ABSPATH . 'includes/Library/Library_Status.php';
require_once ABSPATH . 'includes/Library/Library_Repository.php';
require_once ABSPATH . 'includes/AI/Manual_Generate_Service.php';

use OBEngine\AI\AI_Engine;
use OBEngine\AI\AI_Request;
use OBEngine\AI\AI_Request_Builder;
use OBEngine\AI\AI_Response;
use OBEngine\AI\AI_Response_Normalizer;
use OBEngine\AI\AI_Task_Type;
use OBEngine\AI\Manual_Generate_Service;
use OBEngine\Library\Library_Repository;
use OBEngine\Library\Library_Status;
use OBEngine\Library\Library_Type;
use OBEngine\Providers\Provider_Interface;
use OBEngine\Providers\Provider_Resolver;

function obe_manual_generate_smoke_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

final class OBE_Manual_Generate_Smoke_Fake_Provider implements Provider_Interface {
	public $calls = 0;
	public function provider_id(): string { return 'fake'; }
	public function supports( string $capability ): bool { return in_array( $capability, array( self::CAPABILITY_TEXT_GENERATION, self::CAPABILITY_STRUCTURED_OUTPUT ), true ); }
	public function generate( AI_Request $request ): AI_Response {
		$this->calls++;
		return AI_Response::completed( $request, array( 'provider' => 'fake', 'model' => 'fake-model', 'output_json' => array( 'summary' => 'Safe JSON content', 'recommendations' => array( 'Review before writing.' ), 'safety_notes' => array( 'No public post created.' ) ), 'usage' => array( 'total_tokens' => 0 ) ) );
	}
}

$provider = new OBE_Manual_Generate_Smoke_Fake_Provider();
$resolver = new Provider_Resolver( array( 'fake' => $provider ), 'fake' );
$engine = new AI_Engine( new AI_Request_Builder( null, $resolver ), $resolver, new AI_Response_Normalizer() );
$service = new Manual_Generate_Service( $engine, new Library_Repository() );

$invalid = $service->validate_data( array() );
obe_manual_generate_smoke_assert( ! empty( $invalid['errors'] ), 'required fields should fail validation' );
obe_manual_generate_smoke_assert( Library_Type::SEO_REVIEW === $service->library_type_for_task( AI_Task_Type::GENERATE_SEO_REVIEW ), 'SEO review task should map to seo_review Library type' );

$data = array( 'task_type' => AI_Task_Type::REVIEW_CONTENT, 'title' => 'Smoke Review', 'input' => 'RAW INPUT SHOULD NOT APPEAR', 'instructions' => 'RAW INSTRUCTIONS SHOULD NOT APPEAR', 'output_schema' => '', 'source_label' => 'Smoke', 'library_type' => Library_Type::CONTENT_REVIEW );
$response = AI_Response::from_array( array( 'request_id' => 'req_smoke', 'provider' => 'fake', 'model' => 'fake-model', 'status' => AI_Response::STATUS_COMPLETED, 'output_json' => array( 'summary' => 'JSON-only output' ), 'usage' => array( 'total_tokens' => 0 ) ) );
$library_data = $service->build_library_data( $response, $service->validate_data( $data ) );
obe_manual_generate_smoke_assert( Library_Status::NEEDS_REVIEW === $library_data['status'], 'Library data should default to needs_review' );
obe_manual_generate_smoke_assert( false !== strpos( $library_data['content'], 'JSON-only output' ), 'output_json should be converted to safe content' );
obe_manual_generate_smoke_assert( false === strpos( $library_data['payload_redacted'], 'RAW INPUT SHOULD NOT APPEAR' ), 'raw input should not be included in payload_redacted' );
obe_manual_generate_smoke_assert( false === strpos( $library_data['payload_redacted'], 'RAW INSTRUCTIONS SHOULD NOT APPEAR' ), 'raw instructions should not be included in payload_redacted' );

$result = $service->generate_to_library( $data );
obe_manual_generate_smoke_assert( ! empty( $result['success'] ), 'service should return success' );
obe_manual_generate_smoke_assert( 9001 === $result['library_item_id'], 'service should return fake library item ID' );
obe_manual_generate_smoke_assert( 1 === $provider->calls, 'fake provider should be called exactly once after explicit service invocation' );
obe_manual_generate_smoke_assert( 'private' === $GLOBALS['obe_posts'][9001]->post_status, 'Library item should be private' );
obe_manual_generate_smoke_assert( Library_Status::NEEDS_REVIEW === $GLOBALS['obe_post_meta'][9001][Library_Repository::META_STATUS], 'created Library item should be needs_review' );

foreach ( $GLOBALS['obe_posts'] as $post ) {
	obe_manual_generate_smoke_assert( 'obe_library_item' === $post->post_type, 'no public WordPress posts should be created' );
}

echo "Manual Generate to Library smoke check passed without external calls.\n";
