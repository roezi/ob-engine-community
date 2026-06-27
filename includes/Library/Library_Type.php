<?php
/**
 * Library item type definitions.
 *
 * @package OBEngine\Library
 */

namespace OBEngine\Library;

defined( 'ABSPATH' ) || exit;

/**
 * Centralized OBE Library item types.
 */
final class Library_Type {
	public const SOURCE_PREVIEW     = 'source_preview';
	public const IMPORT_PREVIEW     = 'import_preview';
	public const FIELD_MAPPING      = 'field_mapping';
	public const AUTO_POST_PLAN     = 'auto_post_plan';
	public const CONTENT_DRAFT      = 'content_draft';
	public const CONTENT_REVIEW     = 'content_review';
	public const SEO_REVIEW         = 'seo_review';
	public const TRANSLATION_PLAN   = 'translation_plan';
	public const PERFORMANCE_REPORT = 'performance_report';
	public const WORKFLOW_PLAN      = 'workflow_plan';
	public const WORKFLOW_RUN       = 'workflow_run';

	public static function all(): array {
		return array_keys( self::labels() );
	}

	public static function labels(): array {
		return array(
			self::SOURCE_PREVIEW     => __( 'Source Preview', 'ob-engine' ),
			self::IMPORT_PREVIEW     => __( 'Import Preview', 'ob-engine' ),
			self::FIELD_MAPPING      => __( 'Field Mapping', 'ob-engine' ),
			self::AUTO_POST_PLAN     => __( 'Auto Post Plan', 'ob-engine' ),
			self::CONTENT_DRAFT      => __( 'Content Draft', 'ob-engine' ),
			self::CONTENT_REVIEW     => __( 'Content Review', 'ob-engine' ),
			self::SEO_REVIEW         => __( 'SEO Review', 'ob-engine' ),
			self::TRANSLATION_PLAN   => __( 'Translation Plan', 'ob-engine' ),
			self::PERFORMANCE_REPORT => __( 'Performance Report', 'ob-engine' ),
			self::WORKFLOW_PLAN      => __( 'Workflow Plan', 'ob-engine' ),
			self::WORKFLOW_RUN       => __( 'Workflow Run', 'ob-engine' ),
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
