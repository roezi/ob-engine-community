<?php
/** Editorial tone presets. */
namespace OBEngine\Editorial;
defined( 'ABSPATH' ) || exit;
final class Editorial_Tone {
	public const NATURAL='natural'; public const CONCISE='concise'; public const EDITORIAL='editorial'; public const BAHASA_INDONESIA='bahasa_indonesia'; public const TOURISM_EDITORIAL='tourism_editorial'; public const SEO_READABLE='seo_readable'; public const BRAND_SAFE='brand_safe';
	public static function all(): array { return array_keys( self::labels() ); }
	public static function labels(): array { return array( self::NATURAL=>__( 'Natural', 'ob-engine' ), self::CONCISE=>__( 'Concise', 'ob-engine' ), self::EDITORIAL=>__( 'Editorial', 'ob-engine' ), self::BAHASA_INDONESIA=>__( 'Bahasa Indonesia', 'ob-engine' ), self::TOURISM_EDITORIAL=>__( 'Tourism editorial', 'ob-engine' ), self::SEO_READABLE=>__( 'SEO readable', 'ob-engine' ), self::BRAND_SAFE=>__( 'Brand safe', 'ob-engine' ) ); }
	public static function descriptions(): array { return array( self::NATURAL=>__( 'Improve natural flow while preserving meaning.', 'ob-engine' ), self::CONCISE=>__( 'Tighten wording and remove repetition.', 'ob-engine' ), self::EDITORIAL=>__( 'Improve structure, clarity, and editorial polish.', 'ob-engine' ), self::BAHASA_INDONESIA=>__( 'Improve Bahasa Indonesia naturalness and localization.', 'ob-engine' ), self::TOURISM_EDITORIAL=>__( 'Use a clear destination/editorial style for tourism content.', 'ob-engine' ), self::SEO_READABLE=>__( 'Improve readability for search users without keyword stuffing.', 'ob-engine' ), self::BRAND_SAFE=>__( 'Keep language consistent, clear, and safety-conscious.', 'ob-engine' ) ); }
	public static function is_valid( string $tone ): bool { return in_array( $tone, self::all(), true ); }
	public static function label( string $tone ): string { $labels = self::labels(); return $labels[ $tone ] ?? $labels[ self::NATURAL ]; }
	public static function description( string $tone ): string { $descriptions = self::descriptions(); return $descriptions[ $tone ] ?? $descriptions[ self::NATURAL ]; }
}
