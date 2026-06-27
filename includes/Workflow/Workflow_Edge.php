<?php
/** Workflow visualizer edge metadata. @package OBEngine\Workflow */
namespace OBEngine\Workflow;
defined( 'ABSPATH' ) || exit;
final class Workflow_Edge {
	private $from; private $to; private $label; private $gate;
	public function __construct( array $data ) { $this->from=$this->clean($data['from']??''); $this->to=$this->clean($data['to']??''); $this->label=$this->text($data['label']??''); $this->gate=$this->text($data['gate']??''); }
	public function to_array(): array { return array('from'=>$this->from,'to'=>$this->to,'label'=>$this->label,'gate'=>$this->gate); }
	public function get_from(): string { return $this->from; } public function get_to(): string { return $this->to; } public function get_label(): string { return $this->label; } public function get_gate(): string { return $this->gate; }
	private function clean( $v ): string { return function_exists('sanitize_key') ? sanitize_key((string)$v) : strtolower(preg_replace('/[^a-z0-9_\-]/','',(string)$v)); }
	private function text( $v ): string { return function_exists('sanitize_text_field') ? sanitize_text_field((string)$v) : trim(strip_tags((string)$v)); }
}
