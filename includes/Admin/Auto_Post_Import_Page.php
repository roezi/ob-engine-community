<?php
/** Auto Post / Import source intake preview admin page. @package OBEngine\Admin */
namespace OBEngine\Admin;
use OBEngine\Sources\Source_Intake_Service;
use OBEngine\Sources\Source_Preview;
use OBEngine\Sources\Source_Type;
use OBEngine\Support\Capabilities;
use OBEngine\Support\View;
defined( 'ABSPATH' ) || exit;
final class Auto_Post_Import_Page {
	private const NONCE_ACTION = 'obe_source_intake_preview';
	private const NONCE_NAME = 'obe_source_intake_preview_nonce';
	public function render(): void { if ( ! current_user_can( Capabilities::MANAGE ) ) { wp_die( esc_html__( 'You do not have permission to manage OBE Auto Post / Import.', 'ob-engine' ) ); } $result = null; $data = $this->default_form_data(); if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['obe_source_preview_action'] ) ) { check_admin_referer( self::NONCE_ACTION, self::NONCE_NAME ); $data = $this->posted_data(); $result = ( new Source_Intake_Service() )->create_preview( $data ); $data['source_input'] = ''; } ?>
		<div class="wrap ob-engine-wrap ob-engine-page-form">
			<?php View::heading( __( 'Auto Post / Import', 'ob-engine' ) ); ?>
			<?php $this->render_notices( $result ); ?>
			<div class="notice notice-info inline"><p><?php esc_html_e( 'Preview source data before mapping, generation, approval, or WordPress writes.', 'ob-engine' ); ?></p></div>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'This page does not call AI providers, import content, write posts, or publish. It stores a redacted preview in Library.', 'ob-engine' ); ?></p></div>
			<form method="post" action="" class="ob-engine-card">
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>
				<input type="hidden" name="obe_source_preview_action" value="preview" />
				<table class="form-table" role="presentation"><tbody>
					<tr><th scope="row"><label for="obe-source-label"><?php esc_html_e( 'Source label', 'ob-engine' ); ?></label></th><td><input class="regular-text" id="obe-source-label" name="obe_source_preview[source_label]" type="text" value="<?php echo esc_attr( $data['source_label'] ); ?>" placeholder="<?php echo esc_attr__( 'Manual Source', 'ob-engine' ); ?>" /></td></tr>
					<tr><th scope="row"><label for="obe-source-type"><?php esc_html_e( 'Source type', 'ob-engine' ); ?></label></th><td><select id="obe-source-type" name="obe_source_preview[source_type]"><option value="auto"><?php esc_html_e( 'Auto-detect', 'ob-engine' ); ?></option><?php foreach ( Source_Type::labels() as $type => $label ) : ?><option value="<?php echo esc_attr( $type ); ?>" <?php selected( $data['source_type'], $type ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></td></tr>
					<tr><th scope="row"><label for="obe-source-input"><?php esc_html_e( 'Source input', 'ob-engine' ); ?> <span class="description"><?php esc_html_e( 'Required', 'ob-engine' ); ?></span></label></th><td><textarea id="obe-source-input" name="obe_source_preview[source_input]" rows="12" class="large-text" maxlength="20000" required><?php echo esc_textarea( $data['source_input'] ); ?></textarea><p class="description"><?php esc_html_e( 'Paste up to 20,000 characters. OBE stores only a redacted preview summary, not the full raw input.', 'ob-engine' ); ?></p></td></tr>
				</tbody></table>
				<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Preview and save to Library', 'ob-engine' ); ?></button> <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=' . Admin_Menu::LIBRARY_SLUG ) ); ?>"><?php esc_html_e( 'View Library', 'ob-engine' ); ?></a> <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=' . Admin_Menu::SLUG ) ); ?>"><?php esc_html_e( 'Review', 'ob-engine' ); ?></a></p>
			</form>
			<?php if ( is_array( $result ) && ! empty( $result['preview'] ) && $result['preview'] instanceof Source_Preview ) : $this->render_preview_card( $result['preview'] ); endif; ?>
		</div><?php }
	private function render_notices( $result ): void { if ( ! is_array( $result ) ) { return; } if ( ! empty( $result['success'] ) ) { $url = admin_url( 'admin.php?page=' . Admin_Menu::LIBRARY_SLUG . '&library_view=detail&item_id=' . absint( $result['library_item_id'] ) ); echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $result['message_public'] ) . ' <a href="' . esc_url( $url ) . '">' . esc_html__( 'View Library item', 'ob-engine' ) . '</a></p></div>'; return; } echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( isset( $result['message_public'] ) ? $result['message_public'] : __( 'Source preview failed.', 'ob-engine' ) ) . '</p></div>'; }
	private function render_preview_card( Source_Preview $preview ): void { echo '<div class="ob-engine-card"><h2>' . esc_html__( 'Preview summary', 'ob-engine' ) . '</h2>'; echo '<p>' . esc_html( $preview->get_summary() ) . '</p><table class="widefat striped"><tbody>'; echo '<tr><th>' . esc_html__( 'Source type', 'ob-engine' ) . '</th><td>' . esc_html( Source_Type::label( $preview->get_source_type() ) ) . '</td></tr>'; echo '<tr><th>' . esc_html__( 'Item count', 'ob-engine' ) . '</th><td>' . esc_html( (string) $preview->get_item_count() ) . '</td></tr>'; echo '<tr><th>' . esc_html__( 'Column count', 'ob-engine' ) . '</th><td>' . esc_html( (string) $preview->get_column_count() ) . '</td></tr>'; echo '<tr><th>' . esc_html__( 'Columns', 'ob-engine' ) . '</th><td>' . esc_html( implode( ', ', $preview->get_columns() ) ) . '</td></tr>'; echo '<tr><th>' . esc_html__( 'Warnings', 'ob-engine' ) . '</th><td>' . esc_html( $preview->get_warnings() ? implode( '; ', $preview->get_warnings() ) : __( 'None', 'ob-engine' ) ) . '</td></tr>'; echo '</tbody></table></div>'; }
	private function default_form_data(): array { return array( 'source_label' => '', 'source_type' => 'auto', 'source_input' => '' ); }
	private function posted_data(): array { $raw = isset( $_POST['obe_source_preview'] ) && is_array( $_POST['obe_source_preview'] ) ? wp_unslash( $_POST['obe_source_preview'] ) : array(); return array_merge( $this->default_form_data(), $raw ); }
}
