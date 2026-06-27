<?php
/** Editorial revision value object. */
namespace OBEngine\Editorial;
use OBEngine\Library\Library_Status;
defined( 'ABSPATH' ) || exit;
final class Editorial_Revision {
	private $data;
	public function __construct( array $data = array() ) {
		$tone = isset( $data['tone'] ) ? sanitize_key( (string) $data['tone'] ) : Editorial_Tone::NATURAL; if ( ! Editorial_Tone::is_valid( $tone ) ) { $tone = Editorial_Tone::NATURAL; }
		$status = isset( $data['default_status'] ) ? sanitize_key( (string) $data['default_status'] ) : Library_Status::NEEDS_REVIEW; if ( ! in_array( $status, array( Library_Status::NEEDS_REVIEW, Library_Status::DRAFT ), true ) ) { $status = Library_Status::NEEDS_REVIEW; }
		$this->data = array( 'source_library_item_id'=>isset($data['source_library_item_id'])?absint($data['source_library_item_id']):0, 'source_title'=>self::text($data['source_title']??''), 'tone'=>$tone, 'original_summary'=>self::area($data['original_summary']??''), 'revised_title'=>self::text($data['revised_title']??($data['title']??'')), 'revised_content'=>self::post($data['revised_content']??($data['content']??'')), 'revised_excerpt'=>self::area($data['revised_excerpt']??($data['excerpt']??'')), 'meta_title'=>self::text($data['meta_title']??''), 'meta_description'=>self::area($data['meta_description']??''), 'readability_score'=>self::score($data['readability_score']??0), 'tone_score'=>self::score($data['tone_score']??0), 'localization_score'=>self::score($data['localization_score']??0), 'revision_notes'=>self::list($data['revision_notes']??array()), 'safety_notes'=>self::list($data['safety_notes']??array()), 'default_status'=>$status, 'provider'=>self::text($data['provider']??''), 'model'=>self::text($data['model']??''), 'request_id'=>self::text($data['request_id']??''), 'created_at'=>self::text($data['created_at']??gmdate('c')) );
	}
	public static function from_ai_response_data( array $data, array $context = array() ): self { return new self( array_merge( $context, $data ) ); }
	public static function empty(): self { return new self(); }
	public function to_array(): array { return $this->data; }
	public function get_source_library_item_id(): int { return $this->data['source_library_item_id']; }
	public function get_tone(): string { return $this->data['tone']; }
	public function get_revised_title(): string { return $this->data['revised_title']; }
	public function get_revised_content(): string { return $this->data['revised_content']; }
	public function get_revised_excerpt(): string { return $this->data['revised_excerpt']; }
	public function get_revision_notes(): array { return $this->data['revision_notes']; }
	public function get_safety_notes(): array { return $this->data['safety_notes']; }
	public function get_default_status(): string { return $this->data['default_status']; }
	public function summary(): string { $title = '' !== $this->get_revised_title() ? $this->get_revised_title() : __( 'Editorial revision candidate', 'ob-engine' ); return sprintf( '%s — %s. %s', $title, Editorial_Tone::label( $this->get_tone() ), __( 'Saved for human review only.', 'ob-engine' ) ); }
	private static function score( $v ): int { return max( 0, min( 100, (int) $v ) ); }
	private static function list( $v ): array { if ( is_string( $v ) ) { $v = array( $v ); } if ( ! is_array( $v ) ) { return array(); } return array_values( array_filter( array_map( array( __CLASS__, 'area' ), $v ) ) ); }
	private static function text( $v ): string { return sanitize_text_field( (string) $v ); }
	private static function area( $v ): string { return sanitize_textarea_field( (string) $v ); }
	private static function post( $v ): string { return wp_kses_post( (string) $v ); }
}
