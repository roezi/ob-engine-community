<?php
/**
 * AI model profile contract.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Defines supported internal model profiles and safe defaults.
 */
final class Model_Profile {
	public const FAST = 'fast';
	public const BALANCED = 'balanced';
	public const DEEP = 'deep';
	public const BACKGROUND = 'background';

	public static function all(): array {
		return array( self::FAST, self::BALANCED, self::DEEP, self::BACKGROUND );
	}

	public static function is_valid( string $profile ): bool {
		return in_array( $profile, self::all(), true );
	}

	public static function defaults( string $profile ): array {
		switch ( $profile ) {
			case self::FAST:
				return array( 'reasoning_effort' => 'low', 'verbosity' => 'low', 'background' => false );
			case self::DEEP:
				return array( 'reasoning_effort' => 'high', 'verbosity' => 'medium', 'background' => false );
			case self::BACKGROUND:
				return array( 'reasoning_effort' => 'medium', 'verbosity' => 'medium', 'background' => true );
			case self::BALANCED:
			default:
				return array( 'reasoning_effort' => 'medium', 'verbosity' => 'medium', 'background' => false );
		}
	}
}
