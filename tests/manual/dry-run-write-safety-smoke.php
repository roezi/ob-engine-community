<?php
/** Manual smoke checks for Dry-run / Write Safety without a WordPress database. */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' ); }
if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $text ) { return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $text ) ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $text ) { return trim( strip_tags( (string) $text ) ); } }
if ( ! function_exists( 'wp_strip_all_tags' ) ) { function wp_strip_all_tags( $text ) { return strip_tags( (string) $text ); } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $text ) { return (string) $text; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }

require_once ABSPATH . 'includes/Library/Library_Type.php';
require_once ABSPATH . 'includes/Library/Library_Status.php';
require_once ABSPATH . 'includes/Library/Library_Item.php';
require_once ABSPATH . 'includes/Safety/Safety_Status.php';
require_once ABSPATH . 'includes/Safety/Safety_Result.php';
require_once ABSPATH . 'includes/Safety/Write_Target.php';
require_once ABSPATH . 'includes/Safety/Dry_Run_Result.php';

use OBEngine\Library\Library_Item;
use OBEngine\Library\Library_Status;
use OBEngine\Library\Library_Type;
use OBEngine\Safety\Dry_Run_Result;
use OBEngine\Safety\Safety_Result;
use OBEngine\Safety\Safety_Status;
use OBEngine\Safety\Write_Target;

function obe_dry_run_smoke_assert( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } }

obe_dry_run_smoke_assert( Safety_Status::is_valid( Safety_Status::NOT_CHECKED ), 'not_checked status should be valid' );
obe_dry_run_smoke_assert( Safety_Status::is_valid( Safety_Status::PASSED ), 'passed status should be valid' );
obe_dry_run_smoke_assert( ! Safety_Status::is_valid( 'publish' ), 'publish should not be a safety status' );

$passed = Safety_Result::passed( 'Passed.' );
$warning = Safety_Result::warning( 'Warning.', array( 'Review excerpt.' ) );
$failed = Safety_Result::failed( 'Failed.', array( 'Approval required.' ) );
obe_dry_run_smoke_assert( $passed->is_passed(), 'passed result should pass' );
obe_dry_run_smoke_assert( $warning->has_warnings(), 'warning result should report warnings' );
obe_dry_run_smoke_assert( $failed->is_failed(), 'failed result should fail' );

$draft = new Write_Target( array( 'post_type' => 'post', 'post_status' => 'draft', 'title' => 'Safe title', 'content' => 'Safe content' ) );
obe_dry_run_smoke_assert( $draft->validate()->is_passed(), 'draft target should pass' );
$pending = new Write_Target( array( 'post_type' => 'post', 'post_status' => 'pending', 'title' => 'Safe title', 'content' => 'Safe content' ) );
obe_dry_run_smoke_assert( $pending->validate()->is_passed(), 'pending target should pass' );
$publish = new Write_Target( array( 'post_type' => 'post', 'post_status' => 'publish', 'title' => 'Safe title', 'content' => 'Safe content' ) );
obe_dry_run_smoke_assert( $publish->validate()->is_failed(), 'publish target should be rejected' );
$missing = new Write_Target( array( 'post_type' => 'post', 'post_status' => 'draft' ) );
obe_dry_run_smoke_assert( $missing->validate()->is_failed(), 'missing title/content should fail' );

$item = new Library_Item( array( 'id' => 123, 'title' => 'Library Draft', 'content' => 'Draft content', 'summary' => 'Excerpt', 'type' => Library_Type::CONTENT_DRAFT, 'status' => Library_Status::APPROVED ) );
$from_item = Write_Target::from_library_item( $item, array( 'post_status' => 'pending' ) );
obe_dry_run_smoke_assert( 'pending' === $from_item->get_post_status(), 'from_library_item should apply overrides' );
obe_dry_run_smoke_assert( $from_item->validate()->is_passed(), 'from_library_item target should validate' );

$empty = Dry_Run_Result::empty_for_item( 123 );
obe_dry_run_smoke_assert( 123 === $empty->get_library_item_id(), 'empty dry-run result should keep item id' );
obe_dry_run_smoke_assert( Safety_Status::NOT_CHECKED === $empty->get_status(), 'empty dry-run result should default to not_checked' );

// Repository methods intentionally are not called here because they require WordPress post/meta storage.
echo "Dry-run Write Safety smoke checks passed.\n";
