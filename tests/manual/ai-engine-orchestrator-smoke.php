<?php
/**
 * Manual smoke check for the AI Engine orchestrator without external calls.
 *
 * Run from repository root:
 * php tests/manual/ai-engine-orchestrator-smoke.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}

$GLOBALS['obe_provider_settings_option'] = array();

if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $value ) { return trim( (string) $value ); } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'get_current_user_id' ) ) { function get_current_user_id() { return 0; } }
if ( ! function_exists( 'current_time' ) ) { function current_time() { return gmdate( 'Y-m-d H:i:s' ); } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $name, $default = false ) { return isset( $GLOBALS['obe_provider_settings_option'][ $name ] ) ? $GLOBALS['obe_provider_settings_option'][ $name ] : $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $name, $value, $autoload = null ) { $GLOBALS['obe_provider_settings_option'][ $name ] = $value; return true; } }

require_once ABSPATH . 'includes/Support/Secret_Masker.php';
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

use OBEngine\AI\AI_Engine;
use OBEngine\AI\AI_Request;
use OBEngine\AI\AI_Request_Builder;
use OBEngine\AI\AI_Response;
use OBEngine\AI\AI_Response_Normalizer;
use OBEngine\AI\AI_Task_Profile_Resolver;
use OBEngine\AI\AI_Task_Type;
use OBEngine\AI\Model_Profile;
use OBEngine\AI\Structured_Output;
use OBEngine\Providers\Provider_Interface;
use OBEngine\Providers\Provider_Resolver;

function obe_ai_engine_smoke_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

final class OBE_AI_Engine_Smoke_Fake_Provider implements Provider_Interface {
	public function provider_id(): string { return 'fake'; }
	public function supports( string $capability ): bool { return in_array( $capability, array( self::CAPABILITY_TEXT_GENERATION, self::CAPABILITY_STRUCTURED_OUTPUT ), true ); }
	public function generate( AI_Request $request ): AI_Response {
		return AI_Response::completed( $request, array( 'provider' => 'fake', 'model' => 'fake-model', 'output_json' => array( 'title' => 'Smoke draft', 'content' => 'Review-first output.', 'status' => 'draft', 'safety_notes' => array( 'No external calls.' ) ), 'usage' => array( 'total_tokens' => 0 ), 'raw_response_redacted' => array( 'provider' => 'fake' ) ) );
	}
}

$resolver = new AI_Task_Profile_Resolver();
obe_ai_engine_smoke_assert( Model_Profile::BALANCED === $resolver->resolve_model_profile( AI_Task_Type::GENERATE_CONTENT_DRAFT ), 'generate_content_draft should default to balanced profile' );
obe_ai_engine_smoke_assert( Structured_Output::CONTENT_DRAFT_V1 === $resolver->resolve_output_schema( AI_Task_Type::GENERATE_CONTENT_DRAFT ), 'generate_content_draft should default to content_draft_v1 schema' );

$provider_resolver = new Provider_Resolver( array( 'fake' => new OBE_AI_Engine_Smoke_Fake_Provider() ), 'fake' );
$builder = new AI_Request_Builder( $resolver, $provider_resolver );
$request = $builder->from_task_data( array( 'task_type' => AI_Task_Type::GENERATE_CONTENT_DRAFT, 'input' => 'Create a review-first smoke draft.' ) );
$request_data = $request->to_array();
obe_ai_engine_smoke_assert( $request->is_valid(), 'builder should produce a valid request' );
obe_ai_engine_smoke_assert( Model_Profile::BALANCED === $request_data['model_profile'], 'builder should apply balanced model profile' );
obe_ai_engine_smoke_assert( Structured_Output::CONTENT_DRAFT_V1 === $request_data['output_schema'], 'builder should apply content_draft_v1 schema' );

$engine = new AI_Engine( $builder, $provider_resolver, new AI_Response_Normalizer() );
$response = $engine->run( array( 'task_type' => AI_Task_Type::GENERATE_CONTENT_DRAFT, 'input' => 'Create a review-first smoke draft.' ) );
obe_ai_engine_smoke_assert( $response->is_success(), 'AI_Engine::run should return completed response with fake provider' );
obe_ai_engine_smoke_assert( is_array( $response->get_output_json() ), 'normalizer should keep output_json as array' );
obe_ai_engine_smoke_assert( 'Smoke draft' === $response->get_output_json()['title'], 'fake response should be returned without external calls' );

echo "AI Engine orchestrator smoke check passed without external calls.\n";
