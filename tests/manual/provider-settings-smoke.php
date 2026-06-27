<?php
/**
 * Manual smoke checks for provider settings without live API calls.
 *
 * @package OBEngine
 */

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );
}

$GLOBALS['obe_provider_settings_option'] = array();

if ( ! function_exists( '__' ) ) { function __( $text ) { return $text; } }
if ( ! function_exists( 'sanitize_key' ) ) { function sanitize_key( $key ) { return strtolower( preg_replace( '/[^a-z0-9_\-]/', '', (string) $key ) ); } }
if ( ! function_exists( 'sanitize_text_field' ) ) { function sanitize_text_field( $value ) { return trim( strip_tags( (string) $value ) ); } }
if ( ! function_exists( 'esc_url_raw' ) ) { function esc_url_raw( $value ) { return trim( (string) $value ); } }
if ( ! function_exists( 'get_option' ) ) { function get_option( $name, $default = false ) { return isset( $GLOBALS['obe_provider_settings_option'][ $name ] ) ? $GLOBALS['obe_provider_settings_option'][ $name ] : $default; } }
if ( ! function_exists( 'update_option' ) ) { function update_option( $name, $value, $autoload = null ) { $GLOBALS['obe_provider_settings_option'][ $name ] = $value; $GLOBALS['obe_provider_settings_autoload'] = $autoload; return true; } }

require_once ABSPATH . 'includes/Support/Secret_Masker.php';
require_once ABSPATH . 'includes/Providers/Provider_Settings.php';

use OBEngine\Providers\Provider_Settings;

function obe_provider_settings_smoke_assert( $condition, $message ) {
	if ( ! $condition ) {
		fwrite( STDERR, "FAIL: {$message}\n" );
		exit( 1 );
	}
}

$providers = Provider_Settings::providers();
foreach ( array( 'openai', 'gemini', 'anthropic', 'openrouter', 'custom_openai_compatible', 'ollama' ) as $provider_id ) {
	obe_provider_settings_smoke_assert( isset( $providers[ $provider_id ] ), "{$provider_id} provider should be registered" );
}

obe_provider_settings_smoke_assert( Provider_Settings::provider_supports_base_url( 'openrouter' ), 'OpenRouter should support base URL' );
obe_provider_settings_smoke_assert( Provider_Settings::provider_supports_base_url( 'custom_openai_compatible' ), 'custom provider should support base URL' );
obe_provider_settings_smoke_assert( Provider_Settings::provider_supports_base_url( 'ollama' ), 'Ollama should support base URL' );
obe_provider_settings_smoke_assert( ! Provider_Settings::provider_supports_base_url( 'openai' ), 'OpenAI should not support base URL in settings UI' );

foreach ( array( 'openai', 'gemini', 'anthropic', 'openrouter' ) as $provider_id ) {
	obe_provider_settings_smoke_assert( Provider_Settings::provider_requires_api_key( $provider_id ), "{$provider_id} should require an API key" );
}
obe_provider_settings_smoke_assert( ! Provider_Settings::provider_requires_api_key( 'custom_openai_compatible' ), 'custom provider should allow blank key' );
obe_provider_settings_smoke_assert( ! Provider_Settings::provider_requires_api_key( 'ollama' ), 'Ollama should allow blank key' );

$GLOBALS['obe_provider_settings_option'][ Provider_Settings::OPTION_NAME ] = array(
	'selected_provider'        => 'not-valid',
	'provider_api_key'         => 'sk-old-openai-key-1234567890',
	'custom_provider_base_url' => 'https://local-gateway.example.test/v1',
);
$migrated = Provider_Settings::get();
obe_provider_settings_smoke_assert( 'openai' === $migrated['selected_provider'], 'selected provider should be sanitized to openai' );
obe_provider_settings_smoke_assert( 'sk-old-openai-key-1234567890' === $migrated['providers']['openai']['api_key'], 'legacy OpenAI key should migrate' );
obe_provider_settings_smoke_assert( 'https://local-gateway.example.test/v1' === $migrated['providers']['custom_openai_compatible']['base_url'], 'legacy custom base URL should migrate' );
obe_provider_settings_smoke_assert( false === strpos( Provider_Settings::masked_api_key( 'openai', $migrated ), 'sk-old-openai-key-1234567890' ), 'masking should not expose raw key' );
obe_provider_settings_smoke_assert( 'openai' === Provider_Settings::sanitize_provider_id( '../gemini<script>' ), 'invalid selected provider should sanitize to openai' );

Provider_Settings::save(
	array(
		'selected_provider' => 'gemini',
		'providers'         => array(
			'openai' => array(
				'api_key'       => '',
				'default_model' => 'gpt-example',
				'enabled'       => '1',
			),
			'gemini' => array(
				'api_key'       => 'gemini-key-1234567890',
				'default_model' => 'gemini-example',
				'enabled'       => '1',
			),
		),
	)
);
$saved = get_option( Provider_Settings::OPTION_NAME );
obe_provider_settings_smoke_assert( 'sk-old-openai-key-1234567890' === $saved['providers']['openai']['api_key'], 'blank OpenAI key input should keep existing OpenAI key' );
obe_provider_settings_smoke_assert( 'gemini-key-1234567890' === $saved['providers']['gemini']['api_key'], 'Gemini key should save separately' );
obe_provider_settings_smoke_assert( 'sk-old-openai-key-1234567890' === Provider_Settings::get_api_key( 'openai' ), 'OpenAI provider should retrieve key through new accessor' );

Provider_Settings::save(
	array(
		'selected_provider' => 'openai',
		'providers'         => array(
			'gemini' => array( 'clear_api_key' => '1' ),
		),
	)
);
$cleared = get_option( Provider_Settings::OPTION_NAME );
obe_provider_settings_smoke_assert( 'sk-old-openai-key-1234567890' === $cleared['providers']['openai']['api_key'], 'clearing Gemini should not clear OpenAI key' );
obe_provider_settings_smoke_assert( '' === $cleared['providers']['gemini']['api_key'], 'Gemini key should be cleared' );
obe_provider_settings_smoke_assert( false === $GLOBALS['obe_provider_settings_autoload'], 'provider settings should save with autoload false' );

echo "Provider settings smoke checks passed.\n";
