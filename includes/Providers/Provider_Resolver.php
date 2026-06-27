<?php
/**
 * Provider resolver.
 *
 * @package OBEngine\Providers
 */

namespace OBEngine\Providers;

use OBEngine\AI\AI_Error;
use OBEngine\AI\AI_Request;
use OBEngine\Providers\OpenAI\OpenAI_Provider;

defined( 'ABSPATH' ) || exit;

/**
 * Resolves configured provider adapters without live validation.
 */
final class Provider_Resolver {
	private $providers;
	private $selected_provider_id;

	public function __construct( array $providers = array(), string $selected_provider_id = '' ) {
		$this->providers = $providers;
		$this->selected_provider_id = $selected_provider_id;
	}

	public function selected_provider_id(): string {
		if ( '' !== $this->selected_provider_id ) {
			return $this->selected_provider_id;
		}
		$settings = Provider_Settings::get();
		return isset( $settings['selected_provider'] ) ? (string) $settings['selected_provider'] : 'openai';
	}

	public function resolve() {
		$provider_id = $this->selected_provider_id();
		if ( isset( $this->providers[ $provider_id ] ) && $this->providers[ $provider_id ] instanceof Provider_Interface ) {
			return $this->providers[ $provider_id ];
		}
		if ( 'openai' === $provider_id ) {
			return new OpenAI_Provider();
		}
		return null;
	}

	public function supports_provider( string $provider_id ): bool {
		return 'openai' === $provider_id || ( isset( $this->providers[ $provider_id ] ) && $this->providers[ $provider_id ] instanceof Provider_Interface );
	}

	public function provider_error( string $provider_id, AI_Request $request ): AI_Error {
		return new AI_Error(
			array(
				'code'                      => AI_Error::UNSUPPORTED_PROVIDER,
				'message_public'            => 'Selected AI provider is not supported yet.',
				'message_internal_redacted' => 'Provider is not implemented in the community provider resolver.',
				'severity'                  => AI_Error::SEVERITY_WARNING,
				'request_id'                => $request->get_request_id(),
				'provider'                  => $provider_id,
				'task_type'                 => $request->get_task_type(),
			)
		);
	}
}
