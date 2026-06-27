<?php
/** Safe Activity logging API. @package OBEngine\Activity */
namespace OBEngine\Activity;
defined( 'ABSPATH' ) || exit;
final class Activity_Logger {
	private $repository;
	public function __construct( ?Activity_Repository $repository = null ) { $this->repository = $repository ?: new Activity_Repository(); }
	public function log( string $action, array $data = array() ) { try { $data['action']=$action; return $this->repository->insert($data); } catch ( \Throwable $e ) { return false; } }
	public function success( string $action, array $data = array() ) { $data['status']=Activity_Status::SUCCESS; return $this->log($action,$data); }
	public function warning( string $action, array $data = array() ) { $data['status']=Activity_Status::WARNING; return $this->log($action,$data); }
	public function failed( string $action, array $data = array() ) { $data['status']=Activity_Status::FAILED; return $this->log($action,$data); }
	public function library_event( string $action, int $library_item_id, string $label = '', array $context = array() ) { return $this->success( $action, array( 'object_type'=>Activity_Object_Type::LIBRARY_ITEM, 'object_id'=>$library_item_id, 'object_label'=>$label, 'message'=>Activity_Action::label($action), 'context'=>$context ) ); }
}
