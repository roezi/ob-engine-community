<?php
/** Activity object type constants. @package OBEngine\Activity */
namespace OBEngine\Activity;
defined( 'ABSPATH' ) || exit;
final class Activity_Object_Type {
	public const SYSTEM='system'; public const SETTINGS='settings'; public const PROVIDER='provider'; public const AI_REQUEST='ai_request'; public const AI_RESPONSE='ai_response'; public const LIBRARY_ITEM='library_item'; public const EDITORIAL_REVISION='editorial_revision'; public const SOURCE_PREVIEW='source_preview'; public const FIELD_MAPPING='field_mapping'; public const WORKFLOW='workflow'; public const AUTO_POST='auto_post'; public const ADDON='addon'; public const USER='user'; public const WORDPRESS_POST='wordpress_post'; public const UNKNOWN='unknown';
	public static function all(): array { return array_keys( self::labels() ); }
	public static function labels(): array { return array( self::SYSTEM=>__( 'System', 'ob-engine' ), self::SETTINGS=>__( 'Settings', 'ob-engine' ), self::PROVIDER=>__( 'Provider', 'ob-engine' ), self::AI_REQUEST=>__( 'AI request', 'ob-engine' ), self::AI_RESPONSE=>__( 'AI response', 'ob-engine' ), self::LIBRARY_ITEM=>__( 'Library item', 'ob-engine' ), self::EDITORIAL_REVISION=>__( 'Editorial revision', 'ob-engine' ), self::SOURCE_PREVIEW=>__( 'Source preview', 'ob-engine' ), self::FIELD_MAPPING=>__( 'Field mapping', 'ob-engine' ), self::WORKFLOW=>__( 'Workflow', 'ob-engine' ), self::AUTO_POST=>__( 'Auto Post', 'ob-engine' ), self::ADDON=>__( 'Addon', 'ob-engine' ), self::USER=>__( 'User', 'ob-engine' ), self::WORDPRESS_POST=>__( 'WordPress post', 'ob-engine' ), self::UNKNOWN=>__( 'Unknown', 'ob-engine' ) ); }
	public static function is_valid( string $object_type ): bool { return in_array( $object_type, self::all(), true ); }
	public static function label( string $object_type ): string { $labels = self::labels(); return $labels[ $object_type ] ?? $labels[ self::UNKNOWN ]; }
}
