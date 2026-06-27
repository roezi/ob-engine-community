<?php
/** Addons admin page. */
namespace OBEngine\Addons;
use OBEngine\Support\Capabilities;
use OBEngine\Support\View;
defined( 'ABSPATH' ) || exit;
final class Addon_Admin_Page {
	public function render(): void {
		if ( ! current_user_can( Capabilities::MANAGE ) ) { wp_die( esc_html__( 'You do not have permission to manage OBE.', 'ob-engine' ) ); }
		$addons = ( new Addon_Registry() )->all();
		echo '<div class="wrap ob-engine-wrap ob-engine-page-default">'; View::heading( __( 'Addons', 'ob-engine' ) );
		echo '<div class="notice notice-info inline"><p>' . esc_html__( 'Addons are OBE modules inside the OBE admin experience. They do not create separate top-level WordPress menus.', 'ob-engine' ) . '</p></div>';
		echo '<div class="ob-engine-card"><h2>' . esc_html__( 'Overview', 'ob-engine' ) . '</h2><p>' . esc_html__( 'OBE Core owns the admin shell, settings, AI contracts, Library, Activity, Approval, Safety, Writing, and addon contracts. Bundled Community addons own feature workflows.', 'ob-engine' ) . '</p></div>';
		echo '<h2 class="nav-tab-wrapper"><span class="nav-tab nav-tab-active">' . esc_html__( 'Overview', 'ob-engine' ) . '</span><span class="nav-tab">' . esc_html__( 'Installed', 'ob-engine' ) . '</span><span class="nav-tab">' . esc_html__( 'Available', 'ob-engine' ) . '</span><span class="nav-tab">' . esc_html__( 'Developer', 'ob-engine' ) . '</span></h2>';
		echo '<div class="ob-engine-addon-grid">'; foreach ( $addons as $addon ) { $this->render_card( $addon ); } echo '</div>';
		echo '<div class="ob-engine-card"><h2>' . esc_html__( 'Developer', 'ob-engine' ) . '</h2><p>' . esc_html__( 'The ob_engine_addons filter is a metadata contract for future public addon definitions. This Community plugin does not load external addon runtime here.', 'ob-engine' ) . '</p></div></div>';
	}
	private function render_card( Addon_Definition $addon ): void { $data = $addon->to_array(); $disabled = ! $addon->is_enabled(); echo '<div class="ob-engine-card ob-engine-addon-card">'; echo '<h3>' . esc_html( $addon->get_name() ) . '</h3><p>' . esc_html( $addon->get_description() ) . '</p>'; echo '<p><span class="' . esc_attr( Addon_Status::badge_class( $addon->get_status() ) ) . '">' . esc_html( Addon_Status::label( $addon->get_status() ) ) . '</span> <span class="' . esc_attr( Addon_Scope::badge_class( $addon->get_scope() ) ) . '">' . esc_html( Addon_Scope::label( $addon->get_scope() ) ) . '</span></p>'; if ( $addon->is_enabled() && '' !== $addon->get_menu_slug() ) { echo '<p><a class="button button-primary" href="' . esc_url( admin_url( 'admin.php?page=' . $addon->get_menu_slug() ) ) . '">' . esc_html( $data['primary_action'] ) . '</a> <span class="button disabled" aria-disabled="true">' . esc_html( $data['secondary_action'] ) . '</span></p>'; } else { echo '<p><span class="button disabled" aria-disabled="true">' . esc_html( $data['primary_action'] ) . '</span> <span class="button disabled" aria-disabled="true">' . esc_html( $data['secondary_action'] ) . '</span></p>'; } if ( $disabled || $addon->is_pro() || Addon_Scope::PRIVATE_ADAPTER === $addon->get_scope() ) { echo '<p class="description">' . esc_html__( 'Not included in Community runtime.', 'ob-engine' ) . '</p>'; } echo '</div>'; }
}
