<?php
/**
 * Approval record value object.
 *
 * @package OBEngine\Approval
 */

namespace OBEngine\Approval;

defined( 'ABSPATH' ) || exit;

final class Approval_Record {
	private $library_item_id;
	private $status;
	private $note;
	private $actor_id;
	private $actor_label;
	private $decided_at;
	private $updated_at;

	public function __construct( array $data ) {
		$status = isset( $data['status'] ) ? sanitize_key( (string) $data['status'] ) : Approval_Status::PENDING;

		$this->library_item_id = isset( $data['library_item_id'] ) ? absint( $data['library_item_id'] ) : 0;
		$this->status          = Approval_Status::is_valid( $status ) ? $status : Approval_Status::PENDING;
		$this->note            = isset( $data['note'] ) ? sanitize_textarea_field( wp_strip_all_tags( (string) $data['note'] ) ) : '';
		$this->actor_id        = isset( $data['actor_id'] ) ? absint( $data['actor_id'] ) : 0;
		$this->actor_label     = isset( $data['actor_label'] ) ? sanitize_text_field( (string) $data['actor_label'] ) : '';
		$this->decided_at      = isset( $data['decided_at'] ) ? sanitize_text_field( (string) $data['decided_at'] ) : '';
		$this->updated_at      = isset( $data['updated_at'] ) ? sanitize_text_field( (string) $data['updated_at'] ) : '';
	}

	public static function empty_for_item( int $library_item_id ): self {
		return new self(
			array(
				'library_item_id' => $library_item_id,
				'status'          => Approval_Status::PENDING,
			)
		);
	}

	public static function from_meta( int $library_item_id, array $meta ): self {
		$value = static function ( array $meta, string $key ) {
			if ( ! isset( $meta[ $key ] ) ) {
				return '';
			}

			$raw = $meta[ $key ];
			return is_array( $raw ) ? (string) reset( $raw ) : (string) $raw;
		};

		return new self(
			array(
				'library_item_id' => $library_item_id,
				'status'          => $value( $meta, '_obe_approval_status' ),
				'note'            => $value( $meta, '_obe_approval_note' ),
				'actor_id'        => $value( $meta, '_obe_approval_actor_id' ),
				'actor_label'     => $value( $meta, '_obe_approval_actor_label' ),
				'decided_at'      => $value( $meta, '_obe_approval_decided_at' ),
				'updated_at'      => $value( $meta, '_obe_approval_updated_at' ),
			)
		);
	}

	public function to_array(): array {
		return array(
			'library_item_id' => $this->library_item_id,
			'status'          => $this->status,
			'note'            => $this->note,
			'actor_id'        => $this->actor_id,
			'actor_label'     => $this->actor_label,
			'decided_at'      => $this->decided_at,
			'updated_at'      => $this->updated_at,
		);
	}

	public function get_library_item_id(): int { return $this->library_item_id; }
	public function get_status(): string { return $this->status; }
	public function get_note(): string { return $this->note; }
	public function get_actor_id(): int { return $this->actor_id; }
	public function get_actor_label(): string { return $this->actor_label; }
	public function get_decided_at(): string { return $this->decided_at; }
	public function get_updated_at(): string { return $this->updated_at; }
	public function is_approved(): bool { return Approval_Status::APPROVED === $this->status; }
	public function is_rejected(): bool { return Approval_Status::REJECTED === $this->status; }
	public function is_pending(): bool { return Approval_Status::PENDING === $this->status; }
}
