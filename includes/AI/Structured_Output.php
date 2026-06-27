<?php
/**
 * Structured output schema registry.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Public schema IDs and minimal structural expectations.
 */
final class Structured_Output {
	public const INTENT_CLASSIFICATION_V1 = 'intent_classification_v1';
	public const SOURCE_VALIDATION_V1 = 'source_validation_v1';
	public const FIELD_MAPPING_PLAN_V1 = 'field_mapping_plan_v1';
	public const AUTO_POST_PLAN_V1 = 'auto_post_plan_v1';
	public const RESEARCH_PLAN_V1 = 'research_plan_v1';
	public const CONTENT_DRAFT_V1 = 'content_draft_v1';
	public const CONTENT_REVIEW_V1 = 'content_review_v1';
	public const EDITORIAL_REVISION_V1 = 'editorial_revision_v1';
	public const SEO_REVIEW_V1 = 'seo_review_v1';
	public const TRANSLATION_PLAN_V1 = 'translation_plan_v1';
	public const PERFORMANCE_REPORT_V1 = 'performance_report_v1';
	public const WORKFLOW_PLAN_V1 = 'workflow_plan_v1';
	public const ERROR_EXPLANATION_V1 = 'error_explanation_v1';

	public static function all(): array {
		return array( self::INTENT_CLASSIFICATION_V1, self::SOURCE_VALIDATION_V1, self::FIELD_MAPPING_PLAN_V1, self::AUTO_POST_PLAN_V1, self::RESEARCH_PLAN_V1, self::CONTENT_DRAFT_V1, self::CONTENT_REVIEW_V1, self::EDITORIAL_REVISION_V1, self::SEO_REVIEW_V1, self::TRANSLATION_PLAN_V1, self::PERFORMANCE_REPORT_V1, self::WORKFLOW_PLAN_V1, self::ERROR_EXPLANATION_V1 );
	}

	public static function is_valid( string $schema_id ): bool { return in_array( $schema_id, self::all(), true ); }

	public static function required_fields( string $schema_id ): array {
		switch ( $schema_id ) {
			case self::AUTO_POST_PLAN_V1:
				return array( 'source_summary', 'missing_fields', 'proposed_post_type', 'title_options', 'slug_suggestion', 'meta_title', 'meta_description', 'outline', 'research_checklist', 'image_prompt_suggestions', 'custom_field_mapping', 'safety_notes', 'default_status', 'approval_question' );
			case self::CONTENT_DRAFT_V1:
				return array( 'title', 'content', 'excerpt', 'meta_title', 'meta_description', 'status', 'safety_notes' );
			case self::EDITORIAL_REVISION_V1:
				return array( 'revised_title', 'revised_content', 'revised_excerpt', 'readability_score', 'tone_score', 'localization_score', 'revision_notes', 'safety_notes', 'default_status' );
			case self::SEO_REVIEW_V1:
				return array( 'title_review', 'meta_description_review', 'slug_review', 'heading_review', 'internal_link_suggestions', 'schema_suggestions', 'safety_notes' );
			case self::INTENT_CLASSIFICATION_V1:
				return array( 'intent', 'confidence', 'safety_notes' );
			case self::SOURCE_VALIDATION_V1:
				return array( 'is_usable', 'missing_fields', 'risk_notes' );
			case self::FIELD_MAPPING_PLAN_V1:
				return array( 'source_fields', 'target_fields', 'mapping_notes' );
			case self::RESEARCH_PLAN_V1:
				return array( 'research_questions', 'validation_steps', 'safety_notes' );
			case self::CONTENT_REVIEW_V1:
				return array( 'summary', 'recommendations', 'safety_notes' );
			case self::TRANSLATION_PLAN_V1:
				return array( 'source_locale', 'target_locale', 'review_steps', 'safety_notes' );
			case self::PERFORMANCE_REPORT_V1:
				return array( 'summary', 'observations', 'recommended_actions' );
			case self::WORKFLOW_PLAN_V1:
				return array( 'steps', 'approval_gates', 'rollback_notes' );
			case self::ERROR_EXPLANATION_V1:
				return array( 'summary', 'next_steps' );
			default:
				return array();
		}
	}

	public static function target_library_type( string $schema_id ): string {
		$map = array(
			self::AUTO_POST_PLAN_V1 => 'auto_post_plan',
			self::CONTENT_DRAFT_V1 => 'content_draft',
			self::CONTENT_REVIEW_V1 => 'content_review',
			self::EDITORIAL_REVISION_V1 => 'editorial_revision',
			self::SEO_REVIEW_V1 => 'seo_review',
			self::TRANSLATION_PLAN_V1 => 'translation_plan',
			self::PERFORMANCE_REPORT_V1 => 'performance_report',
			self::WORKFLOW_PLAN_V1 => 'workflow_plan',
			self::FIELD_MAPPING_PLAN_V1 => 'field_mapping',
			self::RESEARCH_PLAN_V1 => 'workflow_plan',
		);
		return isset( $map[ $schema_id ] ) ? $map[ $schema_id ] : '';
	}
}
