<?php
/**
 * Admin menu registration.
 *
 * @package OBEngine\Admin
 */

namespace OBEngine\Admin;

use OBEngine\Library\Library_Admin_Page;
use OBEngine\Support\Capabilities;

defined( 'ABSPATH' ) || exit;

/**
 * Registers OB Engine admin pages.
 */
final class Admin_Menu {
	public const SLUG = 'ob-engine';
	public const SETTINGS_SLUG = 'ob-engine-settings';
	public const LIBRARY_SLUG = 'ob-engine-library';

	/**
	 * Register hooks for admin menu and assets.
	 */
	public function register(): void {
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Add top-level and settings admin pages.
	 */
	public function add_menu(): void {
		$dashboard = new Dashboard_Page();
		$settings  = new Settings_Page();
		$library   = new Library_Admin_Page();

		add_menu_page(
			esc_html__( 'OB Engine', 'ob-engine' ),
			esc_html__( 'OB Engine', 'ob-engine' ),
			Capabilities::MANAGE,
			self::SLUG,
			array( $dashboard, 'render' ),
			'dashicons-shield-alt',
			58
		);

		add_submenu_page(
			self::SLUG,
			esc_html__( 'Dashboard', 'ob-engine' ),
			esc_html__( 'Dashboard', 'ob-engine' ),
			Capabilities::MANAGE,
			self::SLUG,
			array( $dashboard, 'render' )
		);

		add_submenu_page(
			self::SLUG,
			esc_html__( 'Library', 'ob-engine' ),
			esc_html__( 'Library', 'ob-engine' ),
			Capabilities::MANAGE,
			self::LIBRARY_SLUG,
			array( $library, 'render' )
		);

		add_submenu_page(
			self::SLUG,
			esc_html__( 'Settings', 'ob-engine' ),
			esc_html__( 'Settings', 'ob-engine' ),
			Capabilities::MANAGE,
			self::SETTINGS_SLUG,
			array( $settings, 'render' )
		);
	}

	/**
	 * Enqueue CSS only on OB Engine admin pages.
	 *
	 * @param string $hook_suffix Current admin hook suffix.
	 */
	public function enqueue_assets( string $hook_suffix ): void {
		if ( false === strpos( $hook_suffix, self::SLUG ) ) {
			return;
		}

		wp_enqueue_style(
			'ob-engine-admin',
			OB_ENGINE_URL . 'assets/admin/admin.css',
			array(),
			OB_ENGINE_VERSION
		);
	}
}
