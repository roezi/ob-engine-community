<?php
/**
 * Manual smoke checks for the OpenAI Responses client without live API calls.
 *
 * @package OBEngine
 */

define( 'ABSPATH', __DIR__ . '/../../' );

require_once __DIR__ . '/../../includes/AI/Model_Profile.php';
require_once __DIR__ . '/../../includes/AI/AI_Task_Type.php';
require_once __DIR__ . '/../../includes/AI/AI_Error.php';
require_once __DIR__ . '/../../includes/AI/Usage_Record.php';
require_once __DIR__ . '/../../includes/AI/AI_Request.php';
require_once __DIR__ . '/../../includes/AI/AI_Response.php';
require_once __DIR__ . '/../../includes/AI/Structured_Output.php';
require_once __DIR__ . '/../../includes/Providers/Provider_Interface.php';
require_once __DIR__ . '/../../includes/Providers/Provider_Settings.php';
require_once __DIR__ . '/../../includes/Providers/Provider_Result.php';
require_once __DIR__ . '/../../includes/Providers/OpenAI/OpenAI_Error_Mapper.php';
require_once __DIR__ . '/../../includes/Providers/OpenAI/OpenAI_Responses_Client.php';
require_once __DIR__ . '/../../includes/Providers/OpenAI/OpenAI_Provider.php';

use OBEngine\AI\AI_Task_Type;
use OBEngine\AI\Model_Profile;
use OBEngine\AI\AI_Request;
use OBEngine\Providers\Provider_Interface;
use OBEngine\Providers\OpenAI\OpenAI_Provider;
use OBEngine\Providers\OpenAI\OpenAI_Responses_Client;

$request = AI_Request::from_array(
	array(
		'task_type'        => AI_Task_Type::GENERATE_CONTENT_DRAFT,
		'input'            => array( 'topic' => 'Example topic.' ),
		'instructions'     => 'Return a safe draft plan only.',
		'model_profile'    => Model_Profile::BALANCED,
		'model'            => 'example-model',
		'reasoning_effort' => 'medium',
		'verbosity'        => 'medium',
		'metadata'         => array( 'source' => 'manual_smoke' ),
	)
);

$client = new OpenAI_Responses_Client();
$payload = $client->build_payload( $request );
$response = $client->map_response(
	$request,
	array(
		'id'          => 'resp_example',
		'model'       => 'example-model',
		'output_text' => 'Example output text.',
		'usage'       => array(
			'input_tokens'          => 10,
			'output_tokens'         => 5,
			'output_tokens_details' => array( 'reasoning_tokens' => 2 ),
			'total_tokens'          => 15,
		),
	)
);
$mapped = $response->to_array();
$provider = new OpenAI_Provider();

$checks = array(
	'payload model maps from request'             => 'example-model' === $payload['model'],
	'payload input maps from request'             => array( 'topic' => 'Example topic.' ) === $payload['input'],
	'payload instructions map from request'       => 'Return a safe draft plan only.' === $payload['instructions'],
	'payload reasoning effort maps from request'  => 'medium' === $payload['reasoning']['effort'],
	'payload text verbosity maps from request'    => 'medium' === $payload['text']['verbosity'],
	'payload store defaults false'                => false === $payload['store'],
	'payload background defaults false'           => false === $payload['background'],
	'mapped response is successful'               => $response->is_success(),
	'output text is extracted'                    => 'Example output text.' === $response->get_output_text(),
	'usage maps to normalized record'             => isset( $mapped['usage']['total_tokens'] ) && 15 === $mapped['usage']['total_tokens'],
	'provider id is openai'                       => 'openai' === $provider->provider_id(),
	'provider supports text generation'           => $provider->supports( Provider_Interface::CAPABILITY_TEXT_GENERATION ),
	'provider supports structured output'         => $provider->supports( Provider_Interface::CAPABILITY_STRUCTURED_OUTPUT ),
	'provider does not support web search yet'    => ! $provider->supports( Provider_Interface::CAPABILITY_WEB_SEARCH ),
	'provider does not support file search yet'   => ! $provider->supports( Provider_Interface::CAPABILITY_FILE_SEARCH ),
	'provider does not support background yet'    => ! $provider->supports( Provider_Interface::CAPABILITY_BACKGROUND ),
);

foreach ( $checks as $label => $passed ) {
	echo ( $passed ? 'PASS' : 'FAIL' ) . ': ' . $label . PHP_EOL;
	if ( ! $passed ) {
		exit( 1 );
	}
}
