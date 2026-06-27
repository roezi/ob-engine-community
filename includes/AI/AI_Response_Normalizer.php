<?php
/**
 * AI response normalizer.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Normalizes provider responses before callers or Activity use them.
 */
final class AI_Response_Normalizer {
	public function normalize( AI_Request $request, AI_Response $response ): AI_Response {
		$data = $response->to_array();
		$data['request_id'] = '' !== (string) $data['request_id'] ? (string) $data['request_id'] : $request->get_request_id();
		$data['output_json'] = isset( $data['output_json'] ) && is_array( $data['output_json'] ) ? $data['output_json'] : array();
		$data['usage'] = isset( $data['usage'] ) && is_array( $data['usage'] ) ? $data['usage'] : array();
		$data['raw_response_redacted'] = isset( $data['raw_response_redacted'] ) && is_array( $data['raw_response_redacted'] ) ? $data['raw_response_redacted'] : array();
		unset( $data['raw_response'], $data['api_key'], $data['token'], $data['prompt'] );
		return AI_Response::from_array( $data );
	}

	public function safe_activity_context( AI_Request $request, AI_Response $response ): array {
		$request_data = $request->to_array();
		$response_data = $response->to_array();
		$context = array(
			'request_id'      => $request->get_request_id(),
			'task_type'       => $request->get_task_type(),
			'provider'        => isset( $response_data['provider'] ) ? (string) $response_data['provider'] : '',
			'model'           => isset( $response_data['model'] ) && '' !== (string) $response_data['model'] ? (string) $response_data['model'] : ( isset( $request_data['model'] ) ? (string) $request_data['model'] : '' ),
			'status'          => $response->get_status(),
			'has_output_text' => '' !== $response->get_output_text(),
			'has_output_json' => array() !== $response->get_output_json(),
			'usage_present'   => ! empty( $response_data['usage'] ),
		);

		if ( ! empty( $response_data['library_item_id'] ) ) {
			$context['library_item_id'] = (int) $response_data['library_item_id'];
		}
		if ( ! empty( $response_data['activity_id'] ) ) {
			$context['activity_id'] = (int) $response_data['activity_id'];
		}

		return $context;
	}
}
