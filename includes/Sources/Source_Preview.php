<?php
/**
 * Source preview value object.
 *
 * @package OBEngine\Sources
 */

namespace OBEngine\Sources;

defined( 'ABSPATH' ) || exit;

/**
 * Immutable-ish redacted source preview payload.
 */
final class Source_Preview {
	private $source_type;
	private $source_label;
	private $item_count;
	private $column_count;
	private $columns;
	private $sample_rows;
	private $summary;
	private $warnings;
	private $payload_redacted;
	private $created_at;

	public function __construct( array $data = array() ) {
		$this->source_type      = isset( $data['source_type'] ) ? (string) $data['source_type'] : Source_Type::PASTE_TEXT;
		$this->source_label     = isset( $data['source_label'] ) ? (string) $data['source_label'] : '';
		$this->item_count       = isset( $data['item_count'] ) ? max( 0, (int) $data['item_count'] ) : 0;
		$this->columns          = isset( $data['columns'] ) && is_array( $data['columns'] ) ? array_slice( array_values( $data['columns'] ), 0, 30 ) : array();
		$this->column_count     = isset( $data['column_count'] ) ? max( 0, (int) $data['column_count'] ) : count( $this->columns );
		$this->sample_rows      = isset( $data['sample_rows'] ) && is_array( $data['sample_rows'] ) ? array_slice( array_values( $data['sample_rows'] ), 0, 10 ) : array();
		$this->summary          = isset( $data['summary'] ) ? (string) $data['summary'] : '';
		$this->warnings         = isset( $data['warnings'] ) && is_array( $data['warnings'] ) ? array_values( $data['warnings'] ) : array();
		$this->payload_redacted = isset( $data['payload_redacted'] ) && is_array( $data['payload_redacted'] ) ? $data['payload_redacted'] : array();
		$this->created_at       = isset( $data['created_at'] ) ? (string) $data['created_at'] : gmdate( 'c' );
	}

	public static function empty(): self { return new self(); }
	public function to_array(): array { return array( 'source_type' => $this->source_type, 'source_label' => $this->source_label, 'item_count' => $this->item_count, 'column_count' => $this->column_count, 'columns' => $this->columns, 'sample_rows' => $this->sample_rows, 'summary' => $this->summary, 'warnings' => $this->warnings, 'payload_redacted' => $this->payload_redacted, 'created_at' => $this->created_at ); }
	public function get_source_type(): string { return $this->source_type; }
	public function get_source_label(): string { return $this->source_label; }
	public function get_item_count(): int { return $this->item_count; }
	public function get_column_count(): int { return $this->column_count; }
	public function get_columns(): array { return $this->columns; }
	public function get_sample_rows(): array { return $this->sample_rows; }
	public function get_summary(): string { return $this->summary; }
	public function get_warnings(): array { return $this->warnings; }
	public function get_payload_redacted(): array { return $this->payload_redacted; }
}
