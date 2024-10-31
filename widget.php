<?php

/**
 * Add function to widgets_init that'll load our widget.
 * @since 0.1
 */

/**
 * Register our widget.
 * 'Example_Widget' is the widget class used below.
 *
 * @since 0.1
 */
function rerss_load_widgets() {
	register_widget( 'Real_Estate_RSS_Widget' );
}
/**
 * Example Widget class.
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update.  Nice!
 *
 * @since 0.1
 */
class Real_Estate_RSS_Widget extends WP_Widget {

	/**
	 * Widget setup.
	 */
	function Real_Estate_RSS_Widget() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'real_estate_rss', 'description' => __('Real Estate RSS Widget', REAL_ESTATE_RSS) );

		/* Widget control settings. */
		$control_ops = array( 'width' => 150, 'height' => 350, 'id_base' => 'real-estate-rss-widget' );

		/* Create the widget. */
		$this->WP_Widget( 'real-estate-rss-widget', __('Real Estate RSS Widget', REAL_ESTATE_RSS), $widget_ops, $control_ops );
	}

	/**
	 * How to display the widget on the screen.
	 */
	function widget( $args, $instance ) {
		extract( $args );

		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$url = $instance['url'];
		$template = $instance['template'];
		$items = $instance['items'];

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

	real_estate_rss("url=$url&template=$template&items=$items&echo=1");

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	/**
	 * Update the widget settings.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['url'] = strip_tags( $new_instance['url'] );

		/* No need to strip tags for sex and show_sex. */
		$instance['template'] = $new_instance['template'];
		$instance['items'] = $new_instance['items'];

		return $instance;
	}

	/**
	 * Displays the widget settings controls on the widget panel.
	 * Make use of the get_field_id() and get_field_name() function
	 * when creating your form elements. This handles the confusing stuff.
	 */
	function form( $instance ) {

		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Real Estate RSS Widget', REAL_ESTATE_RSS), 'url' => __('Insert RSS url', REAL_ESTATE_RSS), 'template' => 1, 'items' => 5 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<!-- Widget Title: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', REAL_ESTATE_RSS); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" class="widefat" />
		</p>

		<!-- Your Name: Text Input -->
		<p>
			<label for="<?php echo $this->get_field_id( 'url' ); ?>"><?php _e('URL:', REAL_ESTATE_RSS); ?></label>
			<input id="<?php echo $this->get_field_id( 'url' ); ?>" name="<?php echo $this->get_field_name( 'url' ); ?>" value="<?php echo $instance['url']; ?>" style="width:100%;" class="widefat" onfocus="if (this.value == 'Insert RSS url') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Insert RSS url';}" />
		</p>

		<!-- Sex: Select Box -->
		<p>
			<label for="<?php echo $this->get_field_id( 'template' ); ?>"><?php _e('Template:', REAL_ESTATE_RSS); ?></label> 
			<select id="<?php echo $this->get_field_id( 'template' ); ?>" name="<?php echo $this->get_field_name( 'template' ); ?>" class="widefat" style="width:100%;">
				<?php
				$templates = rerss_get_templates();
				foreach($templates as $template):
				if ($instance['template'] == "$template->template_id") $selected = ' selected="selected"';
				 	  else $selected = '';
				?>
				<option value="<?php echo $template->template_id;?>"<?php echo $selected;?>><?php echo $template->name;?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'items' ); ?>"><?php _e('Items:', REAL_ESTATE_RSS); ?></label> 
			
			<select id="<?php echo $this->get_field_id( 'items' ); ?>" name="<?php echo $this->get_field_name( 'items' ); ?>" class="widefat" style="width:100%;">
				<?php for($i = 0; $i <= 15; $i++): 
				     if ($instance['items'] == "$i") $selected = ' selected="selected"';
				 	  else $selected = ''; 
				?>
				<option value="<?php echo $i;?>"<?php echo $selected;?>><?php echo $i?></option>
				<?php endfor;?>
			</select>
		</p>

	<?php
	}
}
?>