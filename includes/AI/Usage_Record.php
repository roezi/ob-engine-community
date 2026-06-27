<?php
/**
 * AI usage record value object.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Normalized token usage metadata without pricing calculations.
 */
final class Usage_Record {
	private $data;

	public function __construct( array $data = array() ) {
		$this->data = array(
			'input_tokens'                 => isset( $data['input_tokens'] ) ? (int) $data['input_tokens'] : 0,
			'output_tokens'                => isset( $data['output_tokens'] ) ? (int) $data['output_tokens'] : 0,
			'reasoning_tokens'             => isset( $data['reasoning_tokens'] ) ? (int) $data['reasoning_tokens'] : 0,
			'total_tokens'                 => isset( $data['total_tokens'] ) ? (int) $data['total_tokens'] : 0,
			'model'                        => isset( $data['model'] ) ? (string) $data['model'] : '',
			'provider'                     => isset( $data['provider'] ) ? (string) $data['provider'] : '',
			'task_type'                    => isset( $data['task_type'] ) ? (string) $data['task_type'] : '',
			'request_id'                   => isset( $data['request_id'] ) ? (string) $data['request_id'] : '',
			'library_item_id'              => isset( $data['library_item_id'] ) ? (int) $data['library_item_id'] : 0,
			'estimated_cost_placeholder'   => isset( $data['estimated_cost_placeholder'] ) ? $data['estimated_cost_placeholder'] : null,
			'created_at'                   => isset( $data['created_at'] ) ? (string) $data['created_at'] : gmdate( 'c' ),
		);
	}

	public static function from_array( array $data ): self { return new self( $data ); }
	public function to_array(): array { return $this->data; }
}
