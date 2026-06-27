<?php
/**
 * Secret masking helpers.
 *
 * @package OBEngine\Support
 */

namespace OBEngine\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Masks secrets for safe admin display.
 */
final class Secret_Masker {
	/**
	 * Return a masked secret status without exposing the raw value.
	 */
	public static function mask( string $secret ): string {
		$secret = trim( $secret );

		if ( '' === $secret ) {
			return '';
		}

		$length = function_exists( 'mb_strlen' ) ? mb_strlen( $secret ) : strlen( $secret );
		$tail_length = min( 4, $length );
		$tail = function_exists( 'mb_substr' ) ? mb_substr( $secret, -$tail_length ) : substr( $secret, -$tail_length );

		return '••••••' . $tail;
	}
}
