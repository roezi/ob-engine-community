<?php
/**
 * Source intake type definitions.
 *
 * @package OBEngine\Sources
 */

namespace OBEngine\Sources;

defined( 'ABSPATH' ) || exit;

/**
 * Supported safe source preview input types.
 */
final class Source_Type {
	public const PASTE_TEXT  = 'paste_text';
	public const CSV_TEXT    = 'csv_text';
	public const JSON_TEXT   = 'json_text';
	public const MANUAL_ITEM = 'manual_item';

	public static function all(): array {
		return array_keys( self::labels() );
	}

	public static function labels(): array {
		return array(
			self::PASTE_TEXT  => __( 'Paste text', 'ob-engine' ),
			self::CSV_TEXT    => __( 'CSV text', 'ob-engine' ),
			self::JSON_TEXT   => __( 'JSON text', 'ob-engine' ),
			self::MANUAL_ITEM => __( 'Manual item', 'ob-engine' ),
		);
	}

	public static function is_valid( string $type ): bool {
		return in_array( $type, self::all(), true );
	}

	public static function label( string $type ): string {
		$labels = self::labels();
		return $labels[ $type ] ?? $type;
	}
}
