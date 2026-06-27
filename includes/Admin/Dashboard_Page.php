<?php
/**
 * Dashboard admin page.
 *
 * @package OBEngine\Admin
 */

namespace OBEngine\Admin;

use OBEngine\Support\Capabilities;
use OBEngine\Support\View;

defined( 'ABSPATH' ) || exit;

/**
 * Renders the OB Engine dashboard shell.
 */
final class Dashboard_Page {
	/**
	 * Render the page.
	 */
	public function render(): void {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			wp_die( esc_html__( 'You do not have permission to access OB Engine.', 'ob-engine' ) );
		}

		?>
		<div class="wrap ob-engine-wrap">
			<?php View::heading( __( 'OB Engine Community', 'ob-engine' ) ); ?>

			<div class="ob-engine-card">
				<h2><?php esc_html_e( 'Plugin status', 'ob-engine' ); ?></h2>
				<p><?php View::status_badge( __( 'Installed skeleton', 'ob-engine' ) ); ?></p>
				<p><strong><?php esc_html_e( 'Version:', 'ob-engine' ); ?></strong> <?php echo esc_html( OB_ENGINE_VERSION ); ?></p>
				<p><?php esc_html_e( 'Community Core is dry-run-first and draft-first. No production writes, workflow execution, live provider calls, or auto-publish behavior are implemented in this skeleton.', 'ob-engine' ); ?></p>
			</div>

			<div class="ob-engine-card">
				<h2><?php esc_html_e( 'Blueprint concepts', 'ob-engine' ); ?></h2>
				<ul class="ob-engine-list">
					<li><a href="<?php echo esc_url( plugins_url( 'docs/SAFETY_CONTRACT.md', OB_ENGINE_FILE ) ); ?>"><?php esc_html_e( 'Safety contract', 'ob-engine' ); ?></a></li>
					<li><a href="<?php echo esc_url( plugins_url( 'docs/WORKFLOW_CONTRACT.md', OB_ENGINE_FILE ) ); ?>"><?php esc_html_e( 'Workflow contract', 'ob-engine' ); ?></a></li>
					<li><a href="<?php echo esc_url( plugins_url( 'docs/ADDON_CONTRACT.md', OB_ENGINE_FILE ) ); ?>"><?php esc_html_e( 'Addon contract', 'ob-engine' ); ?></a></li>
				</ul>
			</div>
		</div>
		<?php
	}
}
