<?php
/**
 * Manual smoke checks for Activity Log value objects and redaction.
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}

if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_html_class' ) ) { function sanitize_html_class( $class ) { return preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $class ); } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'wp_json_encode' ) ) { function wp_json_encode( $data, $flags = 0 ) { return json_encode( $data, $flags ); } }

require_once ABSPATH . 'includes/Activity/Activity_Action.php';
require_once ABSPATH . 'includes/Activity/Activity_Status.php';
require_once ABSPATH . 'includes/Activity/Activity_Object_Type.php';
require_once ABSPATH . 'includes/Activity/Activity_Item.php';
require_once ABSPATH . 'includes/Activity/Activity_Redactor.php';

use OBEngine\Activity\Activity_Action;
use OBEngine\Activity\Activity_Item;
use OBEngine\Activity\Activity_Object_Type;
use OBEngine\Activity\Activity_Redactor;
use OBEngine\Activity\Activity_Status;

function obe_activity_smoke_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

obe_activity_smoke_assert( Activity_Action::is_valid( Activity_Action::LIBRARY_ITEM_CREATED ), 'library action should be valid' );
obe_activity_smoke_assert( Activity_Status::is_valid( Activity_Status::SUCCESS ), 'success status should be valid' );
obe_activity_smoke_assert( Activity_Object_Type::is_valid( Activity_Object_Type::LIBRARY_ITEM ), 'library object type should be valid' );

$item = Activity_Item::from_row(
	(object) array(
		'id'               => 7,
		'event_id'         => 'event-1',
		'action'           => Activity_Action::LIBRARY_ITEM_CREATED,
		'status'           => Activity_Status::SUCCESS,
		'object_type'      => Activity_Object_Type::LIBRARY_ITEM,
		'object_id'        => 12,
		'object_label'     => 'Safe title',
		'actor_id'         => 3,
		'message'          => 'Created safely.',
		'context_redacted' => '{"safe":"value"}',
		'created_at'       => '2026-06-27 00:00:00',
	)
);
obe_activity_smoke_assert( 7 === $item->get_id(), 'item id should hydrate' );
obe_activity_smoke_assert( array( 'safe' => 'value' ) === $item->get_context_redacted(), 'context JSON should decode' );

$redactor = new Activity_Redactor();
$redacted = $redactor->redact_array(
	array(
		'api_key'        => 'sk-example1234567890secret',
		'header'         => 'Authorization: Bearer abcdefghijklmnopqrstuvwxyz123456',
		'password'       => 'password=supersecret',
		'private_prompt' => 'private instructions',
		'endpoint_url'   => 'https://private.example.test/path',
	)
);
$json = $redactor->to_json( $redacted );

obe_activity_smoke_assert( false !== strpos( $redacted['api_key'], 'REDACTED' ), 'api_key should be redacted' );
obe_activity_smoke_assert( false !== strpos( $redacted['header'], 'REDACTED_TOKEN' ), 'bearer token should be redacted' );
obe_activity_smoke_assert( false !== strpos( $redacted['password'], 'REDACTED' ), 'password should be redacted' );
obe_activity_smoke_assert( '[REDACTED_PRIVATE_PROMPT]' === $redacted['private_prompt'], 'private prompt should be redacted' );
obe_activity_smoke_assert( '[REDACTED_ENDPOINT]' === $redacted['endpoint_url'], 'endpoint should be redacted' );
obe_activity_smoke_assert( is_array( json_decode( $json, true ) ), 'redacted context should encode as valid JSON' );

echo "Activity Log smoke checks passed.\n";
