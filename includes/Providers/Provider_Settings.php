<?php
/**
 * Provider settings storage.
 *
 * @package OBEngine\Providers
 */

namespace OBEngine\Providers;

use OBEngine\Activity\Activity_Action;
use OBEngine\Activity\Activity_Logger;
use OBEngine\Support\Secret_Masker;

defined( 'ABSPATH' ) || exit;

/**
 * Stores provider configuration without making live API calls.
 */
final class Provider_Settings {
	public const OPTION_NAME = 'ob_engine_provider_settings';
	public const SCHEMA_VERSION = 2;

	/**
	 * Allowed provider identifiers.
	 */
	public static function providers(): array {
		return array(
			'openai'                     => __( 'OpenAI', 'ob-engine' ),
			'gemini'                     => __( 'Gemini', 'ob-engine' ),
			'anthropic'                  => __( 'Anthropic', 'ob-engine' ),
			'openrouter'                 => __( 'OpenRouter', 'ob-engine' ),
			'custom_openai_compatible'   => __( 'Custom OpenAI-compatible', 'ob-engine' ),
			'ollama'                     => __( 'Local Ollama / local provider', 'ob-engine' ),
		);
	}

	/**
	 * Safe defaults for all provider settings.
	 */
	public static function default_provider_settings(): array {
		return array(
			'schema_version'     => self::SCHEMA_VERSION,
			'selected_provider'  => 'openai',
			'providers'          => array(
				'openai'                   => array( 'api_key' => '', 'default_model' => '', 'enabled' => true ),
				'gemini'                   => array( 'api_key' => '', 'default_model' => '', 'enabled' => false ),
				'anthropic'                => array( 'api_key' => '', 'default_model' => '', 'enabled' => false ),
				'openrouter'               => array( 'api_key' => '', 'default_model' => '', 'enabled' => false, 'base_url' => '' ),
				'custom_openai_compatible' => array( 'api_key' => '', 'default_model' => '', 'enabled' => false, 'base_url' => '' ),
				'ollama'                   => array( 'api_key' => '', 'default_model' => '', 'enabled' => false, 'base_url' => '' ),
			),
			'dry_run_first'     => true,
			'draft_first'       => true,
		);
	}

	/**
	 * Get settings merged with safe defaults and migrated legacy values.
	 */
	public static function get(): array {
		$settings = get_option( self::OPTION_NAME, array() );

		if ( ! is_array( $settings ) ) {
			$settings = array();
		}

		return self::normalize_settings( $settings );
	}

	/**
	 * Save sanitized settings.
	 */
	public static function save( array $input ): void {
		$before = self::get();
		$settings = self::normalize_settings( $before );
		$settings['selected_provider'] = isset( $input['selected_provider'] ) ? self::sanitize_provider_id( (string) $input['selected_provider'] ) : $settings['selected_provider'];
		$settings['dry_run_first'] = ! empty( $input['dry_run_first'] );
		$settings['draft_first'] = ! empty( $input['draft_first'] );

		$input_providers = isset( $input['providers'] ) && is_array( $input['providers'] ) ? $input['providers'] : array();

		foreach ( self::providers() as $provider_id => $label ) {
			$provider_input = isset( $input_providers[ $provider_id ] ) && is_array( $input_providers[ $provider_id ] ) ? $input_providers[ $provider_id ] : array();
			$current = $settings['providers'][ $provider_id ];

			$current['enabled'] = ! empty( $provider_input['enabled'] );
			$current['default_model'] = isset( $provider_input['default_model'] ) ? sanitize_text_field( (string) $provider_input['default_model'] ) : '';

			if ( self::provider_supports_base_url( $provider_id ) ) {
				$current['base_url'] = isset( $provider_input['base_url'] ) ? esc_url_raw( (string) $provider_input['base_url'] ) : '';
			}

			if ( ! empty( $provider_input['clear_api_key'] ) ) {
				$current['api_key'] = '';
			} elseif ( isset( $provider_input['api_key'] ) ) {
				$new_key = sanitize_text_field( (string) $provider_input['api_key'] );
				if ( '' !== $new_key ) {
					$current['api_key'] = $new_key;
				}
			}

			$settings['providers'][ $provider_id ] = $current;
		}

		$settings['schema_version'] = self::SCHEMA_VERSION;
		update_option( self::OPTION_NAME, $settings, false );
		self::log_changes( $before, $settings, $input_providers );
	}

	public static function get_provider_config( string $provider_id ): array {
		$settings = self::get();
		$provider_id = self::sanitize_provider_id( $provider_id );
		return $settings['providers'][ $provider_id ];
	}

	public static function get_selected_provider_config(): array {
		$settings = self::get();
		return $settings['providers'][ $settings['selected_provider'] ];
	}

	public static function get_api_key( string $provider_id ): string {
		$config = self::get_provider_config( $provider_id );
		return trim( (string) $config['api_key'] );
	}

	public static function has_api_key( string $provider_id, array $settings = null ): bool {
		$settings = null === $settings ? self::get() : self::normalize_settings( $settings );
		$provider_id = self::sanitize_provider_id( $provider_id );
		return '' !== trim( (string) $settings['providers'][ $provider_id ]['api_key'] );
	}

