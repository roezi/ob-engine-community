<?php
/**
 * Library admin page.
 *
 * @package OBEngine\Library
 */

namespace OBEngine\Library;

use OBEngine\Admin\Admin_Menu;
use OBEngine\Approval\Approval_Repository;
use OBEngine\Approval\Approval_Service;
use OBEngine\Approval\Approval_Status;
use OBEngine\Support\Capabilities;
use OBEngine\Support\View;

use function add_query_arg;

defined( 'ABSPATH' ) || exit;

/**
 * Renders and handles private OBE Library admin UI.
 */
final class Library_Admin_Page {
	private const NONCE_ACTION = 'ob_engine_library_action';
	private const NONCE_NAME   = 'ob_engine_library_nonce';

	/** @var Library_Repository */
	private $repository;

	/** @var Approval_Repository */
	private $approval_repository;

	/** @var Approval_Service */
	private $approval_service;

	public function __construct() {
		$this->repository = new Library_Repository();
		$this->approval_repository = new Approval_Repository( $this->repository );
		$this->approval_service = new Approval_Service( $this->approval_repository, $this->repository );
	}

	public function render(): void {
		if ( ! current_user_can( Capabilities::MANAGE ) ) {
			wp_die( esc_html__( 'You do not have permission to manage OBE Library.', 'ob-engine' ) );
		}

		$this->handle_actions();
		$view = isset( $_GET['library_view'] ) ? sanitize_key( wp_unslash( $_GET['library_view'] ) ) : 'list';
		?>
		<div class="wrap ob-engine-wrap ob-engine-library-wrap">
			<?php View::heading( __( 'Library', 'ob-engine' ) ); ?>
			<?php $this->render_notice(); ?>
			<div class="notice notice-info"><p><?php esc_html_e( 'Library stores OBE plans, drafts, reviews, and previews before any WordPress write.', 'ob-engine' ); ?></p></div>
			<?php
			if ( 'new' === $view ) {
				$this->render_form();
			} elseif ( 'edit' === $view ) {
				$this->render_form( $this->current_item() );
			} elseif ( 'detail' === $view ) {
				$this->render_detail( $this->current_item() );
			} else {
				$this->render_list();
			}
			?>
		</div>
		<?php
	}


	private function render_notice(): void {
		$message = isset( $_GET['message'] ) ? sanitize_key( wp_unslash( $_GET['message'] ) ) : '';
		if ( '' === $message ) {
			return;
		}
		$messages = array(
			'approved' => __( 'Library item approved.', 'ob-engine' ),
			'rejected' => __( 'Library item rejected.', 'ob-engine' ),
			'revoked'  => __( 'Approval revoked; item requires review again.', 'ob-engine' ),
			'error'    => __( 'The requested Library action could not be completed.', 'ob-engine' ),
		);
		if ( ! isset( $messages[ $message ] ) ) {
			return;
		}
		$type = 'error' === $message ? 'notice-error' : 'notice-success';
		echo '<div class="notice ' . esc_attr( $type ) . '"><p>' . esc_html( $messages[ $message ] ) . '</p></div>';
	}

	private function handle_actions(): void {
		if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
			return;
		}
		check_admin_referer( self::NONCE_ACTION, self::NONCE_NAME );
		$action = isset( $_POST['obe_library_action'] ) ? sanitize_key( wp_unslash( $_POST['obe_library_action'] ) ) : '';
		$id     = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : 0;
		$data   = isset( $_POST['obe_library_item'] ) && is_array( $_POST['obe_library_item'] ) ? wp_unslash( $_POST['obe_library_item'] ) : array();

