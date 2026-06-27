<?php
/**
 * Provider capability result value object.
 *
 * @package OBEngine\Providers
 */

namespace OBEngine\Providers;

defined( 'ABSPATH' ) || exit;

/**
 * Wraps provider capability/status metadata.
 */
final class Provider_Result {
	private $data;

	public function __construct( array $data = array() ) {
		$this->data = array(
			'provider'   => isset( $data['provider'] ) ? (string) $data['provider'] : '',
			'capability' => isset( $data['capability'] ) ? (string) $data['capability'] : '',
			'supported'  => ! empty( $data['supported'] ),
			'message'    => isset( $data['message'] ) ? (string) $data['message'] : '',
			'metadata'   => isset( $data['metadata'] ) && is_array( $data['metadata'] ) ? $data['metadata'] : array(),
		);
	}

	public static function supported( string $provider, string $capability ): self {
		return new self( array( 'provider' => $provider, 'capability' => $capability, 'supported' => true ) );
	}

	public static function unsupported( string $provider, string $capability, string $message = '' ): self {
		return new self( array( 'provider' => $provider, 'capability' => $capability, 'supported' => false, 'message' => $message ) );
	}

	public function to_array(): array { return $this->data; }
}
