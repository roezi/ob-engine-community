<?php
/**
 * Small view helpers for admin pages.
 *
 * @package OBEngine\Support
 */

namespace OBEngine\Support;

defined( 'ABSPATH' ) || exit;

/**
 * Shared admin rendering helpers.
 */
final class View {
	/**
	 * Render a standard admin page heading.
	 *
	 * @param string $title Page title.
	 */
	public static function heading( string $title ): void {
		printf( '<h1>%s</h1>', esc_html( $title ) );
	}

	/**
	 * Render a badge-style status label.
	 *
	 * @param string $label Status text.
	 */
	public static function status_badge( string $label ): void {
		printf( '<span class="ob-engine-badge">%s</span>', esc_html( $label ) );
	}
}
