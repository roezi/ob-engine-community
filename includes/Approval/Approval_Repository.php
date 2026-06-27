<?php
/**
 * Approval repository using Library item post meta.
 *
 * @package OBEngine\Approval
 */

namespace OBEngine\Approval;

use OBEngine\Library\Library_Item;
use OBEngine\Library\Library_Repository;
use OBEngine\Library\Library_Status;
use WP_Error;

defined( 'ABSPATH' ) || exit;

final class Approval_Repository {
	public const META_STATUS      = '_obe_approval_status';
	public const META_NOTE        = '_obe_approval_note';
	public const META_ACTOR_ID    = '_obe_approval_actor_id';
	public const META_ACTOR_LABEL = '_obe_approval_actor_label';
	public const META_DECIDED_AT  = '_obe_approval_decided_at';
	public const META_UPDATED_AT  = '_obe_approval_updated_at';

	private $library_repository;

	public function __construct( ?Library_Repository $library_repository = null ) {
		$this->library_repository = $library_repository ?: new Library_Repository();
	}

	public function get( int $library_item_id ): Approval_Record {
		$item = $this->library_repository->get( $library_item_id );
		if ( ! $item ) {
			return Approval_Record::empty_for_item( $library_item_id );
		}

		return Approval_Record::from_meta( $library_item_id, get_post_meta( $library_item_id ) );
	}

	public function approve( int $library_item_id, string $note = '' ) {
		$item = $this->library_repository->get( $library_item_id );
		if ( ! $item || ! $this->can_approve( $item ) ) {
			return new WP_Error( 'obe_approval_not_allowed', __( 'This Library item cannot be approved.', 'ob-engine' ) );
		}

		return $this->decide( $item, Approval_Status::APPROVED, Library_Status::APPROVED, $note );
	}

	public function reject( int $library_item_id, string $note = '' ) {
		$item = $this->library_repository->get( $library_item_id );
		if ( ! $item || ! $this->can_reject( $item ) ) {
			return new WP_Error( 'obe_approval_not_allowed', __( 'This Library item cannot be rejected.', 'ob-engine' ) );
		}

		return $this->decide( $item, Approval_Status::REJECTED, Library_Status::REJECTED, $note );
	}

	public function revoke( int $library_item_id, string $note = '' ) {
		$item = $this->library_repository->get( $library_item_id );
		if ( ! $item || Library_Status::APPROVED !== $item->get_status() ) {
			return new WP_Error( 'obe_approval_revoke_not_allowed', __( 'This Library item approval cannot be revoked.', 'ob-engine' ) );
		}

		return $this->decide( $item, Approval_Status::REVOKED, Library_Status::NEEDS_REVIEW, $note );
	}

	public function can_approve( Library_Item $item ): bool {
		return $this->is_private_library_item( $item ) && in_array( $item->get_status(), array( Library_Status::DRAFT, Library_Status::NEEDS_REVIEW, Library_Status::REJECTED ), true );
	}

	public function can_reject( Library_Item $item ): bool {
		return $this->is_private_library_item( $item ) && in_array( $item->get_status(), array( Library_Status::DRAFT, Library_Status::NEEDS_REVIEW, Library_Status::APPROVED ), true );
	}

	public function sanitize_note( string $note ): string {
		$note = wp_strip_all_tags( $note );
		$note = sanitize_textarea_field( $note );

		if ( function_exists( 'mb_substr' ) ) {
			return mb_substr( $note, 0, 1000 );
		}

		return substr( $note, 0, 1000 );
	}

	private function decide( Library_Item $item, string $approval_status, string $library_status, string $note ) {
		$id  = $item->get_id();
		$now = current_time( 'mysql' );

		update_post_meta( $id, self::META_STATUS, $approval_status );
		update_post_meta( $id, self::META_NOTE, $this->sanitize_note( $note ) );
		update_post_meta( $id, self::META_ACTOR_ID, get_current_user_id() );
		update_post_meta( $id, self::META_ACTOR_LABEL, $this->current_actor_label() );
		update_post_meta( $id, self::META_DECIDED_AT, $now );
		update_post_meta( $id, self::META_UPDATED_AT, $now );

		return $this->library_repository->update_status( $id, $library_status );
	}

	private function is_private_library_item( Library_Item $item ): bool {
		return $item->get_id() > 0 && Library_Repository::POST_TYPE === get_post_type( $item->get_id() ) && 'private' === get_post_status( $item->get_id() );
	}

	private function current_actor_label(): string {
		$user = wp_get_current_user();
		if ( $user && ! empty( $user->display_name ) ) {
			return sanitize_text_field( $user->display_name );
		}

		return __( 'Unknown user', 'ob-engine' );
	}
}
