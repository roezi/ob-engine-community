<?php
/**
 * Manual smoke checks for AI contract value objects.
 *
 * This script does not require a real WordPress runtime and performs no
 * external API calls or WordPress writes.
 *
 * Run from the repository root:
 * php tests/manual/ai-contracts-smoke.php
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
require_once __DIR__ . '/../../includes/Providers/Provider_Result.php';

use OBEngine\AI\AI_Error;
use OBEngine\AI\AI_Request;
use OBEngine\AI\AI_Response;
use OBEngine\AI\AI_Task_Type;
use OBEngine\AI\Model_Profile;
use OBEngine\Providers\Provider_Interface;

$request = AI_Request::from_array(
	array(
		'task_type'        => AI_Task_Type::CLASSIFY_INTENT,
		'input'            => array( 'text' => 'Example input.' ),
		'instructions'     => 'Classify the request intent.',
		'model_profile'    => Model_Profile::FAST,
		'reasoning_effort' => 'low',
		'verbosity'        => 'low',
		'metadata'         => array( 'source' => 'manual_smoke' ),
	)
);

$defaults = Model_Profile::defaults( Model_Profile::FAST );
$completed = AI_Response::completed( $request, array( 'output_json' => array( 'intent' => 'example' ) ) );
$failed = AI_Response::failed( $request, AI_Error::invalid_request( 'Example validation error.' ) );

$checks = array(
	'known task type validates'            => $request->is_valid(),
	'fast profile defaults are expected'   => 'low' === $defaults['reasoning_effort'] && 'low' === $defaults['verbosity'] && false === $defaults['background'],
	'completed response is successful'     => $completed->is_success(),
	'failed response is not successful'    => ! $failed->is_success(),
	'provider generate contract exists'    => method_exists( Provider_Interface::class, 'generate' ),
);

foreach ( $checks as $label => $passed ) {
	echo ( $passed ? 'PASS' : 'FAIL' ) . ': ' . $label . PHP_EOL;
	if ( ! $passed ) {
		exit( 1 );
	}
}
