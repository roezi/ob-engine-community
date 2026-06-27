<?php
/** Manual smoke check for Field Mapping + Validation without external APIs or DB. */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' ); }
if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return max( 0, (int) $value ); } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'wp_json_encode' ) ) { function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); } }
require_once ABSPATH . 'includes/Activity/Activity_Redactor.php';
require_once ABSPATH . 'includes/Activity/Activity_Action.php';
require_once ABSPATH . 'includes/Activity/Activity_Status.php';
require_once ABSPATH . 'includes/Activity/Activity_Object_Type.php';
require_once ABSPATH . 'includes/Activity/Activity_Repository.php';
require_once ABSPATH . 'includes/Activity/Activity_Logger.php';
require_once ABSPATH . 'includes/Library/Library_Type.php';
require_once ABSPATH . 'includes/Library/Library_Status.php';
require_once ABSPATH . 'includes/Library/Library_Repository.php';
require_once ABSPATH . 'includes/Sources/Source_Preview_Redactor.php';
require_once ABSPATH . 'includes/Sources/Field_Target.php';
require_once ABSPATH . 'includes/Sources/Field_Mapping_Item.php';
require_once ABSPATH . 'includes/Sources/Field_Mapping_Plan.php';
require_once ABSPATH . 'includes/Sources/Field_Mapping_Validator.php';
require_once ABSPATH . 'includes/Sources/Field_Mapping_Service.php';
use OBEngine\Library\Library_Status;
use OBEngine\Library\Library_Type;
use OBEngine\Sources\Field_Mapping_Item;
use OBEngine\Sources\Field_Mapping_Plan;
use OBEngine\Sources\Field_Mapping_Service;
use OBEngine\Sources\Field_Mapping_Validator;
use OBEngine\Sources\Field_Target;
function obe_field_mapping_assert( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); } }
obe_field_mapping_assert( Field_Target::is_valid( Field_Target::TITLE ), 'Title target should be valid.' );
obe_field_mapping_assert( Field_Target::suggested_target_for_source_column( 'headline' ) === Field_Target::TITLE, 'Headline should map to title.' );
obe_field_mapping_assert( Field_Target::suggested_target_for_source_column( 'image_url' ) === Field_Target::IMAGE_URL, 'image_url should map to image_url.' );
obe_field_mapping_assert( Field_Target::suggested_target_for_source_column( 'unknown_public_column' ) === Field_Target::NOTES, 'Unknown columns should map to notes.' );
$item = new Field_Mapping_Item( array( 'source_field' => 'Bad', 'target_field' => 'not-real', 'sample_value_redacted' => str_repeat( 'a', 400 ) ) );
obe_field_mapping_assert( $item->is_ignored(), 'Invalid target should become ignore.' );
obe_field_mapping_assert( Field_Mapping_Plan::empty()->get_validation_status() === 'not_checked', 'Empty plan should be not_checked.' );
$validator = new Field_Mapping_Validator();
$passed = $validator->validate( new Field_Mapping_Plan( array( 'mappings' => array( array( 'source_field' => 'title', 'target_field' => Field_Target::TITLE ), array( 'source_field' => 'body', 'target_field' => Field_Target::CONTENT ) ) ) ) );
obe_field_mapping_assert( $passed->get_validation_status() === 'passed', 'Title + content should pass.' );
$summary = $validator->validate( new Field_Mapping_Plan( array( 'mappings' => array( array( 'source_field' => 'title', 'target_field' => Field_Target::TITLE ), array( 'source_field' => 'summary', 'target_field' => Field_Target::SUMMARY ) ) ) ) );
obe_field_mapping_assert( in_array( $summary->get_validation_status(), array( 'passed', 'warning' ), true ), 'Title + summary should pass or warn.' );
$failed = $validator->validate( new Field_Mapping_Plan( array( 'mappings' => array( array( 'source_field' => 'body', 'target_field' => Field_Target::CONTENT ) ) ) ) );
obe_field_mapping_assert( $failed->get_validation_status() === 'failed', 'Missing title should fail.' );
$duplicate = $validator->validate( new Field_Mapping_Plan( array( 'mappings' => array( array( 'source_field' => 'title', 'target_field' => Field_Target::TITLE ), array( 'source_field' => 'name', 'target_field' => Field_Target::TITLE ), array( 'source_field' => 'body', 'target_field' => Field_Target::CONTENT ) ) ) ) );
obe_field_mapping_assert( $duplicate->get_validation_status() === 'warning', 'Duplicate title target should warn.' );
$service = new Field_Mapping_Service();
$library_data = $service->build_library_data( $passed, array() );
obe_field_mapping_assert( $library_data['type'] === Library_Type::FIELD_MAPPING, 'Library type should be field_mapping.' );
obe_field_mapping_assert( $library_data['status'] === Library_Status::NEEDS_REVIEW, 'Passed mapping should default to needs_review.' );
$payload = $service->mapping_payload_redacted( new Field_Mapping_Plan( array( 'mappings' => array( array( 'source_field' => 'title', 'target_field' => Field_Target::TITLE, 'sample_value_redacted' => '[REDACTED_SECRET]' ) ) ) ) );
obe_field_mapping_assert( false === strpos( $payload, 'raw private source input secret' ), 'Payload should not contain raw source input.' );
echo "Field Mapping + Validation smoke passed.\n";
