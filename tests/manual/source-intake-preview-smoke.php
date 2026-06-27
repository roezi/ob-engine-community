<?php
/** Manual smoke check for Source Intake Preview without external APIs or DB. */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' ); }
if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'wp_json_encode' ) ) { function wp_json_encode( $value, $flags = 0 ) { return json_encode( $value, $flags ); } }
if ( ! function_exists( 'is_wp_error' ) ) { function is_wp_error( $value ) { return false; } }
require_once ABSPATH . 'includes/Activity/Activity_Redactor.php';
require_once ABSPATH . 'includes/Library/Library_Type.php';
require_once ABSPATH . 'includes/Library/Library_Status.php';
require_once ABSPATH . 'includes/Library/Library_Repository.php';
require_once ABSPATH . 'includes/Activity/Activity_Action.php';
require_once ABSPATH . 'includes/Activity/Activity_Status.php';
require_once ABSPATH . 'includes/Activity/Activity_Object_Type.php';
require_once ABSPATH . 'includes/Activity/Activity_Repository.php';
require_once ABSPATH . 'includes/Activity/Activity_Logger.php';
require_once ABSPATH . 'includes/Sources/Source_Type.php';
require_once ABSPATH . 'includes/Sources/Source_Preview.php';
require_once ABSPATH . 'includes/Sources/Source_Preview_Redactor.php';
require_once ABSPATH . 'includes/Sources/Source_Preview_Parser.php';
require_once ABSPATH . 'includes/Sources/Source_Intake_Service.php';
use OBEngine\Library\Library_Status;
use OBEngine\Library\Library_Type;
use OBEngine\Sources\Source_Intake_Service;
use OBEngine\Sources\Source_Preview;
use OBEngine\Sources\Source_Preview_Parser;
use OBEngine\Sources\Source_Preview_Redactor;
use OBEngine\Sources\Source_Type;
function obe_source_smoke_assert( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: $message\n" ); exit( 1 ); } }
obe_source_smoke_assert( Source_Type::is_valid( Source_Type::CSV_TEXT ), 'CSV source type should be valid.' );
obe_source_smoke_assert( Source_Preview::empty()->get_item_count() === 0, 'Empty preview should have zero items.' );
$parser = new Source_Preview_Parser();
$csv = $parser->parse_csv_text( "title,url\nOne,https://private.example.test/a\nTwo,token=abcdefghijklmnopqrstuvwxyz1234567890" );
obe_source_smoke_assert( $csv->get_item_count() === 2, 'CSV should count two data rows.' );
obe_source_smoke_assert( in_array( 'title', $csv->get_columns(), true ), 'CSV headers should become columns.' );
$json = $parser->parse_json_text( '[{"title":"One","email":"person@example.com"},{"title":"Two"}]' );
obe_source_smoke_assert( $json->get_item_count() === 2, 'JSON array should count two objects.' );
$text = $parser->parse_plain_text( "Line one\nLine two" );
obe_source_smoke_assert( $text->get_item_count() === 2, 'Plain text should count non-empty lines.' );
$redactor = new Source_Preview_Redactor();
$redacted = $redactor->redact_value( 'Bearer abcdefghijklmnopqrstuvwxyz123456 and https://private.example.test/path' );
obe_source_smoke_assert( false === strpos( $redacted, 'abcdefghijklmnopqrstuvwxyz123456' ), 'Token-like values should be redacted.' );
obe_source_smoke_assert( false === strpos( $redacted, 'private.example.test' ), 'Endpoints should be redacted.' );
$service = new Source_Intake_Service( $parser );
$data = array( 'source_type' => Source_Type::CSV_TEXT, 'source_label' => 'Smoke', 'source_input' => "title,secret\nOne,sk-abcdefghijklmnopqrstuvwxyz123456" );
$preview = $parser->parse( $data );
$library_data = $service->build_library_data( $preview, $data );
obe_source_smoke_assert( $library_data['type'] === Library_Type::SOURCE_PREVIEW, 'Library type should be source_preview.' );
obe_source_smoke_assert( $library_data['status'] === Library_Status::NEEDS_REVIEW, 'Library status should be needs_review.' );
obe_source_smoke_assert( false === strpos( $service->preview_payload_redacted( $preview ), 'sk-abcdefghijklmnopqrstuvwxyz123456' ), 'Payload should not contain full raw token input.' );
echo "Source Intake Preview smoke passed.\n";
