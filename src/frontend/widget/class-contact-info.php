<?php
/**
 * The file outputs contact information details in your sidebars.
 *
 * @link          https://sixa.ch
 * @author        sixa AG
 * @since         1.0.0
 *
 * @package       Sixa_Snippets
 * @subpackage    Sixa_Snippets/Frontend/Widget
 * @phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
 */

namespace Sixa_Snippets\Frontend\Widget;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Contact_Info' ) ) :

	/**
	 * The file that outputs the contact-info details.
	 */
	class Contact_Info extends \WP_Widget {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array    $args    Widget setting arguments.
		 * @return    void
		 */
		public function __construct( $args = array() ) {
			$args['defaults']    = isset( $args['defaults'] ) ? $args['defaults'] : array();
			$args['label']       = isset( $args['label'] ) ? $args['label'] : esc_html_x( 'Contact Info', 'widget name', 'sixa-snippets' );
			$args['description'] = isset( $args['description'] ) ? $args['description'] : esc_html_x( 'Display a link to your location, and contact information.', 'widget description', 'sixa-snippets' );
			$widget_key          = 'sixa-contact-info';
			$widget_ops          = array(
				'classname'                   => sprintf( '%s-widget', $widget_key ),
				'description'                 => esc_html( $args['description'] ),
				'customize_selective_refresh' => true,
			);
			$widget_defaults     = array(
				'title'   => '',
				'address' => '',
				'showmap' => 0,
				'phone'   => '',
				'email'   => '',
			);
			parent::__construct( $widget_key, esc_html( $args['label'] ), $widget_ops );
			$this->alt_option_name = 'sixa_contact_info';
			$this->defaults        = wp_parse_args( $args['defaults'], $widget_defaults );
		}

		/**
		 * Outputs the content for the current widget’s instance.
		 *
		 * @since     1.0.0
		 * @param     array    $args        Display arguments including 'before_title', 'after_title'.
		 * @param     array    $instance    Settings for the current widget instance.
		 * @return    void
		 */
		public function widget( $args, $instance ) {
			$html    = array();
			$title   = ( ! empty( $instance['title'] ) ) ? $instance['title'] : $this->defaults['title'];
			$title   = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			$address = isset( $instance['address'] ) ? $instance['address'] : $this->defaults['address'];
			$showmap = isset( $instance['showmap'] ) ? $instance['showmap'] : false;
			$phone   = isset( $instance['phone'] ) ? $instance['phone'] : $this->defaults['phone'];
			$email   = isset( $instance['email'] ) ? $instance['email'] : $this->defaults['email'];

			// Title.
			if ( $title ) {
				$html['title'] = $args['before_title'] . $title . $args['after_title'];
			}

			// Open wrapper div tag.
			$html['div_open'] = '<div itemscope="itemscope" itemtype="https://schema.org/LocalBusiness">';

			// Location/Address.
			if ( $address ) {
				$html['map_open'] = '<address itemscope="itemscope" itemtype="https://schema.org/PostalAddress" itemprop="address">';

				if ( $showmap ) {
					$html['showmap'] = self::build_map( $address );
				}

				$html['map_link']  = sprintf( '<a href="%s" target="_blank" rel="noopener noreferrer nofollow">%s</a>', esc_url( self::get_map_link( $address ) ), wp_kses_post( nl2br( $address ) ) );
				$html['map_close'] = '</address>';
			}

			// Telephone.
			if ( $phone ) {
				$html['phone'] = sprintf( '<p itemprop="telephone"><a href="tel:%s">%s</a></p>', filter_var( $phone, FILTER_SANITIZE_NUMBER_INT ), esc_html( $phone ) );
			}

			// Email-address.
			if ( $phone ) {
				$html['email'] = sprintf( '<p itemprop="email"><a href="mailto:%s">%s</a></p>', filter_var( $email, FILTER_VALIDATE_EMAIL ), esc_html( $email ) );
			}

			$html['div_close'] = '</div>';
			$html              = join( '', apply_filters( 'sixa_contact_info_widget_html', $html ) );
			$html              = $args['before_widget'] . $html . $args['after_widget'];

			echo apply_filters( 'sixa_contact_info_widget_output', $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Handles updating the settings for the current widget instance.
		 *
		 * @since    1.0.0
		 * @param    array $new_instance     New settings for this instance as input entered by the user.
		 * @param    array $old_instance     Old settings for this instance.
		 * @return   array
		 */
		public function update( $new_instance, $old_instance ) {
			$instance            = $old_instance;
			$instance['title']   = sanitize_text_field( $new_instance['title'] );
			$instance['address'] = sanitize_textarea_field( $new_instance['address'] );
			$instance['showmap'] = isset( $new_instance['showmap'] ) ? 1 : 0;
			$instance['phone']   = sanitize_text_field( $new_instance['phone'] );
			$instance['email']   = sanitize_email( $new_instance['email'] );

			return $instance;
		}

		/**
		 * Outputs the settings form for the widget.
		 *
		 * @since     1.0.0
		 * @param     array    $instance    Current settings.
		 * @return    void
		 */
		public function form( $instance ) {
			$instance = wp_parse_args( $instance, $this->defaults );
			$title    = isset( $instance['title'] ) ? $instance['title'] : '';
			$address  = isset( $instance['address'] ) ? $instance['address'] : $this->defaults['address'];
			$showmap  = isset( $instance['showmap'] ) ? $instance['showmap'] : $this->defaults['showmap'];
			$phone    = isset( $instance['phone'] ) ? $instance['phone'] : $this->defaults['phone'];
			$email    = isset( $instance['email'] ) ? $instance['email'] : $this->defaults['email'];

			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php echo esc_html_x( 'Title:', 'widget form', 'sixa-snippets' ); ?>
				</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>">
					<?php echo esc_html_x( 'Address:', 'widget form', 'sixa-snippets' ); ?>
				</label>
				<textarea rows="5" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'address' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'address' ) ); ?>"><?php echo esc_textarea( $address ); ?></textarea>
			</p>
			<p>
				<input class="checkbox" type="checkbox"<?php checked( $showmap, 1 ); ?> id="<?php echo esc_attr( $this->get_field_id( 'showmap' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'showmap' ) ); ?>" value="1" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'showmap' ) ); ?>">
					<?php echo esc_html_x( 'Show map?', 'widget form', 'sixa-snippets' ); ?>
				</label>
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>">
					<?php echo esc_html_x( 'Phone:', 'widget form', 'sixa-snippets' ); ?>
				</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'phone' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'phone' ) ); ?>" type="tel" value="<?php echo esc_attr( $phone ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>">
					<?php echo esc_html_x( 'Email:', 'widget form', 'sixa-snippets' ); ?>
				</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'email' ) ); ?>" type="email" value="<?php echo esc_attr( $email ); ?>" />
			</p>
			<?php
		}

		/**
		 * Generate a Google Maps link for the supplied address.
		 *
		 * @since     1.0.0
		 * @param     string    $address    The URL to encode.
		 * @return    string
		 */
		public static function get_map_link( $address ) {
			return apply_filters( 'sixa_contact_info_widget_map_link', sprintf( 'https://maps.google.com/maps?z=16&q=%s', self::do_urlencode_address( $address ) ) );
		}

		/**
		 * Builds map display HTML code from the entered address.
		 *
		 * @since     1.0.0
		 * @param     string    $address    The URL to encode.
		 * @return    mixed
		 */
		public static function build_map( $address ) {
			$src            = add_query_arg( 'output', 'embed', self::get_map_link( $address ) );
			$raw_attributes = apply_filters(
				'sixa_contact_info_widget_map_attrs',
				array(
					'width'           => '100%',
					'height'          => '400',
					'frameborder'     => '0',
					'aria-hidden'     => 'false',
					'crossorigin'     => 'anonymous',
					'allowfullscreen' => 'true',
				)
			);
			$attributes     = array_map(
				function( $attribute ) use ( $raw_attributes ) {
					return sprintf( '%s="%s"', esc_attr( array_search( $attribute, $raw_attributes, true ) ), esc_attr( $attribute ) );
				},
				$raw_attributes
			);

			// Implode and escape HTML attributes for output.
			return sprintf( '<iframe %s src="%s"></iframe>', implode( ' ', $attributes ), esc_url( $src ) );
		}

		/**
		 * Encode an URL.
		 *
		 * @since     1.0.0
		 * @param     string    $address    The URL to encode.
		 * @return    string
		 */
		public static function do_urlencode_address( $address ) {
			$address = strtolower( $address );
			// Trim any any unwanted whitespace.
			$address = preg_replace( '/\s+/', '', trim( $address ) );
			// Use + not %20.
			$address = str_ireplace( ' ', '+', $address );

			return rawurlencode( $address );
		}
	}

endif;
