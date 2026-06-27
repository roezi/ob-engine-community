<?php
/**
 * OpenAI Responses API client.
 *
 * @package OBEngine\Providers\OpenAI
 */

namespace OBEngine\Providers\OpenAI;

use OBEngine\AI\AI_Error;
use OBEngine\AI\AI_Request;
use OBEngine\AI\AI_Response;
use OBEngine\AI\Structured_Output;
use OBEngine\AI\Usage_Record;

defined( 'ABSPATH' ) || exit;

/**
 * Safe, mockable client for mapping OBE requests to OpenAI Responses API calls.
 */
final class OpenAI_Responses_Client {
	public const ENDPOINT = 'https://api.openai.com/v1/responses';
	public const PROVIDER_ID = 'openai';

	public function build_payload( AI_Request $request ): array {
		$data = $request->to_array();
		$payload = array(
			'model'        => isset( $data['model'] ) ? (string) $data['model'] : '',
			'input'        => isset( $data['input'] ) ? $data['input'] : '',
			'instructions' => isset( $data['instructions'] ) ? $data['instructions'] : '',
			'reasoning'    => array( 'effort' => isset( $data['reasoning_effort'] ) ? (string) $data['reasoning_effort'] : 'medium' ),
			'text'         => array( 'verbosity' => isset( $data['verbosity'] ) ? (string) $data['verbosity'] : 'medium' ),
			'store'        => ! empty( $data['store'] ),
			'metadata'     => isset( $data['metadata'] ) && is_array( $data['metadata'] ) ? $data['metadata'] : array(),
			'background'   => ! empty( $data['background'] ),
		);

		if ( ! empty( $data['output_schema'] ) && Structured_Output::is_valid( (string) $data['output_schema'] ) ) {
			$payload['text']['format'] = $this->build_text_format( (string) $data['output_schema'] );
		}
		if ( ! empty( $data['tools'] ) && is_array( $data['tools'] ) ) {
			$payload['tools'] = $data['tools'];
		}
		if ( ! empty( $data['tool_choice'] ) && 'none' !== $data['tool_choice'] ) {
			$payload['tool_choice'] = $data['tool_choice'];
		}
		if ( ! empty( $data['safety_identifier'] ) ) {
			$payload['safety_identifier'] = (string) $data['safety_identifier'];
		}
		if ( ! empty( $data['prompt_cache_key'] ) ) {
			$payload['prompt_cache_key'] = (string) $data['prompt_cache_key'];
		}

		return $payload;
	}

	public function send_payload( array $payload, string $api_key ): array {
		if ( ! function_exists( 'wp_remote_post' ) ) {
			$error = OpenAI_Error_Mapper::from_exception_message( 'WordPress HTTP API is unavailable.' );
			return array( 'success' => false, 'error' => $error->to_array() );
		}

		$response = wp_remote_post(
			self::ENDPOINT,
			array(
				'timeout' => 45,
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				),
				'body'    => function_exists( 'wp_json_encode' ) ? wp_json_encode( $payload ) : json_encode( $payload ),
			)
		);

		if ( function_exists( 'is_wp_error' ) && is_wp_error( $response ) ) {
			$error = OpenAI_Error_Mapper::from_wp_error( $response );
			return array( 'success' => false, 'error' => $error->to_array() );
		}

		$status = function_exists( 'wp_remote_retrieve_response_code' ) ? (int) wp_remote_retrieve_response_code( $response ) : 0;
		$body_raw = function_exists( 'wp_remote_retrieve_body' ) ? (string) wp_remote_retrieve_body( $response ) : '';
		$body = json_decode( $body_raw, true );
		if ( ! is_array( $body ) ) {
			$body = array();
		}

		if ( 200 > $status || 300 <= $status ) {
			$error = OpenAI_Error_Mapper::from_http_status( $status, $body );
			return array( 'success' => false, 'error' => $error->to_array(), 'status' => $status );
		}

