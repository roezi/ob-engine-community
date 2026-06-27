<?php
/**
 * AI error value object.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Normalized, redacted AI error contract.
 */
final class AI_Error {
	public const MISSING_PROVIDER_KEY = 'missing_provider_key';
	public const UNSUPPORTED_PROVIDER = 'unsupported_provider';
	public const UNSUPPORTED_CAPABILITY = 'unsupported_capability';
	public const INVALID_REQUEST = 'invalid_request';
	public const PROVIDER_AUTH_FAILED = 'provider_auth_failed';
	public const PROVIDER_RATE_LIMITED = 'provider_rate_limited';
	public const PROVIDER_TIMEOUT = 'provider_timeout';
	public const PROVIDER_SERVER_ERROR = 'provider_server_error';
	public const STRUCTURED_OUTPUT_INVALID = 'structured_output_invalid';
	public const REDACTION_FAILED = 'redaction_failed';
	public const UNKNOWN_ERROR = 'unknown_error';

	public const SEVERITY_INFO = 'info';
	public const SEVERITY_WARNING = 'warning';
	public const SEVERITY_ERROR = 'error';
	public const SEVERITY_CRITICAL = 'critical';

	private $data;

	public function __construct( array $data = array() ) {
		$this->data = array(
			'code'                       => isset( $data['code'] ) ? (string) $data['code'] : self::UNKNOWN_ERROR,
			'provider_code'              => isset( $data['provider_code'] ) ? (string) $data['provider_code'] : '',
			'message_public'             => isset( $data['message_public'] ) ? (string) $data['message_public'] : 'An unknown AI error occurred.',
			'message_internal_redacted'  => isset( $data['message_internal_redacted'] ) ? (string) $data['message_internal_redacted'] : '',
			'retryable'                  => ! empty( $data['retryable'] ),
			'severity'                   => isset( $data['severity'] ) ? (string) $data['severity'] : self::SEVERITY_ERROR,
			'request_id'                 => isset( $data['request_id'] ) ? (string) $data['request_id'] : '',
			'provider'                   => isset( $data['provider'] ) ? (string) $data['provider'] : '',
			'task_type'                  => isset( $data['task_type'] ) ? (string) $data['task_type'] : '',
		);
	}

	public static function from_array( array $data ): self { return new self( $data ); }

	public static function missing_provider_key(): self {
		return new self( array( 'code' => self::MISSING_PROVIDER_KEY, 'message_public' => 'Provider API key is missing.', 'severity' => self::SEVERITY_WARNING ) );
	}

	public static function invalid_request( string $message_public, array $context = array() ): self {
		$context['code'] = self::INVALID_REQUEST;
		$context['message_public'] = $message_public;
		$context['severity'] = isset( $context['severity'] ) ? $context['severity'] : self::SEVERITY_ERROR;
		return new self( $context );
	}

	public function to_array(): array { return $this->data; }
	public function get_public_message(): string { return $this->data['message_public']; }
	public function is_retryable(): bool { return (bool) $this->data['retryable']; }
}
