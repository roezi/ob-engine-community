<?php
/** Addon scope definitions. */
namespace OBEngine\Addons;
defined( 'ABSPATH' ) || exit;
final class Addon_Scope {
	public const COMMUNITY = 'community';
	public const BASIC = 'basic';
	public const PRO = 'pro';
	public const PRIVATE_ADAPTER = 'private_adapter';
	public static function all(): array { return array_keys( self::labels() ); }
	public static function labels(): array { return array( self::COMMUNITY => __( 'Community', 'ob-engine' ), self::BASIC => __( 'Basic', 'ob-engine' ), self::PRO => __( 'Pro', 'ob-engine' ), self::PRIVATE_ADAPTER => __( 'Private Adapter', 'ob-engine' ) ); }
	public static function is_valid( string $scope ): bool { return in_array( $scope, self::all(), true ); }
	public static function label( string $scope ): string { $labels = self::labels(); return $labels[ $scope ] ?? $scope; }
	public static function badge_class( string $scope ): string { $map = array( self::COMMUNITY => 'is-community', self::BASIC => 'is-basic', self::PRO => 'is-pro', self::PRIVATE_ADAPTER => 'is-private-adapter' ); return 'ob-engine-addon-badge ' . ( $map[ $scope ] ?? 'is-community' ); }
}
