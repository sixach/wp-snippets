<?php
/**
 * The file outputs a given reusable-post content on the page.
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

if ( ! class_exists( 'Reusable_Post' ) ) :

	/**
	 * The file that outputs the reusable-post content.
	 */
	class Reusable_Post extends \WP_Widget {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since     1.0.0
		 * @param     array    $args    Widget setting arguments.
		 * @return    void
		 */
		public function __construct( $args = array() ) {
			$args['defaults']    = isset( $args['defaults'] ) ? $args['defaults'] : array();
			$args['label']       = isset( $args['label'] ) ? $args['label'] : esc_html_x( 'Reusable Post', 'widget name', 'sixa-snippets' );
			$args['description'] = isset( $args['description'] ) ? $args['description'] : esc_html_x( 'Your site&#8217;s reusable blocks post.', 'widget description', 'sixa-snippets' );
			$widget_key          = 'sixa-reusable-post';
			$widget_ops          = array(
				'classname'                   => sprintf( '%s-widget', $widget_key ),
				'description'                 => esc_html( $args['description'] ),
				'customize_selective_refresh' => true,
			);
			$widget_defaults     = array(
				'title'     => '',
				'post_id'   => 'no', // The falsy string value added to avoid `get_post` method pulling the global post object instead.
			);
			parent::__construct( $widget_key, esc_html( $args['label'] ), $widget_ops );
			$this->alt_option_name = 'sixa_reusable_post';
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
			$post_id = isset( $instance['post_id'] ) ? intval( $instance['post_id'] ) : $this->defaults['post_id'];
			$post    = sanitize_post( get_post( $post_id ), 'display' );

			if ( $title ) {
				$html['title'] = $args['before_title'] . $title . $args['after_title'];
			}

			// Proceed, if the post object is of the `WP_Post` class.
			if ( $post instanceof \WP_Post ) {
                // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				$html['post'] = sprintf( '<div class="%s">%s</div>', implode( ' ', get_post_class( '', $post_id ) ), apply_filters( 'the_content', $post->post_content ) );
			}

			$html = force_balance_tags( join( '', apply_filters( 'sixa_reusable_post_widget_html', $html ) ) );
			$html = preg_replace( array( '#<p>\s*+(<br\s*/*>)?\s*</p>#i', '~\s?<p>(\s|&nbsp;)+</p>\s?~' ), array( '', '' ), $html );
			$html = $args['before_widget'] . $html . $args['after_widget'];

			echo apply_filters( 'sixa_reusable_post_widget_output', $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		/**
		 * Handles updating the settings for the current widget instance.
		 *
		 * @since     1.0.0
		 * @param     array    $new_instance    New settings for this instance as input entered by the user.
		 * @param     array    $old_instance    Old settings for this instance.
		 * @return    array
		 */
		public function update( $new_instance, $old_instance ) {
			$instance            = $old_instance;
			$instance['title']   = sanitize_text_field( $new_instance['title'] );
			$instance['post_id'] = sanitize_text_field( $new_instance['post_id'] );
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
			$post_id  = isset( $instance['post_id'] ) ? $instance['post_id'] : $this->defaults['post_id'];
			$posts    = wp_list_pluck(
				get_posts(
					apply_filters(
						'sixa_reusable_post_list_args',
						array(
							'numberposts' => -1,
							'post_type'   => 'wp_block',
						)
					)
				),
				'post_title',
				'ID'
			);
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php echo esc_html_x( 'Title:', 'widget form', 'sixa-snippets' ); ?>
				</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>">
					<?php echo esc_html_x( 'Post:', 'widget form', 'sixa-snippets' ); ?>
				</label>
				<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'post_id' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'post_id' ) ); ?>">
					<option value="<?php echo esc_attr( $this->defaults['post_id'] ); ?>">
							<?php echo esc_html_x( '&mdash; Select &mdash;', 'placeholder', 'sixa-snippets' ); ?>
					</option>
					<?php foreach ( $posts as $key => $value ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $key, $post_id, true ); ?>>
							<?php echo wp_kses_post( $value ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
			<?php
		}

	}
endif;
