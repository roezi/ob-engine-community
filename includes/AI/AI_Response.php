<?php
/**
 * AI response value object.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Normalized provider response with redacted diagnostics only.
 */
final class AI_Response {
	public const STATUS_COMPLETED = 'completed';
	public const STATUS_FAILED = 'failed';
	public const STATUS_REFUSED = 'refused';
	public const STATUS_PARTIAL = 'partial';
	public const STATUS_CANCELLED = 'cancelled';

	private $data;

	public function __construct( array $data = array() ) {
		$this->data = array(
			'request_id'             => isset( $data['request_id'] ) ? (string) $data['request_id'] : '',
			'provider'               => isset( $data['provider'] ) ? (string) $data['provider'] : '',
			'model'                  => isset( $data['model'] ) ? (string) $data['model'] : '',
			'status'                 => isset( $data['status'] ) ? (string) $data['status'] : self::STATUS_FAILED,
			'output_text'            => isset( $data['output_text'] ) ? (string) $data['output_text'] : '',
			'output_json'            => isset( $data['output_json'] ) && is_array( $data['output_json'] ) ? $data['output_json'] : array(),
			'refusal'                => isset( $data['refusal'] ) ? (string) $data['refusal'] : '',
			'finish_reason'          => isset( $data['finish_reason'] ) ? (string) $data['finish_reason'] : '',
			'usage'                  => isset( $data['usage'] ) && is_array( $data['usage'] ) ? $data['usage'] : array(),
			'error'                  => isset( $data['error'] ) && is_array( $data['error'] ) ? $data['error'] : array(),
			'raw_response_redacted'  => isset( $data['raw_response_redacted'] ) && is_array( $data['raw_response_redacted'] ) ? $data['raw_response_redacted'] : array(),
			'library_item_id'        => isset( $data['library_item_id'] ) ? (int) $data['library_item_id'] : 0,
			'activity_id'            => isset( $data['activity_id'] ) ? (int) $data['activity_id'] : 0,
			'created_at'             => isset( $data['created_at'] ) ? (string) $data['created_at'] : gmdate( 'c' ),
		);
	}

	public static function completed( AI_Request $request, array $data ): self {
		$data['request_id'] = $request->get_request_id();
		$data['status'] = self::STATUS_COMPLETED;
		return new self( $data );
	}

	public static function failed( AI_Request $request, AI_Error $error ): self {
		return new self( array( 'request_id' => $request->get_request_id(), 'status' => self::STATUS_FAILED, 'error' => $error->to_array() ) );
	}

	public static function from_array( array $data ): self { return new self( $data ); }
	public function to_array(): array { return $this->data; }
	public function is_success(): bool { return self::STATUS_COMPLETED === $this->data['status']; }
	public function get_status(): string { return $this->data['status']; }
	public function get_output_text(): string { return $this->data['output_text']; }
	public function get_output_json(): array { return $this->data['output_json']; }
}
