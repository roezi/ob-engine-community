<?php
/** Manual smoke checks for static Workflow visualizer metadata. */
if ( ! defined( 'ABSPATH' ) ) { define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' ); }
if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $text ) { return trim( preg_replace( '/[\r\n\t ]+/', ' ', strip_tags( (string) $text ) ) ); } }
if ( ! function_exists( 'wp_json_encode' ) ) { function wp_json_encode( $data, $flags = 0 ) { return json_encode( $data, $flags ); } }
require_once ABSPATH . 'includes/Library/Library_Type.php';
require_once ABSPATH . 'includes/Workflow/Workflow_Node.php';
require_once ABSPATH . 'includes/Workflow/Workflow_Edge.php';
require_once ABSPATH . 'includes/Workflow/Workflow_Graph.php';
use OBEngine\Workflow\Workflow_Graph;
function obe_workflow_smoke_assert( $condition, $message ) { if ( ! $condition ) { fwrite( STDERR, "FAIL: {$message}\n" ); exit( 1 ); } }
$graph = new Workflow_Graph(); $nodes = $graph->nodes(); $edges = $graph->edges();
obe_workflow_smoke_assert( count( $nodes ) >= 9 && count( $edges ) >= 8, 'graph should build required nodes and edges' );
$write_nodes = array(); $ai_nodes = array(); $by = array();
foreach ( $nodes as $node ) { $by[ $node->get_id() ] = $node; if ( $node->writes_wordpress() ) { $write_nodes[] = $node->get_id(); } if ( $node->calls_ai_provider() ) { $ai_nodes[] = $node->get_id(); } }
obe_workflow_smoke_assert( array( 'write_draft' ) === $write_nodes, 'write_draft should be the only WordPress write node' );
sort( $ai_nodes ); obe_workflow_smoke_assert( array( 'auto_post_plan', 'draft_candidate', 'editorial_humanizer' ) === $ai_nodes, 'AI provider nodes should be plan, draft, and editorial revision only' );
foreach ( array( 'source_preview', 'field_mapping', 'approval', 'dry_run' ) as $id ) { obe_workflow_smoke_assert( ! $by[$id]->calls_ai_provider() && ! $by[$id]->writes_wordpress(), "$id should be no-AI/no-write" ); }
obe_workflow_smoke_assert( array() === $graph->validate(), 'graph should validate' );
echo "Workflow visualizer smoke checks passed. No external calls were made.\n";
