<?php
/** Auto Post plan generation service. @package OBEngine\AutoPost */
namespace OBEngine\AutoPost;

use OBEngine\Activity\Activity_Action;
use OBEngine\Activity\Activity_Logger;
use OBEngine\Activity\Activity_Object_Type;
use OBEngine\AI\AI_Engine;
use OBEngine\AI\AI_Response;
use OBEngine\AI\AI_Task_Type;
use OBEngine\AI\Structured_Output;
use OBEngine\Library\Library_Item;
use OBEngine\Library\Library_Repository;
use OBEngine\Library\Library_Status;
use OBEngine\Library\Library_Type;
use WP_Error;

defined( 'ABSPATH' ) || exit;

final class Auto_Post_Plan_Service {
	private $engine;
	private $library_repository;
	private $logger;

	public function __construct( AI_Engine $engine = null, Library_Repository $library_repository = null, Activity_Logger $logger = null ) {
		$this->engine = $engine ?: new AI_Engine();
		$this->library_repository = $library_repository ?: new Library_Repository();
		$this->logger = $logger ?: new Activity_Logger();
	}

	public function generate_plan( array $data ) {
		$data = $this->validate_data( $data );
		if ( empty( $data['field_mapping_library_item_id'] ) ) { return $this->fail( 'Field Mapping is required.', array() ); }
		$mapping_item = $this->library_repository->get( $data['field_mapping_library_item_id'] );
		if ( ! $mapping_item instanceof Library_Item || Library_Type::FIELD_MAPPING !== $mapping_item->get_type() ) { return $this->fail( 'Select a valid Field Mapping Library item.', array( 'field_mapping_library_item_id' => $data['field_mapping_library_item_id'] ) ); }
		$data['mapping_title'] = $mapping_item->get_title();
		$data['source_label'] = '' !== $mapping_item->get_source_label() ? $mapping_item->get_source_label() : 'Field Mapping';
		$mapping_payload = $this->extract_mapping_payload( $data['field_mapping_library_item_id'] );
		$response = $this->engine->run( $this->build_ai_task_data( $data, $mapping_payload ) );
		if ( ! $response->is_success() ) {
			$this->logger->failed( Activity_Action::AUTO_POST_PLAN_FAILED, array( 'object_type' => Activity_Object_Type::AUTO_POST, 'object_label' => $mapping_item->get_title(), 'message' => 'Auto Post plan generation failed.', 'context' => array( 'field_mapping_library_item_id' => $data['field_mapping_library_item_id'] ) ) );
			return array( 'success' => false, 'message_public' => 'Auto Post plan generation failed. Review provider settings and try again.' );
		}
		$plan = $this->build_auto_post_plan( $response, $data, $mapping_payload );
		$library_data = $this->build_library_data( $plan, $data );
		$created = $this->library_repository->create( $library_data );
		if ( $created instanceof WP_Error || ! $created ) { return array( 'success' => false, 'message_public' => 'Auto Post plan could not be saved to Library.' ); }
		$response_data = $response->to_array();
		$this->logger->success( Activity_Action::AUTO_POST_PLAN_CREATED, array( 'object_type' => Activity_Object_Type::AUTO_POST, 'object_id' => (int) $created, 'object_label' => $library_data['title'], 'message' => 'Auto Post plan saved to Library.', 'context' => array( 'field_mapping_library_item_id' => $plan->get_field_mapping_library_item_id(), 'source_preview_library_item_id' => $plan->get_source_preview_library_item_id(), 'auto_post_plan_library_item_id' => (int) $created, 'provider' => isset( $response_data['provider'] ) ? (string) $response_data['provider'] : '', 'model' => isset( $response_data['model'] ) ? (string) $response_data['model'] : '', 'request_id' => isset( $response_data['request_id'] ) ? (string) $response_data['request_id'] : '', 'has_output_json' => ! empty( $response_data['output_json'] ), 'has_output_text' => ! empty( $response_data['output_text'] ), 'default_status' => $plan->get_default_status(), 'proposed_post_type' => $plan->get_proposed_post_type() ) ) );
		return array( 'success' => true, 'library_item_id' => (int) $created, 'plan' => $plan, 'message_public' => 'Auto Post plan saved to Library for review.' );
	}

