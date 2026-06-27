<?php
/**
 * OpenAI provider implementation.
 *
 * @package OBEngine\Providers\OpenAI
 */

namespace OBEngine\Providers\OpenAI;

use OBEngine\AI\AI_Error;
use OBEngine\AI\AI_Request;
use OBEngine\AI\AI_Response;
use OBEngine\Providers\Provider_Interface;
use OBEngine\Providers\Provider_Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Provider adapter for OpenAI Responses API.
 */
final class OpenAI_Provider implements Provider_Interface {
	private $client;

	public function __construct( OpenAI_Responses_Client $client = null ) {
		$this->client = $client ? $client : new OpenAI_Responses_Client();
	}

	public function provider_id(): string {
		return OpenAI_Responses_Client::PROVIDER_ID;
	}

	public function supports( string $capability ): bool {
		return in_array(
			$capability,
			array(
				self::CAPABILITY_TEXT_GENERATION,
				self::CAPABILITY_STRUCTURED_OUTPUT,
			),
			true
		);
	}

	public function generate( AI_Request $request ): AI_Response {
		$settings = Provider_Settings::get();

		if ( ! isset( $settings['selected_provider'] ) || OpenAI_Responses_Client::PROVIDER_ID !== $settings['selected_provider'] ) {
			return AI_Response::failed(
				$request,
				new AI_Error(
					array(
						'code'           => AI_Error::UNSUPPORTED_PROVIDER,
						'message_public' => 'Selected provider is not supported by this client.',
						'severity'       => AI_Error::SEVERITY_WARNING,
						'request_id'     => $request->get_request_id(),
						'provider'       => $this->provider_id(),
						'task_type'      => $request->get_task_type(),
					)
				)
			);
		}

		$api_key = Provider_Settings::get_api_key( 'openai' );

		if ( '' === $api_key ) {
			return AI_Response::failed( $request, AI_Error::missing_provider_key() );
		}

		if ( ! $request->is_valid() ) {
			return AI_Response::failed(
				$request,
				AI_Error::invalid_request(
					implode( ' ', $request->validate() ),
					array(
						'request_id' => $request->get_request_id(),
						'provider'   => $this->provider_id(),
						'task_type'  => $request->get_task_type(),
					)
				)
			);
		}

		$request_data = $request->to_array();
		if ( empty( $request_data['model'] ) ) {
			return AI_Response::failed(
				$request,
				AI_Error::invalid_request(
					'AI request model is required for OpenAI provider.',
					array(
						'request_id' => $request->get_request_id(),
						'provider'   => $this->provider_id(),
						'task_type'  => $request->get_task_type(),
					)
				)
			);
		}

		return $this->client->create_response( $request, $api_key );
	}
}
