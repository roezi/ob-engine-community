<?php
/**
 * Manual smoke check for AI contract classes without a WordPress runtime.
 *
 * Run from repository root:
 * php tests/manual/ai-contracts-smoke.php
 */

define( 'ABSPATH', dirname( __DIR__, 2 ) . '/' );

require_once ABSPATH . 'includes/AI/Model_Profile.php';
require_once ABSPATH . 'includes/AI/AI_Task_Type.php';
require_once ABSPATH . 'includes/AI/AI_Error.php';
require_once ABSPATH . 'includes/AI/Usage_Record.php';
require_once ABSPATH . 'includes/AI/AI_Request.php';
require_once ABSPATH . 'includes/AI/AI_Response.php';
require_once ABSPATH . 'includes/AI/Structured_Output.php';
require_once ABSPATH . 'includes/Providers/Provider_Interface.php';
require_once ABSPATH . 'includes/Providers/Provider_Result.php';

use OBEngine\AI\AI_Error;
use OBEngine\AI\AI_Request;
use OBEngine\AI\AI_Response;
use OBEngine\AI\AI_Task_Type;
use OBEngine\AI\Model_Profile;
use OBEngine\Providers\Provider_Interface;
use OBEngine\Providers\Provider_Result;

$request = AI_Request::from_array(
	array(
		'task_type'    => AI_Task_Type::CLASSIFY_INTENT,
		'input'        => 'Summarize this source safely.',
		'instructions' => 'Classify intent only; do not write content.',
		'model_profile'=> Model_Profile::FAST,
		'metadata'     => array( 'smoke' => true ),
	)
);

assert( $request->is_valid() );
assert( 'low' === $request->to_array()['reasoning_effort'] );
assert( 'low' === $request->to_array()['verbosity'] );

$response = AI_Response::completed( $request, array( 'output_json' => array( 'intent' => 'summarize_activity' ) ) );
assert( $response->is_success() );
assert( array( 'intent' => 'summarize_activity' ) === $response->get_output_json() );

$failed = AI_Response::failed( $request, AI_Error::invalid_request( 'Invalid smoke request.' ) );
assert( 'failed' === $failed->get_status() );

$result = Provider_Result::unsupported( 'example', Provider_Interface::CAPABILITY_WEB_SEARCH, 'Not enabled.' );
assert( false === $result->to_array()['supported'] );

echo "AI contracts smoke check passed.\n";
