<?php
/**
 * AI request value object.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Normalized AI request structure. Sanitization belongs to callers.
 */
final class AI_Request {
	private $data;

	private $provided_fields;

	private $original_data;

	public function __construct( array $data = array() ) {
		$this->provided_fields = array_keys( $data );
		$this->original_data = $data;
		$profile = isset( $data['model_profile'] ) ? (string) $data['model_profile'] : '';
		if ( '' === $profile && isset( $data['task_type'] ) ) {
			$profile = AI_Task_Type::default_model_profile( (string) $data['task_type'] );
		}
		$defaults = Model_Profile::defaults( $profile );

		$this->data = array(
			'request_id'        => isset( $data['request_id'] ) && '' !== (string) $data['request_id'] ? (string) $data['request_id'] : self::generate_request_id(),
			'task_type'         => isset( $data['task_type'] ) ? (string) $data['task_type'] : '',
			'input'             => isset( $data['input'] ) ? $data['input'] : '',
			'instructions'      => isset( $data['instructions'] ) ? $data['instructions'] : '',
			'model_profile'     => $profile,
			'model'             => isset( $data['model'] ) ? (string) $data['model'] : '',
			'reasoning_effort'  => isset( $data['reasoning_effort'] ) && '' !== (string) $data['reasoning_effort'] ? (string) $data['reasoning_effort'] : $defaults['reasoning_effort'],
			'verbosity'         => isset( $data['verbosity'] ) && '' !== (string) $data['verbosity'] ? (string) $data['verbosity'] : $defaults['verbosity'],
			'output_schema'     => isset( $data['output_schema'] ) ? (string) $data['output_schema'] : '',
			'tools'             => isset( $data['tools'] ) && is_array( $data['tools'] ) ? $data['tools'] : array(),
			'tool_choice'       => isset( $data['tool_choice'] ) ? (string) $data['tool_choice'] : 'none',
			'store'             => ! empty( $data['store'] ),
			'metadata'          => isset( $data['metadata'] ) && is_array( $data['metadata'] ) ? $data['metadata'] : array(),
			'safety_identifier' => isset( $data['safety_identifier'] ) ? (string) $data['safety_identifier'] : '',
			'prompt_cache_key'  => isset( $data['prompt_cache_key'] ) ? (string) $data['prompt_cache_key'] : '',
			'background'        => isset( $data['background'] ) ? (bool) $data['background'] : (bool) $defaults['background'],
			'source_context'    => isset( $data['source_context'] ) && is_array( $data['source_context'] ) ? $data['source_context'] : array(),
			'library_context'   => isset( $data['library_context'] ) && is_array( $data['library_context'] ) ? $data['library_context'] : array(),
			'redaction_policy'  => isset( $data['redaction_policy'] ) && is_array( $data['redaction_policy'] ) ? $data['redaction_policy'] : array(),
			'created_by'        => isset( $data['created_by'] ) ? $data['created_by'] : '',
			'created_at'        => isset( $data['created_at'] ) ? (string) $data['created_at'] : gmdate( 'c' ),
		);
	}

	public static function from_array( array $data ): self { return new self( $data ); }
	public function to_array(): array { return $this->data; }

	public function validate(): array {
		$errors = array();
		foreach ( array( 'task_type', 'input', 'instructions', 'model_profile', 'metadata' ) as $field ) {
			if ( ! in_array( $field, $this->provided_fields, true ) || ( is_string( $this->data[ $field ] ) && '' === $this->data[ $field ] ) ) {
				$errors[] = $field . ' is required.';
			}
		}
		if ( ! AI_Task_Type::is_valid( $this->data['task_type'] ) ) { $errors[] = 'task_type is not supported.'; }
		if ( ! Model_Profile::is_valid( $this->data['model_profile'] ) ) { $errors[] = 'model_profile is not supported.'; }
		if ( in_array( 'metadata', $this->provided_fields, true ) && ! is_array( $this->original_data['metadata'] ) ) { $errors[] = 'metadata must be an array.'; }
		return $errors;
	}

	public function is_valid(): bool { return array() === $this->validate(); }
	public function get_request_id(): string { return $this->data['request_id']; }
	public function get_task_type(): string { return $this->data['task_type']; }
	public function get_model_profile(): string { return $this->data['model_profile']; }

	private static function generate_request_id(): string {
		if ( function_exists( 'wp_generate_uuid4' ) ) { return wp_generate_uuid4(); }
		return uniqid( 'obe_ai_', true );
	}
}
