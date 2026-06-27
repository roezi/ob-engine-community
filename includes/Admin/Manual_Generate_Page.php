<?php
/**
 * Manual Generate admin page.
 *
 * @package OBEngine\Admin
 */

namespace OBEngine\Admin;

use OBEngine\AI\AI_Request_Builder;
use OBEngine\AI\AI_Task_Type;
use OBEngine\AI\Manual_Generate_Service;
use OBEngine\AI\Structured_Output;
use OBEngine\Library\Library_Type;
use OBEngine\Support\Capabilities;
use OBEngine\Support\View;

\defined( 'ABSPATH' ) || exit;

/**
 * Renders an explicit admin-controlled generation form.
 */
final class Manual_Generate_Page {
	public const SLUG = 'ob-engine-generate';
	private const NONCE_ACTION = 'obe_manual_generate';
	private const NONCE_NAME = 'obe_manual_generate_nonce';

	public function render(): void {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			wp_die( esc_html__( 'You do not have permission to manage OB Engine.', 'ob-engine' ) );
		}

		$result = null;
		$data   = $this->default_form_data();
		if ( 'POST' === $_SERVER['REQUEST_METHOD'] && isset( $_POST['obe_manual_generate_action'] ) ) {
			check_admin_referer( self::NONCE_ACTION, self::NONCE_NAME );
			$data = $this->posted_data();
			$result = ( new Manual_Generate_Service() )->generate_to_library( $data );
		}
		?>
		<div class="wrap ob-engine-wrap ob-engine-page-form">
			<?php View::heading( __( 'Manual Generate', 'ob-engine' ) ); ?>
			<?php $this->render_notices( $result ); ?>

			<div class="notice notice-info inline"><p><?php esc_html_e( 'Generate review-first AI output and save it to Library. This does not write or publish WordPress content.', 'ob-engine' ); ?></p></div>
			<div class="notice notice-warning inline"><p><?php esc_html_e( 'This action may call the selected provider using your saved BYOK settings. Review provider/model settings before generating.', 'ob-engine' ); ?></p></div>

