<?php
/*
Plugin Name: Recent Posts Widget plugin
Description: Display Recent posts
Version: 1.0.0
Author: Nikola Vucic
*/

class Recent_Posts_Widget extends WP_Widget {

	function __construct() {
		parent::__construct(

			// base ID of the widget
			'recent_posts_widget',

			// name of the widget
			__( 'Recent Posts Widget', 'quantox' ),

			// widget options
			array ( 'description' => __( 'A widget to display recent posts', 'quantox' ) ) );
        
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {


		$title = ( ! empty( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( 'Recent Posts', 'quantox' );
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance[ 'number' ] ) ) ? absint( $instance[ 'number' ] ) : 5;
		if ( ! $number ) {
			$number = 5;
		}

		$show_thumbnail = isset( $instance[ 'show_thumbnail' ] ) ? $instance[ 'show_thumbnail' ] : false;
        $dropdown = ! empty( $instance['dropdown'] ) ? sanitize_text_field($instance['dropdown']) : 'ASC';
		$show_excerpt  = isset( $instance[ 'show_excerpt' ] ) ? $instance[ 'show_excerpt' ] : false;
        $excerpt_lenght = ( ! empty( $instance[ 'excerpt_lenght' ] ) ) ? $instance[ 'excerpt_lenght' ] : false;

        
		/**
		 * Filter the arguments for the Recent Posts widget.
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'      => $number,
			'no_found_rows'       => true,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
            'order'               => $dropdown
		) ) );

		if ( $r->have_posts() ) :
			?>
			<?php echo $args[ 'before_widget' ]; ?>
			<?php if ( $title ) {
			echo $args[ 'before_title' ];
			echo esc_html( $title );
			echo $args[ 'after_title' ];
		} ?>
			<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<div>
				<div>
					<a href="<?php esc_url( the_permalink() ); ?>"></a>
                    <?php if ( $show_thumbnail ) :
						echo get_the_post_thumbnail();
					 endif; ?>
				</div>
				<div>
					<a href="<?php esc_url( the_permalink() ); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a>
					<p>
						<?php if( $show_excerpt ) :
                            if( $excerpt_lenght == false ) {
                                echo get_the_excerpt();
                            } else {
                                echo substr( get_the_excerpt(), 0, $excerpt_lenght );
                            }
						endif; ?>
					</p>
				</div>
			</div>
		<?php endwhile; ?>
			<?php echo $args[ 'after_widget' ]; ?>
			<?php
			// Reset the global $the_post as this query will have stomped on it
			wp_reset_postdata();

		endif;
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$title          = isset( $instance[ 'title' ] ) ? esc_html( $instance[ 'title' ] ) : __( 'New title', 'quantox' );
		$number         = isset( $instance[ 'number' ] ) ? absint( $instance[ 'number' ] ) : 5;
		$show_thumbnail = isset( $instance[ 'show_thumbnail' ] ) ? (bool) $instance[ 'show_thumbnail' ] : false;
        $dropdown       = isset( $instance['dropdown'] ) ? $instance['dropdown'] : 'ASC';
		$show_excerpt   = isset( $instance[ 'show_excerpt' ] ) ? (bool) $instance[ 'show_excerpt' ] : false;
        $excerpt_lenght = isset( $instance[ 'excerpt_lenght' ] ) ? esc_html( $instance[ 'excerpt_lenght' ] ) : '';
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo _e( 'Title: ', 'quantox' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>"
			       type="text"
			       value="<?php echo esc_html( $title ); ?>">
		</p>

		<p>
			<label
				for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of posts to show:', 'quantox' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'number' ); ?>"
			       name="<?php echo $this->get_field_name( 'number' ); ?>"
			       type="number"
			       min="1"
			       value="<?php echo $number; ?>"
			       size="3"/>
        </p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_thumbnail ); ?>
			       id="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_thumbnail' ); ?>"/>
			<label
				for="<?php echo $this->get_field_id( 'show_thumbnail' ); ?>"><?php _e( 'Display post thumbnail', 'quantox' ); ?></label>
		</p>
        
        <p>
            <label for="<?php echo $this->get_field_id('dropdown'); ?>"><?php _e('Select order:', 'quantox'); ?></label>
            <select id="<?php echo $this->get_field_id('dropdown'); ?>" name="<?php echo $this->get_field_name('dropdown'); ?>">
                <option value="ASC" <?php selected($dropdown, 'ASC'); ?>><?php _e('Ascending order', 'quantox') ?></option>
                <option value="DESC" <?php selected($dropdown, 'DESC'); ?>><?php _e('Descending order', 'quantox') ?></option>
            </select>
        </p>

		<p>
			<input class="checkbox" type="checkbox" <?php checked( $show_excerpt ); ?>
			       id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"
			       name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>"/>
			<label
				for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>"><?php _e( 'Display excerpt', 'quantox' ); ?></label>
		</p>
        
        <p>
			<label for="<?php echo $this->get_field_id( 'excerpt_lenght' ); ?>"><?php echo _e( 'Excerpt lenght: ', 'quantox' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'excerpt_lenght' ); ?>"
			       name="<?php echo $this->get_field_name( 'excerpt_lenght' ); ?>"
			       type="text"
			       value="<?php echo esc_html( $excerpt_lenght ); ?>">
		</p>

		<?php
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance[ 'title' ]         = ( ! empty ( $new_instance[ 'title' ] ) ) ? strip_tags( $new_instance[ 'title' ] ) : '';
		$instance[ 'number' ]        = (int) $new_instance[ 'number' ];
		$instance[ 'show_thumbnail' ]     = isset( $new_instance[ 'show_thumbnail' ] ) ? (bool) $new_instance[ 'show_thumbnail' ] : false;
        $instance['dropdown'] = ! empty( $new_instance['dropdown'] ) ? sanitize_text_field($new_instance['dropdown']) : 'ASC';
		$instance[ 'show_excerpt' ] = isset( $new_instance[ 'show_excerpt' ] ) ? (bool) $new_instance[ 'show_excerpt' ] : false;
        $instance[ 'excerpt_lenght' ]         = ( ! empty ( $new_instance[ 'excerpt_lenght' ] ) ) ? intval( preg_replace('/\D/', '', $new_instance[ 'excerpt_lenght' ]) ) : '';
        
		return $instance;
	}

}

// register Recent Post widget
function recent_posts_register_widget() {
    register_widget( 'Recent_Posts_Widget' );
}
add_action( 'widgets_init', 'recent_posts_register_widget' );