		if ( 'create' === $action ) {
			$result = $this->repository->create( $data );
			$this->redirect_after_write( is_wp_error( $result ) ? 0 : (int) $result, is_wp_error( $result ) ? 'error' : 'created' );
		} elseif ( 'update' === $action && $id ) {
			$result = $this->repository->update( $id, $data );
			$this->redirect_after_write( $id, is_wp_error( $result ) ? 'error' : 'updated' );
		} elseif ( 'archive' === $action && $id ) {
			$result = $this->repository->archive( $id );
			$this->redirect_after_write( $id, is_wp_error( $result ) ? 'error' : 'archived' );
		} elseif ( 'delete' === $action && $id ) {
			$result = $this->repository->delete( $id );
			$this->redirect_after_write( 0, is_wp_error( $result ) ? 'error' : 'deleted' );
		} elseif ( 'approve' === $action && $id ) {
			$note = isset( $_POST['obe_approval_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['obe_approval_note'] ) ) : '';
			$result = $this->approval_service->approve_library_item( $id, $note );
			$this->redirect_after_write( $id, is_wp_error( $result ) ? 'error' : 'approved' );
		} elseif ( 'reject' === $action && $id ) {
			$note = isset( $_POST['obe_approval_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['obe_approval_note'] ) ) : '';
			$result = $this->approval_service->reject_library_item( $id, $note );
			$this->redirect_after_write( $id, is_wp_error( $result ) ? 'error' : 'rejected' );
		} elseif ( 'revoke' === $action && $id ) {
			$note = isset( $_POST['obe_approval_note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['obe_approval_note'] ) ) : '';
			$result = $this->approval_service->revoke_library_item( $id, $note );
			$this->redirect_after_write( $id, is_wp_error( $result ) ? 'error' : 'revoked' );
		}
	}

	private function render_list(): void {
		$current = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';
		$tabs = array( '' => __( 'All', 'ob-engine' ), Library_Status::DRAFT => __( 'Draft', 'ob-engine' ), Library_Status::NEEDS_REVIEW => __( 'Needs Review', 'ob-engine' ), Library_Status::APPROVED => __( 'Approved', 'ob-engine' ), Library_Status::WRITTEN => __( 'Written', 'ob-engine' ), Library_Status::ARCHIVED => __( 'Archived', 'ob-engine' ) );
		?>
		<h2 class="nav-tab-wrapper">
			<?php foreach ( $tabs as $status => $label ) : ?>
				<a class="nav-tab <?php echo $current === $status ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( $this->url( array( 'status' => $status ) ) ); ?>"><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</h2>
		<div class="ob-engine-action-bar"><a class="button button-primary" href="<?php echo esc_url( $this->url( array( 'library_view' => 'new' ) ) ); ?>"><?php esc_html_e( 'Add Library Item', 'ob-engine' ); ?></a></div>
		<table class="widefat striped">
			<thead><tr><th><?php esc_html_e( 'Title', 'ob-engine' ); ?></th><th><?php esc_html_e( 'Type', 'ob-engine' ); ?></th><th><?php esc_html_e( 'Status', 'ob-engine' ); ?></th><th><?php esc_html_e( 'Source', 'ob-engine' ); ?></th><th><?php esc_html_e( 'Updated', 'ob-engine' ); ?></th><th><?php esc_html_e( 'Actions', 'ob-engine' ); ?></th></tr></thead>
			<tbody>
			<?php $items = $this->repository->query( array( 'status' => $current, 'limit' => 50 ) ); ?>
			<?php if ( empty( $items ) ) : ?>
				<tr><td colspan="6"><?php esc_html_e( 'No Library items yet. Add one to store review-first plans, drafts, reviews, or previews.', 'ob-engine' ); ?></td></tr>
			<?php endif; ?>
			<?php foreach ( $items as $item ) : $data = $item->to_array(); ?>
				<tr>
					<td><strong><?php echo esc_html( $item->get_title() ); ?></strong></td>
					<td><?php echo esc_html( Library_Type::label( $item->get_type() ) ); ?></td>
					<td><span class="<?php echo esc_attr( Library_Status::badge_class( $item->get_status() ) ); ?>"><?php echo esc_html( Library_Status::label( $item->get_status() ) ); ?></span></td>
					<td><?php echo esc_html( $data['source_label'] ); ?></td>
					<td><?php echo esc_html( $data['updated_at'] ); ?></td>
					<td><?php $this->row_actions( $item ); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	private function render_detail( ?Library_Item $item ): void {
		if ( ! $item ) { echo '<div class="notice notice-error"><p>' . esc_html__( 'Library item not found.', 'ob-engine' ) . '</p></div>'; return; }
		$data = $item->to_array();
		?>
		<div class="ob-engine-action-bar"><a class="button" href="<?php echo esc_url( $this->url() ); ?>"><?php esc_html_e( 'Back to Library', 'ob-engine' ); ?></a><a class="button" href="<?php echo esc_url( $this->url( array( 'library_view' => 'edit', 'item_id' => $item->get_id() ) ) ); ?>"><?php esc_html_e( 'Edit', 'ob-engine' ); ?></a><?php $this->action_form( 'archive', $item->get_id(), __( 'Archive', 'ob-engine' ) ); ?><?php $this->action_form( 'delete', $item->get_id(), __( 'Delete', 'ob-engine' ), true ); ?></div>
		<div class="ob-engine-card">
			<h2><?php echo esc_html( $item->get_title() ); ?></h2>
			<p class="ob-engine-meta"><span class="ob-engine-status-badge"><?php echo esc_html( Library_Type::label( $item->get_type() ) ); ?></span> <span class="<?php echo esc_attr( Library_Status::badge_class( $item->get_status() ) ); ?>"><?php echo esc_html( Library_Status::label( $item->get_status() ) ); ?></span> <?php echo esc_html( $data['updated_at'] ); ?></p>
		</div>
		<div class="ob-engine-detail-grid">
			<?php $this->render_approval_panel( $item ); ?>
			<div class="ob-engine-card"><h2><?php esc_html_e( 'Content summary', 'ob-engine' ); ?></h2><p><?php echo esc_html( $item->get_summary() ); ?></p><div><?php echo wp_kses_post( wpautop( $item->get_content() ) ); ?></div></div>
			<div class="ob-engine-card"><h2><?php esc_html_e( 'Source summary', 'ob-engine' ); ?></h2><p><?php echo esc_html( $item->get_source_label() ); ?></p></div>
			<div class="ob-engine-card"><h2><?php esc_html_e( 'Redacted payload / summary', 'ob-engine' ); ?></h2><pre><?php echo esc_html( $item->get_payload_redacted() ); ?></pre></div>
		</div>
		<?php
	}


	private function render_approval_panel( Library_Item $item ): void {
		$record = $this->approval_repository->get( $item->get_id() );
		$can_approve = $this->approval_repository->can_approve( $item );
		$can_reject = $this->approval_repository->can_reject( $item );
		$is_locked = in_array( $item->get_status(), array( Library_Status::ARCHIVED, Library_Status::WRITTEN, Library_Status::FAILED ), true );
		?>
		<div class="ob-engine-card ob-engine-approval-panel">
			<h2><?php esc_html_e( 'Approval', 'ob-engine' ); ?></h2>
			<p><span class="<?php echo esc_attr( Approval_Status::badge_class( $record->get_status() ) ); ?>"><?php echo esc_html( Approval_Status::label( $record->get_status() ) ); ?></span></p>
			<p class="description"><?php esc_html_e( 'Approval is required before any future WordPress write. This panel does not write or publish content.', 'ob-engine' ); ?></p>
			<?php if ( '' !== $record->get_actor_label() ) : ?><p><strong><?php esc_html_e( 'Actor:', 'ob-engine' ); ?></strong> <?php echo esc_html( $record->get_actor_label() ); ?></p><?php endif; ?>
			<?php if ( '' !== $record->get_decided_at() ) : ?><p><strong><?php esc_html_e( 'Decided:', 'ob-engine' ); ?></strong> <?php echo esc_html( $record->get_decided_at() ); ?></p><?php endif; ?>
			<?php if ( '' !== $record->get_note() ) : ?><p><strong><?php esc_html_e( 'Note:', 'ob-engine' ); ?></strong><br /><?php echo nl2br( esc_html( $record->get_note() ) ); ?></p><?php endif; ?>
			<?php if ( $record->is_approved() ) : ?><p><?php esc_html_e( 'This item is approved for future dry-run/write-draft steps.', 'ob-engine' ); ?></p><?php endif; ?>
			<?php if ( $record->is_rejected() ) : ?><p><?php esc_html_e( 'This item was rejected and must be revised before future write actions.', 'ob-engine' ); ?></p><?php endif; ?>
			<?php if ( $is_locked ) : ?><p><?php esc_html_e( 'Approval actions are unavailable for archived, written, or failed Library items.', 'ob-engine' ); ?></p><?php endif; ?>
			<?php if ( $can_approve ) : ?>
				<?php $this->approval_form( 'approve', $item->get_id(), __( 'Approval note', 'ob-engine' ), __( 'Approve', 'ob-engine' ), false ); ?>
			<?php endif; ?>
			<?php if ( $can_reject ) : ?>
				<?php $this->approval_form( 'reject', $item->get_id(), __( 'Rejection note', 'ob-engine' ), __( 'Reject', 'ob-engine' ), true ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	private function approval_form( string $action, int $id, string $note_label, string $button_label, bool $danger ): void {
		$textarea_id = 'obe-approval-note-' . $action;
		?>
		<form method="post" action="" class="ob-engine-approval-form">
			<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>
			<input type="hidden" name="obe_library_action" value="<?php echo esc_attr( $action ); ?>" />
			<input type="hidden" name="item_id" value="<?php echo esc_attr( $id ); ?>" />
			<label class="ob-engine-label" for="<?php echo esc_attr( $textarea_id ); ?>"><?php echo esc_html( $note_label ); ?></label>
			<textarea class="large-text" rows="3" id="<?php echo esc_attr( $textarea_id ); ?>" name="obe_approval_note"></textarea>
			<p><button class="button <?php echo $danger ? 'button-secondary' : 'button-primary'; ?>" type="submit"><?php echo esc_html( $button_label ); ?></button></p>
		</form>
		<?php
	}

	private function render_form( ?Library_Item $item = null ): void {
		$data = $item ? $item->to_array() : array( 'title' => '', 'type' => Library_Type::CONTENT_DRAFT, 'status' => Library_Status::default_status(), 'source_label' => '', 'summary' => '', 'content' => '', 'payload_redacted' => '' );
		?>
		<form method="post" action="">
			<?php wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME ); ?>
			<input type="hidden" name="obe_library_action" value="<?php echo $item ? 'update' : 'create'; ?>" />
			<?php if ( $item ) : ?><input type="hidden" name="item_id" value="<?php echo esc_attr( $item->get_id() ); ?>" /><?php endif; ?>
			<div class="ob-engine-card">
				<label class="ob-engine-label" for="obe-library-title"><?php esc_html_e( 'Title', 'ob-engine' ); ?></label><input class="regular-text" id="obe-library-title" name="obe_library_item[title]" value="<?php echo esc_attr( $data['title'] ); ?>" required />
				<label class="ob-engine-label" for="obe-library-type"><?php esc_html_e( 'Type', 'ob-engine' ); ?></label><select id="obe-library-type" name="obe_library_item[type]"><?php foreach ( Library_Type::labels() as $value => $label ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $data['type'], $value ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select>
				<label class="ob-engine-label" for="obe-library-status"><?php esc_html_e( 'Status', 'ob-engine' ); ?></label><select id="obe-library-status" name="obe_library_item[status]"><?php foreach ( Library_Status::labels() as $value => $label ) : ?><option value="<?php echo esc_attr( $value ); ?>" <?php selected( $data['status'], $value ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select>
				<label class="ob-engine-label" for="obe-library-source"><?php esc_html_e( 'Source label', 'ob-engine' ); ?></label><input class="regular-text" id="obe-library-source" name="obe_library_item[source_label]" value="<?php echo esc_attr( $data['source_label'] ); ?>" />
				<label class="ob-engine-label" for="obe-library-summary"><?php esc_html_e( 'Summary', 'ob-engine' ); ?></label><textarea class="large-text" rows="4" id="obe-library-summary" name="obe_library_item[summary]"><?php echo esc_textarea( $data['summary'] ); ?></textarea>
				<label class="ob-engine-label" for="obe-library-content"><?php esc_html_e( 'Content', 'ob-engine' ); ?></label><textarea class="large-text" rows="8" id="obe-library-content" name="obe_library_item[content]"><?php echo esc_textarea( $data['content'] ); ?></textarea>
				<label class="ob-engine-label" for="obe-library-payload"><?php esc_html_e( 'Redacted payload / summary', 'ob-engine' ); ?></label><textarea class="large-text code" rows="6" id="obe-library-payload" name="obe_library_item[payload_redacted]"><?php echo esc_textarea( $data['payload_redacted'] ); ?></textarea>
				<p class="description"><?php esc_html_e( 'Store redacted summary data only. Do not paste raw source files, credentials, API keys, private prompts, tokens, or large private payloads.', 'ob-engine' ); ?></p>
			</div>
			<?php submit_button( $item ? __( 'Update Library Item', 'ob-engine' ) : __( 'Create Library Item', 'ob-engine' ) ); ?>
		</form>
		<?php
	}

	private function row_actions( Library_Item $item ): void {
		echo '<a href="' . esc_url( $this->url( array( 'library_view' => 'detail', 'item_id' => $item->get_id() ) ) ) . '">' . esc_html__( 'View', 'ob-engine' ) . '</a> | ';
		echo '<a href="' . esc_url( $this->url( array( 'library_view' => 'edit', 'item_id' => $item->get_id() ) ) ) . '">' . esc_html__( 'Edit', 'ob-engine' ) . '</a> ';
		$this->action_form( 'archive', $item->get_id(), __( 'Archive', 'ob-engine' ) );
		$this->action_form( 'delete', $item->get_id(), __( 'Delete', 'ob-engine' ), true );
	}

	private function action_form( string $action, int $id, string $label, bool $danger = false ): void {
		$confirm = $danger ? __( 'Delete this private Library item? Archive is preferred unless permanent deletion is required.', 'ob-engine' ) : __( 'Archive this private Library item?', 'ob-engine' );
		printf( '<form method="post" action="" style="display:inline">%s<input type="hidden" name="obe_library_action" value="%s"/><input type="hidden" name="item_id" value="%d"/><button class="button-link %s" type="submit" onclick="return confirm(\'%s\');">%s</button></form> ', wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME, true, false ), esc_attr( $action ), esc_attr( $id ), $danger ? 'ob-engine-danger-link' : '', esc_js( $confirm ), esc_html( $label ) );
	}

	private function current_item(): ?Library_Item {
		$id = isset( $_GET['item_id'] ) ? absint( $_GET['item_id'] ) : 0;
		return $id ? $this->repository->get( $id ) : null;
	}

	private function redirect_after_write( int $id, string $message ): void {
		$args = array( 'message' => $message );
		if ( $id ) { $args['library_view'] = 'detail'; $args['item_id'] = $id; }
		wp_safe_redirect( $this->url( $args ) );
		exit;
	}

	private function url( array $args = array() ): string {
		return add_query_arg( array_filter( array_merge( array( 'page' => Admin_Menu::LIBRARY_SLUG ), $args ), static function ( $value ) { return '' !== $value && null !== $value; } ), admin_url( 'admin.php' ) );
	}
}
