<?php
/**
 * Library status definitions.
 *
 * @package OBEngine\Library
 */

namespace OBEngine\Library;

defined( 'ABSPATH' ) || exit;

/**
 * Centralized OBE Library statuses.
 */
final class Library_Status {
	public const DRAFT        = 'draft';
	public const NEEDS_REVIEW = 'needs_review';
	public const APPROVED     = 'approved';
	public const REJECTED     = 'rejected';
	public const WRITTEN      = 'written';
	public const ARCHIVED     = 'archived';
	public const FAILED       = 'failed';

	public static function all(): array {
		return array_keys( self::labels() );
	}

	public static function labels(): array {
		return array(
			self::DRAFT        => __( 'Draft', 'ob-engine' ),
			self::NEEDS_REVIEW => __( 'Needs Review', 'ob-engine' ),
			self::APPROVED     => __( 'Approved', 'ob-engine' ),
			self::REJECTED     => __( 'Rejected', 'ob-engine' ),
			self::WRITTEN      => __( 'Written', 'ob-engine' ),
			self::ARCHIVED     => __( 'Archived', 'ob-engine' ),
			self::FAILED       => __( 'Failed', 'ob-engine' ),
		);
	}

	public static function is_valid( string $status ): bool {
		return in_array( $status, self::all(), true );
	}

	public static function label( string $status ): string {
		$labels = self::labels();
		return $labels[ $status ] ?? $status;
	}

	public static function default_status(): string {
		return self::DRAFT;
	}

	public static function badge_class( string $status ): string {
		$map = array(
			self::APPROVED     => 'is-approved',
			self::NEEDS_REVIEW => 'is-needs-review',
			self::REJECTED     => 'is-danger',
			self::FAILED       => 'is-danger',
			self::WRITTEN      => 'is-written',
			self::ARCHIVED     => 'is-archived',
		);

		return 'ob-engine-status-badge ' . ( $map[ $status ] ?? 'is-draft' );
	}
}
