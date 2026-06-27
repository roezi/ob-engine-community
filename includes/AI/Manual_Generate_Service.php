<?php
/**
 * Manual Generate to Library service.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

use OBEngine\Activity\Activity_Action;
use OBEngine\Activity\Activity_Logger;
use OBEngine\Activity\Activity_Object_Type;
use OBEngine\Library\Library_Repository;
use OBEngine\Library\Library_Status;
use OBEngine\Library\Library_Type;

\defined( 'ABSPATH' ) || exit;

/**
 * Runs explicit admin-triggered AI generation and stores output in the private Library.
 */
final class Manual_Generate_Service {
	public const SOURCE = 'manual_generate';
	public const DEFAULT_SOURCE_LABEL = 'Manual Generate';

	private $engine;
	private $library_repository;
	private $logger;

	public function __construct( AI_Engine $engine = null, Library_Repository $library_repository = null, Activity_Logger $logger = null ) {
		$this->engine             = $engine ?: new AI_Engine();
		$this->library_repository = $library_repository ?: new Library_Repository();
		$this->logger             = $logger ?: new Activity_Logger();
	}

	public function generate_to_library( array $data ): array {
		$validated = $this->validate_data( $data );
		if ( ! empty( $validated['errors'] ) ) {
			$this->logger->failed( Activity_Action::SAFETY_CHECK_FAILED, array( 'object_type' => Activity_Object_Type::AI_REQUEST, 'object_label' => 'manual_generate', 'message' => 'Manual generate validation failed.', 'context' => $this->safe_context_from_data( $validated ) ) );
			return array( 'success' => false, 'message_public' => implode( ' ', $validated['errors'] ) );
		}

		$task_data = array(
			'task_type'      => $validated['task_type'],
			'input'          => $validated['input'],
			'instructions'   => $validated['instructions'],
			'metadata'       => array( 'source' => self::SOURCE ),
			'source_context' => array( 'source_label' => $validated['source_label'] ),
		);
		if ( '' !== $validated['model'] ) {
			$task_data['model'] = $validated['model'];
		}
		if ( '' !== $validated['output_schema'] ) {
			$task_data['output_schema'] = $validated['output_schema'];
		}

		$response = $this->engine->run( $task_data );
		if ( ! $response->is_success() ) {
			return array( 'success' => false, 'message_public' => $this->public_error_message( $response ) );
		}

		$library_data = $this->build_library_data( $response, $validated );
		if ( '' === trim( $library_data['content'] ) ) {
			$this->log_response_failed( $response, $validated );
			return array( 'success' => false, 'message_public' => __( 'Provider returned no usable output.', 'ob-engine' ) );
		}

		$item_id = $this->library_repository->create( $library_data );
		if ( function_exists( 'is_wp_error' ) && is_wp_error( $item_id ) ) {
			return array( 'success' => false, 'message_public' => __( 'Generated output could not be saved to Library.', 'ob-engine' ) );
		}

		$context = $this->safe_context_from_response( $response, $validated );
		$context['library_item_id'] = (int) $item_id;
		$this->logger->library_event( Activity_Action::LIBRARY_ITEM_CREATED_FROM_AI, (int) $item_id, $library_data['title'], $context );

		return array( 'success' => true, 'library_item_id' => (int) $item_id, 'message_public' => __( 'Generated output was saved to Library for review.', 'ob-engine' ) );
	}

	public function validate_data( array $data ): array {
		$task_type    = isset( $data['task_type'] ) ? sanitize_key( (string) $data['task_type'] ) : '';
		$title        = $this->library_title_from_data( $data );
		$input        = isset( $data['input'] ) ? sanitize_textarea_field( (string) $data['input'] ) : '';
		$instructions = isset( $data['instructions'] ) ? sanitize_textarea_field( (string) $data['instructions'] ) : '';
		$model        = isset( $data['model'] ) ? sanitize_text_field( (string) $data['model'] ) : '';
		$schema       = isset( $data['output_schema'] ) ? sanitize_key( (string) $data['output_schema'] ) : '';
		$source_label = isset( $data['source_label'] ) ? sanitize_text_field( (string) $data['source_label'] ) : '';
		$library_type = isset( $data['library_type'] ) ? sanitize_key( (string) $data['library_type'] ) : '';
		$errors       = array();

		if ( ! $this->is_allowed_task_type( $task_type ) ) { $errors[] = __( 'Select a supported task type.', 'ob-engine' ); }
		if ( '' === $title ) { $errors[] = __( 'Library title is required.', 'ob-engine' ); }
		if ( '' === $input ) { $errors[] = __( 'Input is required.', 'ob-engine' ); }
		if ( '' !== $schema && ! Structured_Output::is_valid( $schema ) ) { $errors[] = __( 'Output schema is not supported.', 'ob-engine' ); }
		if ( '' === $library_type ) { $library_type = $this->library_type_for_task( $task_type ); }
		if ( ! Library_Type::is_valid( $library_type ) ) { $errors[] = __( 'Library type is not supported.', 'ob-engine' ); }

		return array( 'task_type' => $task_type, 'title' => $title, 'input' => $input, 'instructions' => $instructions, 'model' => $model, 'output_schema' => $schema, 'source_label' => $source_label, 'library_type' => $library_type, 'errors' => $errors );
	}

