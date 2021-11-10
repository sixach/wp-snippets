<?php
/**
 * Helper functions.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.4.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Includes
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Includes;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( Utils::class ) ) :

	/**
	 * Utils Class.
	 */
	final class Utils {

		/**
		 * Return `true` if WooCommerce is installed and `false` otherwise.
		 *
		 * @since     1.3.0
		 * @return    bool
		 */
		public static function is_woocommerce_activated(): bool {
			// This statement prevents from producing fatal errors,
			// in case the WooCommerce plugin is not activated on the site.
			$woocommerce_plugin     = apply_filters( 'sixa_woocommerce_path', 'woocommerce/woocommerce.php' );
			$subsite_active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			$network_active_plugins = apply_filters( 'active_plugins', get_site_option( 'active_sitewide_plugins' ) );

			// Bail early in case the plugin is not activated on the website.
			if ( ( empty( $subsite_active_plugins ) || ! in_array( $woocommerce_plugin, $subsite_active_plugins, true ) ) && ( empty( $network_active_plugins ) || ! array_key_exists( $woocommerce_plugin, $network_active_plugins ) ) ) {
				return false;
			}

			return true;
		}

		/**
		 * This function normalizes HTML entities.
		 * Perform a regular expression to convert all HTML entities to their named counterparts.
		 *
		 * @since     1.4.1
		 * @param     string $input    Given input string, text or HTML markup.
		 * @return    string
		 */
		public static function normalize_character_entities( string $input ): string {
			/**
			 * List of preg* regular expression patterns to search for,
			 * used in conjunction with $plain_replace.
			 *
			 * @see    https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
			 */
			$plain_search = array(
				"/\r/",                                                     // Non-legal carriage return.
				'/&(nbsp|#0*160);/i',                                       // Non-breaking space.
				'/&(quot|rdquo|ldquo|#0*8220|#0*8221|#0*147|#0*148);/i',    // Double quotes.
				'/&(apos|rsquo|lsquo|#0*8216|#0*8217);/i',                  // Single quotes.
				'/&gt;/i',                                                  // Greater-than.
				'/&lt;/i',                                                  // Less-than.
				'/&#0*38;/i',                                               // Ampersand.
				'/&amp;/i',                                                 // Ampersand.
				'/&(copy|#0*169);/i',                                       // Copyright.
				'/&(trade|#0*8482|#0*153);/i',                              // Trademark.
				'/&(reg|#0*174);/i',                                        // Registered.
				'/&(mdash|#0*151|#0*8212);/i',                              // mdash.
				'/&(ndash|minus|#0*8211|#0*8722);/i',                       // ndash.
				'/&(bull|#0*149|#0*8226);/i',                               // Bullet.
				'/&(pound|#0*163);/i',                                      // Pound sign.
				'/&(euro|#0*8364);/i',                                      // Euro sign.
				'/&(dollar|#0*36);/i',                                      // Dollar sign.
				'/&[^&\s;]+;/i',                                            // Unknown/unhandled entities.
				'/[ ]{2,}/',                                                // Runs of spaces, post-handling.
			);
			/**
			 * List of pattern replacements corresponding to patterns searched.
			 */
			$plain_replace = array(
				'',        // Non-legal carriage return.
				' ',       // Non-breaking space.
				'"',       // Double quotes.
				"'",       // Single quotes.
				'>',       // Greater-than.
				'<',       // Less-than.
				'&',       // Ampersand.
				'&',       // Ampersand.
				'(c)',     // Copyright.
				'(tm)',    // Trademark.
				'(R)',     // Registered.
				'--',      // mdash.
				'-',       // ndash.
				'*',       // Bullet.
				'£',       // Pound sign.
				'€',       // Euro sign. € ?.
				'$',       // Dollar sign.
				'',        // Unknown/unhandled entities.
				' ',       // Runs of spaces, post-handling.
			);

			return preg_replace( $plain_search, $plain_replace, $input );
		}

		/**
		 * This function normalizes HTML entities for use in XML.
		 * Perform a regular expression to convert all HTML entities to their symbols
		 * or hex encoding.
		 *
		 * Note that this function keeps `&amp;` as a named entity.
		 * This is because `&amp;` is valid in XML and `&` is invalid.
		 *
		 * Since we are also cleaning up all unknown entities during clean up,
		 * we have to store `&amp;` as a temporary value. For this we have
		 * chosen `&#MYAMP;` but this could be any arbitrary value that passes the clean up.
		 *
		 * Further we are not replacing hex encoded entities. These are all valid in XML.
		 *
		 * Also note that the list of named entities is far from complete and could be
		 * extended in the future.
		 *
		 * @since     1.4.2
		 * @param     string $input    Given input string, text or HTML markup.
		 * @return    string
		 */
		public static function normalize_xml_character_entities( string $input ): string {
			/**
			 * List of regular expressions to search for named HTML entities.
			 * XML only knows quot, amp, apos, lt, and gt as named entities.
			 * Every other named entity must be replaced by its symbol or hex
			 * encoded counterpart (or removed from the content).
			 *
			 * @see    https://en.wikipedia.org/wiki/List_of_XML_and_HTML_character_entity_references#Predefined_entities_in_XML
			 */
			$plain_search = array(
				"/\r/",                       // Non-legal carriage return.
				'/&nbsp;/i',                  // Non-breaking space.
				'/&(quot|rdquo|ldquo);/i',    // Double quotes.
				'/&(apos|rsquo|lsquo);/i',    // Single quotes.
				'/&gt;/i',                    // Greater-than.
				'/&lt;/i',                    // Less-than.
				'/&amp;/i',                   // Ampersand.
				'/&copy;/i',                  // Copyright.
				'/&trade;/i',                 // Trademark.
				'/&reg;/i',                   // Registered.
				'/&mdash;/i',                 // mdash.
				'/&(ndash|minus);/i',         // ndash.
				'/&bull;/i',                  // Bullet.
				'/&pound;/i',                 // Pound sign.
				'/&euro;/i',                  // Euro sign.
				'/&dollar;/i',                // Dollar sign.
				'/&[a-z]+;/i',                // Unknown/unhandled entities.
				'/&#MYAMP;/i',                // Add ampersand back.
				'/[ ]{2,}/',                  // Runs of spaces, post-handling.
			);
			/**
			 * List of pattern replacements corresponding to patterns searched.
			 */
			$plain_replace = array(
				'',           // Non-legal carriage return.
				' ',          // Non-breaking space.
				'"',          // Double quotes.
				"'",          // Single quotes.
				'>',          // Greater-than.
				'<',          // Less-than.
				'&#MYAMP;',   // Ampersand.
				'&#169;',     // Copyright.
				'&#8482;',    // Trademark.
				'&#174;',     // Registered.
				'--',         // mdash.
				'-',          // ndash.
				'*',          // Bullet.
				'£',          // Pound sign.
				'€',          // Euro sign. € ?.
				'$',          // Dollar sign.
				'',           // Unknown/unhandled entities.
				'&amp;',      // Add ampersand back.
				' ',          // Runs of spaces, post-handling.
			);

			return preg_replace( $plain_search, $plain_replace, $input );
		}

	}

endif;
