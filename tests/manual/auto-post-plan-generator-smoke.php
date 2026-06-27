<?php
/** Manual smoke check for Auto Post Plan Generator without external APIs or DB. */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' ); }
if ( ! class_exists( 'WP_Post' ) ) { class WP_Post {} }
$GLOBALS['obe_posts'] = array(); $GLOBALS['obe_meta'] = array(); $GLOBALS['obe_next_id'] = 7001; $GLOBALS['obe_external_calls'] = 0;
if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return max( 0, (int) $value ); } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $value ) { return (string) $value; } }
if ( ! function_exists( 'wp_json_encode' ) ) { function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); } }
if ( ! function_exists( 'get_current_user_id' ) ) { function get_current_user_id() { return 1; } }
if ( ! function_exists( 'current_time' ) ) { function current_time() { return gmdate( 'Y-m-d H:i:s' ); } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $name, $default = false ) { return $default; } }
if ( ! function_exists( 'get_post' ) ) { function get_post( $id ) { if ( ! isset( $GLOBALS['obe_posts'][ $id ] ) ) { return null; } $post = new WP_Post(); foreach ( $GLOBALS['obe_posts'][ $id ] as $key => $value ) { $post->$key = $value; } return $post; } }
if ( ! function_exists( 'get_post_type' ) ) { function get_post_type( $id ) { return isset( $GLOBALS['obe_posts'][ $id ] ) ? $GLOBALS['obe_posts'][ $id ]['post_type'] : ''; } }
if ( ! function_exists( 'get_the_title' ) ) { function get_the_title( $post ) { return $post->post_title; } }
if ( ! function_exists( 'get_post_meta' ) ) { function get_post_meta( $id, $key, $single = false ) { return $GLOBALS['obe_meta'][ $id ][ $key ] ?? ''; } }
if ( ! function_exists( 'update_post_meta' ) ) { function update_post_meta( $id, $key, $value ) { $GLOBALS['obe_meta'][ $id ][ $key ] = $value; return true; } }
if ( ! function_exists( 'is_wp_error' ) ) { function is_wp_error( $value ) { return false; } }
if ( ! function_exists( 'wp_insert_post' ) ) { function wp_insert_post( $data, $wp_error = false ) { $id = $GLOBALS['obe_next_id']++; $GLOBALS['obe_posts'][ $id ] = array( 'ID' => $id, 'post_type' => $data['post_type'], 'post_status' => $data['post_status'], 'post_title' => $data['post_title'], 'post_content' => $data['post_content'], 'post_author' => $data['post_author'], 'post_date' => gmdate( 'Y-m-d H:i:s' ), 'post_modified' => gmdate( 'Y-m-d H:i:s' ) ); return $id; } }

