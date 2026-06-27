<?php
/** Manual smoke checks for Approval Gate value objects without a WordPress database. */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' ); }
if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $text ) { return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $text ) ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $text ) { return trim( strip_tags( (string) $text ) ); } }
if ( ! function_exists( 'wp_strip_all_tags' ) ) { function wp_strip_all_tags( $text ) { return strip_tags( (string) $text ); } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }

require_once ABSPATH . 'includes/Approval/Approval_Status.php';
require_once ABSPATH . 'includes/Approval/Approval_Record.php';

use OBEngine\Approval\Approval_Record;
use OBEngine\Approval\Approval_Status;

function obe_approval_smoke_assert( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } }

obe_approval_smoke_assert( Approval_Status::is_valid( Approval_Status::PENDING ), 'pending status should be valid' );
obe_approval_smoke_assert( Approval_Status::is_valid( Approval_Status::APPROVED ), 'approved status should be valid' );
obe_approval_smoke_assert( ! Approval_Status::is_valid( 'published' ), 'published should not be a valid approval status' );

$empty = Approval_Record::empty_for_item( 123 );
obe_approval_smoke_assert( 123 === $empty->get_library_item_id(), 'empty record should keep item id' );
obe_approval_smoke_assert( $empty->is_pending(), 'empty record should be pending' );

$approved = Approval_Record::from_meta( 123, array( '_obe_approval_status' => array( 'approved' ), '_obe_approval_note' => array( '<b>Looks safe</b>' ), '_obe_approval_actor_id' => array( '7' ), '_obe_approval_actor_label' => array( 'Reviewer' ), '_obe_approval_decided_at' => array( '2026-06-27 00:00:00' ), '_obe_approval_updated_at' => array( '2026-06-27 00:00:01' ) ) );
obe_approval_smoke_assert( $approved->is_approved(), 'approved meta should hydrate as approved' );
obe_approval_smoke_assert( 'Looks safe' === $approved->get_note(), 'approval note should be plain text' );

$rejected = Approval_Record::from_meta( 124, array( '_obe_approval_status' => array( 'rejected' ) ) );
obe_approval_smoke_assert( $rejected->is_rejected(), 'rejected meta should hydrate as rejected' );

$keys = array_keys( $approved->to_array() );
obe_approval_smoke_assert( array( 'library_item_id', 'status', 'note', 'actor_id', 'actor_label', 'decided_at', 'updated_at' ) === $keys, 'to_array should expose expected keys' );

echo "Approval Gate smoke checks passed.\n";
