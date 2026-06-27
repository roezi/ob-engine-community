<?php
/** Addon status definitions. */
namespace OBEngine\Addons;
defined( 'ABSPATH' ) || exit;
final class Addon_Status {
	public const ENABLED = 'enabled';
	public const DISABLED = 'disabled';
	public const COMING_SOON = 'coming_soon';
	public const PRO = 'pro';
	public const NEEDS_SETUP = 'needs_setup';
	public static function all(): array { return array_keys( self::labels() ); }
	public static function labels(): array { return array( self::ENABLED => __( 'Enabled', 'ob-engine' ), self::DISABLED => __( 'Disabled', 'ob-engine' ), self::COMING_SOON => __( 'Coming Soon', 'ob-engine' ), self::PRO => __( 'Pro', 'ob-engine' ), self::NEEDS_SETUP => __( 'Needs Setup', 'ob-engine' ) ); }
	public static function is_valid( string $status ): bool { return in_array( $status, self::all(), true ); }
	public static function label( string $status ): string { $labels = self::labels(); return $labels[ $status ] ?? $status; }
	public static function badge_class( string $status ): string { $map = array( self::ENABLED => 'is-enabled', self::DISABLED => 'is-disabled', self::COMING_SOON => 'is-coming-soon', self::PRO => 'is-pro', self::NEEDS_SETUP => 'is-needs-setup' ); return 'ob-engine-addon-badge ' . ( $map[ $status ] ?? 'is-disabled' ); }
}
