<?php
/** End-to-end safety gate smoke without WordPress DB or external APIs. */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' ); }
if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $text ) { return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $text ) ) ); } }
if ( ! function_exists( 'sanitize_textarea_field' ) ) { function sanitize_textarea_field( $text ) { return trim( strip_tags( (string) $text ) ); } }
if ( ! function_exists( 'wp_strip_all_tags' ) ) { function wp_strip_all_tags( $text ) { return strip_tags( (string) $text ); } }
if ( ! function_exists( 'wp_kses_post' ) ) { function wp_kses_post( $text ) { return (string) $text; } }
if ( ! function_exists( 'absint' ) ) { function absint( $value ) { return abs( (int) $value ); } }
if ( ! function_exists( 'current_time' ) ) { function current_time( $type ) { return '2026-06-27 00:00:00'; } }
if ( ! function_exists( 'wp_json_encode' ) ) { function wp_json_encode( $data, $flags = 0 ) { return json_encode( $data, $flags ); } }
class WP_Post { public $ID; public $post_type; public $post_content; public $post_author = 1; public $post_date = '2026-06-27 00:00:00'; public $post_modified = '2026-06-27 00:00:00'; public $post_title = 'Safe title'; }
$GLOBALS['obe_smoke_items'] = array(); $GLOBALS['obe_smoke_meta'] = array();
function get_post_type( $id ) { return isset( $GLOBALS['obe_smoke_items'][$id] ) ? $GLOBALS['obe_smoke_items'][$id]['post_type'] : ''; }
function get_post_status( $id ) { return isset( $GLOBALS['obe_smoke_items'][$id] ) ? $GLOBALS['obe_smoke_items'][$id]['post_status'] : ''; }
function get_post( $id ) { if ( empty( $GLOBALS['obe_smoke_items'][$id] ) ) { return null; } $p = new WP_Post(); $p->ID = $id; $p->post_type = $GLOBALS['obe_smoke_items'][$id]['post_type']; $p->post_content = 'Safe content'; return $p; }
function get_the_title( $post ) { return $post->post_title; }
function get_post_meta( $id, $key = '', $single = false ) { $m = $GLOBALS['obe_smoke_meta'][$id] ?? array(); if ( '' === $key ) { $out = array(); foreach ( $m as $k => $v ) { $out[$k] = array( $v ); } return $out; } return $m[$key] ?? ''; }
foreach ( array('Library/Library_Type','Library/Library_Status','Library/Library_Item','Library/Library_Repository','Approval/Approval_Status','Approval/Approval_Record','Approval/Approval_Repository','Safety/Safety_Status','Safety/Safety_Result','Safety/Write_Target','Safety/Dry_Run_Result','Safety/Dry_Run_Repository','Activity/Activity_Action','Activity/Activity_Status','Activity/Activity_Object_Type','Activity/Activity_Repository','Activity/Activity_Logger','Safety/Dry_Run_Service','Writing/Write_Status','Writing/Write_Result','Writing/Write_Record','Writing/Write_Repository','Writing/WordPress_Draft_Writer','Writing/Write_Draft_Service','Workflow/Workflow_Node','Workflow/Workflow_Edge','Workflow/Workflow_Graph') as $f ) { require_once ABSPATH . 'includes/' . $f . '.php'; }
use OBEngine\Approval\Approval_Repository; use OBEngine\Approval\Approval_Status; use OBEngine\Library\Library_Item; use OBEngine\Library\Library_Repository; use OBEngine\Library\Library_Status; use OBEngine\Library\Library_Type; use OBEngine\Safety\Dry_Run_Service; use OBEngine\Safety\Write_Target; use OBEngine\Workflow\Workflow_Graph; use OBEngine\Writing\WordPress_Draft_Writer; use OBEngine\Writing\Write_Draft_Service;
function obe_full_smoke_assert( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } }

