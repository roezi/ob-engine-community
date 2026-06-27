<?php
/**
 * Main plugin coordinator.
 *
 * @package OBEngine\Core
 */

namespace OBEngine\Core;

use OBEngine\Admin\Admin_Menu;

defined( 'ABSPATH' ) || exit;

/**
 * Boots the minimal community plugin skeleton.
 */
final class Plugin {
	/**
	 * Singleton instance.
	 *
	 * @var self|null
	 */
	private static $instance = null;

	/**
	 * Get the plugin instance.
	 */
	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register WordPress hooks.
	 */
	public function register(): void {
		if ( is_admin() ) {
			( new Admin_Menu() )->register();
		}
	}
}
