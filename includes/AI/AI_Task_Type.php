<?php
/**
 * AI task type contract.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Defines supported AI task types and their default model profiles.
 */
final class AI_Task_Type {
	public const CLASSIFY_INTENT = 'classify_intent';
	public const VALIDATE_SOURCE = 'validate_source';
	public const MAP_FIELDS = 'map_fields';
	public const GENERATE_AUTO_POST_PLAN = 'generate_auto_post_plan';
	public const GENERATE_RESEARCH_PLAN = 'generate_research_plan';
	public const GENERATE_CONTENT_DRAFT = 'generate_content_draft';
	public const IMPROVE_CONTENT_DRAFT = 'improve_content_draft';
	public const REVIEW_CONTENT = 'review_content';
	public const GENERATE_SEO_REVIEW = 'generate_seo_review';
	public const GENERATE_TRANSLATION_PLAN = 'generate_translation_plan';
	public const GENERATE_PERFORMANCE_REPORT = 'generate_performance_report';
	public const SUMMARIZE_ACTIVITY = 'summarize_activity';
	public const EXPLAIN_ERROR = 'explain_error';
	public const WORKFLOW_PLAN = 'workflow_plan';

	public static function all(): array {
		return array(
			self::CLASSIFY_INTENT,
			self::VALIDATE_SOURCE,
			self::MAP_FIELDS,
			self::GENERATE_AUTO_POST_PLAN,
			self::GENERATE_RESEARCH_PLAN,
			self::GENERATE_CONTENT_DRAFT,
			self::IMPROVE_CONTENT_DRAFT,
			self::REVIEW_CONTENT,
			self::GENERATE_SEO_REVIEW,
			self::GENERATE_TRANSLATION_PLAN,
			self::GENERATE_PERFORMANCE_REPORT,
			self::SUMMARIZE_ACTIVITY,
			self::EXPLAIN_ERROR,
			self::WORKFLOW_PLAN,
		);
	}

	public static function is_valid( string $task_type ): bool {
		return in_array( $task_type, self::all(), true );
	}

	public static function default_model_profile( string $task_type ): string {
		if ( in_array( $task_type, array( self::REVIEW_CONTENT, self::WORKFLOW_PLAN ), true ) ) {
			return Model_Profile::DEEP;
		}

		if ( in_array( $task_type, array( self::CLASSIFY_INTENT, self::VALIDATE_SOURCE, self::SUMMARIZE_ACTIVITY, self::EXPLAIN_ERROR ), true ) ) {
			return Model_Profile::FAST;
		}

		return Model_Profile::BALANCED;
	}
}
