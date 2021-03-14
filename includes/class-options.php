<?php
/**
 * The file registers a plugin’s options page.
 *
 * @link       https://sixa.ch
 * @author     Mahdi Yazdani
 * @since      1.0.0
 *
 * @package    sixa-snippets
 * @subpackage sixa-snippets/includes
 */

namespace SixaSnippets\Includes;

/**
 * INSTRUCTIONS:
 *
 * 1. Update the namespace used above.
 * 2. Search and replace text-domains `@@textdomain`.
 * 3. Initialize the class to register plugin settings page when needed:
 *
 * add_action( 'admin_menu', function() { new Options(); } );
 *
 * Note: Do not initialize this class before the `admin_menu` hook.
 *
 * Snippet below will add a set of fields to the plugin’s options page:
 *
 * add_action(
 *  'sixa_options_fieldset',
 *  function( $slug ) {
 *      $options = get_option( Options::$key, array() );
 *
 *      // Text.
 *      Options::add_field(
 *          'sixa_options_text',                    // Required: Slug-name to identify the field.
 *          __( 'Text field', '@@textdomain' ),     // Required: Formatted title of the field.
 *          function() use ( $options ) {           // Required: Function that generates an input field with a desired field type.
 *              Options::text_field(
 *                  array(
 *                      'id'    => 'sixa_options_text',
 *                      'name'  => sprintf( '%s[text-input]', Options::$key ),
 *                      'value' => isset( $options['text-input'] ) ? $options['text-input'] : '',
 *                  )
 *              );
 *          }
 *      );
 *      // Checkbox.
 *      Options::add_field(
 *          'sixa_options_checkbox',
 *          __( 'Checkbox', '@@textdomain' ),
 *          function() use ( $options ) {
 *              Options::checkbox_field(
 *                  array(
 *                      'id'          => 'sixa_options_checkbox',
 *                      'name'        => sprintf( '%s[checkbox-choice]', Options::$key ),
 *                      'value'       => isset( $options['checkbox-choice'] ) ? 'yes' : 'no',
 *                      'description' => __( 'Check me out', '@@textdomain' ),
 *                  )
 *              );
 *          }
 *      );
 *      // Select.
 *      Options::add_field(
 *          'sixa_options_select',
 *          __( 'Select', '@@textdomain' ),
 *          function() use ( $options ) {
 *              Options::select_field(
 *                  array(
 *                      'id'      => 'sixa_options_select',
 *                      'name'    => sprintf( '%s[select-choice]', Options::$key ),
 *                      'value'   => isset( $options['select-choice'] ) ? $options['select-choice'] : '',
 *                      'options' => array(
 *                          'option1' => __( 'Option 1', '@@textdomain' ),
 *                          'option2' => __( 'Option 2', '@@textdomain' ),
 *                          'option3' => __( 'Option 3', '@@textdomain' ),
 *                      ),
 *                  )
 *              );
 *          }
 *      );
 *  }
 * );
 */

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
		 * @since    1.0.0
		 * @access   public
		 * @var      string $key     Name of the option to retrieve.
		 */
		public static $key = 'sixa_options';

		/**
		 * The slug-name of the settings page.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @var      string $slug     The slug-name of the settings page on which to show the section.
		 */
		public static $slug = 'sixa-settings';

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @param    array $args     Plugin setting arguments.
		 * @return   void
		 */
		public function __construct( $args = array() ) {
			$labels      = isset( $args['labels'] ) ? $args['labels'] : array();
			$parent_slug = isset( $args['parent_slug'] ) ? $args['parent_slug'] : '';

			$this->register( $labels, $parent_slug );
			$this->fieldset();
		}

		/**
		 * Registers a top or submenu level menu page.
		 *
		 * @since    1.0.0
		 * @param    array $labels          The text to be displayed in the title and menu.
		 * @param    array $parent_slug     Optional. The slug name for the parent menu.
		 * @return   void
		 */
		protected function register( $labels, $parent_slug ) {
			$method     = 'add_menu_page';
			$page_title = isset( $labels['page_title'] ) ? $labels['page_title'] : _x( 'Plugin Options', 'post type', '@@textdomain' );
			$menu_title = isset( $labels['menu_title'] ) ? $labels['menu_title'] : _x( 'Sixa Options', 'post type', '@@textdomain' );
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
		 * @since    1.0.0
		 * @return   void
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
		 * @since    1.0.0
		 * @return   void
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
		 * @since    1.0.0
		 * @param    array $fieldset     Plugin setting options.
		 * @return   array
		 */
		public static function sanitize( $fieldset ) {
			return array_map( 'sanitize_text_field', $fieldset );
		}

		/**
		 * Add a new field to a section of a settings page.
		 *
		 * @since    1.0.0
		 * @param    string   $id           Slug-name to identify the field.
		 * @param    string   $title        Formatted title of the field.
		 * @param    callable $callback     Function that fills the field with the desired form inputs.
		 * @param    string   $page         Optional. The slug-name of the settings page on which to show the section.
		 * @param    string   $section      Optional. The slug-name of the section of the settings page in which to show the box.
		 * @return   void
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
		 * @since    1.0.0
		 * @param    array $raw_attributes     Attribute name value pairs.
		 * @return   string
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
		 * @since    1.0.0
		 * @param    array   $field     Arguments.
		 * @param    boolean $echo      Optional. Echo the output or return it.
		 * @return   mixed
		 */
		public static function hidden_field( $field, $echo = true ) {
			$return         = '';
			$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
			$field['value'] = isset( $field['value'] ) ? $field['value'] : '';
			$return        .= sprintf( '<input type="hidden" class="%s" name="%s" id="%s" value="%s" />', esc_attr( $field['class'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), esc_attr( $field['value'] ) );
			$return        .= '</p>';

			if ( ! $echo ) {
				return $return;
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Output a text input box.
		 *
		 * @since    1.0.0
		 * @param    array   $field     Arguments.
		 * @param    boolean $echo      Optional. Echo the output or return it.
		 * @return   mixed
		 */
		public static function text_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = isset( $field['label'] ) ? $field['label'] : '';
			$field['placeholder']       = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
			$field['class']             = isset( $field['class'] ) ? $field['class'] : 'short';
			$field['style']             = isset( $field['style'] ) ? $field['style'] : '';
			$field['wrapper_class']     = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
			$field['value']             = isset( $field['value'] ) ? $field['value'] : '';
			$field['name']              = isset( $field['name'] ) ? $field['name'] : $field['id'];
			$field['type']              = isset( $field['type'] ) ? $field['type'] : 'text';
			$field['custom_attributes'] = isset( $field['custom_attributes'] ) ? $field['custom_attributes'] : array();
			$return                    .= sprintf( '<p class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), wp_kses_post( $field['label'] ) );
			$return                    .= sprintf( '<input type="%s" class="%s" style="%s" name="%s" id="%s" value="%s" placeholder="%s" %s />', esc_attr( $field['type'] ), esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), esc_attr( $field['value'] ), esc_attr( $field['placeholder'] ), self::implode_html_attributes( $field['custom_attributes'] ) );

			if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</p>';

			if ( ! $echo ) {
				return $return;
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Output a textarea input box.
		 *
		 * @since    1.0.0
		 * @param    array   $field     Arguments.
		 * @param    boolean $echo      Optional. Echo the output or return it.
		 * @return   mixed
		 */
		public static function textarea_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = isset( $field['label'] ) ? $field['label'] : '';
			$field['placeholder']       = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
			$field['class']             = isset( $field['class'] ) ? $field['class'] : 'short';
			$field['style']             = isset( $field['style'] ) ? $field['style'] : '';
			$field['wrapper_class']     = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
			$field['value']             = isset( $field['value'] ) ? $field['value'] : '';
			$field['name']              = isset( $field['name'] ) ? $field['name'] : $field['id'];
			$field['custom_attributes'] = isset( $field['custom_attributes'] ) ? $field['custom_attributes'] : array();
			$field['rows']              = isset( $field['rows'] ) ? $field['rows'] : 2;
			$field['cols']              = isset( $field['cols'] ) ? $field['cols'] : 20;
			$return                    .= sprintf( '<p class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), wp_kses_post( $field['label'] ) );
			$return                    .= sprintf( '<textarea class="%s" style="%s" name="%s" id="%s" placeholder="%s" rows="%d" cols="%d" %s>%s</textarea>', esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), esc_attr( $field['placeholder'] ), absint( $field['rows'] ), absint( $field['cols'] ), self::implode_html_attributes( $field['custom_attributes'] ), esc_attr( $field['value'] ) );

			if ( ! empty( $field['description'] ) && false === $field['desc_tip'] ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</p>';

			if ( ! $echo ) {
				return $return;
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Output a checkbox input.
		 *
		 * @since    1.0.0
		 * @param    array   $field     Arguments.
		 * @param    boolean $echo      Optional. Echo the output or return it.
		 * @return   mixed
		 */
		public static function checkbox_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = isset( $field['label'] ) ? $field['label'] : '';
			$field['class']             = isset( $field['class'] ) ? $field['class'] : 'checkbox';
			$field['style']             = isset( $field['style'] ) ? $field['style'] : '';
			$field['wrapper_class']     = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
			$field['value']             = isset( $field['value'] ) ? $field['value'] : 'no';
			$field['name']              = isset( $field['name'] ) ? $field['name'] : $field['id'];
			$field['custom_attributes'] = isset( $field['custom_attributes'] ) ? $field['custom_attributes'] : array();
			$return                    .= sprintf( '<p class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), wp_kses_post( $field['label'] ) );
			$return                    .= sprintf( '<input type="checkbox" class="%s" style="%s" name="%s" id="%s" value="yes" %s %s />', esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), checked( 'yes', esc_attr( $field['value'] ), false ), self::implode_html_attributes( $field['custom_attributes'] ) );

			if ( ! empty( $field['description'] ) ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</p>';

			if ( ! $echo ) {
				return $return;
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Output radio inputs.
		 *
		 * @since    1.0.0
		 * @param    array   $field     Arguments.
		 * @param    boolean $echo      Optional. Echo the output or return it.
		 * @return   mixed
		 */
		public static function select_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = isset( $field['label'] ) ? $field['label'] : '';
			$field['class']             = isset( $field['class'] ) ? $field['class'] : 'select short';
			$field['style']             = isset( $field['style'] ) ? $field['style'] : '';
			$field['wrapper_class']     = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
			$field['value']             = isset( $field['value'] ) ? $field['value'] : '';
			$field['name']              = isset( $field['name'] ) ? $field['name'] : $field['id'];
			$field['show_option_none']  = isset( $field['show_option_none'] ) ? true : false;
			$field['custom_attributes'] = isset( $field['custom_attributes'] ) ? $field['custom_attributes'] : array();
			$return                    .= sprintf( '<p class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<label for="%s">%s</label>', esc_attr( $field['id'] ), wp_kses_post( $field['label'] ) );
			$return                    .= sprintf( '<select class="%s" style="%s" name="%s" id="%s" %s>', esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), self::implode_html_attributes( $field['custom_attributes'] ) );

			if ( ! ! $field['show_option_none'] ) {
				/* translators: 1: Open option tag, 2: Close option tag. */
				$return .= sprintf( _x( '%1$s&mdash; Select &mdash;%2$s', 'showing no pages', '@@textdomain' ), '<option value="">', '</option>' );
			}

			foreach ( $field['options'] as $key => $value ) {
				$return .= sprintf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( esc_attr( $key ), esc_attr( $field['value'] ), false ), esc_html( $value ) );
			}

			$return .= '</select>';

			if ( ! empty( $field['description'] ) ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</p>';

			if ( ! $echo ) {
				return $return;
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Output radio inputs.
		 *
		 * @since    1.0.0
		 * @param    array   $field     Arguments.
		 * @param    boolean $echo      Optional. Echo the output or return it.
		 * @return   mixed
		 */
		public static function radio_field( $field, $echo = true ) {
			$return                     = '';
			$field['label']             = isset( $field['label'] ) ? $field['label'] : '';
			$field['class']             = isset( $field['class'] ) ? $field['class'] : 'select short';
			$field['style']             = isset( $field['style'] ) ? $field['style'] : '';
			$field['wrapper_class']     = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
			$field['value']             = isset( $field['value'] ) ? $field['value'] : '';
			$field['name']              = isset( $field['name'] ) ? $field['name'] : $field['id'];
			$field['custom_attributes'] = isset( $field['custom_attributes'] ) ? $field['custom_attributes'] : array();
			$return                    .= sprintf( '<fieldset class="form-field %s_field %s">', esc_attr( $field['id'] ), esc_attr( $field['wrapper_class'] ) );
			$return                    .= sprintf( '<legend for="%s">%s</legend><ul>', esc_attr( $field['id'] ), wp_kses_post( $field['label'] ) );

			foreach ( $field['options'] as $key => $value ) {
				$return .= sprintf( '<li><label><input type="radio" class="%s" style="%s" name="%s" id="%s" value="%s" %s %s />%s</label></li>', esc_attr( $field['class'] ), esc_attr( $field['style'] ), esc_attr( $field['name'] ), esc_attr( $field['id'] ), esc_attr( $key ), checked( esc_attr( $key ), esc_attr( $field['value'] ), false ), self::implode_html_attributes( $field['custom_attributes'] ), esc_html( $value ) );
			}

			$return .= '</ul>';

			if ( ! empty( $field['description'] ) ) {
				$return .= sprintf( '<span class="description">%s</span>', wp_kses_post( $field['description'] ) );
			}

			$return .= '</fieldset>';

			if ( ! $echo ) {
				return $return;
			}

			echo $return; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

	}
endif;
