<?php
/**
 * Admin menu registration.
 *
 * @package OBEngine\Admin
 */

namespace OBEngine\Admin;

use OBEngine\Activity\Activity_Admin_Page;
use OBEngine\Addons\Addon_Admin_Page;
use OBEngine\Addons\Addon_Registry;
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
	public const GENERATE_SLUG = 'ob-engine-generate';
	public const AUTO_POST_SLUG = 'ob-engine-auto-post';
	public const ACTIVITY_SLUG = 'ob-engine-activity';
	public const ADDONS_SLUG = 'ob-engine-addons';

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
		$generate  = new Manual_Generate_Page();
		$auto_post = new Auto_Post_Import_Page();
		$humanizer = new Editorial_Humanizer_Page();
		$addons    = new Addon_Admin_Page();
		$activity  = new Activity_Admin_Page();

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


		$registry = new Addon_Registry();
		$auto_post_addon = $registry->get( 'auto_post' );
		// Auto Post / Import is a bundled Community addon registered under the OBE admin shell.
		if ( $auto_post_addon && $auto_post_addon->is_enabled() ) {
			add_submenu_page(
				self::SLUG,
				esc_html__( 'Auto Post / Import', 'ob-engine' ),
				esc_html__( 'Auto Post / Import', 'ob-engine' ),
				Capabilities::MANAGE,
				$auto_post_addon->get_menu_slug(),
				array( $auto_post, 'render' )
			);
		}


		$humanizer_addon = $registry->get( 'editorial_humanizer' );
		// Editorial Humanizer / Readability is a bundled Community addon under OBE.
		if ( $humanizer_addon && $humanizer_addon->is_enabled() ) {
			add_submenu_page(
				self::SLUG,
				esc_html__( 'Humanizer', 'ob-engine' ),
				esc_html__( 'Humanizer', 'ob-engine' ),
				Capabilities::MANAGE,
				$humanizer_addon->get_menu_slug(),
				array( $humanizer, 'render' )
			);
		}

		add_submenu_page(
			self::SLUG,
			esc_html__( 'Manual Generate', 'ob-engine' ),
			esc_html__( 'Generate', 'ob-engine' ),
			Capabilities::MANAGE,
			self::GENERATE_SLUG,
			array( $generate, 'render' )
		);


		add_submenu_page(
			self::SLUG,
			esc_html__( 'Addons', 'ob-engine' ),
			esc_html__( 'Addons', 'ob-engine' ),
			Capabilities::MANAGE,
			self::ADDONS_SLUG,
			array( $addons, 'render' )
		);

		add_submenu_page(
			self::SLUG,
			esc_html__( 'Activity', 'ob-engine' ),
			esc_html__( 'Activity', 'ob-engine' ),
			Capabilities::MANAGE,
			self::ACTIVITY_SLUG,
			array( $activity, 'render' )
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