			<form method="post" action="" class="ob-engine-card">
				<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>
				<input type="hidden" name="obe_manual_generate_action" value="generate" />
				<table class="form-table" role="presentation"><tbody>
					<?php $this->render_task_select( $data ); ?>
					<tr><th scope="row"><label for="obe-library-title"><?php esc_html_e( 'Library title', 'ob-engine' ); ?> <span class="description"><?php esc_html_e( 'Required', 'ob-engine' ); ?></span></label></th><td><input class="regular-text" id="obe-library-title" name="obe_manual_generate[title]" type="text" value="<?php echo esc_attr( $data['title'] ); ?>" required /></td></tr>
					<tr><th scope="row"><label for="obe-input"><?php esc_html_e( 'Input', 'ob-engine' ); ?> <span class="description"><?php esc_html_e( 'Required', 'ob-engine' ); ?></span></label></th><td><textarea id="obe-input" name="obe_manual_generate[input]" rows="8" class="large-text" required><?php echo esc_textarea( $data['input'] ); ?></textarea><p class="description"><?php esc_html_e( 'This is sent to the provider only after you submit this form.', 'ob-engine' ); ?></p></td></tr>
					<tr><th scope="row"><label for="obe-instructions"><?php esc_html_e( 'Instructions', 'ob-engine' ); ?></label></th><td><textarea id="obe-instructions" name="obe_manual_generate[instructions]" rows="5" class="large-text"><?php echo esc_textarea( $data['instructions'] ); ?></textarea><p class="description"><?php esc_html_e( 'Leave blank to use safe default instructions for the selected task.', 'ob-engine' ); ?></p></td></tr>
					<tr><th scope="row"><label for="obe-model"><?php esc_html_e( 'Optional model override', 'ob-engine' ); ?></label></th><td><input class="regular-text" id="obe-model" name="obe_manual_generate[model]" type="text" value="<?php echo esc_attr( $data['model'] ); ?>" /></td></tr>
					<?php $this->render_schema_select( $data ); ?>
					<tr><th scope="row"><label for="obe-source-label"><?php esc_html_e( 'Source label', 'ob-engine' ); ?></label></th><td><input class="regular-text" id="obe-source-label" name="obe_manual_generate[source_label]" type="text" value="<?php echo esc_attr( $data['source_label'] ); ?>" placeholder="<?php echo esc_attr__( 'Manual Generate', 'ob-engine' ); ?>" /></td></tr>
					<?php $this->render_library_type_select( $data ); ?>
				</tbody></table>
				<p class="submit"><button type="submit" class="button button-primary"><?php esc_html_e( 'Generate and save to Library', 'ob-engine' ); ?></button> <a class="button button-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=' . Admin_Menu::LIBRARY_SLUG ) ); ?>"><?php esc_html_e( 'Back to Library', 'ob-engine' ); ?></a></p>
			</form>
		</div>
		<?php
	}

	private function render_notices( $result ): void {
		if ( ! is_array( $result ) ) { return; }
		if ( ! empty( $result['success'] ) ) {
			$url = admin_url( 'admin.php?page=' . Admin_Menu::LIBRARY_SLUG . '&library_view=detail&item_id=' . absint( $result['library_item_id'] ) );
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $result['message_public'] ) . ' <a href="' . esc_url( $url ) . '">' . esc_html__( 'View Library item', 'ob-engine' ) . '</a></p></div>';
			return;
		}
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( isset( $result['message_public'] ) ? $result['message_public'] : __( 'Manual generation failed.', 'ob-engine' ) ) . '</p></div>';
	}

	private function default_form_data(): array { return array( 'task_type' => AI_Task_Type::GENERATE_CONTENT_DRAFT, 'title' => '', 'input' => '', 'instructions' => '', 'model' => '', 'output_schema' => '', 'source_label' => '', 'library_type' => Library_Type::CONTENT_DRAFT ); }
	private function posted_data(): array { $raw = isset( $_POST['obe_manual_generate'] ) && is_array( $_POST['obe_manual_generate'] ) ? wp_unslash( $_POST['obe_manual_generate'] ) : array(); return array_merge( $this->default_form_data(), $raw ); }
	private function task_options(): array { return array( AI_Task_Type::GENERATE_CONTENT_DRAFT, AI_Task_Type::IMPROVE_CONTENT_DRAFT, AI_Task_Type::REVIEW_CONTENT, AI_Task_Type::GENERATE_SEO_REVIEW, AI_Task_Type::GENERATE_TRANSLATION_PLAN, AI_Task_Type::GENERATE_PERFORMANCE_REPORT, AI_Task_Type::WORKFLOW_PLAN, AI_Task_Type::EXPLAIN_ERROR, AI_Task_Type::SUMMARIZE_ACTIVITY ); }
	private function render_task_select( array $data ): void { echo '<tr><th scope="row"><label for="obe-task-type">' . esc_html__( 'Task type', 'ob-engine' ) . '</label></th><td><select id="obe-task-type" name="obe_manual_generate[task_type]">'; foreach ( $this->task_options() as $task ) { printf( '<option value="%s" %s>%s</option>', esc_attr( $task ), selected( $data['task_type'], $task, false ), esc_html( $task ) ); } echo '</select></td></tr>'; }
	private function render_schema_select( array $data ): void { echo '<tr><th scope="row"><label for="obe-output-schema">' . esc_html__( 'Output schema', 'ob-engine' ) . '</label></th><td><select id="obe-output-schema" name="obe_manual_generate[output_schema]"><option value="">' . esc_html__( 'Use detected default schema', 'ob-engine' ) . '</option>'; foreach ( Structured_Output::all() as $schema ) { printf( '<option value="%s" %s>%s</option>', esc_attr( $schema ), selected( $data['output_schema'], $schema, false ), esc_html( $schema ) ); } echo '</select><p class="description">' . esc_html__( 'Optional. Defaults are detected from the selected task type.', 'ob-engine' ) . '</p></td></tr>'; }
	private function render_library_type_select( array $data ): void { echo '<tr><th scope="row"><label for="obe-library-type">' . esc_html__( 'Save output as Library type', 'ob-engine' ) . '</label></th><td><select id="obe-library-type" name="obe_manual_generate[library_type]">'; foreach ( Library_Type::labels() as $type => $label ) { printf( '<option value="%s" %s>%s</option>', esc_attr( $type ), selected( $data['library_type'], $type, false ), esc_html( $label ) ); } echo '</select><p class="description">' . esc_html__( 'Generated items are always saved with Needs Review status.', 'ob-engine' ) . '</p></td></tr>'; }
}
