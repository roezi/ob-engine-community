<?php
/**
 * Library item value object.
 *
 * @package OBEngine\Library
 */

namespace OBEngine\Library;

use WP_Post;

defined( 'ABSPATH' ) || exit;

/**
 * Immutable-style Library item wrapper.
 */
final class Library_Item {
	private $id;
	private $title;
	private $content;
	private $type;
	private $status;
	private $source_label;
	private $summary;
	private $payload_redacted;
	private $created_context;
	private $author_id;
	private $created_at;
	private $updated_at;

	public function __construct( array $data ) {
		$this->id                = isset( $data['id'] ) ? absint( $data['id'] ) : 0;
		$this->title             = isset( $data['title'] ) ? (string) $data['title'] : '';
		$this->content           = isset( $data['content'] ) ? (string) $data['content'] : '';
		$this->type              = isset( $data['type'] ) && Library_Type::is_valid( (string) $data['type'] ) ? (string) $data['type'] : Library_Type::CONTENT_DRAFT;
		$this->status            = isset( $data['status'] ) && Library_Status::is_valid( (string) $data['status'] ) ? (string) $data['status'] : Library_Status::default_status();
		$this->source_label      = isset( $data['source_label'] ) ? (string) $data['source_label'] : '';
		$this->summary           = isset( $data['summary'] ) ? (string) $data['summary'] : '';
		$this->payload_redacted  = isset( $data['payload_redacted'] ) ? (string) $data['payload_redacted'] : '';
		$this->created_context   = isset( $data['created_context'] ) ? (string) $data['created_context'] : 'manual_admin';
		$this->author_id         = isset( $data['author_id'] ) ? absint( $data['author_id'] ) : 0;
		$this->created_at        = isset( $data['created_at'] ) ? (string) $data['created_at'] : '';
		$this->updated_at        = isset( $data['updated_at'] ) ? (string) $data['updated_at'] : '';
	}

	public static function from_post( WP_Post $post ): self {
		return new self(
			array(
				'id'               => $post->ID,
				'title'            => get_the_title( $post ),
				'content'          => $post->post_content,
				'type'             => get_post_meta( $post->ID, Library_Repository::META_TYPE, true ),
				'status'           => get_post_meta( $post->ID, Library_Repository::META_STATUS, true ),
				'source_label'     => get_post_meta( $post->ID, Library_Repository::META_SOURCE_LABEL, true ),
				'summary'          => get_post_meta( $post->ID, Library_Repository::META_SUMMARY, true ),
				'payload_redacted' => get_post_meta( $post->ID, Library_Repository::META_PAYLOAD_REDACTED, true ),
				'created_context'  => get_post_meta( $post->ID, Library_Repository::META_CREATED_CONTEXT, true ),
				'author_id'        => $post->post_author,
				'created_at'       => $post->post_date,
				'updated_at'       => $post->post_modified,
			)
		);
	}

	public function to_array(): array {
		return get_object_vars( $this );
	}

	public function get_id(): int { return $this->id; }
	public function get_title(): string { return $this->title; }
	public function get_type(): string { return $this->type; }
	public function get_status(): string { return $this->status; }
	public function get_content(): string { return $this->content; }
	public function get_source_label(): string { return $this->source_label; }
	public function get_summary(): string { return $this->summary; }
	public function get_payload_redacted(): string { return $this->payload_redacted; }
	public function get_updated_at(): string { return $this->updated_at; }

	public function is_reviewable(): bool {
		return in_array( $this->status, array( Library_Status::DRAFT, Library_Status::NEEDS_REVIEW ), true );
	}

	public function is_writable_candidate(): bool {
		return Library_Status::APPROVED === $this->status;
	}
}
