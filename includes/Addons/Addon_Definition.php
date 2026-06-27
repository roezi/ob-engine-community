<?php
/** Addon definition value object. */
namespace OBEngine\Addons;
defined( 'ABSPATH' ) || exit;
final class Addon_Definition {
	private $data;
	public function __construct( array $data ) {
		$id = isset( $data['id'] ) ? sanitize_key( (string) $data['id'] ) : '';
		$status = isset( $data['status'] ) ? sanitize_key( (string) $data['status'] ) : Addon_Status::DISABLED;
		$scope = isset( $data['scope'] ) ? sanitize_key( (string) $data['scope'] ) : Addon_Scope::COMMUNITY;
		if ( ! Addon_Status::is_valid( $status ) ) { $status = Addon_Status::DISABLED; }
		if ( ! Addon_Scope::is_valid( $scope ) ) { $scope = Addon_Scope::COMMUNITY; }
		$this->data = array(
			'id' => $id, 'name' => sanitize_text_field( $data['name'] ?? $id ), 'description' => sanitize_text_field( $data['description'] ?? '' ), 'status' => $status, 'scope' => $scope,
			'menu_slug' => sanitize_key( (string) ( $data['menu_slug'] ?? '' ) ), 'page_title' => sanitize_text_field( $data['page_title'] ?? $data['name'] ?? '' ), 'menu_title' => sanitize_text_field( $data['menu_title'] ?? $data['name'] ?? '' ),
			'capability' => sanitize_text_field( $data['capability'] ?? '' ), 'callback_class' => sanitize_text_field( $data['callback_class'] ?? '' ), 'callback_method' => sanitize_key( (string) ( $data['callback_method'] ?? '' ) ),
			'primary_action' => sanitize_text_field( $data['primary_action'] ?? '' ), 'secondary_action' => sanitize_text_field( $data['secondary_action'] ?? '' ), 'docs_url' => esc_url_raw( $data['docs_url'] ?? '' ), 'order' => absint( $data['order'] ?? 100 ),
		);
	}
	public static function from_array( array $data ): self { return new self( $data ); }
	public function to_array(): array { return $this->data; }
	public function get_id(): string { return $this->data['id']; }
	public function get_name(): string { return $this->data['name']; }
	public function get_description(): string { return $this->data['description']; }
	public function get_status(): string { return $this->data['status']; }
	public function get_scope(): string { return $this->data['scope']; }
	public function get_menu_slug(): string { return $this->data['menu_slug']; }
	public function get_callback_class(): string { return $this->data['callback_class']; }
	public function get_callback_method(): string { return $this->data['callback_method']; }
	public function is_enabled(): bool { return Addon_Status::ENABLED === $this->get_status(); }
	public function is_pro(): bool { return Addon_Status::PRO === $this->get_status() || Addon_Scope::PRO === $this->get_scope(); }
	public function is_community(): bool { return Addon_Scope::COMMUNITY === $this->get_scope(); }
}
