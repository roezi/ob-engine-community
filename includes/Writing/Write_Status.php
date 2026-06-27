<?php
/** Write status definitions. @package OBEngine\Writing */
namespace OBEngine\Writing;
defined( 'ABSPATH' ) || exit;
final class Write_Status {
	public const NOT_WRITTEN = 'not_written';
	public const WRITTEN     = 'written';
	public const FAILED      = 'failed';
	public static function all(): array { return array_keys( self::labels() ); }
	public static function labels(): array { return array( self::NOT_WRITTEN => __( 'Not written', 'ob-engine' ), self::WRITTEN => __( 'Written', 'ob-engine' ), self::FAILED => __( 'Failed', 'ob-engine' ) ); }
	public static function is_valid( string $status ): bool { return in_array( $status, self::all(), true ); }
	public static function label( string $status ): string { $labels = self::labels(); return $labels[ $status ] ?? $labels[ self::NOT_WRITTEN ]; }
	public static function badge_class( string $status ): string { $map = array( self::NOT_WRITTEN => 'is-draft', self::WRITTEN => 'is-written', self::FAILED => 'is-danger' ); return 'ob-engine-status-badge ' . ( $map[ $status ] ?? $map[ self::NOT_WRITTEN ] ); }
}
