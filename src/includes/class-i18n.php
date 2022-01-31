<?php
/**
 * I18n functions.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.6.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Includes
 */

namespace Sixa_Snippets\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( I18n::class ) ) :

	/**
	 * I18n Class.
	 */
	final class I18n {

		/**
		 * Return the translation file from this package given a locale string.
		 * Note that the file with given locale may not exist.
		 *
		 * @since     1.6.0
		 * @param     string $locale    A locale string, e.g. `de_CH`.
		 * @return    string               The full path to a translation file with the given locale.
		 */
		public static function get_package_language_file( string $locale ): string {
			return sprintf( '%s/languages/%s.mo', untrailingslashit( dirname( __FILE__, 3 ) ), $locale );
		}

		/**
		 * Load the textdomain with given locale from this package.
		 * Call this function in your theme or in your plugin to load the translations
		 * from WP Snippets.
		 *
		 * @since     1.6.0
		 * @param     null|string $locale    A locale string, e.g. `de_CH`. Defaults to the current locale.
		 * @return    void
		 */
		public static function load_package_textdomain( ?string $locale = null ): void {
			$locale = $locale ?? determine_locale();
			load_textdomain( 'sixa-snippets', self::get_package_language_file( $locale ) );
		}
	}

endif;
