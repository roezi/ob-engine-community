<?php
/** Workflow visualizer node metadata. @package OBEngine\Workflow */
namespace OBEngine\Workflow;
defined( 'ABSPATH' ) || exit;
final class Workflow_Node {
	private $id; private $label; private $description; private $input; private $output; private $library_type; private $requires_human_review; private $calls_ai_provider; private $writes_wordpress; private $risk_level; private $activity_events; private $gate_notes;
	public function __construct( array $data ) { $this->id=$this->clean($data['id']??''); $this->label=$this->text($data['label']??''); $this->description=$this->text($data['description']??''); $this->input=$this->text($data['input']??''); $this->output=$this->text($data['output']??''); $this->library_type=$this->clean($data['library_type']??''); $this->requires_human_review=!empty($data['requires_human_review']); $this->calls_ai_provider=!empty($data['calls_ai_provider']); $this->writes_wordpress=!empty($data['writes_wordpress']); $this->risk_level=$this->clean($data['risk_level']??'low'); $this->activity_events=$this->list($data['activity_events']??array()); $this->gate_notes=$this->list($data['gate_notes']??array()); }
	public static function from_array( array $data ): self { return new self( $data ); }
	public function to_array(): array { return array( 'id'=>$this->id,'label'=>$this->label,'description'=>$this->description,'input'=>$this->input,'output'=>$this->output,'library_type'=>$this->library_type,'requires_human_review'=>$this->requires_human_review,'calls_ai_provider'=>$this->calls_ai_provider,'writes_wordpress'=>$this->writes_wordpress,'risk_level'=>$this->risk_level,'activity_events'=>$this->activity_events,'gate_notes'=>$this->gate_notes ); }
	public function get_id(): string { return $this->id; } public function get_label(): string { return $this->label; } public function get_risk_level(): string { return $this->risk_level; } public function calls_ai_provider(): bool { return $this->calls_ai_provider; } public function writes_wordpress(): bool { return $this->writes_wordpress; } public function requires_human_review(): bool { return $this->requires_human_review; }
	private function clean( $v ): string { return function_exists('sanitize_key') ? sanitize_key((string)$v) : strtolower(preg_replace('/[^a-z0-9_\-]/','',(string)$v)); }
	private function text( $v ): string { return function_exists('sanitize_text_field') ? sanitize_text_field((string)$v) : trim(strip_tags((string)$v)); }
	private function list( $items ): array { $out=array(); foreach ( (array) $items as $item ) { $out[]=$this->text($item); } return $out; }
}
