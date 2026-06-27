<?php
/**
 * Settings admin page.
 *
 * @package OBEngine\Admin
 */

namespace OBEngine\Admin;

use OBEngine\Support\Capabilities;
use OBEngine\Support\View;

defined( 'ABSPATH' ) || exit;

/**
 * Renders and saves harmless placeholder settings.
 */
final class Settings_Page {
	private const OPTION_NAME = 'ob_engine_settings';
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
		$settings = $this->get_settings();

		?>
		<div class="wrap ob-engine-wrap">
			<?php View::heading( __( 'OB Engine Settings', 'ob-engine' ) ); ?>

			<?php if ( $saved ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Placeholder settings saved.', 'ob-engine' ); ?></p></div>
			<?php endif; ?>

			<div class="notice notice-info">
				<p><?php esc_html_e( 'Provider credentials are placeholders only. Live API calls and safe masked API key storage are not implemented yet, so API key fields are intentionally not saved.', 'ob-engine' ); ?></p>
			</div>

			<form method="post" action="">
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>

				<div class="ob-engine-card">
					<h2><?php esc_html_e( 'Provider', 'ob-engine' ); ?></h2>
					<p><?php esc_html_e( 'Choose a placeholder provider for future BYOK configuration. This does not enable live calls.', 'ob-engine' ); ?></p>
					<label for="ob-engine-provider" class="ob-engine-label"><?php esc_html_e( 'Selected provider', 'ob-engine' ); ?></label>
					<select id="ob-engine-provider" name="ob_engine_settings[selected_provider]">
						<?php foreach ( $this->providers() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $settings['selected_provider'], $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="ob-engine-card">
					<h2><?php esc_html_e( 'BYOK placeholders', 'ob-engine' ); ?></h2>
					<?php foreach ( $this->providers() as $value => $label ) : ?>
						<label class="ob-engine-label" for="ob-engine-key-<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></label>
						<input id="ob-engine-key-<?php echo esc_attr( $value ); ?>" class="regular-text" type="password" value="" placeholder="<?php esc_attr_e( 'API key storage not implemented yet', 'ob-engine' ); ?>" autocomplete="off" disabled />
					<?php endforeach; ?>
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

				<?php submit_button( __( 'Save placeholder settings', 'ob-engine' ) ); ?>
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
		$provider = isset( $input['selected_provider'] ) ? sanitize_key( $input['selected_provider'] ) : 'openai';
		$providers = array_keys( $this->providers() );

		if ( ! in_array( $provider, $providers, true ) ) {
			$provider = 'openai';
		}

		$settings = array(
			'selected_provider' => $provider,
			'dry_run_first'     => ! empty( $input['dry_run_first'] ),
			'draft_first'       => ! empty( $input['draft_first'] ),
		);

		update_option( self::OPTION_NAME, $settings, false );

		return true;
	}

	/**
	 * Get saved settings with safe defaults.
	 */
	private function get_settings(): array {
		$defaults = array(
			'selected_provider' => 'openai',
			'dry_run_first'     => true,
			'draft_first'       => true,
		);

		$settings = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return wp_parse_args( $settings, $defaults );
	}

	/**
	 * Providers shown as future BYOK placeholders.
	 */
	private function providers(): array {
		return array(
			'openai' => __( 'OpenAI', 'ob-engine' ),
			'gemini' => __( 'Gemini', 'ob-engine' ),
			'custom' => __( 'Custom provider', 'ob-engine' ),
		);
	}
}
