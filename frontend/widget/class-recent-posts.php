<?php
/**
 * The file outputs a list of most recent published posts on the sidebar.
 *
 * @link       https://sixa.ch
 * @author     Mahdi Yazdani
 * @since      1.0.0
 *
 * @package    sixa-snippets
 * @subpackage sixa-snippets/frontend/widget
 */

namespace SixaSnippets\Frontend\Widget;

/**
 * INSTRUCTIONS:
 *
 * 1. Update the namespace used above.
 * 2. Search and replace text-domains `@@textdomain`.
 * 3. Initialize the class to register the widget when needed:
 *
 * add_action( 'widgets_init', function() {
 *     register_widget( Recent_Posts::class );
 * } );
 *
 * Note: Do not initialize this class before the `widgets_init` hook.
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Recent_Posts' ) ) :

	/**
	 * The file that outputs a list of recent posts.
	 */
	class Recent_Posts extends \WP_Widget {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.0.0
		 * @return   void
		 */
		public function __construct() {
			$widget_key = 'sixa-recent-posts';
			$widget_ops = array(
				'classname'                   => sprintf( '%s-widget', $widget_key ),
				'description'                 => esc_html_x( 'Your site&#8217;s recent blog posts.', 'widget description', '@@textdomain' ),
				'customize_selective_refresh' => true,
			);
			parent::__construct( $widget_key, esc_html_x( 'Recent Posts', 'widget name', '@@textdomain' ), $widget_ops );
			$this->alt_option_name = 'sixa_recent_posts';
			$this->defaults        = array(
				'title'       => __( 'Recent Posts', '@@textdomain' ),
				'number'      => 4,
				'show_date'   => 0,
				'show_author' => 0,
				'show_thumb'  => 0,
			);
		}

		/**
		 * Outputs the content for the current widget’s instance.
		 *
		 * @since    1.0.0
		 * @param    array $args         Display arguments including 'before_title', 'after_title'.
		 * @param    array $instance     Settings for the current widget instance.
		 * @return   void
		 */
		public function widget( $args, $instance ) {
			$html   = array();
			$title  = ( ! empty( $instance['title'] ) ) ? $instance['title'] : $this->defaults['title'];
			$title  = apply_filters( 'widget_title', $title, $instance, $this->id_base );
			$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : $this->defaults['number'];

			if ( ! $number ) {
				$number = $this->defaults['number'];
			}

			$show_date    = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
			$show_author  = isset( $instance['show_author'] ) ? $instance['show_author'] : false;
			$show_thumb   = isset( $instance['show_thumb'] ) ? $instance['show_thumb'] : false;
			$recent_posts = wp_get_recent_posts(
				apply_filters(
					'sixa_recent_posts_widget_args',
					array(
						'numberposts'      => $number,
						'post_status'      => 'publish',
						'suppress_filters' => false,
					)
				),
				ARRAY_A
			);

			// Bail early, if the query has no posts to loop over.
			if ( ! is_array( $recent_posts ) || empty( $recent_posts ) ) {
				return;
			}

			if ( $title ) {
				$html['title'] = $args['before_title'] . $title . $args['after_title'];
			}

			$html['ul_open'] = '<ul>';

			foreach ( (array) $recent_posts as $post ) {
				$post_id = $post['ID'];

				if ( get_queried_object_id() === $post_id ) {
					$aria_current = ' aria-current="page"';
				}

				$html[ sprintf( 'li_open_%d', $post_id ) ] = sprintf( '<li class="%s"%s>', implode( ' ', get_post_class( '', $post ) ), $aria_current );

				// Thumbnail.
				if ( ! ! $show_thumb && has_post_thumbnail( $post ) ) {
					$html[ sprintf( 'thumbnail_%d', $post_id ) ] = sprintf( '<figure class="entry-thumbnail"><a href="%s">%s</a></figure>', esc_url( get_permalink( $post ) ), get_the_post_thumbnail( $post ) );
				}

				// Title.
				$html[ sprintf( 'title_%d', $post_id ) ] = sprintf( '<a href="%s" class="entry-title" rel="bookmark">%s</a>', esc_url( get_permalink( $post ) ), wp_kses_post( $post['post_title'] ) );

				// Author.
				if ( ! ! $show_author ) {
					/* translators: 1: Open span tag, 2: Author anchor tag, 3: Close span tag. */
					$html[ sprintf( 'author_%d', $post_id ) ] = sprintf( _x( '%1$sby %2$s%3$s', 'recent posts widget', '@@textdomain' ), '<span class="post-author">', sprintf( '<a href="%s" class="url fn" rel="author">%s</a>', esc_url( get_author_posts_url( get_the_author_meta( 'ID', $post['post_author'] ) ) ), get_the_author_meta( 'display_name', $post['post_author'] ) ), '</span>' );
				}

				// Date.
				if ( ! ! $show_date ) {
					$html[ sprintf( 'time_%d', $post_id ) ] = sprintf( '<a href="%s" rel="bookmark"><time class="entry-date published" datetime="%s">%s</time></a>', esc_url( get_permalink( $post ) ), esc_attr( get_the_date( 'c' ) ), esc_html( get_the_date() ) );
				}

				$html[ sprintf( 'li_close_%d', $post_id ) ] = '</li>';
			}

			$html['ul_close'] = '</ul>';
			$html             = join( '', apply_filters( 'sixa_recent_posts_widget_html', $html ) );
			$html             = $args['before_widget'] . $html . $args['after_widget'];

			echo apply_filters( 'sixa_recent_posts_widget_output', $html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
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
			$instance                = $old_instance;
			$instance['title']       = sanitize_text_field( $new_instance['title'] );
			$instance['number']      = intval( $new_instance['number'] );
			$instance['show_author'] = isset( $new_instance['show_author'] ) ? 1 : 0;
			$instance['show_date']   = isset( $new_instance['show_date'] ) ? 1 : 0;
			$instance['show_thumb']  = isset( $new_instance['show_thumb'] ) ? 1 : 0;

			return $instance;
		}

		/**
		 * Outputs the settings form for the widget.
		 *
		 * @since    1.0.0
		 * @param    array $instance     Current settings.
		 * @return   void
		 */
		public function form( $instance ) {
			$instance    = wp_parse_args( $instance, $this->defaults );
			$title       = isset( $instance['title'] ) ? $instance['title'] : '';
			$number      = isset( $instance['number'] ) ? $instance['number'] : $this->defaults['number'];
			$show_author = isset( $instance['show_author'] ) ? $instance['show_author'] : $this->defaults['show_author'];
			$show_date   = isset( $instance['show_date'] ) ? $instance['show_date'] : $this->defaults['show_date'];
			$show_thumb  = isset( $instance['show_thumb'] ) ? $instance['show_thumb'] : $this->defaults['show_thumb'];
			?>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">
					<?php echo esc_html_x( 'Title:', 'widget form', '@@textdomain' ); ?>
				</label>
				<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>">
					<?php echo esc_html_x( 'Number of posts to show:', 'widget form', '@@textdomain' ); ?>
				</label>
				<input class="tiny-text" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" min="1" value="<?php echo absint( $number ); ?>" size="3" />
			</p>
			<p>
				<input class="checkbox" type="checkbox"<?php checked( $show_author, 1 ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_author' ) ); ?>" value="1" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_author' ) ); ?>">
					<?php echo esc_html_x( 'Display post author?', 'widget form', '@@textdomain' ); ?>
				</label>
			</p>
			<p>
				<input class="checkbox" type="checkbox"<?php checked( $show_date, 1 ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_date' ) ); ?>" value="1" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_date' ) ); ?>">
					<?php echo esc_html_x( 'Display post date?', 'widget form', '@@textdomain' ); ?>
				</label>
			</p>
			<p>
				<input class="checkbox" type="checkbox"<?php checked( $show_thumb, 1 ); ?> id="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'show_thumb' ) ); ?>" value="1" />
				<label for="<?php echo esc_attr( $this->get_field_id( 'show_thumb' ) ); ?>">
					<?php echo esc_html_x( 'Display post thumbnail?', 'widget form', '@@textdomain' ); ?>
				</label>
			</p>
			<?php
		}

	}
endif;
