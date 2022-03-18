<?php
/**
 * Helper functions.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.7.1
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Includes
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Includes;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! class_exists( Utils::class ) ) :

	/**
	 * Utils Class.
	 */
	final class Utils {

		/**
		 * "Templates" folder name.
		 *
		 * @since    1.7.0
		 * @var      string
		 */
		public const TEMPLATES_FOLDER = 'templates';

		/**
		 * Query a third-party plugin activation.
		 * This statement prevents from producing fatal errors,
		 * in case the the plugin is not activated on the site.
		 *
		 * @since     1.7.0
		 * @param     string $slug        Plugin slug to check for the activation state.
		 * @param     string $filename    Optional. Plugin’s main file name.
		 * @return    bool
		 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
		 */
		public static function is_plugin_activated( string $slug, string $filename = '' ): bool {
			$filename               = empty( $filename ) ? $slug : $filename;
			$plugin_path            = apply_filters( 'sixa_third_party_plugin_path', sprintf( '%s/%s.php', esc_html( $slug ), esc_html( $filename ) ) );
			$subsite_active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
			$network_active_plugins = apply_filters( 'active_plugins', get_site_option( 'active_sitewide_plugins' ) );

			// Bail early in case the plugin is not activated on the website.
			// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			if ( ( empty( $subsite_active_plugins ) || ! in_array( $plugin_path, $subsite_active_plugins ) ) && ( empty( $network_active_plugins ) || ! array_key_exists( $plugin_path, $network_active_plugins ) ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Return `true` if "WooCommerce" is installed/activated and `false` otherwise.
		 *
		 * @since     1.7.0
		 * @return    bool
		 */
		public static function is_woocommerce_activated(): bool {
			return self::is_plugin_activated( 'woocommerce' );
		}

		/**
		 * Return `true` if "Polylang" is installed/activated and `false` otherwise.
		 *
		 * @since     1.7.1
		 * @return    bool
		 */
		public static function is_polylang_activated(): bool {
			return self::is_plugin_activated( 'polylang' ) || self::is_plugin_activated( 'polylang-pro', 'polylang' );
		}

		/**
		 * Determines if a post, identified by the specified ID,
		 * exist within the WordPress database.
		 *
		 * @since     1.7.0
		 * @param     null|string $post_id    Post ID.
		 * @return    bool
		 */
		public static function is_post_exists( ?string $post_id = '' ): bool {
			return ! empty( $post_id ) && is_string( get_post_type( $post_id ) );
		}

		/**
		 * Retrieves post id of given post-object or currently queried object id.
		 *
		 * @since     1.7.0
		 * @param     int|WP_Post|null $post    Post ID or post object.
		 * @return    int
		 */
		public static function get_post_id( $post = null ): ?int {
			$post_id  = null;
			$get_post = get_post( $post, 'OBJECT' );

			if ( is_null( $get_post ) ) {
				$post_id = (int) get_queried_object_id();
			} elseif ( property_exists( $get_post, 'ID' ) ) {
				$post_id = (int) $get_post->ID;
			}

			return $post_id;
		}

		/**
		 * Post id of the translation if exists, null otherwise.
		 *
		 * @since     1.7.0
		 * @param     string $post_id    The return format, 'input', 'string', or 'array'.
		 * @return    null|string
		 */
		public static function get_localized_post_id( ?string $post_id = '' ): ?string {
			$return = null;

			if ( self::is_post_exists( $post_id ) ) {
				$return = $post_id;
				if ( self::is_polylang_activated() ) {
					$pll_post_id = pll_get_post( $post_id );
					if ( $pll_page_id && ! is_null( $pll_page_id ) ) {
						$return = $pll_post_id;
					}
				}
			}

			return $return;
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
				'/&fnof;/i',                  // Function sign.
				'/&yen;/i',                   // Yen sign.
				'/&cent;/i',                  // Cent sign.
				'/&curren;/i',                // Currency sign.
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
				'&#x192;',    // Function sign.
				'&#xa5;',     // Yen sign.
				'&#xa2;',     // Cent sign.
				'&#xa4;',     // Currency sign.
				'',           // Unknown/unhandled entities.
				'&amp;',      // Add ampersand back.
				' ',          // Runs of spaces, post-handling.
			);

			return preg_replace( $plain_search, $plain_replace, $input );
		}

		/**
		 * Output, and sanitize any queued inline JS.
		 *
		 * @since     1.5.0
		 * @param     string  $inline_js    JavaScript code to be escaped and outputted.
		 * @param     boolean $echo         Optional. Echo the output or return it.
		 * @return    string
		 */
		public static function output_inline_js( string $inline_js, bool $echo = false ): string {
			$return    = "\n<script type=\"text/javascript\">\n";
			$inline_js = wp_check_invalid_utf8( $inline_js );
			$inline_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $inline_js );
			$inline_js = str_replace( "\r", '', $inline_js );
			$return   .= $inline_js;
			$return   .= "\n</script>\n";

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Output, and sanitize any queued inline CSS styles.
		 *
		 * @since     1.5.0
		 * @param     string  $inline_style    CSS style code to be escaped and outputted.
		 * @param     boolean $echo            Optional. Echo the output or return it.
		 * @return    string
		 */
		public static function output_inline_style( string $inline_style, bool $echo = false ): string {
			$return = "\n<style type=\"text/css\">\n";
			/**
			 * List of preg* regular expression patterns to search for,
			 * used in conjunction with $plain_replace.
			 *
			 * @see    https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
			 */
			$plain_search = array(
				'/\s+/',                                            // Normalize whitespace.
				'/(\s+)(\/\*(.*?)\*\/)(\s+)/',                      // Remove spaces before and after comment.
				'~/\*(?![\!|\*])(.*?)\*/~',                         // Remove comment blocks, everything between /* and */, unless preserved with /*! ... */ or /** ... */.
				'/;(?=\s*})/',                                      // Remove ; before }.
				'/(,|:|;|\{|}|\*\/|>) /',                           // Remove space after , : ; { } */ >.
				'/ (,|;|\{|}|\)|>)/',                               // Remove space before , ; { } ( ) >.
				'/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i',   // Strips leading 0 on decimal values (converts 0.5px into .5px).
				'/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i',        // Strips units if value is 0 (converts 0px to 0).
				'/0 0 0 0/',                                        // Converts all zeros value into short-hand.
				'/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i',      // Shorten 6-character hex color codes to 3-character where possible.
				'#(?<=[\{;])(border|outline):none(?=[;\}\!])#',      // Replace `(border|outline):none` with `(border|outline):0`.
				'#(background-position):0(?=[;\}])#si',
			);
			/**
			 * List of pattern replacements corresponding to patterns searched.
			 */
			$plain_replace = array(
				' ',
				'$2',
				'',
				'',
				'$1',
				'$1',
				'${1}.${2}${3}',
				'${1}0',
				'0',
				'#\1\2\3',
				'$1:0',
				'$1:0 0',
			);
			$return       .= preg_replace( $plain_search, $plain_replace, $inline_style );
			$return       .= $inline_style;
			$return       .= "\n</style>\n";

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Call a shortcode function by tag name.
		 *
		 * @since     1.7.0
		 * @param     string $tag        The shortcode whose function to call.
		 * @param     array  $atts       Optional. The attributes to pass to the shortcode function. Optional.
		 * @param     string $content    Optional. The shortcode's content. Default is null (none).
		 * @return    string|null
		 */
		public static function do_shortcode( string $tag, array $atts = array(), ?string $content = null ): ?string {
			global $shortcode_tags;

			if ( ! isset( $shortcode_tags[ $tag ] ) ) {
				return null;
			}

			return call_user_func( $shortcode_tags[ $tag ], $atts, $content, $tag );
		}

		/**
		 * Titlifies every slug given to a human-friendly title string.
		 *
		 * @since     1.7.0
		 * @param     string $input        The value to titlify.
		 * @param     string $delimiter    Optional. The delimiter to be replaced with.
		 * @return    string
		 */
		public static function titlify( string $input, string $delimiter = ' ' ): string {
			$input = preg_replace( '/[\']/', '', iconv( 'UTF-8', 'ASCII//TRANSLIT', $input ) );
			$input = preg_replace( '/[&]/', 'and', $input );
			$input = preg_replace( '/[^A-Za-z0-9-]+/', $delimiter, $input );
			$input = preg_replace( '/[\s-]+/', $delimiter, $input );
			$input = trim( $input, $delimiter );
			$input = ucwords( $input );

			return $input;
		}

		/**
		 * Slugifies every string, even when it contains unicode!
		 *
		 * @since     1.7.0
		 * @param     string $input    The value to slugify.
		 * @return    string
		 */
		public static function slugify( string $input ): string {
			$input = preg_replace( '~[^\pL\d]+~u', '-', $input );
			$input = iconv( 'utf-8', 'us-ascii//TRANSLIT', $input );
			$input = preg_replace( '~[^-\w]+~', '', $input );
			$input = trim( $input, '-' );
			$input = preg_replace( '~-+~', '-', $input );
			$input = strtolower( $input );

			if ( empty( $input ) ) {
				return 'n-a';
			}

			return $input;
		}

		/**
		 * Underlinifies every string given.
		 *
		 * @since     1.7.0
		 * @param     string $input    Given string or filename.
		 * @return    string
		 */
		public static function underlinify( string $input ): string {
			return preg_replace( '/-/', '_', self::slugify( $input ) );
		}

		/**
		 * Implode and escape HTML attributes for output.
		 *
		 * @since     1.7.0
		 * @param     array $raw_attributes    Attribute name value pairs.
		 * @return    string
		 */
		public static function implode_html_attributes( array $raw_attributes ): string {
			$attributes = array();
			foreach ( $raw_attributes as $name => $value ) {
				$attributes[] = esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
			}
			return implode( ' ', $attributes );
		}

		/**
		 * Converts a string (e.g. 'yes' or 'no') to a bool.
		 *
		 * @since     1.7.0
		 * @param     string|bool $string    String to convert. If a bool is passed it will be returned as-is.
		 * @return    bool
		 */
		public static function string_to_bool( $string ): bool {
			return is_bool( $string ) ? $string : ( 'yes' === strtolower( $string ) || 1 === $string || 'true' === strtolower( $string ) || '1' === $string );
		}

		/**
		 * Converts a bool to a 'yes' or 'no'.
		 *
		 * @since     1.7.0
		 * @param     bool|string $bool    Bool to convert. If a string is passed it will first be converted to a bool.
		 * @return    string
		 */
		public static function bool_to_string( $bool ): string {
			if ( ! is_bool( $bool ) ) {
				$bool = self::string_to_bool( $bool );
			}
			return true === $bool ? 'yes' : 'no';
		}

		/**
		 * Clean variables using sanitize_text_field. Arrays are cleaned recursively.
		 * Non-scalar values are ignored.
		 *
		 * @since     1.7.0
		 * @param     string|array $input    Data to sanitize.
		 * @return    string|array
		 */
		public static function clean( $input ) {
			if ( is_array( $input ) ) {
				return array_map( 'self::clean', $input );
			} else {
				return is_scalar( $input ) ? sanitize_text_field( $input ) : $input;
			}
		}

		/**
		 * Recursive sanitation for an array.
		 * Returns the sanitized values of an array.
		 *
		 * @since     1.7.0
		 * @param     array $input    Array of values.
		 * @return    array
		 */
		public static function clean_array( array $input ): array {
			// Bail early, in case the input value is missing or not an array.
			if ( empty( $input ) || ! is_array( $input ) ) {
				return array();
			}
			// Loop through the array to sanitize each key/values recursively.
			foreach ( $input as $key => &$value ) {
				if ( is_array( $value ) ) {
					$value = self::clean_array( $value );
				} else {
					$value = self::clean( $value );
				}
			}

			return $input;
		}

		/**
		 * Sanitize multiple HTML classes in one pass.
		 *
		 * @since     1.7.0
		 * @param     array  $classes          Classes to be sanitized.
		 * @param     string $return_format    The return format, 'input', 'string', or 'array'.
		 * @return    array|string
		 */
		public static function sanitize_html_classes( array $classes, string $return_format = 'input' ) {
			if ( 'input' === $return_format ) {
				$return_format = is_array( $classes ) ? 'array' : 'string';
			}

			$classes           = is_array( $classes ) ? $classes : explode( ' ', $classes );
			$sanitized_classes = array_map( 'sanitize_html_class', $classes );

			if ( 'array' === $return_format ) {
				return $sanitized_classes;
			} else {
				return implode( ' ', $sanitized_classes );
			}
		}

		/**
		 * Returns a list of Columns (breakpoint) CSS class names along with
		 * the inline CSS style for the gap range defined viewport-wide.
		 *
		 * @since     1.7.0
		 * @param     array $attributes    Available block attributes and their corresponding values.
		 * @return    array
		 */
		public static function get_block_wrapper_columns_attributes( array $attributes ) {
			$classnames = array( 'sixa-columns' );
			$columns    = $attributes['columns'] ?? '';
			$gap        = $attributes['gap'] ?? '0px';

			if ( $gap ) {
				$styles[] = sprintf( '--columns-gap: %s', $gap );
			}

			if ( is_array( $columns ) ) {
				foreach ( $columns as $device => $column ) {
					$classnames[] = sprintf( 'sixa-columns-%s-%s', $column, $device );
				}
			}

			$wrapper_attributes = array(
				'class' => implode( ' ', array_map( 'sanitize_html_class', $classnames ) ),
				'style' => implode( ';', array_map( 'esc_attr', $styles ) ),
			);

			return apply_filters( 'sixa_block_wrapper_columns_attributes', $wrapper_attributes );
		}

		/**
		 * Returns the template file name without extension being added to it.
		 *
		 * @since     1.7.0
		 * @param     string $file    Template file name (filename).
		 * @return    string
		 */
		public static function get_template_filename( string $file ): string {
			return preg_replace( '/\\.[^.\\s]{3,4}$/', '', $file );
		}

		/**
		 * Returns the template file directory and relative file path.
		 *
		 * @since     1.7.0
		 * @param     string $directory    Parent directory's path.
		 * @param     string $file         File path.
		 * @return    string
		 */
		public static function get_template_path( string $directory, string $file ): string {
			return sprintf( untrailingslashit( $directory ) . '/' . self::TEMPLATES_FOLDER . '/' . self::get_template_filename( $file ) . '.php' );
		}

		/**
		 * Returns the HTML template instead of outputting.
		 *
		 * @since     1.7.0
		 * @param     string $directory        Parent directory's path.
		 * @param     string $template_name    Template name.
		 * @param     array  $args             Arguments. (default: array).
		 * @return    string
		 */
		public static function get_template_html( string $directory, string $template_name, array $args = array() ): string {
			ob_start();
			load_template( untrailingslashit( $directory ) . '/' . self::TEMPLATES_FOLDER . '/' . $template_name . '.php', false, $args );
			return ob_get_clean();
		}

		/**
		 * Like `get_template_part()` put lets you pass args to the template file.
		 * Args are available in the template as `$template_args` array.
		 *
		 * @since     1.7.0
		 * @param     string $directory        Parent directory's path.
		 * @param     string $file             File path.
		 * @param     array  $template_args    Args which are to be passed to the template file.
		 * @param     array  $cache_args       The args to store in the cache.
		 * @return    string|null
		 * @phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
		 */
		public static function get_template_part( string $directory, string $file, array $template_args = array(), array $cache_args = array() ): ?string {
			$template_args = wp_parse_args( $template_args );
			$cache_args    = wp_parse_args( $cache_args );

			if ( $cache_args ) {
				foreach ( $template_args as $key => $value ) {
					if ( is_scalar( $value ) || is_array( $value ) ) {
						$cache_args[ $key ] = $value;
					} elseif ( is_object( $value ) && method_exists( $value, 'get_id' ) ) {
						$cache_args[ $key ] = call_user_func( 'get_id', $value );
					}
				}

				// Retrieves the cache contents from the cache by key and group.
				$cache = wp_cache_get( $file, maybe_serialize( $cache_args ) ); // Serialize data, if needed.

				if ( false !== $cache ) {
					if ( ! empty( $template_args['return'] ) ) {
						return $cache;
					}

					echo $cache;
					return null;
				}
			}

			$file = self::get_template_path( $directory, $file );

			ob_start();
			$return = require $file;
			$data   = ob_get_clean();

			if ( $cache_args ) {
				// Saves the data to the cache.
				wp_cache_set( $file, $data, maybe_serialize( $cache_args ), 3600 ); // Serialize data, if needed.
			}

			if ( ! empty( $template_args['return'] ) ) {
				if ( false === $return ) {
					return null;
				} else {
					return $data;
				}
			}

			echo $data;
			return null;
		}
	}

endif;