	public static function masked_api_key( string $provider_id, array $settings = null ): string {
		$settings = null === $settings ? self::get() : self::normalize_settings( $settings );
		$provider_id = self::sanitize_provider_id( $provider_id );
		return Secret_Masker::mask( (string) $settings['providers'][ $provider_id ]['api_key'] );
	}

	public static function provider_supports_base_url( string $provider_id ): bool {
		return in_array( self::sanitize_provider_id( $provider_id ), array( 'openrouter', 'custom_openai_compatible', 'ollama' ), true );
	}

	public static function provider_requires_api_key( string $provider_id ): bool {
		return in_array( self::sanitize_provider_id( $provider_id ), array( 'openai', 'gemini', 'anthropic', 'openrouter' ), true );
	}

	public static function sanitize_provider_id( string $provider_id ): string {
		$provider_id = sanitize_key( $provider_id );
		return array_key_exists( $provider_id, self::providers() ) ? $provider_id : 'openai';
	}

	private static function normalize_settings( array $settings ): array {
		$defaults = self::default_provider_settings();
		$normalized = $defaults;
		$normalized['selected_provider'] = isset( $settings['selected_provider'] ) ? self::sanitize_provider_id( (string) $settings['selected_provider'] ) : 'openai';
		$normalized['dry_run_first'] = array_key_exists( 'dry_run_first', $settings ) ? ! empty( $settings['dry_run_first'] ) : true;
		$normalized['draft_first'] = array_key_exists( 'draft_first', $settings ) ? ! empty( $settings['draft_first'] ) : true;

		$providers = isset( $settings['providers'] ) && is_array( $settings['providers'] ) ? $settings['providers'] : array();
		foreach ( self::providers() as $provider_id => $label ) {
			$provider = isset( $providers[ $provider_id ] ) && is_array( $providers[ $provider_id ] ) ? $providers[ $provider_id ] : array();
			$normalized['providers'][ $provider_id ]['api_key'] = isset( $provider['api_key'] ) ? (string) $provider['api_key'] : $normalized['providers'][ $provider_id ]['api_key'];
			$normalized['providers'][ $provider_id ]['default_model'] = isset( $provider['default_model'] ) ? (string) $provider['default_model'] : '';
			$normalized['providers'][ $provider_id ]['enabled'] = isset( $provider['enabled'] ) ? ! empty( $provider['enabled'] ) : $normalized['providers'][ $provider_id ]['enabled'];
			if ( self::provider_supports_base_url( $provider_id ) ) {
				$normalized['providers'][ $provider_id ]['base_url'] = isset( $provider['base_url'] ) ? (string) $provider['base_url'] : '';
			}
		}

		if ( ! empty( $settings['provider_api_key'] ) && '' === $normalized['providers']['openai']['api_key'] ) {
			$normalized['providers']['openai']['api_key'] = (string) $settings['provider_api_key'];
		}
		if ( ! empty( $settings['custom_provider_base_url'] ) && '' === $normalized['providers']['custom_openai_compatible']['base_url'] ) {
			$normalized['providers']['custom_openai_compatible']['base_url'] = (string) $settings['custom_provider_base_url'];
		}

		return $normalized;
	}

	private static function log_changes( array $before, array $after, array $input_providers ): void {
		if ( ! class_exists( Activity_Logger::class ) ) {
			return;
		}

		$logger = new Activity_Logger();
		foreach ( self::providers() as $provider_id => $label ) {
			$before_config = $before['providers'][ $provider_id ];
			$after_config = $after['providers'][ $provider_id ];
			$context = self::activity_context( $provider_id, $after );

			if ( isset( $input_providers[ $provider_id ]['api_key'] ) && '' !== trim( (string) $input_providers[ $provider_id ]['api_key'] ) && $before_config['api_key'] !== $after_config['api_key'] ) {
				$logger->success( Activity_Action::PROVIDER_KEY_SAVED, array( 'message' => Activity_Action::label( Activity_Action::PROVIDER_KEY_SAVED ), 'context' => $context ) );
			}
			if ( '' !== trim( (string) $before_config['api_key'] ) && '' === trim( (string) $after_config['api_key'] ) ) {
				$logger->success( Activity_Action::PROVIDER_KEY_CLEARED, array( 'message' => Activity_Action::label( Activity_Action::PROVIDER_KEY_CLEARED ), 'context' => $context ) );
			}
			if ( $before_config['enabled'] !== $after_config['enabled'] || $before_config['default_model'] !== $after_config['default_model'] || ( $before_config['base_url'] ?? '' ) !== ( $after_config['base_url'] ?? '' ) || $before['selected_provider'] !== $after['selected_provider'] ) {
				$logger->success( Activity_Action::SETTINGS_UPDATED, array( 'message' => Activity_Action::label( Activity_Action::SETTINGS_UPDATED ), 'context' => $context ) );
			}
		}
	}

	private static function activity_context( string $provider_id, array $settings ): array {
		$config = $settings['providers'][ $provider_id ];
		return array(
			'provider_id'           => $provider_id,
			'selected_provider'     => $settings['selected_provider'],
			'enabled'               => ! empty( $config['enabled'] ),
			'has_key'               => '' !== trim( (string) $config['api_key'] ),
			'base_url_present'      => ! empty( $config['base_url'] ),
			'default_model_present' => ! empty( $config['default_model'] ),
		);
	}
}
