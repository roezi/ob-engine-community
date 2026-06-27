<?php
/**
 * AI task profile resolver.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

defined( 'ABSPATH' ) || exit;

/**
 * Resolves task defaults for model profiles and structured schemas.
 */
final class AI_Task_Profile_Resolver {
	public function resolve_model_profile( string $task_type, string $requested_profile = '' ): string {
		if ( '' !== $requested_profile && Model_Profile::is_valid( $requested_profile ) ) {
			return $requested_profile;
		}

		return AI_Task_Type::default_model_profile( $task_type );
	}

	public function resolve_model_defaults( string $model_profile ): array {
		if ( ! Model_Profile::is_valid( $model_profile ) ) {
			$model_profile = Model_Profile::BALANCED;
		}

		return Model_Profile::defaults( $model_profile );
	}

	public function resolve_output_schema( string $task_type, string $requested_schema = '' ): string {
		if ( '' !== $requested_schema && Structured_Output::is_valid( $requested_schema ) ) {
			return $requested_schema;
		}

		$map = array(
			AI_Task_Type::GENERATE_AUTO_POST_PLAN      => Structured_Output::AUTO_POST_PLAN_V1,
			AI_Task_Type::GENERATE_RESEARCH_PLAN       => Structured_Output::RESEARCH_PLAN_V1,
			AI_Task_Type::GENERATE_CONTENT_DRAFT       => Structured_Output::CONTENT_DRAFT_V1,
			AI_Task_Type::IMPROVE_CONTENT_DRAFT        => Structured_Output::CONTENT_REVIEW_V1,
			AI_Task_Type::REVIEW_CONTENT               => Structured_Output::CONTENT_REVIEW_V1,
			AI_Task_Type::EDITORIAL_REVISION           => Structured_Output::EDITORIAL_REVISION_V1,
			AI_Task_Type::GENERATE_SEO_REVIEW          => Structured_Output::SEO_REVIEW_V1,
			AI_Task_Type::GENERATE_TRANSLATION_PLAN    => Structured_Output::TRANSLATION_PLAN_V1,
			AI_Task_Type::GENERATE_PERFORMANCE_REPORT  => Structured_Output::PERFORMANCE_REPORT_V1,
			AI_Task_Type::WORKFLOW_PLAN                => Structured_Output::WORKFLOW_PLAN_V1,
			AI_Task_Type::CLASSIFY_INTENT              => Structured_Output::INTENT_CLASSIFICATION_V1,
			AI_Task_Type::VALIDATE_SOURCE              => Structured_Output::SOURCE_VALIDATION_V1,
			AI_Task_Type::MAP_FIELDS                   => Structured_Output::FIELD_MAPPING_PLAN_V1,
			AI_Task_Type::EXPLAIN_ERROR                => Structured_Output::ERROR_EXPLANATION_V1,
		);

		return isset( $map[ $task_type ] ) ? $map[ $task_type ] : '';
	}

	public function task_requires_structured_output( string $task_type ): bool {
		return '' !== $this->resolve_output_schema( $task_type );
	}
}
