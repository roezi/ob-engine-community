<?php
/** Field mapping item value object. @package OBEngine\Sources */
namespace OBEngine\Sources;
defined( 'ABSPATH' ) || exit;
final class Field_Mapping_Item {
	private $source_field; private $target_field; private $required; private $sample_value_redacted; private $notes;
	public function __construct( array $data = array() ) { $target = isset($data['target_field']) ? sanitize_key((string)$data['target_field']) : Field_Target::IGNORE; $this->source_field = isset($data['source_field']) ? sanitize_text_field((string)$data['source_field']) : ''; $this->target_field = Field_Target::is_valid($target) ? $target : Field_Target::IGNORE; $this->required = ! empty($data['required']); $sample = isset($data['sample_value_redacted']) ? sanitize_textarea_field((string)$data['sample_value_redacted']) : ''; $this->sample_value_redacted = $this->truncate($sample); $this->notes = isset($data['notes']) ? sanitize_textarea_field((string)$data['notes']) : ''; }
	public static function from_array( array $data ): self { return new self($data); }
	public function to_array(): array { return array('source_field'=>$this->source_field,'target_field'=>$this->target_field,'required'=>$this->required,'sample_value_redacted'=>$this->sample_value_redacted,'notes'=>$this->notes); }
	public function get_source_field(): string { return $this->source_field; } public function get_target_field(): string { return $this->target_field; } public function is_required(): bool { return $this->required; } public function get_sample_value_redacted(): string { return $this->sample_value_redacted; } public function get_notes(): string { return $this->notes; } public function is_ignored(): bool { return Field_Target::IGNORE === $this->target_field; }
	private function truncate( string $value ): string { $length = function_exists('mb_strlen') ? mb_strlen($value) : strlen($value); if ( $length <= 240 ) { return $value; } $slice = function_exists('mb_substr') ? mb_substr($value,0,240) : substr($value,0,240); return $slice . ' [REDACTED_TRUNCATED]'; }
}
