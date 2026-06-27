<?php
/** Safety result value object. @package OBEngine\Safety */
namespace OBEngine\Safety;
defined( 'ABSPATH' ) || exit;
final class Safety_Result {
	private $status; private $message; private $checks; private $blockers; private $warnings; private $context;
	public function __construct( array $data ) {
		$status = isset( $data['status'] ) ? sanitize_key( (string) $data['status'] ) : Safety_Status::NOT_CHECKED;
		$this->status = Safety_Status::is_valid( $status ) ? $status : Safety_Status::NOT_CHECKED;
		$this->message = isset( $data['message'] ) ? sanitize_textarea_field( wp_strip_all_tags( (string) $data['message'] ) ) : '';
		$this->checks = isset( $data['checks'] ) && is_array( $data['checks'] ) ? array_map( 'sanitize_text_field', $data['checks'] ) : array();
		$this->blockers = isset( $data['blockers'] ) && is_array( $data['blockers'] ) ? array_map( 'sanitize_text_field', $data['blockers'] ) : array();
		$this->warnings = isset( $data['warnings'] ) && is_array( $data['warnings'] ) ? array_map( 'sanitize_text_field', $data['warnings'] ) : array();
		$this->context = isset( $data['context'] ) && is_array( $data['context'] ) ? $this->sanitize_context( $data['context'] ) : array();
	}
	public static function passed( string $message = '', array $context = array() ): self { return new self( array( 'status' => Safety_Status::PASSED, 'message' => $message, 'context' => $context ) ); }
	public static function warning( string $message = '', array $warnings = array(), array $context = array() ): self { return new self( array( 'status' => Safety_Status::WARNING, 'message' => $message, 'warnings' => $warnings, 'context' => $context ) ); }
	public static function failed( string $message = '', array $blockers = array(), array $context = array() ): self { return new self( array( 'status' => Safety_Status::FAILED, 'message' => $message, 'blockers' => $blockers, 'context' => $context ) ); }
	public function to_array(): array { return array( 'status' => $this->status, 'message' => $this->message, 'checks' => $this->checks, 'blockers' => $this->blockers, 'warnings' => $this->warnings, 'context' => $this->context ); }
	public function is_passed(): bool { return Safety_Status::PASSED === $this->status; }
	public function is_failed(): bool { return Safety_Status::FAILED === $this->status; }
	public function has_warnings(): bool { return Safety_Status::WARNING === $this->status || ! empty( $this->warnings ); }
	public function get_status(): string { return $this->status; }
	public function get_message(): string { return $this->message; }
	public function get_blockers(): array { return $this->blockers; }
	public function get_warnings(): array { return $this->warnings; }
	private function sanitize_context( array $context ): array { $clean = array(); foreach ( $context as $key => $value ) { $clean[ sanitize_key( (string) $key ) ] = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : ''; } return $clean; }
}
