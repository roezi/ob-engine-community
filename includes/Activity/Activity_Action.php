<?php
/**
 * Activity action constants.
 *
 * @package OBEngine\Activity
 */

namespace OBEngine\Activity;

defined( 'ABSPATH' ) || exit;

final class Activity_Action {
	public const SETTINGS_UPDATED             = 'settings_updated';
	public const PROVIDER_KEY_SAVED           = 'provider_key_saved';
	public const PROVIDER_KEY_CLEARED         = 'provider_key_cleared';
	public const PROVIDER_KEY_MISSING         = 'provider_key_missing';
	public const PROVIDER_KEY_USED            = 'provider_key_used';
	public const AI_REQUEST_CREATED           = 'ai_request_created';
	public const AI_RESPONSE_COMPLETED        = 'ai_response_completed';
	public const AI_RESPONSE_FAILED           = 'ai_response_failed';
	public const AI_STRUCTURED_OUTPUT_INVALID = 'ai_structured_output_invalid';
	public const AI_OUTPUT_REDACTED           = 'ai_output_redacted';
	public const LIBRARY_ITEM_CREATED         = 'library_item_created';
	public const LIBRARY_ITEM_UPDATED         = 'library_item_updated';
	public const LIBRARY_ITEM_ARCHIVED        = 'library_item_archived';
	public const LIBRARY_ITEM_DELETED         = 'library_item_deleted';
	public const LIBRARY_ITEM_CREATED_FROM_AI = 'library_item_created_from_ai';
	public const SOURCE_PREVIEW_CREATED     = 'source_preview_created';
	public const SOURCE_PREVIEW_FAILED      = 'source_preview_failed';
	public const SOURCE_INPUT_REDACTED      = 'source_input_redacted';
	public const FIELD_MAPPING_CREATED    = 'field_mapping_created';
	public const FIELD_MAPPING_VALIDATED  = 'field_mapping_validated';
	public const FIELD_MAPPING_FAILED     = 'field_mapping_failed';
	public const AUTO_POST_PLAN_CREATED   = 'auto_post_plan_created';
	public const AUTO_POST_PLAN_FAILED    = 'auto_post_plan_failed';
	public const AUTO_POST_DRAFT_CREATED  = 'auto_post_draft_created';
	public const AUTO_POST_DRAFT_FAILED   = 'auto_post_draft_failed';
	public const EDITORIAL_REVISION_CREATED = 'editorial_revision_created';
	public const EDITORIAL_REVISION_FAILED  = 'editorial_revision_failed';
	public const WORKFLOW_PLAN_CREATED        = 'workflow_plan_created';
	public const WORKFLOW_RUN_CREATED         = 'workflow_run_created';
	public const APPROVAL_REQUIRED            = 'approval_required';
	public const APPROVAL_CREATED             = 'approval_created';
	public const APPROVAL_REJECTED            = 'approval_rejected';
	public const WORDPRESS_WRITE_PLANNED      = 'wordpress_write_planned';
	public const WORDPRESS_DRAFT_WRITTEN      = 'wordpress_draft_written';
	public const SAFETY_CHECK_FAILED          = 'safety_check_failed';
	public const UNKNOWN                      = 'unknown';

	public static function all(): array { return array_keys( self::labels() ); }

	public static function labels(): array {
		return array(
			self::SETTINGS_UPDATED => __( 'Settings updated', 'ob-engine' ), self::PROVIDER_KEY_SAVED => __( 'Provider key saved', 'ob-engine' ), self::PROVIDER_KEY_CLEARED => __( 'Provider key cleared', 'ob-engine' ), self::PROVIDER_KEY_MISSING => __( 'Provider key missing', 'ob-engine' ), self::PROVIDER_KEY_USED => __( 'Provider key used', 'ob-engine' ), self::AI_REQUEST_CREATED => __( 'AI request created', 'ob-engine' ), self::AI_RESPONSE_COMPLETED => __( 'AI response completed', 'ob-engine' ), self::AI_RESPONSE_FAILED => __( 'AI response failed', 'ob-engine' ), self::AI_STRUCTURED_OUTPUT_INVALID => __( 'AI structured output invalid', 'ob-engine' ), self::AI_OUTPUT_REDACTED => __( 'AI output redacted', 'ob-engine' ), self::LIBRARY_ITEM_CREATED => __( 'Library item created', 'ob-engine' ), self::LIBRARY_ITEM_UPDATED => __( 'Library item updated', 'ob-engine' ), self::LIBRARY_ITEM_ARCHIVED => __( 'Library item archived', 'ob-engine' ), self::LIBRARY_ITEM_DELETED => __( 'Library item deleted', 'ob-engine' ), self::LIBRARY_ITEM_CREATED_FROM_AI => __( 'Library item created from AI', 'ob-engine' ), self::SOURCE_PREVIEW_CREATED => __( 'Source preview created', 'ob-engine' ), self::SOURCE_PREVIEW_FAILED => __( 'Source preview failed', 'ob-engine' ), self::SOURCE_INPUT_REDACTED => __( 'Source input redacted', 'ob-engine' ), self::FIELD_MAPPING_CREATED => __( 'Field mapping created', 'ob-engine' ), self::FIELD_MAPPING_VALIDATED => __( 'Field mapping validated', 'ob-engine' ), self::FIELD_MAPPING_FAILED => __( 'Field mapping failed', 'ob-engine' ), self::AUTO_POST_PLAN_CREATED => __( 'Auto Post plan created', 'ob-engine' ), self::AUTO_POST_PLAN_FAILED => __( 'Auto Post plan failed', 'ob-engine' ), self::AUTO_POST_DRAFT_CREATED => __( 'Auto Post draft created', 'ob-engine' ), self::AUTO_POST_DRAFT_FAILED => __( 'Auto Post draft failed', 'ob-engine' ), self::EDITORIAL_REVISION_CREATED => __( 'Editorial revision created', 'ob-engine' ), self::EDITORIAL_REVISION_FAILED => __( 'Editorial revision failed', 'ob-engine' ), self::WORKFLOW_PLAN_CREATED => __( 'Workflow plan created', 'ob-engine' ), self::WORKFLOW_RUN_CREATED => __( 'Workflow run created', 'ob-engine' ), self::APPROVAL_REQUIRED => __( 'Approval required', 'ob-engine' ), self::APPROVAL_CREATED => __( 'Approval created', 'ob-engine' ), self::APPROVAL_REJECTED => __( 'Approval rejected', 'ob-engine' ), self::WORDPRESS_WRITE_PLANNED => __( 'WordPress write planned', 'ob-engine' ), self::WORDPRESS_DRAFT_WRITTEN => __( 'WordPress draft written', 'ob-engine' ), self::SAFETY_CHECK_FAILED => __( 'Safety check failed', 'ob-engine' ), self::UNKNOWN => __( 'Unknown', 'ob-engine' ),
		);
	}

	public static function is_valid( string $action ): bool { return in_array( $action, self::all(), true ); }
	public static function label( string $action ): string { $labels = self::labels(); return $labels[ $action ] ?? $labels[ self::UNKNOWN ]; }
}
