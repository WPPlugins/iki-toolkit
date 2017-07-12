<?php


/**
 * Registers iki content blow widget
 */
function register_iki_content_block_widget() {
	register_widget( 'Iki_Content_Block_Widget' );
}

add_action( 'widgets_init', 'register_iki_content_block_widget' );

class Iki_Content_Block_Widget extends WP_Widget {

	public function __construct() {
		$widget_ops = array( 'description' => __( 'Displays Content block in a widget', 'iki-toolkit' ) );
		parent::__construct( 'iki_content_block', __( 'Iki Themes Content Block Widget', 'iki-toolkit' ), $widget_ops );
	}

	public function form( $instance ) {

		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		$title    = $instance['title'];
		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?> <input class="widefat"
																									  id="<?php echo $this->get_field_id( 'title' ); ?>"
																									  name="<?php echo $this->get_field_name( 'title' ); ?>"
																									  type="text"
																									  value="<?php echo esc_attr( $title ); ?>"/></label>
		</p>
		<?php

		$postID = ''; // Initialize the variable
		if ( isset( $instance['custom_post_id'] ) ) {
			$postID = esc_attr( $instance['custom_post_id'] );
		};
		?>

		<p>
			<label
					for="<?php echo $this->get_field_id( 'custom_post_id' ); ?>"> <?php echo __( 'Content Block to Display:', 'iki-toolkit' ) ?>
				<select class="widefat" id="<?php echo $this->get_field_id( 'custom_post_id' ); ?>"
						name="<?php echo $this->get_field_name( 'custom_post_id' ); ?>">
					<?php
					$args           = array(
						'post_type'        => 'iki_content_block',
						'suppress_filters' => 0,
						'numberposts'      => - 1,
						'order'            => 'ASC'
					);
					$content_blocks = get_posts( $args );
					if ( $content_blocks ) {
						foreach ( $content_blocks as $content_block ) : setup_postdata( $content_block );
							echo '<option value="' . $content_block->ID . '"';
							if ( $postID == $content_block->ID ) {
								echo ' selected';
							};
							echo '>' . $content_block->post_title . '</option>';
						endforeach;
					} else {
						echo '<option value="">' . __( 'No content blocks available', 'iki-toolkit' ) . '</option>';
					};
					?>
				</select>
			</label>
		</p>
		<p>
			<?php
			echo '<a href="post.php?post=' . $postID . '&action=edit">' . __( 'Edit Content Block', 'iki-toolkit' ) . '</a>';
			?>
		</p>
	<?php } //end form

	public function update( $new_instance, $old_instance ) {
		$instance                   = $old_instance;
		$instance['custom_post_id'] = strip_tags( $new_instance['custom_post_id'] );
		$new_instance               = wp_parse_args( (array) $new_instance, array( 'title' => '' ) );
		$instance['title']          = sanitize_text_field( $new_instance['title'] );

		return $instance;
	}

	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

		echo $args['before_widget'];
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$post_id  = ( $instance['custom_post_id'] != '' ) ? esc_attr( $instance['custom_post_id'] ) : __( 'Find', 'iki-toolkit' );
		$bb_press = false;

		if ( class_exists( 'bbpress', false ) ) {
			$bb_press = true;
			bbp_restore_all_filters( 'the_content', 0 );
		}

		if ( $bb_press ) {
			if ( bbp_is_theme_compat_active() ) {
				bbp_remove_all_filters( 'the_content', 0 );
			}
		}

		Iki_CB_Factory::get_instance()->content_block( $post_id );
		echo $args['after_widget'];
	}


}