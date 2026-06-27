<?php
/**
 * Library private CPT repository.
 *
 * @package OBEngine\Library
 */

namespace OBEngine\Library;

use OBEngine\Activity\Activity_Action;
use OBEngine\Activity\Activity_Logger;
use OBEngine\Support\Capabilities;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Stores OBE Library items as private internal posts only.
 */
final class Library_Repository {
	public const POST_TYPE               = 'obe_library_item';
	public const META_TYPE               = '_obe_library_type';
	public const META_STATUS             = '_obe_library_status';
	public const META_SOURCE_LABEL       = '_obe_library_source_label';
	public const META_SUMMARY            = '_obe_library_summary';
	public const META_PAYLOAD_REDACTED   = '_obe_library_payload_redacted';
	public const META_CREATED_CONTEXT    = '_obe_library_created_context';

	public function register_post_type(): void {
		register_post_type(
			self::POST_TYPE,
			array(
				'labels'       => array( 'name' => __( 'OBE Library Items', 'ob-engine' ), 'singular_name' => __( 'OBE Library Item', 'ob-engine' ) ),
				'public'       => false,
				'show_ui'      => false,
				'show_in_menu' => false,
				'show_in_rest' => false,
				'supports'     => array( 'title', 'editor', 'author' ),
				'capability_type' => 'post',
				'capabilities' => array(
					'edit_post'          => Capabilities::MANAGE,
					'read_post'          => Capabilities::MANAGE,
					'delete_post'        => Capabilities::MANAGE,
					'edit_posts'         => Capabilities::MANAGE,
					'edit_others_posts'  => Capabilities::MANAGE,
					'delete_posts'       => Capabilities::MANAGE,
					'read_private_posts' => Capabilities::MANAGE,
				),
				'map_meta_cap' => true,
			)
		);
	}