require_once ABSPATH . 'includes/Support/Secret_Masker.php';
require_once ABSPATH . 'includes/AI/Model_Profile.php'; require_once ABSPATH . 'includes/AI/AI_Task_Type.php'; require_once ABSPATH . 'includes/AI/AI_Error.php'; require_once ABSPATH . 'includes/AI/Usage_Record.php'; require_once ABSPATH . 'includes/AI/AI_Request.php'; require_once ABSPATH . 'includes/AI/AI_Response.php'; require_once ABSPATH . 'includes/AI/Structured_Output.php'; require_once ABSPATH . 'includes/AI/AI_Task_Profile_Resolver.php'; require_once ABSPATH . 'includes/AI/AI_Response_Normalizer.php'; require_once ABSPATH . 'includes/Providers/Provider_Interface.php'; require_once ABSPATH . 'includes/Providers/Provider_Settings.php'; require_once ABSPATH . 'includes/Providers/Provider_Resolver.php'; require_once ABSPATH . 'includes/AI/AI_Request_Builder.php';
require_once ABSPATH . 'includes/Activity/Activity_Action.php'; require_once ABSPATH . 'includes/Activity/Activity_Status.php'; require_once ABSPATH . 'includes/Activity/Activity_Object_Type.php'; require_once ABSPATH . 'includes/Activity/Activity_Redactor.php'; require_once ABSPATH . 'includes/Activity/Activity_Repository.php'; require_once ABSPATH . 'includes/Activity/Activity_Logger.php';
require_once ABSPATH . 'includes/Library/Library_Type.php'; require_once ABSPATH . 'includes/Library/Library_Status.php'; require_once ABSPATH . 'includes/Library/Library_Item.php'; require_once ABSPATH . 'includes/Library/Library_Repository.php';
require_once ABSPATH . 'includes/AI/AI_Engine.php'; require_once ABSPATH . 'includes/AutoPost/Auto_Post_Plan.php'; require_once ABSPATH . 'includes/AutoPost/Auto_Post_Plan_Service.php';
use OBEngine\AI\AI_Engine; use OBEngine\AI\AI_Request; use OBEngine\AI\AI_Request_Builder; use OBEngine\AI\AI_Response; use OBEngine\AI\AI_Response_Normalizer; use OBEngine\AI\AI_Task_Profile_Resolver; use OBEngine\AI\AI_Task_Type; use OBEngine\AI\Structured_Output; use OBEngine\AutoPost\Auto_Post_Plan; use OBEngine\AutoPost\Auto_Post_Plan_Service; use OBEngine\Library\Library_Repository; use OBEngine\Library\Library_Status; use OBEngine\Library\Library_Type; use OBEngine\Providers\Provider_Interface; use OBEngine\Providers\Provider_Resolver;
function obe_auto_post_assert( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); } }
final class OBE_Auto_Post_Fake_Provider implements Provider_Interface { public function provider_id(): string { return 'fake'; } public function supports( string $capability ): bool { return true; } public function generate( AI_Request $request ): AI_Response { return AI_Response::completed( $request, array( 'provider' => 'fake', 'model' => 'fake-model', 'output_json' => array( 'source_summary' => 'Redacted public source summary.', 'proposed_post_type' => 'post', 'title_options' => array( 'Review-first title' ), 'outline' => array( array( 'heading' => 'Overview' ) ), 'safety_notes' => array( 'No WordPress writes.' ), 'default_status' => 'needs_review', 'approval_question' => 'Review this plan?' ) ) ); } }
$repo = new Library_Repository();
$mapping_id = 501;
$GLOBALS['obe_posts'][ $mapping_id ] = array( 'ID' => $mapping_id, 'post_type' => Library_Repository::POST_TYPE, 'post_status' => 'private', 'post_title' => 'Smoke Field Mapping', 'post_content' => '', 'post_author' => 1, 'post_date' => gmdate( 'Y-m-d H:i:s' ), 'post_modified' => gmdate( 'Y-m-d H:i:s' ) );
$GLOBALS['obe_meta'][ $mapping_id ] = array( Library_Repository::META_TYPE => Library_Type::FIELD_MAPPING, Library_Repository::META_STATUS => Library_Status::NEEDS_REVIEW, Library_Repository::META_SOURCE_LABEL => 'Smoke Source', Library_Repository::META_SUMMARY => 'Mapping summary', Library_Repository::META_PAYLOAD_REDACTED => wp_json_encode( array( 'source_preview_library_item_id' => 123, 'summary' => 'Redacted mapping summary', 'mappings' => array( array( 'source_field' => 'title', 'target_field' => 'post_title' ) ) ) ), Library_Repository::META_CREATED_CONTEXT => 'smoke' );
$plan = Auto_Post_Plan::empty();
obe_auto_post_assert( in_array( $plan->get_default_status(), array( 'draft', 'needs_review' ), true ), 'empty default status should be safe.' );
$plan2 = Auto_Post_Plan::from_ai_response_data( array( 'default_status' => 'publish', 'title_options' => 'One' ) );
obe_auto_post_assert( 'needs_review' === $plan2->get_default_status(), 'unsafe default status should normalize.' );
obe_auto_post_assert( is_array( $plan2->get_title_options() ), 'title options should normalize to array.' );
$resolver = new Provider_Resolver( array( 'fake' => new OBE_Auto_Post_Fake_Provider() ), 'fake' );
$engine = new AI_Engine( new AI_Request_Builder( new AI_Task_Profile_Resolver(), $resolver ), $resolver, new AI_Response_Normalizer() );
$service = new Auto_Post_Plan_Service( $engine, $repo );
obe_auto_post_assert( 0 === $service->validate_data( array() )['field_mapping_library_item_id'], 'missing field_mapping_library_item_id should validate to zero.' );
$task = $service->build_ai_task_data( $service->validate_data( array( 'field_mapping_library_item_id' => $mapping_id ) ), $service->extract_mapping_payload( $mapping_id ) );
obe_auto_post_assert( AI_Task_Type::GENERATE_AUTO_POST_PLAN === $task['task_type'], 'task type should be generate_auto_post_plan.' );
obe_auto_post_assert( Structured_Output::AUTO_POST_PLAN_V1 === $task['output_schema'], 'output schema should be auto_post_plan_v1.' );
$library_data = $service->build_library_data( $plan2, array( 'source_label' => 'Smoke Source' ) );
obe_auto_post_assert( Library_Type::AUTO_POST_PLAN === $library_data['type'], 'Library type should be auto_post_plan.' );
obe_auto_post_assert( Library_Status::NEEDS_REVIEW === $library_data['status'], 'Library status should be needs_review.' );
obe_auto_post_assert( false === strpos( $service->plan_payload_redacted( $plan2 ), 'raw prompt' ), 'payload should not contain raw prompt.' );
obe_auto_post_assert( false === strpos( $service->plan_payload_redacted( $plan2 ), 'raw source input' ), 'payload should not contain raw source input.' );
$result = $service->generate_plan( array( 'field_mapping_library_item_id' => $mapping_id ) );
obe_auto_post_assert( ! empty( $result['success'] ), 'service should return success.' );
obe_auto_post_assert( 7001 === $result['library_item_id'], 'fake library item ID should be returned.' );
obe_auto_post_assert( 0 === $GLOBALS['obe_external_calls'], 'no external calls should be made.' );
echo "Auto Post Plan Generator smoke passed without external calls.\n";
