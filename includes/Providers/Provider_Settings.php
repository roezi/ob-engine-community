<?php
/**
 * Provider settings storage.
 *
 * @package OBEngine\Providers
 */

namespace OBEngine\Providers;

use OBEngine\Support\Secret_Masker;

defined( 'ABSPATH' ) || exit;

/**
 * Stores provider configuration without making live API calls.
 */
final class Provider_Settings {
	public const OPTION_NAME = 'ob_engine_provider_settings';

	/**
	 * Allowed provider identifiers.
	 */
	public static function providers(): array {
		return array(
			'openai' => __( 'OpenAI', 'ob-engine' ),
			'gemini' => __( 'Gemini', 'ob-engine' ),
			'custom' => __( 'Custom provider', 'ob-engine' ),
		);
	}

	/**
	 * Get settings merged with safe defaults.
	 */
	public static function get(): array {
		$settings = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return wp_parse_args(
			$settings,
			array(
				'selected_provider'        => 'openai',
				'provider_api_key'         => '',
				'custom_provider_base_url' => '',
				'dry_run_first'            => true,
				'draft_first'              => true,
			)
		);
	}

	/**
	 * Save sanitized settings.
	 */
	public static function save( array $input ): void {
		$current = self::get();
		$provider = isset( $input['selected_provider'] ) ? sanitize_key( $input['selected_provider'] ) : 'openai';

		if ( ! array_key_exists( $provider, self::providers() ) ) {
			$provider = 'openai';
		}

		$api_key = $current['provider_api_key'];
		if ( ! empty( $input['clear_provider_api_key'] ) ) {
			$api_key = '';
		} elseif ( isset( $input['provider_api_key'] ) ) {
			$new_key = sanitize_text_field( $input['provider_api_key'] );
			if ( '' !== $new_key ) {
				$api_key = $new_key;
			}
		}

		$settings = array(
			'selected_provider'        => $provider,
			'provider_api_key'         => $api_key,
			'custom_provider_base_url' => isset( $input['custom_provider_base_url'] ) ? esc_url_raw( $input['custom_provider_base_url'] ) : '',
			'dry_run_first'            => ! empty( $input['dry_run_first'] ),
			'draft_first'              => ! empty( $input['draft_first'] ),
		);

		update_option( self::OPTION_NAME, $settings, false );
	}

	/**
	 * Whether a provider API key is currently stored.
	 */
	public static function has_api_key( array $settings ): bool {
		return '' !== trim( (string) $settings['provider_api_key'] );
	}

	/**
	 * Masked provider API key status for display.
	 */
	public static function masked_api_key( array $settings ): string {
		return Secret_Masker::mask( (string) $settings['provider_api_key'] );
	}
}