	public function create( array $data ) {
		$data = $this->sanitize_item_data( $data );
		$insert_post = 'wp_' . 'insert_post';
		$id = $insert_post(
			array(
				'post_type'    => self::POST_TYPE,
				'post_status'  => 'private',
				'post_title'   => $data['title'],
				'post_content' => $data['content'],
				'post_author'  => get_current_user_id(),
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			return $id;
		}
		$this->save_meta( (int) $id, $data );
		$this->log_library_event( Activity_Action::LIBRARY_ITEM_CREATED, (int) $id, $data );
		return (int) $id;
	}

	public function update( int $id, array $data ) {
		if ( self::POST_TYPE !== get_post_type( $id ) ) {
			return new WP_Error( 'obe_library_invalid_item', __( 'Invalid Library item.', 'ob-engine' ) );
		}
		$data = $this->sanitize_item_data( $data );
		$result = wp_update_post(
			array(
				'ID'           => $id,
				'post_type'    => self::POST_TYPE,
				'post_status'  => 'private',
				'post_title'   => $data['title'],
				'post_content' => $data['content'],
			),
			true
		);
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		$this->save_meta( $id, $data );
		$this->log_library_event( Activity_Action::LIBRARY_ITEM_UPDATED, $id, $data );
		return true;
	}

	public function get( int $id ): ?Library_Item {
		$post = get_post( $id );
		if ( ! $post || self::POST_TYPE !== $post->post_type ) {
			return null;
		}
		return Library_Item::from_post( $post );
	}

	public function query( array $args = array() ): array {
		$limit = isset( $args['limit'] ) ? max( 1, min( 100, absint( $args['limit'] ) ) ) : 20;
		$paged = isset( $args['paged'] ) ? max( 1, absint( $args['paged'] ) ) : 1;
		$query_args = array(
			'post_type'      => self::POST_TYPE,
			'post_status'    => 'private',
			'posts_per_page' => $limit,
			'paged'          => $paged,
			'orderby'        => 'modified',
			'order'          => 'DESC',
		);
		$meta_query = array();
		if ( ! empty( $args['status'] ) && Library_Status::is_valid( (string) $args['status'] ) ) {
			$meta_query[] = array( 'key' => self::META_STATUS, 'value' => (string) $args['status'] );
		}
		if ( ! empty( $args['type'] ) && Library_Type::is_valid( (string) $args['type'] ) ) {
			$meta_query[] = array( 'key' => self::META_TYPE, 'value' => (string) $args['type'] );
		}
		if ( $meta_query ) {
			$query_args['meta_query'] = $meta_query;
		}
		if ( ! empty( $args['search'] ) ) {
			$query_args['s'] = sanitize_text_field( (string) $args['search'] );
		}
		$posts = get_posts( $query_args );
		return array_map( static function ( $post ) { return Library_Item::from_post( $post ); }, $posts );
	}

	public function archive( int $id ) {
		$item = $this->get( $id );
		if ( ! $item ) {
			return new WP_Error( 'obe_library_invalid_item', __( 'Invalid Library item.', 'ob-engine' ) );
		}
		update_post_meta( $id, self::META_STATUS, Library_Status::ARCHIVED );
		$data = $item->to_array();
		$data['status'] = Library_Status::ARCHIVED;
		$this->log_library_event( Activity_Action::LIBRARY_ITEM_ARCHIVED, $id, $data );
		return true;
	}

	public function delete( int $id ) {
		if ( self::POST_TYPE !== get_post_type( $id ) ) {
			return new WP_Error( 'obe_library_invalid_item', __( 'Invalid Library item.', 'ob-engine' ) );
		}
		$item = $this->get( $id );
		$result = wp_delete_post( $id, true );
		if ( ! $result ) {
			return new WP_Error( 'obe_library_delete_failed', __( 'Library item could not be deleted.', 'ob-engine' ) );
		}
		if ( $item ) {
			$this->log_library_event( Activity_Action::LIBRARY_ITEM_DELETED, $id, $item->to_array() );
		}
		return true;
	}


	public function update_status( int $id, string $status ) {
		if ( self::POST_TYPE !== get_post_type( $id ) ) {
			return new WP_Error( 'obe_library_invalid_item', __( 'Invalid Library item.', 'ob-engine' ) );
		}
		if ( ! Library_Status::is_valid( $status ) ) {
			return new WP_Error( 'obe_library_invalid_status', __( 'Invalid Library status.', 'ob-engine' ) );
		}
		update_post_meta( $id, self::META_STATUS, $status );
		return true;
	}

	public function sanitize_item_data( array $data ): array {
		$type   = isset( $data['type'] ) ? sanitize_key( (string) $data['type'] ) : Library_Type::CONTENT_DRAFT;
		$status = isset( $data['status'] ) ? sanitize_key( (string) $data['status'] ) : Library_Status::default_status();
		return array(
			'title'             => isset( $data['title'] ) ? sanitize_text_field( (string) $data['title'] ) : __( 'Untitled Library Item', 'ob-engine' ),
			'content'           => isset( $data['content'] ) ? wp_kses_post( (string) $data['content'] ) : '',
			'type'              => Library_Type::is_valid( $type ) ? $type : Library_Type::CONTENT_DRAFT,
			'status'            => Library_Status::is_valid( $status ) ? $status : Library_Status::default_status(),
			'source_label'      => isset( $data['source_label'] ) ? sanitize_text_field( (string) $data['source_label'] ) : '',
			'summary'           => isset( $data['summary'] ) ? sanitize_textarea_field( (string) $data['summary'] ) : '',
			'payload_redacted'  => isset( $data['payload_redacted'] ) ? sanitize_textarea_field( (string) $data['payload_redacted'] ) : '',
			'created_context'   => isset( $data['created_context'] ) ? sanitize_key( (string) $data['created_context'] ) : 'manual_admin',
		);
	}

	private function log_library_event( string $action, int $id, array $data ): void {
		$context = array(
			'item_id'      => $id,
			'title'        => isset( $data['title'] ) ? (string) $data['title'] : '',
			'type'         => isset( $data['type'] ) ? (string) $data['type'] : '',
			'status'       => isset( $data['status'] ) ? (string) $data['status'] : '',
			'source_label' => isset( $data['source_label'] ) ? (string) $data['source_label'] : '',
		);

		( new Activity_Logger() )->library_event( $action, $id, isset( $data['title'] ) ? (string) $data['title'] : '', $context );
	}

	private function save_meta( int $id, array $data ): void {
		update_post_meta( $id, self::META_TYPE, $data['type'] );
		update_post_meta( $id, self::META_STATUS, $data['status'] );
		update_post_meta( $id, self::META_SOURCE_LABEL, $data['source_label'] );
		update_post_meta( $id, self::META_SUMMARY, $data['summary'] );
		update_post_meta( $id, self::META_PAYLOAD_REDACTED, $data['payload_redacted'] );
		update_post_meta( $id, self::META_CREATED_CONTEXT, $data['created_context'] );
	}
}
