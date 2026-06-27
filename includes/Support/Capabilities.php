<?php
/**
 * Capability helpers for OB Engine admin screens.
 *
 * @package OBEngine\Support
 */

namespace OBEngine\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Centralizes capability names used by the community plugin skeleton.
 */
final class Capabilities {
	/**
	 * Capability required to manage OB Engine settings.
	 */
	public const MANAGE = 'manage_options';
}
