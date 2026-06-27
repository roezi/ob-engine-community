<?php
/**
 * AI request builder.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

use OBEngine\Providers\Provider_Resolver;
use OBEngine\Providers\Provider_Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Builds normalized AI requests from task data.
 */
final class AI_Request_Builder {
	private $profile_resolver;
	private $provider_resolver;

	public function __construct( AI_Task_Profile_Resolver $profile_resolver = null, Provider_Resolver $provider_resolver = null ) {
		$this->profile_resolver = $profile_resolver ?: new AI_Task_Profile_Resolver();
		$this->provider_resolver = $provider_resolver;
	}

	public function from_task_data( array $task_data ): AI_Request {
		$data = $this->sanitize_task_data( $task_data );
		$task_type = isset( $data['task_type'] ) && '' !== $data['task_type'] ? $data['task_type'] : AI_Task_Type::SUMMARIZE_ACTIVITY;
		$model_profile = $this->profile_resolver->resolve_model_profile( $task_type, isset( $data['model_profile'] ) ? $data['model_profile'] : '' );
		$model_defaults = $this->profile_resolver->resolve_model_defaults( $model_profile );
		$output_schema = $this->profile_resolver->resolve_output_schema( $task_type, isset( $data['output_schema'] ) ? $data['output_schema'] : '' );
		$metadata = isset( $data['metadata'] ) && is_array( $data['metadata'] ) ? $data['metadata'] : array();
		$metadata['source'] = 'ai_engine';

		return AI_Request::from_array(
			array(
				'task_type'        => $task_type,
				'input'            => isset( $data['input'] ) ? $data['input'] : '',
				'instructions'     => isset( $data['instructions'] ) && '' !== $data['instructions'] ? $data['instructions'] : $this->default_instructions_for_task( $task_type ),
				'model_profile'    => $model_profile,
				'model'            => isset( $data['model'] ) && '' !== $data['model'] ? $data['model'] : $this->default_model(),
				'reasoning_effort' => $model_defaults['reasoning_effort'],
				'verbosity'        => $model_defaults['verbosity'],
				'output_schema'    => $output_schema,
				'metadata'         => $metadata,
				'source_context'   => isset( $data['source_context'] ) && is_array( $data['source_context'] ) ? $data['source_context'] : array(),
				'library_context'  => isset( $data['library_context'] ) && is_array( $data['library_context'] ) ? $data['library_context'] : array(),
				'created_by'       => isset( $data['created_by'] ) ? $data['created_by'] : '',
				'store'            => false,
				'tools'            => array(),
				'tool_choice'      => '',
				'background'       => false,
			)
		);
	}

	public function default_instructions_for_task( string $task_type ): string {
		$schema = $this->default_schema_for_task( $task_type );
		return 'Prepare safe, review-first AI output for the requested OBE task. ' .
			( '' !== $schema ? 'Return safe structured output that matches the requested public schema. ' : '' ) .
			'Do not write to WordPress. Do not publish content. Avoid private data exposure. Do not include credentials, private prompts, private endpoints, tokens, or client-specific business rules.';
	}

	public function default_schema_for_task( string $task_type ): string {
		return $this->profile_resolver->resolve_output_schema( $task_type );
	}

	public function sanitize_task_data( array $task_data ): array {
		$allowed = array( 'task_type', 'input', 'instructions', 'model_profile', 'model', 'output_schema', 'metadata', 'source_context', 'library_context', 'created_by' );
		$data = array();
		foreach ( $allowed as $key ) {
			if ( array_key_exists( $key, $task_data ) ) {
				$data[ $key ] = is_string( $task_data[ $key ] ) ? trim( $task_data[ $key ] ) : $task_data[ $key ];
			}
		}
		return $data;
	}

	private function default_model(): string {
		$provider_id = $this->provider_resolver ? $this->provider_resolver->selected_provider_id() : 'openai';
		$config = Provider_Settings::get_provider_config( $provider_id );
		return isset( $config['default_model'] ) ? (string) $config['default_model'] : '';
	}
}
