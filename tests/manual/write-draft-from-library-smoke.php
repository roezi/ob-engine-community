<?php
/** Manual smoke checks for Write Draft from Library without a WordPress database. */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' ); }
if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $text ) { return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $text ) ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $text ) { return trim( strip_tags( (string) $text ) ); } }
if ( ! function_exists( 'wp_strip_all_tags' ) ) { function wp_strip_all_tags( $text ) { return strip_tags( (string) $text ); } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $text ) { return (string) $text; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'current_time' ) ) { function current_time( $type ) { return '2026-06-27 00:00:00'; } }

require_once ABSPATH . 'includes/Library/Library_Type.php';
require_once ABSPATH . 'includes/Library/Library_Status.php';
require_once ABSPATH . 'includes/Library/Library_Item.php';
require_once ABSPATH . 'includes/Library/Library_Repository.php';
require_once ABSPATH . 'includes/Approval/Approval_Status.php';
require_once ABSPATH . 'includes/Approval/Approval_Record.php';
require_once ABSPATH . 'includes/Approval/Approval_Repository.php';
require_once ABSPATH . 'includes/Safety/Safety_Status.php';
require_once ABSPATH . 'includes/Safety/Safety_Result.php';
require_once ABSPATH . 'includes/Safety/Write_Target.php';
require_once ABSPATH . 'includes/Safety/Dry_Run_Result.php';
require_once ABSPATH . 'includes/Safety/Dry_Run_Repository.php';
require_once ABSPATH . 'includes/Activity/Activity_Action.php';
require_once ABSPATH . 'includes/Activity/Activity_Status.php';
require_once ABSPATH . 'includes/Activity/Activity_Object_Type.php';
require_once ABSPATH . 'includes/Activity/Activity_Repository.php';
require_once ABSPATH . 'includes/Activity/Activity_Logger.php';
require_once ABSPATH . 'includes/Writing/Write_Status.php';
require_once ABSPATH . 'includes/Writing/Write_Result.php';
require_once ABSPATH . 'includes/Writing/Write_Record.php';
require_once ABSPATH . 'includes/Writing/Write_Repository.php';
require_once ABSPATH . 'includes/Writing/WordPress_Draft_Writer.php';
require_once ABSPATH . 'includes/Writing/Write_Draft_Service.php';

use OBEngine\Safety\Write_Target;
use OBEngine\Writing\WordPress_Draft_Writer;
use OBEngine\Writing\Write_Draft_Service;
use OBEngine\Writing\Write_Record;
use OBEngine\Writing\Write_Result;
use OBEngine\Writing\Write_Status;

function obe_write_draft_smoke_assert( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } }

obe_write_draft_smoke_assert( Write_Status::is_valid( Write_Status::NOT_WRITTEN ), 'not_written status should be valid' );
obe_write_draft_smoke_assert( Write_Status::is_valid( Write_Status::WRITTEN ), 'written status should be valid' );
obe_write_draft_smoke_assert( ! Write_Status::is_valid( 'publish' ), 'publish should not be a write status' );

$success = Write_Result::success( 456, 'post', 'draft' );
$failed = Write_Result::failed( 'Blocked.', array( 'post_status' => 'publish' ) );
obe_write_draft_smoke_assert( $success->is_success() && 456 === $success->get_post_id(), 'success factory should produce successful result' );
obe_write_draft_smoke_assert( $failed->is_failed(), 'failed factory should produce failed result' );

$empty = Write_Record::empty_for_item( 123 );
obe_write_draft_smoke_assert( 123 === $empty->get_library_item_id(), 'empty record should keep item id' );
obe_write_draft_smoke_assert( Write_Status::NOT_WRITTEN === $empty->get_status(), 'empty record should default to not_written' );

$writer = new WordPress_Draft_Writer();
$publish = new Write_Target( array( 'post_type' => 'post', 'post_status' => 'publish', 'title' => 'Safe title', 'content' => 'Safe content' ) );
obe_write_draft_smoke_assert( $writer->validate_target( $publish )->is_failed(), 'writer should reject publish status' );
$draft = new Write_Target( array( 'post_type' => 'post', 'post_status' => 'draft', 'title' => 'Safe title', 'content' => 'Safe content' ) );
$pending = new Write_Target( array( 'post_type' => 'post', 'post_status' => 'pending', 'title' => 'Safe title', 'content' => 'Safe content' ) );
obe_write_draft_smoke_assert( $writer->validate_target( $draft )->is_passed(), 'writer should accept draft status' );
obe_write_draft_smoke_assert( $writer->validate_target( $pending )->is_passed(), 'writer should accept pending status' );

$service = new Write_Draft_Service();
obe_write_draft_smoke_assert( $service instanceof Write_Draft_Service, 'service should be constructable' );

echo "Write Draft from Library smoke checks passed. No external calls were made.\n";
