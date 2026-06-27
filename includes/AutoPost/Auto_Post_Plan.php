<?php
/** Auto Post plan value object. @package OBEngine\AutoPost */
namespace OBEngine\AutoPost;

use OBEngine\Library\Library_Status;

defined( 'ABSPATH' ) || exit;

final class Auto_Post_Plan {
	private $data;

	public function __construct( array $data = array() ) {
		$default_status = isset( $data['default_status'] ) ? sanitize_key( (string) $data['default_status'] ) : Library_Status::NEEDS_REVIEW;
		if ( ! in_array( $default_status, array( Library_Status::DRAFT, Library_Status::NEEDS_REVIEW ), true ) ) {
			$default_status = Library_Status::NEEDS_REVIEW;
		}
		$this->data = array(
			'field_mapping_library_item_id' => isset( $data['field_mapping_library_item_id'] ) ? absint( $data['field_mapping_library_item_id'] ) : 0,
			'source_preview_library_item_id' => isset( $data['source_preview_library_item_id'] ) ? absint( $data['source_preview_library_item_id'] ) : 0,
			'source_summary' => $this->clean_text( $data['source_summary'] ?? '' ),
			'field_mapping_summary' => $this->clean_text( $data['field_mapping_summary'] ?? '' ),
			'proposed_post_type' => '' !== $this->clean_key( $data['proposed_post_type'] ?? '' ) ? $this->clean_key( $data['proposed_post_type'] ?? '' ) : 'post',
			'title_options' => $this->clean_list( $data['title_options'] ?? array() ),
			'slug_suggestion' => $this->clean_key( $data['slug_suggestion'] ?? '' ),
			'meta_title' => $this->clean_text( $data['meta_title'] ?? '' ),
			'meta_description' => $this->clean_textarea( $data['meta_description'] ?? '' ),
			'outline' => $this->clean_array( $data['outline'] ?? array() ),
			'research_checklist' => $this->clean_list( $data['research_checklist'] ?? array() ),
			'image_prompt_suggestions' => $this->clean_list( $data['image_prompt_suggestions'] ?? array() ),
			'custom_field_mapping' => $this->clean_array( $data['custom_field_mapping'] ?? array() ),
			'safety_notes' => $this->clean_list( $data['safety_notes'] ?? array() ),
			'default_status' => $default_status,
			'approval_question' => $this->clean_text( $data['approval_question'] ?? 'Review this Auto Post plan before any draft generation?' ),
			'provider' => $this->clean_key( $data['provider'] ?? '' ),
			'model' => $this->clean_text( $data['model'] ?? '' ),
			'request_id' => $this->clean_text( $data['request_id'] ?? '' ),
			'created_at' => $this->clean_text( $data['created_at'] ?? gmdate( 'c' ) ),
		);
	}

	public static function from_ai_response_data( array $data, array $context = array() ): self { return new self( array_merge( $data, $context ) ); }
	public static function empty(): self { return new self(); }
	public function to_array(): array { return $this->data; }
	public function get_field_mapping_library_item_id(): int { return $this->data['field_mapping_library_item_id']; }
	public function get_source_preview_library_item_id(): int { return $this->data['source_preview_library_item_id']; }
	public function get_source_summary(): string { return $this->data['source_summary']; }
	public function get_proposed_post_type(): string { return $this->data['proposed_post_type']; }
	public function get_title_options(): array { return $this->data['title_options']; }
	public function get_outline(): array { return $this->data['outline']; }
	public function get_safety_notes(): array { return $this->data['safety_notes']; }
	public function get_default_status(): string { return $this->data['default_status']; }
	public function get_approval_question(): string { return $this->data['approval_question']; }
	public function summary(): string { return trim( sprintf( 'Auto Post plan for %s with default status %s. %s', $this->data['proposed_post_type'], $this->data['default_status'], $this->data['source_summary'] ) ); }

	private function clean_text( $value ): string { return function_exists( 'sanitize_text_field' ) ? sanitize_text_field( (string) $value ) : trim( strip_tags( (string) $value ) ); }
	private function clean_textarea( $value ): string { return function_exists( 'sanitize_textarea_field' ) ? sanitize_textarea_field( (string) $value ) : trim( strip_tags( (string) $value ) ); }
	private function clean_key( $value ): string { return function_exists( 'sanitize_key' ) ? sanitize_key( (string) $value ) : strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $value ) ); }
	private function clean_list( $value ): array { $items = is_array( $value ) ? $value : ( '' === (string) $value ? array() : array( $value ) ); return array_values( array_filter( array_map( array( $this, 'clean_textarea' ), $items ), static function ( $item ) { return '' !== $item; } ) ); }
	private function clean_array( $value ): array { if ( ! is_array( $value ) ) { return array(); } return json_decode( wp_json_encode( $value ), true ) ?: array(); }
}
