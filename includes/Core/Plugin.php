<?php
/**
 * Main plugin coordinator.
 *
 * @package OBEngine\Core
 */

namespace OBEngine\Core;

use OBEngine\Admin\Admin_Menu;
use OBEngine\Library\Library_Repository;

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
		add_action( 'init', array( new Library_Repository(), 'register_post_type' ) );

		if ( is_admin() ) {
			( new Admin_Menu() )->register();
		}
	}
}
