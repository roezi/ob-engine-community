<?php
/** Dry-run meta repository. @package OBEngine\Safety */
namespace OBEngine\Safety;
use OBEngine\Library\Library_Repository;
use WP_Error;
defined( 'ABSPATH' ) || exit;
final class Dry_Run_Repository {
	public const META_STATUS = '_obe_dry_run_status'; public const META_SUMMARY = '_obe_dry_run_summary'; public const META_TARGET_POST_TYPE = '_obe_dry_run_target_post_type'; public const META_TARGET_STATUS = '_obe_dry_run_target_status'; public const META_RESULT_REDACTED = '_obe_dry_run_result_redacted'; public const META_ACTOR_ID = '_obe_dry_run_actor_id'; public const META_CREATED_AT = '_obe_dry_run_created_at'; public const META_UPDATED_AT = '_obe_dry_run_updated_at';
	public function get( int $library_item_id ): Dry_Run_Result { if ( ! $this->is_library_item( $library_item_id ) ) { return Dry_Run_Result::empty_for_item( $library_item_id ); } return Dry_Run_Result::from_meta( $library_item_id, get_post_meta( $library_item_id ) ); }
	public function save( int $library_item_id, Dry_Run_Result $result ) { if ( ! $this->is_library_item( $library_item_id ) ) { return new WP_Error( 'obe_dry_run_invalid_item', __( 'Invalid Library item.', 'ob-engine' ) ); } $data = $this->sanitize_result_data( $result->to_array() ); update_post_meta( $library_item_id, self::META_STATUS, $data['status'] ); update_post_meta( $library_item_id, self::META_SUMMARY, $data['summary'] ); update_post_meta( $library_item_id, self::META_TARGET_POST_TYPE, $data['target']['post_type'] ); update_post_meta( $library_item_id, self::META_TARGET_STATUS, $data['target']['post_status'] ); update_post_meta( $library_item_id, self::META_RESULT_REDACTED, wp_json_encode( $data['result_redacted'] ) ); update_post_meta( $library_item_id, self::META_ACTOR_ID, $data['actor_id'] ); update_post_meta( $library_item_id, self::META_CREATED_AT, $data['created_at'] ); update_post_meta( $library_item_id, self::META_UPDATED_AT, $data['updated_at'] ); return true; }
	public function clear( int $library_item_id ) { if ( ! $this->is_library_item( $library_item_id ) ) { return new WP_Error( 'obe_dry_run_invalid_item', __( 'Invalid Library item.', 'ob-engine' ) ); } foreach ( array( self::META_STATUS, self::META_SUMMARY, self::META_TARGET_POST_TYPE, self::META_TARGET_STATUS, self::META_RESULT_REDACTED, self::META_ACTOR_ID, self::META_CREATED_AT, self::META_UPDATED_AT ) as $key ) { delete_post_meta( $library_item_id, $key ); } return true; }
	public function sanitize_result_data( array $data ): array { return ( new Dry_Run_Result( $data ) )->to_array(); }
	private function is_library_item( int $library_item_id ): bool { return $library_item_id > 0 && Library_Repository::POST_TYPE === get_post_type( $library_item_id ) && 'private' === get_post_status( $library_item_id ); }
}
