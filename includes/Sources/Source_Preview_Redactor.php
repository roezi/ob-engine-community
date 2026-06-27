<?php
/** Source preview redaction helpers. @package OBEngine\Sources */
namespace OBEngine\Sources;
use OBEngine\Activity\Activity_Redactor;
defined( 'ABSPATH' ) || exit;
final class Source_Preview_Redactor {
	private $activity_redactor;
	public function __construct( ?Activity_Redactor $activity_redactor = null ) { $this->activity_redactor = $activity_redactor ?: new Activity_Redactor(); }
	public function redact_value( $value ) {
		if ( is_array( $value ) ) { return $this->redact_row( $value ); }
		if ( is_object( $value ) ) { return '[REDACTED_PRIVATE_PAYLOAD]'; }
		if ( ! is_string( $value ) ) { return ( is_scalar( $value ) || null === $value ) ? $value : '[REDACTED_PRIVATE_PAYLOAD]'; }
		$value = $this->activity_redactor->redact_string( $this->truncate_string( $value ) );
		$value = preg_replace( '/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', '[REDACTED_EMAIL]', $value );
		$value = preg_replace( '/\b(?:api[_-]?key|token|secret|password|cookie|authorization)\b\s*[:=]\s*[^\s,;&]+/i', '$1=[REDACTED_SECRET]', $value );
		$value = preg_replace( '/\b[a-f0-9]{32,}\b/i', '[REDACTED_TOKEN]', $value );
		$value = preg_replace( '/\b[A-Za-z0-9_\-]{28,}\.[A-Za-z0-9_\-]{12,}\.[A-Za-z0-9_\-]{12,}\b/', '[REDACTED_TOKEN]', $value );
		$value = preg_replace( '/\b(prompt|private_prompt)\b\s*[:=].*/i', '$1=[REDACTED_PRIVATE_PROMPT]', $value );
		$value = preg_replace( '/\b(field[_ -]?map|private_field_map)\b\s*[:=].*/i', '$1=[REDACTED_PRIVATE_FIELD_MAP]', $value );
		return is_string( $value ) ? $value : '[REDACTED_PRIVATE_PAYLOAD]';
	}
	public function redact_row( array $row ): array { $out = array(); foreach ( $row as $key => $value ) { $key_string = strtolower( (string) $key ); if ( preg_match( '/api_key|key|secret|token|bearer|authorization|cookie|password|credential|endpoint|private_prompt|private_field_map/', $key_string ) ) { $out[ $key ] = '[REDACTED_SECRET]'; } else { $out[ $key ] = $this->redact_value( $value ); } } return $out; }
	public function redact_rows( array $rows ): array { $out = array(); foreach ( array_slice( $rows, 0, 10 ) as $row ) { $out[] = is_array( $row ) ? $this->redact_row( $row ) : $this->redact_value( $row ); } return $out; }
	public function truncate_string( string $value, int $max_length = 240 ): string { $length = function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value ); if ( $length <= $max_length ) { return $value; } $slice = function_exists( 'mb_substr' ) ? mb_substr( $value, 0, $max_length ) : substr( $value, 0, $max_length ); return $slice . ' [REDACTED_TRUNCATED]'; }
}