function obe_full_smoke_find_string_in_files( string $base_dir, string $needle ): array {
	$base_dir = rtrim( $base_dir, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
	$matches  = array();

	if ( ! is_dir( $base_dir ) ) {
		return $matches;
	}

	$iterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $base_dir, FilesystemIterator::SKIP_DOTS )
	);

	foreach ( $iterator as $file ) {
		if ( ! $file->isFile() ) {
			continue;
		}

		$path = $file->getPathname();
		$contents = file_get_contents( $path );
		if ( false === $contents || false === strpos( $contents, $needle ) ) {
			continue;
		}

		$relative = substr( $path, strlen( ABSPATH ) );
		$matches[] = str_replace( DIRECTORY_SEPARATOR, '/', $relative );
	}

	sort( $matches );
	return $matches;
}
foreach ( Library_Type::all() as $type ) { $expected = in_array( $type, array( Library_Type::CONTENT_DRAFT, Library_Type::EDITORIAL_REVISION ), true ); obe_full_smoke_assert( $expected === Library_Type::is_writeable( $type ), "$type writeability should match contract" ); }
function obe_item( $id, $type, $status ) { return new Library_Item( array( 'id'=>$id, 'title'=>'Safe title', 'content'=>'Safe content', 'summary'=>'Safe excerpt', 'type'=>$type, 'status'=>$status ) ); }
$GLOBALS['obe_smoke_items'][10] = array( 'post_type'=>Library_Repository::POST_TYPE, 'post_status'=>'private' );
$GLOBALS['obe_smoke_meta'][10] = array( Library_Repository::META_TYPE=>Library_Type::CONTENT_DRAFT, Library_Repository::META_STATUS=>Library_Status::APPROVED, Approval_Repository::META_STATUS=>Approval_Status::APPROVED, '_obe_dry_run_status'=>'passed', '_obe_dry_run_target_post_type'=>'post', '_obe_dry_run_target_status'=>'draft' );
$dry = new Dry_Run_Service(); $write = new Write_Draft_Service();
obe_full_smoke_assert( $dry->can_run_for_item( obe_item( 10, Library_Type::SOURCE_PREVIEW, Library_Status::APPROVED ) )->is_failed(), 'Dry_Run_Service blocks source_preview' );
obe_full_smoke_assert( $write->can_write( obe_item( 10, Library_Type::FIELD_MAPPING, Library_Status::APPROVED ) )->is_failed(), 'Write_Draft_Service blocks field_mapping' );
obe_full_smoke_assert( $write->can_write( obe_item( 10, Library_Type::CONTENT_DRAFT, Library_Status::NEEDS_REVIEW ) )->is_failed(), 'Write_Draft_Service blocks not approved Library status' );
obe_full_smoke_assert( $dry->can_run_for_item( obe_item( 10, Library_Type::CONTENT_DRAFT, Library_Status::APPROVED ) )->is_passed(), 'content_draft is dry-runnable only after approved' );
obe_full_smoke_assert( $write->can_write( obe_item( 10, Library_Type::EDITORIAL_REVISION, Library_Status::APPROVED ) )->is_passed(), 'editorial_revision is writeable after approved and dry-run passed' );
obe_full_smoke_assert( ( new WordPress_Draft_Writer() )->validate_target( new Write_Target( array( 'post_status'=>'publish', 'title'=>'Safe title', 'content'=>'Safe content' ) ) )->is_failed(), 'Write Draft rejects publish' );
$graph = new Workflow_Graph(); obe_full_smoke_assert( array() === $graph->validate(), 'Workflow graph has required gates' );
$wp_insert_matches = obe_full_smoke_find_string_in_files( ABSPATH . 'includes', 'wp_insert_post' );
$unexpected_wp_insert = array_diff( $wp_insert_matches, array( 'includes/Writing/WordPress_Draft_Writer.php' ) );
obe_full_smoke_assert( empty( $unexpected_wp_insert ), 'No file outside controlled writer path contains wp_insert_post' );
obe_full_smoke_assert( in_array( 'includes/Writing/WordPress_Draft_Writer.php', $wp_insert_matches, true ), 'Controlled writer path contains wp_insert_post' );
echo "Full workflow safety smoke checks passed. No external calls were made.\n";