	public function library_type_for_task( string $task_type ): string {
		$map = array( AI_Task_Type::GENERATE_CONTENT_DRAFT => Library_Type::CONTENT_DRAFT, AI_Task_Type::IMPROVE_CONTENT_DRAFT => Library_Type::CONTENT_REVIEW, AI_Task_Type::REVIEW_CONTENT => Library_Type::CONTENT_REVIEW, AI_Task_Type::GENERATE_SEO_REVIEW => Library_Type::SEO_REVIEW, AI_Task_Type::GENERATE_TRANSLATION_PLAN => Library_Type::TRANSLATION_PLAN, AI_Task_Type::GENERATE_PERFORMANCE_REPORT => Library_Type::PERFORMANCE_REPORT, AI_Task_Type::WORKFLOW_PLAN => Library_Type::WORKFLOW_PLAN, AI_Task_Type::EXPLAIN_ERROR => Library_Type::CONTENT_REVIEW, AI_Task_Type::SUMMARIZE_ACTIVITY => Library_Type::WORKFLOW_PLAN );
		return $map[ $task_type ] ?? Library_Type::CONTENT_REVIEW;
	}

	public function library_title_from_data( array $data ): string { return isset( $data['title'] ) ? sanitize_text_field( (string) $data['title'] ) : ''; }

	public function build_library_data( AI_Response $response, array $data ): array {
		$output_text = trim( $response->get_output_text() );
		$content     = '' !== $output_text ? $output_text : $this->json_content( $response->get_output_json() );
		return array( 'title' => $this->library_title_from_data( $data ), 'type' => isset( $data['library_type'] ) ? (string) $data['library_type'] : $this->library_type_for_task( (string) $data['task_type'] ), 'status' => Library_Status::NEEDS_REVIEW, 'source_label' => ! empty( $data['source_label'] ) ? (string) $data['source_label'] : self::DEFAULT_SOURCE_LABEL, 'summary' => $this->response_summary( $response ), 'content' => $content, 'payload_redacted' => $this->response_payload_redacted_for_task( $response, isset( $data['task_type'] ) ? (string) $data['task_type'] : '' ), 'created_context' => self::SOURCE );
	}

	public function response_summary( AI_Response $response ): string {
		$text = trim( wp_strip_all_tags( $response->get_output_text() ) );
		if ( '' === $text && ! empty( $response->get_output_json() ) ) { $text = 'Structured AI output saved for review.'; }
		if ( function_exists( 'mb_substr' ) ) { return mb_substr( $text, 0, 300 ); }
		return substr( $text, 0, 300 );
	}

	public function response_payload_redacted( AI_Response $response ): string {
		$data = $response->to_array();
		$payload = array( 'request_id' => (string) $data['request_id'], 'provider' => (string) $data['provider'], 'model' => (string) $data['model'], 'status' => (string) $data['status'], 'task_type' => '', 'has_output_text' => '' !== trim( (string) $data['output_text'] ), 'has_output_json' => ! empty( $data['output_json'] ) );
		if ( ! empty( $data['usage'] ) ) { $payload['usage'] = $data['usage']; }
		if ( ! empty( $data['output_json'] ) && strlen( wp_json_encode( $data['output_json'] ) ) <= 2000 ) { $payload['output_json'] = $data['output_json']; }
		return wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

	private function response_payload_redacted_for_task( AI_Response $response, string $task_type ): string {
		$payload = json_decode( $this->response_payload_redacted( $response ), true );
		if ( ! is_array( $payload ) ) {
			$payload = array();
		}
		$payload['task_type'] = sanitize_key( $task_type );
		return wp_json_encode( $payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	}

	private function is_allowed_task_type( string $task_type ): bool { return in_array( $task_type, array( AI_Task_Type::GENERATE_CONTENT_DRAFT, AI_Task_Type::IMPROVE_CONTENT_DRAFT, AI_Task_Type::REVIEW_CONTENT, AI_Task_Type::GENERATE_SEO_REVIEW, AI_Task_Type::GENERATE_TRANSLATION_PLAN, AI_Task_Type::GENERATE_PERFORMANCE_REPORT, AI_Task_Type::WORKFLOW_PLAN, AI_Task_Type::EXPLAIN_ERROR, AI_Task_Type::SUMMARIZE_ACTIVITY ), true ); }
	private function json_content( array $json ): string { return empty( $json ) ? '' : wp_json_encode( $json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ); }
	private function public_error_message( AI_Response $response ): string { $data = $response->to_array(); return ! empty( $data['error']['message_public'] ) ? (string) $data['error']['message_public'] : __( 'AI generation failed. Review provider, key, model, and request settings.', 'ob-engine' ); }
	private function log_response_failed( AI_Response $response, array $data ): void { $this->logger->failed( Activity_Action::AI_RESPONSE_FAILED, array( 'object_type' => Activity_Object_Type::AI_REQUEST, 'object_label' => 'manual_generate', 'message' => 'Manual generate AI response failed.', 'context' => $this->safe_context_from_response( $response, $data ) ) ); }
	private function safe_context_from_data( array $data ): array { return array( 'task_type' => isset( $data['task_type'] ) ? (string) $data['task_type'] : '', 'library_type' => isset( $data['library_type'] ) ? (string) $data['library_type'] : '', 'source_label_present' => ! empty( $data['source_label'] ), 'output_schema' => isset( $data['output_schema'] ) ? (string) $data['output_schema'] : '' ); }
	private function safe_context_from_response( AI_Response $response, array $data ): array { $raw = $response->to_array(); return array_merge( $this->safe_context_from_data( $data ), array( 'provider' => isset( $raw['provider'] ) ? (string) $raw['provider'] : '', 'model' => isset( $raw['model'] ) ? (string) $raw['model'] : '', 'has_output_text' => '' !== trim( $response->get_output_text() ), 'has_output_json' => ! empty( $response->get_output_json() ) ) ); }
}
