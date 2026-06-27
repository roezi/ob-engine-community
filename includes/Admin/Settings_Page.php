<?php
/**
 * Settings admin page.
 *
 * @package OBEngine\Admin
 */

namespace OBEngine\Admin;

use OBEngine\Providers\Provider_Settings;
use OBEngine\Support\Capabilities;
use OBEngine\Support\View;

defined( 'ABSPATH' ) || exit;

/**
 * Renders and saves safe provider settings.
 */
final class Settings_Page {
	private const NONCE_ACTION = 'ob_engine_save_settings';
	private const NONCE_NAME = 'ob_engine_settings_nonce';

	/**
	 * Render the settings page.
	 */
	public function render(): void {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			wp_die( esc_html__( 'You do not have permission to manage OB Engine settings.', 'ob-engine' ) );
		}

		$saved = $this->maybe_save();
		$settings = Provider_Settings::get();
		$providers = Provider_Settings::providers();

		?>
		<div class="wrap ob-engine-wrap">
			<?php View::heading( __( 'OB Engine Settings', 'ob-engine' ) ); ?>

			<?php if ( $saved ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Provider settings saved.', 'ob-engine' ); ?></p></div>
			<?php endif; ?>

			<div class="notice notice-info">
				<p><?php esc_html_e( 'Provider settings are stored for future BYOK use only. No live API calls, provider validation, workflow execution, or external HTTP requests are implemented on this screen.', 'ob-engine' ); ?></p>
			</div>

			<form method="post" action="">
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>

				<div class="ob-engine-card">
					<h2><?php esc_html_e( 'Provider', 'ob-engine' ); ?></h2>
					<p><?php esc_html_e( 'Choose the provider to use when provider execution is implemented later. This does not enable live calls.', 'ob-engine' ); ?></p>
					<label for="ob-engine-provider" class="ob-engine-label"><?php esc_html_e( 'Selected provider', 'ob-engine' ); ?></label>
					<select id="ob-engine-provider" name="ob_engine_settings[selected_provider]">
						<?php foreach ( $providers as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['selected_provider'], $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="ob-engine-card">
					<h2><?php esc_html_e( 'BYOK provider key', 'ob-engine' ); ?></h2>
					<p><?php esc_html_e( 'Enter a provider API key to store it for future use. The raw key is never printed back to this page.', 'ob-engine' ); ?></p>
					<label class="ob-engine-label" for="ob-engine-provider-key"><?php esc_html_e( 'API key', 'ob-engine' ); ?></label>
					<input id="ob-engine-provider-key" class="regular-text" type="password" name="ob_engine_settings[provider_api_key]" value="" autocomplete="new-password" placeholder="<?php esc_attr_e( 'Leave blank to keep existing key', 'ob-engine' ); ?>" />
					<?php if ( Provider_Settings::has_api_key( $settings ) ) : ?>
						<p>
							<?php esc_html_e( 'Stored key:', 'ob-engine' ); ?>
							<code><?php echo esc_html( Provider_Settings::masked_api_key( $settings ) ); ?></code>
						</p>
						<label>
							<input type="checkbox" name="ob_engine_settings[clear_provider_api_key]" value="1" />
							<?php esc_html_e( 'Clear stored API key', 'ob-engine' ); ?>
						</label>
					<?php else : ?>
						<p><?php esc_html_e( 'No provider API key is currently stored.', 'ob-engine' ); ?></p>
					<?php endif; ?>

					<label class="ob-engine-label" for="ob-engine-custom-provider-base-url"><?php esc_html_e( 'Custom provider base URL placeholder', 'ob-engine' ); ?></label>
					<input id="ob-engine-custom-provider-base-url" class="regular-text" type="url" name="ob_engine_settings[custom_provider_base_url]" value="<?php echo esc_attr( $settings['custom_provider_base_url'] ); ?>" placeholder="<?php esc_attr_e( 'https://example.com', 'ob-engine' ); ?>" />
				</div>

				<div class="ob-engine-card">
					<h2><?php esc_html_e( 'Safety flags', 'ob-engine' ); ?></h2>
					<label>
						<input type="checkbox" name="ob_engine_settings[dry_run_first]" value="1" <?php checked( $settings['dry_run_first'] ); ?> />
						<?php esc_html_e( 'Dry-run-first mode acknowledged', 'ob-engine' ); ?>
					</label>
					<br />
					<label>
						<input type="checkbox" name="ob_engine_settings[draft_first]" value="1" <?php checked( $settings['draft_first'] ); ?> />
						<?php esc_html_e( 'Draft-first mode acknowledged', 'ob-engine' ); ?>
					</label>
				</div>

				<?php submit_button( __( 'Save provider settings', 'ob-engine' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Save settings when the form is posted.
	 */
	private function maybe_save(): bool {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return false;
		}

		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			wp_die( esc_html__( 'You do not have permission to save OB Engine settings.', 'ob-engine' ) );
		}

		check_admin_referer( self::NONCE_ACTION, self::NONCE_NAME );

		$input = isset( $_POST['ob_engine_settings'] ) && is_array( $_POST['ob_engine_settings'] ) ? wp_unslash( $_POST['ob_engine_settings'] ) : array();
		Provider_Settings::save( $input );

		return true;
	}
}
