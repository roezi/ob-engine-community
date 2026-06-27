<?php
/**
 * Approval service with Activity logging.
 *
 * @package OBEngine\Approval
 */

namespace OBEngine\Approval;

use OBEngine\Activity\Activity_Action;
use OBEngine\Activity\Activity_Logger;
use OBEngine\Activity\Activity_Object_Type;
use OBEngine\Activity\Activity_Status;
use OBEngine\Library\Library_Repository;

defined( 'ABSPATH' ) || exit;

final class Approval_Service {
	private $repository;
	private $library_repository;
	private $logger;

	public function __construct( ?Approval_Repository $repository = null, ?Library_Repository $library_repository = null, ?Activity_Logger $logger = null ) {
		$this->library_repository = $library_repository ?: new Library_Repository();
		$this->repository         = $repository ?: new Approval_Repository( $this->library_repository );
		$this->logger             = $logger ?: new Activity_Logger();
	}

	public function approve_library_item( int $library_item_id, string $note = '' ) {
		return $this->decide( $library_item_id, $note, Approval_Status::APPROVED );
	}

	public function reject_library_item( int $library_item_id, string $note = '' ) {
		return $this->decide( $library_item_id, $note, Approval_Status::REJECTED );
	}

	public function revoke_library_item( int $library_item_id, string $note = '' ) {
		return $this->decide( $library_item_id, $note, Approval_Status::REVOKED );
	}

	private function decide( int $library_item_id, string $note, string $approval_status ) {
		$before          = $this->library_repository->get( $library_item_id );
		$previous_status = $before ? $before->get_status() : '';

		if ( Approval_Status::APPROVED === $approval_status ) {
			$result = $this->repository->approve( $library_item_id, $note );
		} elseif ( Approval_Status::REJECTED === $approval_status ) {
			$result = $this->repository->reject( $library_item_id, $note );
		} else {
			$result = $this->repository->revoke( $library_item_id, $note );
		}

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		$after  = $this->library_repository->get( $library_item_id );
		$record = $this->repository->get( $library_item_id );

		$this->log_decision( $approval_status, $before, $after, $previous_status, $record, '' !== $this->repository->sanitize_note( $note ) );

		return $record;
	}

	private function log_decision( string $approval_status, $before, $after, string $previous_status, Approval_Record $record, bool $note_present ): void {
		try {
			$item = $after ?: $before;
			if ( ! $item ) {
				return;
			}

			$map = array(
				Approval_Status::APPROVED => array( Activity_Action::APPROVAL_CREATED, Activity_Status::SUCCESS, __( 'Library item approved.', 'ob-engine' ) ),
				Approval_Status::REJECTED => array( Activity_Action::APPROVAL_REJECTED, Activity_Status::WARNING, __( 'Library item rejected.', 'ob-engine' ) ),
				Approval_Status::REVOKED  => array( Activity_Action::APPROVAL_REQUIRED, Activity_Status::WARNING, __( 'Approval revoked; item requires review again.', 'ob-engine' ) ),
			);
			$entry = $map[ $approval_status ];

			$this->logger->log(
				$entry[0],
				array(
					'status'       => $entry[1],
					'object_type'  => Activity_Object_Type::LIBRARY_ITEM,
					'object_id'    => $item->get_id(),
					'object_label' => $item->get_title(),
					'message'      => $entry[2],
					'context'      => array(
						'library_item_id'    => $item->get_id(),
						'library_item_title' => $item->get_title(),
						'library_item_type'  => $item->get_type(),
						'previous_status'    => $previous_status,
						'new_status'         => $item->get_status(),
						'approval_status'    => $record->get_status(),
						'note_present'       => $note_present,
					),
				)
			);
		} catch ( \Throwable $e ) {
			return;
		}
	}
}
