<?php
/**
 * Approval status definitions.
 *
 * @package OBEngine\Approval
 */

namespace OBEngine\Approval;

defined( 'ABSPATH' ) || exit;

final class Approval_Status {
	public const PENDING  = 'pending';
	public const APPROVED = 'approved';
	public const REJECTED = 'rejected';
	public const REVOKED  = 'revoked';

	public static function all(): array {
		return array_keys( self::labels() );
	}

	public static function labels(): array {
		return array(
			self::PENDING  => __( 'Pending', 'ob-engine' ),
			self::APPROVED => __( 'Approved', 'ob-engine' ),
			self::REJECTED => __( 'Rejected', 'ob-engine' ),
			self::REVOKED  => __( 'Revoked', 'ob-engine' ),
		);
	}

	public static function is_valid( string $status ): bool {
		return in_array( $status, self::all(), true );
	}

	public static function label( string $status ): string {
		$labels = self::labels();
		return $labels[ $status ] ?? $labels[ self::PENDING ];
	}

	public static function badge_class( string $status ): string {
		$map = array(
			self::PENDING  => 'is-needs-review',
			self::APPROVED => 'is-approved',
			self::REJECTED => 'is-danger',
			self::REVOKED  => 'is-warning',
		);

		return 'ob-engine-status-badge ' . ( $map[ $status ] ?? $map[ self::PENDING ] );
	}
}
