<?php
/**
 * OpenAI error mapper.
 *
 * @package OBEngine\Providers\OpenAI
 */

namespace OBEngine\Providers\OpenAI;

use OBEngine\AI\AI_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Converts OpenAI transport/API failures into safe normalized AI errors.
 */
final class OpenAI_Error_Mapper {
	public static function from_http_status( int $status, array $body = array(), string $request_id = '', string $task_type = '' ): AI_Error {
		$code = AI_Error::UNKNOWN_ERROR;
		$message = 'Provider request failed.';
		$retryable = false;
		$severity = AI_Error::SEVERITY_ERROR;

		if ( 401 === $status || 403 === $status ) {
			$code = AI_Error::PROVIDER_AUTH_FAILED;
			$message = 'Provider authentication failed. Check the saved API key.';
		} elseif ( 429 === $status ) {
			$code = AI_Error::PROVIDER_RATE_LIMITED;
			$message = 'Provider rate limit reached. Try again later.';
			$retryable = true;
			$severity = AI_Error::SEVERITY_WARNING;
		} elseif ( 408 === $status || 504 === $status ) {
			$code = AI_Error::PROVIDER_TIMEOUT;
			$message = 'Provider request timed out. Try again.';
			$retryable = true;
			$severity = AI_Error::SEVERITY_WARNING;
		} elseif ( 400 === $status || 422 === $status ) {
			$code = AI_Error::INVALID_REQUEST;
			$message = 'Provider rejected the request.';
		} elseif ( 500 <= $status ) {
			$code = AI_Error::PROVIDER_SERVER_ERROR;
			$message = 'Provider server error. Try again later.';
			$retryable = true;
		}

		return new AI_Error(
			array(
				'code'                      => $code,
				'provider_code'             => self::provider_code( $body ),
				'message_public'            => $message,
				'message_internal_redacted' => self::internal_message( $status, $body ),
				'retryable'                 => $retryable,
				'severity'                  => $severity,
				'request_id'                => $request_id,
				'provider'                  => OpenAI_Responses_Client::PROVIDER_ID,
				'task_type'                 => $task_type,
			)
		);
	}

	public static function from_wp_error( $wp_error, string $request_id = '', string $task_type = '' ): AI_Error {
		$provider_code = '';
		if ( is_object( $wp_error ) && method_exists( $wp_error, 'get_error_code' ) ) {
			$provider_code = (string) $wp_error->get_error_code();
		}

		return new AI_Error(
			array(
				'code'                      => AI_Error::PROVIDER_TIMEOUT,
				'provider_code'             => self::redact_text( $provider_code ),
				'message_public'            => 'Provider request timed out. Try again.',
				'message_internal_redacted' => 'WordPress HTTP error: ' . self::redact_text( $provider_code ),
				'retryable'                 => true,
				'severity'                  => AI_Error::SEVERITY_WARNING,
				'request_id'                => $request_id,
				'provider'                  => OpenAI_Responses_Client::PROVIDER_ID,
				'task_type'                 => $task_type,
			)
		);
	}

	public static function from_exception_message( string $message, string $request_id = '', string $task_type = '' ): AI_Error {
		return new AI_Error(
			array(
				'code'                      => AI_Error::UNKNOWN_ERROR,
				'message_public'            => 'Provider request failed.',
				'message_internal_redacted' => self::redact_text( $message ),
				'retryable'                 => false,
				'severity'                  => AI_Error::SEVERITY_ERROR,
				'request_id'                => $request_id,
				'provider'                  => OpenAI_Responses_Client::PROVIDER_ID,
				'task_type'                 => $task_type,
			)
		);
	}

	private static function provider_code( array $body ): string {
		if ( isset( $body['error'] ) && is_array( $body['error'] ) && isset( $body['error']['code'] ) ) {
			return self::redact_text( (string) $body['error']['code'] );
		}
		return '';
	}

	private static function internal_message( int $status, array $body ): string {
		$message = 'HTTP status ' . (string) $status;
		if ( isset( $body['error'] ) && is_array( $body['error'] ) && isset( $body['error']['message'] ) ) {
			$message .= ': ' . self::redact_text( (string) $body['error']['message'] );
		}
		return $message;
	}

	private static function redact_text( string $text ): string {
		$text = preg_replace( '/Bearer\s+[A-Za-z0-9._\-]+/i', 'Bearer [redacted]', $text );
		$text = preg_replace( '/sk-[A-Za-z0-9_\-]+/i', '[redacted-api-key]', (string) $text );
		$text = preg_replace( '#https://[^\s?]+\?[^\s]+#i', '[redacted-url]', (string) $text );
		return (string) $text;
	}
}