	public function validate_data( array $data ): array { return array( 'field_mapping_library_item_id' => isset( $data['field_mapping_library_item_id'] ) ? absint( $data['field_mapping_library_item_id'] ) : 0, 'additional_instructions' => isset( $data['additional_instructions'] ) ? sanitize_textarea_field( (string) $data['additional_instructions'] ) : '', 'source_label' => isset( $data['source_label'] ) ? sanitize_text_field( (string) $data['source_label'] ) : '', 'mapping_title' => isset( $data['mapping_title'] ) ? sanitize_text_field( (string) $data['mapping_title'] ) : '' ); }
	public function extract_mapping_payload( int $field_mapping_library_item_id ): array { $item = $this->library_repository->get( $field_mapping_library_item_id ); if ( ! $item ) { return array(); } $decoded = json_decode( $item->get_payload_redacted(), true ); return is_array( $decoded ) ? $decoded : array( 'summary' => $item->get_summary() ); }
	public function build_ai_task_data( array $data, array $mapping_payload ): array { $source_preview_id = isset( $mapping_payload['source_preview_library_item_id'] ) ? absint( $mapping_payload['source_preview_library_item_id'] ) : 0; return array( 'task_type' => AI_Task_Type::GENERATE_AUTO_POST_PLAN, 'input' => array( 'field_mapping_summary' => isset( $mapping_payload['summary'] ) ? (string) $mapping_payload['summary'] : '', 'field_mapping_redacted' => $mapping_payload ), 'instructions' => '' !== $data['additional_instructions'] ? $data['additional_instructions'] : 'Generate a review-first Auto Post plan. Save planning guidance only; do not generate final draft content or write WordPress posts.', 'output_schema' => Structured_Output::AUTO_POST_PLAN_V1, 'metadata' => array( 'source' => 'auto_post_plan_generator', 'field_mapping_library_item_id' => $data['field_mapping_library_item_id'] ), 'library_context' => array_filter( array( 'field_mapping_library_item_id' => $data['field_mapping_library_item_id'], 'source_preview_library_item_id' => $source_preview_id ) ) ); }
	public function build_auto_post_plan( AI_Response $response, array $data, array $mapping_payload ): Auto_Post_Plan { $rd = $response->to_array(); $payload = $response->get_output_json(); if ( empty( $payload ) && '' !== $response->get_output_text() ) { $payload = array( 'source_summary' => $response->get_output_text(), 'safety_notes' => array( 'Review generated plan before draft generation.' ) ); } return Auto_Post_Plan::from_ai_response_data( $payload, array( 'field_mapping_library_item_id' => $data['field_mapping_library_item_id'], 'source_preview_library_item_id' => isset( $mapping_payload['source_preview_library_item_id'] ) ? absint( $mapping_payload['source_preview_library_item_id'] ) : 0, 'field_mapping_summary' => isset( $mapping_payload['summary'] ) ? (string) $mapping_payload['summary'] : '', 'provider' => $rd['provider'] ?? '', 'model' => $rd['model'] ?? '', 'request_id' => $rd['request_id'] ?? '' ) ); }
	public function build_library_data( Auto_Post_Plan $plan, array $data ): array { $label = ! empty( $data['source_label'] ) ? $data['source_label'] : ( ! empty( $data['mapping_title'] ) ? $data['mapping_title'] : 'Field Mapping' ); return array( 'title' => 'Auto Post Plan: ' . $label, 'type' => Library_Type::AUTO_POST_PLAN, 'status' => Library_Status::NEEDS_REVIEW, 'source_label' => $label, 'summary' => $plan->summary(), 'content' => $this->readable_plan_content( $plan ), 'payload_redacted' => $this->plan_payload_redacted( $plan ), 'created_context' => 'auto_post_plan_generator' ); }
	public function plan_payload_redacted( Auto_Post_Plan $plan ): string { return wp_json_encode( $plan->to_array(), JSON_UNESCAPED_SLASHES ); }
	public function readable_plan_content( Auto_Post_Plan $plan ): string { $out = "Auto Post Plan\n\nSummary: " . $plan->summary() . "\nProposed post type: " . $plan->get_proposed_post_type() . "\nDefault status: " . $plan->get_default_status() . "\n\nTitle options:\n- " . implode( "\n- ", $plan->get_title_options() ) . "\n\nSafety notes:\n- " . implode( "\n- ", $plan->get_safety_notes() ) . "\n\nApproval question: " . $plan->get_approval_question(); return $out; }
	private function fail( string $message, array $context ) { $this->logger->failed( Activity_Action::AUTO_POST_PLAN_FAILED, array( 'object_type' => Activity_Object_Type::AUTO_POST, 'message' => $message, 'context' => $context ) ); return array( 'success' => false, 'message_public' => $message ); }
}