		return array( 'success' => true, 'body' => $body, 'status' => $status );
	}

	public function create_response( AI_Request $request, string $api_key ): AI_Response {
		$result = $this->send_payload( $this->build_payload( $request ), $api_key );
		if ( empty( $result['success'] ) ) {
			if ( isset( $result['error'] ) && is_array( $result['error'] ) ) {
				$error_data = $result['error'];
				$error_data['request_id'] = $request->get_request_id();
				$error_data['provider'] = self::PROVIDER_ID;
				$error_data['task_type'] = $request->get_task_type();
				$error = AI_Error::from_array( $error_data );
			} else {
				$error = OpenAI_Error_Mapper::from_exception_message( 'OpenAI request failed.', $request->get_request_id(), $request->get_task_type() );
			}
			return AI_Response::failed( $request, $error );
		}
		return $this->map_response( $request, isset( $result['body'] ) && is_array( $result['body'] ) ? $result['body'] : array() );
	}

	public function map_response( AI_Request $request, array $raw_response ): AI_Response {
		if ( isset( $raw_response['error'] ) && is_array( $raw_response['error'] ) ) {
			$error = OpenAI_Error_Mapper::from_http_status( 0, $raw_response, $request->get_request_id(), $request->get_task_type() );
			return AI_Response::failed( $request, $error );
		}

		$usage = $this->extract_usage( $request, $raw_response );
		return AI_Response::completed(
			$request,
			array(
				'provider'              => self::PROVIDER_ID,
				'model'                 => isset( $raw_response['model'] ) ? (string) $raw_response['model'] : '',
				'output_text'           => $this->extract_output_text( $raw_response ),
				'output_json'           => $this->extract_output_json( $raw_response ),
				'finish_reason'         => isset( $raw_response['status'] ) ? (string) $raw_response['status'] : '',
				'usage'                 => $usage->to_array(),
				'raw_response_redacted' => $this->compact_redacted_response( $raw_response ),
			)
		);
	}

	public function extract_output_text( array $raw_response ): string {
		if ( isset( $raw_response['output_text'] ) && is_string( $raw_response['output_text'] ) ) {
			return $raw_response['output_text'];
		}
		$text = $this->find_text_in_items( isset( $raw_response['output'] ) && is_array( $raw_response['output'] ) ? $raw_response['output'] : array() );
		if ( '' !== $text ) {
			return $text;
		}
		return $this->find_text_in_items( isset( $raw_response['message'] ) && is_array( $raw_response['message'] ) ? array( $raw_response['message'] ) : array() );
	}

	public function extract_output_json( array $raw_response ): array {
		if ( isset( $raw_response['output_json'] ) && is_array( $raw_response['output_json'] ) ) {
			return $raw_response['output_json'];
		}
		if ( isset( $raw_response['structured_output'] ) && is_array( $raw_response['structured_output'] ) ) {
			return $raw_response['structured_output'];
		}
		return array();
	}

	public function extract_usage( AI_Request $request, array $raw_response ): Usage_Record {
		$usage = isset( $raw_response['usage'] ) && is_array( $raw_response['usage'] ) ? $raw_response['usage'] : array();
		$input = isset( $usage['input_tokens'] ) ? (int) $usage['input_tokens'] : 0;
		$output = isset( $usage['output_tokens'] ) ? (int) $usage['output_tokens'] : 0;
		$reasoning = isset( $usage['output_tokens_details']['reasoning_tokens'] ) ? (int) $usage['output_tokens_details']['reasoning_tokens'] : 0;
		$total = isset( $usage['total_tokens'] ) ? (int) $usage['total_tokens'] : $input + $output;

		return new Usage_Record(
			array(
				'input_tokens'     => $input,
				'output_tokens'    => $output,
				'reasoning_tokens' => $reasoning,
				'total_tokens'     => $total,
				'model'            => isset( $raw_response['model'] ) ? (string) $raw_response['model'] : '',
				'provider'         => self::PROVIDER_ID,
				'task_type'        => $request->get_task_type(),
				'request_id'       => $request->get_request_id(),
			)
		);
	}

	private function build_text_format( string $schema_id ): array {
		$properties = array();
		foreach ( Structured_Output::required_fields( $schema_id ) as $field ) {
			$properties[ $field ] = array(
				'description' => 'Contract field: ' . $field,
			);
		}

		return array(
			'type'   => 'json_schema',
			'name'   => $schema_id,
			'schema' => array(
				'type'                 => 'object',
				'required'             => array_keys( $properties ),
				'properties'           => $properties,
				'additionalProperties' => true,
			),
		);
	}

	private function find_text_in_items( array $items ): string {
		$parts = array();
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			if ( isset( $item['text'] ) && is_string( $item['text'] ) ) {
				$parts[] = $item['text'];
			}
			if ( isset( $item['content'] ) && is_array( $item['content'] ) ) {
				$nested = $this->find_text_in_items( $item['content'] );
				if ( '' !== $nested ) {
					$parts[] = $nested;
				}
			}
		}
		return trim( implode( "\n", $parts ) );
	}

	private function compact_redacted_response( array $raw_response ): array {
		return array(
			'id'     => isset( $raw_response['id'] ) ? (string) $raw_response['id'] : '',
			'object' => isset( $raw_response['object'] ) ? (string) $raw_response['object'] : '',
			'model'  => isset( $raw_response['model'] ) ? (string) $raw_response['model'] : '',
			'status' => isset( $raw_response['status'] ) ? (string) $raw_response['status'] : '',
			'usage'  => isset( $raw_response['usage'] ) && is_array( $raw_response['usage'] ) ? $raw_response['usage'] : array(),
		);
	}
}
