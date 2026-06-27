<?php
/** Auto Post draft candidate value object. @package OBEngine\AutoPost */
namespace OBEngine\AutoPost;

use OBEngine\Library\Library_Status;

defined( 'ABSPATH' ) || exit;

final class Auto_Post_Draft {
	private $data;

	public function __construct( array $data = array() ) {
		$default_status = isset( $data['default_status'] ) ? $this->clean_key( $data['default_status'] ) : ( isset( $data['status'] ) ? $this->clean_key( $data['status'] ) : Library_Status::NEEDS_REVIEW );
		if ( ! in_array( $default_status, array( Library_Status::DRAFT, Library_Status::NEEDS_REVIEW ), true ) ) { $default_status = Library_Status::NEEDS_REVIEW; }
		$this->data = array(
			'auto_post_plan_library_item_id' => isset( $data['auto_post_plan_library_item_id'] ) ? absint( $data['auto_post_plan_library_item_id'] ) : 0,
			'field_mapping_library_item_id' => isset( $data['field_mapping_library_item_id'] ) ? absint( $data['field_mapping_library_item_id'] ) : 0,
			'source_preview_library_item_id' => isset( $data['source_preview_library_item_id'] ) ? absint( $data['source_preview_library_item_id'] ) : 0,
			'title' => $this->clean_text( $data['title'] ?? '' ),
			'content' => $this->clean_content( $data['content'] ?? '' ),
			'excerpt' => $this->clean_textarea( $data['excerpt'] ?? '' ),
			'meta_title' => $this->clean_text( $data['meta_title'] ?? '' ),
			'meta_description' => $this->clean_textarea( $data['meta_description'] ?? '' ),
			'slug_suggestion' => $this->clean_key( $data['slug_suggestion'] ?? '' ),
			'outline_used' => $this->clean_array( $data['outline_used'] ?? array() ),
			'source_summary' => $this->clean_textarea( $data['source_summary'] ?? '' ),
			'safety_notes' => $this->clean_list( $data['safety_notes'] ?? array() ),
			'default_status' => $default_status,
			'provider' => $this->clean_key( $data['provider'] ?? '' ),
			'model' => $this->clean_text( $data['model'] ?? '' ),
			'request_id' => $this->clean_text( $data['request_id'] ?? '' ),
			'created_at' => $this->clean_text( $data['created_at'] ?? gmdate( 'c' ) ),
		);
	}

	public static function from_ai_response_data( array $data, array $context = array() ): self { return new self( array_merge( $data, $context ) ); }
	public static function empty(): self { return new self(); }
	public function to_array(): array { return $this->data; }
	public function get_auto_post_plan_library_item_id(): int { return $this->data['auto_post_plan_library_item_id']; }
	public function get_title(): string { return $this->data['title']; }
	public function get_content(): string { return $this->data['content']; }
	public function get_excerpt(): string { return $this->data['excerpt']; }
	public function get_meta_title(): string { return $this->data['meta_title']; }
	public function get_meta_description(): string { return $this->data['meta_description']; }
	public function get_slug_suggestion(): string { return $this->data['slug_suggestion']; }
	public function get_safety_notes(): array { return $this->data['safety_notes']; }
	public function get_default_status(): string { return $this->data['default_status']; }
	public function summary(): string { return trim( sprintf( 'Auto Post draft candidate%s with default status %s. %s', '' !== $this->data['title'] ? ' for "' . $this->data['title'] . '"' : '', $this->data['default_status'], $this->data['source_summary'] ) ); }
	private function clean_text( $value ): string { return function_exists( 'sanitize_text_field' ) ? sanitize_text_field( (string) $value ) : trim( strip_tags( (string) $value ) ); }
	private function clean_textarea( $value ): string { return function_exists( 'sanitize_textarea_field' ) ? sanitize_textarea_field( (string) $value ) : trim( strip_tags( (string) $value ) ); }
	private function clean_content( $value ): string { return function_exists( 'wp_kses_post' ) ? wp_kses_post( (string) $value ) : (string) $value; }
	private function clean_key( $value ): string { return function_exists( 'sanitize_key' ) ? sanitize_key( (string) $value ) : strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $value ) ); }
	private function clean_list( $value ): array { $items = is_array( $value ) ? $value : ( '' === (string) $value ? array() : array( $value ) ); return array_values( array_filter( array_map( array( $this, 'clean_textarea' ), $items ), static function ( $item ) { return '' !== $item; } ) ); }
	private function clean_array( $value ): array { if ( ! is_array( $value ) ) { return array(); } return json_decode( wp_json_encode( $value ), true ) ?: array(); }
}
