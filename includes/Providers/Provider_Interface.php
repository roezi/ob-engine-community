<?php
/**
 * Provider layer interface contract.
 *
 * @package OBEngine\Providers
 */

namespace OBEngine\Providers;

use OBEngine\AI\AI_Request;
use OBEngine\AI\AI_Response;

defined( 'ABSPATH' ) || exit;

/**
 * Contract for future AI providers. Implementations must not write WordPress content.
 */
interface Provider_Interface {
	public const CAPABILITY_TEXT_GENERATION = 'text_generation';
	public const CAPABILITY_STRUCTURED_OUTPUT = 'structured_output';
	public const CAPABILITY_IMAGE_INPUT = 'image_input';
	public const CAPABILITY_FILE_INPUT = 'file_input';
	public const CAPABILITY_TOOL_CALLING = 'tool_calling';
	public const CAPABILITY_WEB_SEARCH = 'web_search';
	public const CAPABILITY_FILE_SEARCH = 'file_search';
	public const CAPABILITY_BACKGROUND = 'background';
	public const CAPABILITY_STREAMING = 'streaming';

	/** Get the stable provider identifier. */
	public function provider_id(): string;

	/** Whether the provider supports a normalized capability. */
	public function supports( string $capability ): bool;

	/** Generate a normalized AI response for a request. No transport is implemented here. */
	public function generate( AI_Request $request ): AI_Response;
}
