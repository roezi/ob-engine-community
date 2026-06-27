<?php
/**
 * AI Engine orchestrator.
 *
 * @package OBEngine\AI
 */

namespace OBEngine\AI;

use OBEngine\Activity\Activity_Action;
use OBEngine\Activity\Activity_Logger;
use OBEngine\Activity\Activity_Object_Type;
use OBEngine\Providers\Provider_Interface;
use OBEngine\Providers\Provider_Resolver;
use OBEngine\Providers\Provider_Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Runtime bridge between task contracts and provider adapters.
 */
final class AI_Engine {
	private $builder;
	private $provider_resolver;
	private $normalizer;
	private $logger;

	public function __construct( AI_Request_Builder $builder = null, Provider_Resolver $provider_resolver = null, AI_Response_Normalizer $normalizer = null, Activity_Logger $logger = null ) {
		$this->provider_resolver = $provider_resolver ?: new Provider_Resolver();
		$this->builder = $builder ?: new AI_Request_Builder( null, $this->provider_resolver );
		$this->normalizer = $normalizer ?: new AI_Response_Normalizer();
		$this->logger = $logger ?: new Activity_Logger();
	}

	public function run( array $task_data ): AI_Response {
		$request = $this->build_request( $task_data );
		if ( ! $request->is_valid() ) {
			$response = AI_Response::failed( $request, AI_Error::invalid_request( implode( ' ', $request->validate() ), array( 'request_id' => $request->get_request_id(), 'task_type' => $request->get_task_type() ) ) );
			$this->log_response_failed( $request, $response );
			return $response;
		}

		$provider_id = $this->provider_resolver->selected_provider_id();
		$this->log_request_created( $request, $provider_id );
		$provider = $this->provider_resolver->resolve();

		if ( ! $provider instanceof Provider_Interface ) {
			$response = AI_Response::failed( $request, $this->provider_resolver->provider_error( $provider_id, $request ) );
			$this->log_response_failed( $request, $response );
			return $response;
		}

		if ( ! $this->provider_supports_request( $provider, $request ) ) {
			$response = AI_Response::failed( $request, new AI_Error( array( 'code' => AI_Error::UNSUPPORTED_CAPABILITY, 'message_public' => 'Selected AI provider does not support the requested AI capability.', 'severity' => AI_Error::SEVERITY_WARNING, 'request_id' => $request->get_request_id(), 'provider' => $provider->provider_id(), 'task_type' => $request->get_task_type() ) ) );
			$this->log_response_failed( $request, $response );
			return $response;
		}

		$this->logger->success( Activity_Action::PROVIDER_KEY_USED, array( 'object_type' => Activity_Object_Type::AI_REQUEST, 'object_label' => $request->get_task_type(), 'message' => 'AI provider generation attempted.', 'context' => array( 'provider_id' => $provider->provider_id(), 'has_key' => Provider_Settings::has_api_key( $provider->provider_id() ) ) ) );
		$response = $this->normalizer->normalize( $request, $provider->generate( $request ) );

		if ( $response->is_success() ) {
			$this->logger->success( Activity_Action::AI_RESPONSE_COMPLETED, array( 'object_type' => Activity_Object_Type::AI_REQUEST, 'object_label' => $request->get_task_type(), 'message' => 'AI response completed.', 'context' => $this->normalizer->safe_activity_context( $request, $response ) ) );
		} else {
			$this->log_response_failed( $request, $response );
		}

		return $response;
	}

	public function build_request( array $task_data ): AI_Request {
		return $this->builder->from_task_data( $task_data );
	}

	public function provider_supports_request( Provider_Interface $provider, AI_Request $request ): bool {
		$data = $request->to_array();
		if ( ! $provider->supports( Provider_Interface::CAPABILITY_TEXT_GENERATION ) ) {
			return false;
		}
		if ( ! empty( $data['output_schema'] ) && ! $provider->supports( Provider_Interface::CAPABILITY_STRUCTURED_OUTPUT ) ) {
			return false;
		}
		return true;
	}

	private function log_request_created( AI_Request $request, string $provider_id ): void {
		$data = $request->to_array();
		$this->logger->success( Activity_Action::AI_REQUEST_CREATED, array( 'object_type' => Activity_Object_Type::AI_REQUEST, 'object_label' => $request->get_task_type(), 'message' => 'AI request created.', 'context' => array( 'request_id' => $request->get_request_id(), 'task_type' => $request->get_task_type(), 'model_profile' => $request->get_model_profile(), 'model_present' => ! empty( $data['model'] ), 'output_schema' => isset( $data['output_schema'] ) ? (string) $data['output_schema'] : '', 'provider_id' => $provider_id, 'source_context_present' => ! empty( $data['source_context'] ), 'library_context_present' => ! empty( $data['library_context'] ) ) ) );
	}

	private function log_response_failed( AI_Request $request, AI_Response $response ): void {
		$data = $response->to_array();
		$context = $this->normalizer->safe_activity_context( $request, $response );
		if ( isset( $data['error']['code'] ) ) {
			$context['error_code'] = (string) $data['error']['code'];
			if ( AI_Error::MISSING_PROVIDER_KEY === $data['error']['code'] ) {
				$this->logger->warning( Activity_Action::PROVIDER_KEY_MISSING, array( 'object_type' => Activity_Object_Type::AI_REQUEST, 'object_label' => $request->get_task_type(), 'message' => 'AI provider key is missing.', 'context' => array( 'provider_id' => isset( $data['error']['provider'] ) ? (string) $data['error']['provider'] : '', 'has_key' => false ) ) );
			}
		}
		$this->logger->failed( Activity_Action::AI_RESPONSE_FAILED, array( 'object_type' => Activity_Object_Type::AI_REQUEST, 'object_label' => $request->get_task_type(), 'message' => 'AI response failed.', 'context' => $context ) );
	}
}
