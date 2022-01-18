<?php
/**
 * The file registers a plugin’s options page.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.0.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Dashboard
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Dashboard;

use Sixa_Snippets\Includes\Utils as Utils;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Options' ) ) :

	/**
	 * The file that defines the plugin option’s class.
	 */
	class Options {

		/**
		 * Name of the option.
		 *
		 * @since     1.0.0
		 * @access    public
		 * @var       string    $key    Name of the option to retrieve.
		 */
		public static $key = 'sixa_options';

		/**
		 * The slug-name of the settings page.
		 *
		 * @since     1.0.0
		 * @access    public
		 * @var       string    $slug    The slug-name of the settings page on which to show the section.
		 */
		public static $slug = 'sixa-settings';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array    $args    Plugin setting arguments.
		 * @return    void
		 */
		public function __construct( $args = array() ) {
			$labels      = $args['labels'] ?? array();
			$parent_slug = $args['parent_slug'] ?? '';

			$this->register( $labels, $parent_slug );
			$this->fieldset();
		}

		/**
		 * Registers a top or submenu level menu page.
		 *
		 * @since     1.0.0
		 * @param     array    $labels         The text to be displayed in the title and menu.
		 * @param     array    $parent_slug    Optional. The slug name for the parent menu.
		 * @return    void
		 */
		protected function register( $labels, $parent_slug ) {
			$method     = 'add_menu_page';
			$page_title = $labels['page_title'] ?? _x( 'Plugin Options', 'post type', 'sixa-snippets' );
			$menu_title = $labels['menu_title'] ?? _x( 'Sixa Options', 'post type', 'sixa-snippets' );
			$args       = array( $page_title, $menu_title, 'manage_options', sanitize_key( self::$slug ), array( $this, 'render' ), '', 81 );

			if ( ! empty( $parent_slug ) ) {
				array_unshift( $args, $parent_slug );
				$method = 'add_submenu_page';
			}

			call_user_func_array( $method, $args );
		}

		/**
		 * The function to be called to output the content for this page.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		public function render() {
			?>
			<div class="wrap" role="complementary">
				<h2>
					<?php echo wp_kses_post( get_admin_page_title() ); ?>
				</h2>

				<?php settings_errors(); ?>

				<form method="POST" autocomplete="off" action="options.php" enctype="multipart/form-data">
					<?php
					settings_fields( 'sixa_settings_fields' );
					do_settings_sections( self::$slug );
					submit_button();
					?>
				</form>
			</div>
			<?php
		}

		/**
		 * Register plugin page setting and its data.
		 *
		 * @since     1.0.0
		 * @return    void
		 */
		protected function fieldset() {
			register_setting(
				'sixa_settings_fields',
				self::$key,
				array( __CLASS__, 'sanitize' )
			);

			add_settings_section(
				self::$slug,
				'',
				'',
				self::$slug
			);

			do_action( 'sixa_options_fieldset', self::$slug );
		}

		/**
		 * Callback function that sanitizes the option's value.
		 *
		 * @since     1.0.0
		 * @param     array    $fieldset    Plugin setting options.
		 * @return    array
		 */
		public static function sanitize( $fieldset ) {
			return array_map( 'sanitize_text_field', $fieldset );
		}

		/**
		 * Add a new field to a section of a settings page.
		 *
		 * @since     1.0.0
		 * @param     string      $id          Slug-name to identify the field.
		 * @param     string      $title       Formatted title of the field.
		 * @param     callable    $callback    Function that fills the field with the desired form inputs.
		 * @param     string      $page        Optional. The slug-name of the settings page on which to show the section.
		 * @param     string      $section     Optional. The slug-name of the section of the settings page in which to show the box.
		 * @return    void
		 */
		public static function add_field( $id = null, $title = null, $callback = null, $page = null, $section = null ) {
			// Bail early, in case all required arguments are not being provided.
			if ( ! isset( $id, $title, $callback ) ) {
				return;
			}

			if ( ! isset( $page, $section ) ) {
				$page    = self::$slug;
				$section = self::$slug;
			}

			add_settings_field( $id, $title, $callback, $page, $section );
		}

		/**
		 * Implode and escape HTML attributes for output.
		 *
		 * @since     1.0.0
		 * @param     array     $raw_attributes    Attribute name value pairs.
		 * @return    string
		 */
		public static function implode_html_attributes( $raw_attributes ) {
			$attributes = array();
			foreach ( $raw_attributes as $name => $value ) {
				$attributes[] = sprintf( '%s="%s"', esc_attr( $name ), esc_attr( $value ) );
			}

			return implode( ' ', $attributes );
		}

		/**
		 * Output a hidden input box.
		 *
		 * @since     1.0.0
		 * @param     array      $field    Arguments.
		 * @param     boolean    $echo     Optional. Echo the output or return it.
		 * @return    string
		 */
		public static function hidden_field( $field, $echo = true ) {
			$return         = '';
			$field['name']  = $field['name'] ?? $field['id'];
			$field['class'] = $field['class'] ?? '';
			$field['value'] = $field['value'] ?? '';
			$return        .= sprintf( '<input type="hidden" class="%s" name="%s" id="%s" value="%s" />', esc_attr( $field['class'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), esc_attr( $field['value'] ) );

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Output a text input box.
		 *
		 * @since     1.0.0
		 * @param     array      $field    Arguments.
		 * @param     boolean    $echo     Optional. Echo the output or return it.
		 * @return    string
		 */
		public static function text_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = $field['label'] ?? '';
			$field['placeholder']       = $field['placeholder'] ?? '';
			$field['class']             = $field['class'] ?? 'short';
			$field['style']             = $field['style'] ?? '';
			$field['wrapper_class']     = $field['wrapper_class'] ?? '';
			$field['value']             = $field['value'] ?? '';
			$field['name']              = $field['name'] ?? $field['id'];
			$field['type']              = $field['type'] ?? 'text';
			$field['custom_attributes'] = $field['custom_attributes'] ?? array();
			$return                    .= sprintf( '<p class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), wp_kses_post( $field['label'] ) );
			$return                    .= sprintf( '<input type="%s" class="%s" style="%s" name="%s" id="%s" value="%s" placeholder="%s" %s />', esc_attr( $field['type'] ), esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), esc_attr( $field['value'] ), esc_attr( $field['placeholder'] ), self::implode_html_attributes( $field['custom_attributes'] ) );

			if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</p>';

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Output a textarea input box.
		 *
		 * @since     1.0.0
		 * @param     array      $field    Arguments.
		 * @param     boolean    $echo     Optional. Echo the output or return it.
		 * @return    string
		 */
		public static function textarea_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = $field['label'] ?? '';
			$field['placeholder']       = $field['placeholder'] ?? '';
			$field['class']             = $field['class'] ?? 'short';
			$field['style']             = $field['style'] ?? '';
			$field['wrapper_class']     = $field['wrapper_class'] ?? '';
			$field['value']             = $field['value'] ?? '';
			$field['name']              = $field['name'] ?? $field['id'];
			$field['custom_attributes'] = $field['custom_attributes'] ?? array();
			$field['rows']              = $field['rows'] ?? 2;
			$field['cols']              = $field['cols'] ?? 20;
			$return                    .= sprintf( '<p class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), wp_kses_post( $field['label'] ) );
			$return                    .= sprintf( '<textarea class="%s" style="%s" name="%s" id="%s" placeholder="%s" rows="%d" cols="%d" %s>%s</textarea>', esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), esc_attr( $field['placeholder'] ), absint( $field['rows'] ), absint( $field['cols'] ), self::implode_html_attributes( $field['custom_attributes'] ), esc_attr( $field['value'] ) );

			if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</p>';

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Output a checkbox input.
		 *
		 * @since     1.0.0
		 * @param     array      $field    Arguments.
		 * @param     boolean    $echo     Optional. Echo the output or return it.
		 * @return    string
		 */
		public static function checkbox_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = $field['label'] ?? '';
			$field['class']             = $field['class'] ?? 'checkbox';
			$field['style']             = $field['style'] ?? '';
			$field['wrapper_class']     = $field['wrapper_class'] ?? '';
			$field['value']             = $field['value'] ?? 'no';
			$field['name']              = $field['name'] ?? $field['id'];
			$field['custom_attributes'] = $field['custom_attributes'] ?? array();
			$return                    .= sprintf( '<p class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), wp_kses_post( $field['label'] ) );
			$return                    .= sprintf( '<input type="checkbox" class="%s" style="%s" name="%s" id="%s" value="yes" %s %s />', esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), checked( 'yes', esc_attr( $field['value'] ), false ), self::implode_html_attributes( $field['custom_attributes'] ) );

			if ( ! empty( $field['description'] ) ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</p>';

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Output radio inputs.
		 *
		 * @since     1.0.0
		 * @param     array      $field    Arguments.
		 * @param     boolean    $echo     Optional. Echo the output or return it.
		 * @return    string
		 */
		public static function select_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = $field['label'] ?? '';
			$field['class']             = $field['class'] ?? 'select short';
			$field['style']             = $field['style'] ?? '';
			$field['wrapper_class']     = $field['wrapper_class'] ?? '';
			$field['value']             = $field['value'] ?? '';
			$field['name']              = $field['name'] ?? $field['id'];
			$field['show_option_none']  = isset( $field['show_option_none'] ) ? true : false;
			$field['custom_attributes'] = $field['custom_attributes'] ?? array();
			$return                    .= sprintf( '<p class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), wp_kses_post( $field['label'] ) );
			$return                    .= sprintf( '<select class="%s" style="%s" name="%s" id="%s" %s>', esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), self::implode_html_attributes( $field['custom_attributes'] ) );

			if ( ! ! $field['show_option_none'] ) {
				/* translators: 1: Open option tag, 2: Close option tag. */
				$return .= sprintf( _x( '%1$s&mdash; Select &mdash;%2$s', 'showing no pages', 'sixa-snippets' ), '<option value="">', '</option>' );
			}

			foreach ( $field['options'] as $key => $value ) {
				$return .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( esc_attr( $key ), esc_attr( $field['value'] ), false ), esc_html( $value ) );
			}

			$return .= '</select>';

			if ( ! empty( $field['description'] ) ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</p>';

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Output radio inputs.
		 *
		 * @since     1.0.0
		 * @param     array      $field    Arguments.
		 * @param     boolean    $echo     Optional. Echo the output or return it.
		 * @return    string
		 */
		public static function radio_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = $field['label'] ?? '';
			$field['class']             = $field['class'] ?? 'radio';
			$field['style']             = $field['style'] ?? '';
			$field['wrapper_class']     = $field['wrapper_class'] ?? '';
			$field['value']             = $field['value'] ?? '';
			$field['name']              = $field['name'] ?? $field['id'];
			$field['custom_attributes'] = $field['custom_attributes'] ?? array();
			$return                    .= sprintf( '<fieldset class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<legend>%s</legend><ul id="%s" %s>', wp_kses_post( $field['label'] ), esc_attr( $field['id'] ), self::implode_html_attributes( $field['custom_attributes'] ) );

			foreach ( $field['choices'] as $key => $value ) {
				$return .= sprintf( '<li><label><input type="radio" class="%s" style="%s" name="%s" value="%s" %s />%s</label></li>', esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $key ), checked( esc_attr( $key ), esc_attr( $field['value'] ), false ), esc_html( $value ) );
			}

			$return .= '</ul>';

			if ( ! empty( $field['description'] ) ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</fieldset>';

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

		/**
		 * Output media upload inputs.
		 *
		 * @since     1.0.0
		 * @param     array      $field    Arguments.
		 * @param     boolean    $echo     Optional. Echo the output or return it.
		 * @return    string
		 */
		public static function media_field( $field, $echo = true ) {
			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}

			$return                     = '';
			$field['label']             = $field['label'] ?? esc_html__( 'Set thumbnail', 'sixa-snippets' );
			$field['class']             = $field['class'] ?? 'media-upload';
			$field['style']             = $field['style'] ?? '';
			$field['wrapper_class']     = $field['wrapper_class'] ?? '';
			$field['value']             = $field['value'] ?? '';
			$field['name']              = $field['name'] ?? $field['id'];
			$field['custom_attributes'] = $field['custom_attributes'] ?? array();
			$field['unique_id']         = wp_unique_id( $field['id'] );
			$return                    .= sprintf( '<p class="form-field %s_field %s" id="%s" style="%s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ), esc_attr( $field['unique_id'] ), esc_attr( $field['style'] ) );
			$return                    .= '<img src="' . wp_get_attachment_image_url( $field['value'], 'thumbnail', false ) . '" class="attachment-thumbnail" alt="' . trim( wp_strip_all_tags( get_post_meta( $field['value'], '_wp_attachment_image_alt', true ) ) ) . '" width="100" height="100" />';
			$return                    .= '<a href="#" class="button button-upload">' . wp_kses_post( $field['label'] ) . '</a>';
			$return                    .= '<a href="#" class="button-link-delete" style="display:block">' . esc_html__( 'Remove thumbnail', 'sixa-snippets' ) . '</a>';
			$return                    .= sprintf( '<input type="hidden" class="%s" name="%s" id="%s" value="%s" %s />', esc_attr( $field['class'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), esc_attr( $field['value'] ), self::implode_html_attributes( $field['custom_attributes'] ) );

			if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</p>';
			$return .= Utils::output_inline_style(
				'
				#' . $field['unique_id'] . ' img:not([src^="http"]){display:none}
				#' . $field['unique_id'] . ' img:not([src^="http"]) + .button + a{display:none!important}
				#' . $field['unique_id'] . ' img[src^="http"] + .button{display:none}
			'
			);
			$return .= Utils::output_inline_js(
				'
				var uploadButton = document.querySelector( "#' . $field['unique_id'] . ' .button" );
				var removeButton = document.querySelector( "#' . $field['unique_id'] . ' .button-link-delete" );
				uploadButton.addEventListener( "click", ( event ) => {
					event.preventDefault();
					const custom_uploader = wp.media( {
						button: {
							text: "' . __( 'Use this image', 'sixa-snippets' ) . '"
						},
						library : {
							type : "image"
						},
						multiple: false,
						title: "' . __( 'Insert image', 'sixa-snippets' ) . '"
					} ).on( "select", function() {
						var attachment = custom_uploader.state().get("selection").first().toJSON();
						var image = document.querySelector( "#' . $field['unique_id'] . ' img" );
						var input = document.querySelector( "#' . $field['unique_id'] . ' input" );
						image.src = attachment.url;
						input.value = attachment.id;
					} ).open();
				} );
				removeButton.addEventListener( "click", ( event ) => {
					event.preventDefault();
					var image = document.querySelector( "#' . $field['unique_id'] . ' img" );
					var input = document.querySelector( "#' . $field['unique_id'] . ' input" );
					image.src = "";
					input.value = "";
				} );
			'
			);

			if ( $echo ) {
				echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			return $return;
		}

	}
endif;
