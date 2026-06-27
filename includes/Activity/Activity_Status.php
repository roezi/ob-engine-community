<?php
/** Activity status constants. @package OBEngine\Activity */
namespace OBEngine\Activity;
defined( 'ABSPATH' ) || exit;
final class Activity_Status {
	public const INFO = 'info'; public const SUCCESS = 'success'; public const WARNING = 'warning'; public const FAILED = 'failed';
	public static function all(): array { return array( self::INFO, self::SUCCESS, self::WARNING, self::FAILED ); }
	public static function labels(): array { return array( self::INFO => __( 'Info', 'ob-engine' ), self::SUCCESS => __( 'Success', 'ob-engine' ), self::WARNING => __( 'Warning', 'ob-engine' ), self::FAILED => __( 'Failed', 'ob-engine' ) ); }
	public static function is_valid( string $status ): bool { return in_array( $status, self::all(), true ); }
	public static function label( string $status ): string { $labels = self::labels(); return $labels[ $status ] ?? $labels[ self::INFO ]; }
	public static function badge_class( string $status ): string { $status = self::is_valid( $status ) ? $status : self::INFO; return 'ob-engine-status-badge is-' . sanitize_html_class( $status ); }
}
