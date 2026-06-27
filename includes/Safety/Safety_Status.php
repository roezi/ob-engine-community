<?php
/** Safety status constants. @package OBEngine\Safety */
namespace OBEngine\Safety;
defined( 'ABSPATH' ) || exit;
final class Safety_Status {
	public const NOT_CHECKED = 'not_checked';
	public const PASSED      = 'passed';
	public const WARNING     = 'warning';
	public const FAILED      = 'failed';
	public static function all(): array { return array_keys( self::labels() ); }
	public static function labels(): array { return array( self::NOT_CHECKED => __( 'Not checked', 'ob-engine' ), self::PASSED => __( 'Passed', 'ob-engine' ), self::WARNING => __( 'Warning', 'ob-engine' ), self::FAILED => __( 'Failed', 'ob-engine' ) ); }
	public static function is_valid( string $status ): bool { return in_array( $status, self::all(), true ); }
	public static function label( string $status ): string { $labels = self::labels(); return $labels[ $status ] ?? $labels[ self::NOT_CHECKED ]; }
	public static function badge_class( string $status ): string { $map = array( self::NOT_CHECKED => 'is-draft', self::PASSED => 'is-approved', self::WARNING => 'is-warning', self::FAILED => 'is-danger' ); return 'ob-engine-status-badge ' . ( $map[ $status ] ?? $map[ self::NOT_CHECKED ] ); }
}
