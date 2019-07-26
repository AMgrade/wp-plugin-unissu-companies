<?php

function uwc_load_widget()
{
	register_widget('uwc_widget');
}

add_action('widgets_init', 'uwc_load_widget');

class uwc_widget extends WP_Widget
{

	function __construct()
	{
		parent::__construct(

			'uwc_widget',

			__('Unissu Companies Widget', 'uwc_widget_domain'),

			array('description' => __('Set number of companies to display in the widget', 'uwc_widget_domain'),)
		);
	}

	public function widget($args, $instance)
	{
		global $post;

		$token_un = 'TOKEN_KEY';
		$title = apply_filters('widget_title', $instance['title']);
		$count = $instance['number_c'];

		/*
		 * First API call - GET all Solutions
		 */
		$ch_sol = curl_init();
		curl_setopt($ch_sol, CURLOPT_URL, 'https://api.unissu.com/api/v1/solutions/');
		curl_setopt($ch_sol, CURLOPT_HTTPHEADER, array(
			'Authorization: '. $token_un . ''
		));
		curl_setopt($ch_sol, CURLOPT_RETURNTRANSFER, true);
		$result_sol = curl_exec($ch_sol);
		curl_close($ch_sol);

		$array_sol = json_decode($result_sol, true);
		$arr_res_sol = $array_sol['results']; //Array os Solutions
		$tags = wp_get_post_tags( $post->ID, array( 'fields' => 'names' ) ); //Array of post tags
		$new_arr_sol = array();

		foreach ($arr_res_sol as $res_sol) {
			foreach ($tags as $tag) {
				if(in_array($tag, $res_sol,true)) {
					$new_arr_sol['solutions_ids'] .= $res_sol['id'] . ',';
				}
			}
		}

		/*
		 * Second API call - GET filtering companies
		 */
		$ch_company = curl_init();
		curl_setopt($ch_company, CURLOPT_URL, 'https://api.unissu.com/api/v1/vendors/?technology=' . $new_arr_sol['solutions_ids'] .'');
		curl_setopt($ch_company, CURLOPT_HTTPHEADER, array(
			'Authorization: '. $token_un . ''
		));
		curl_setopt($ch_company, CURLOPT_RETURNTRANSFER, true);
		$result = curl_exec($ch_company);
		curl_close($ch_company);

		$array = json_decode($result, true);
		shuffle($array['results']); //Random companies array
		$arr_res = array_slice($array['results'], 0, $count); //Set limit to companies display

		echo $args['before_widget'];
		if ( !empty($title) && $new_arr_sol['solutions_ids'] !== null ) {
			echo $args['before_title'] . $title . $args['after_title'];
			echo '<div class="company-list">';
			foreach ($arr_res as $res) {
				$name = $res['name'];
				$logo = $res['logo_thumbnails'][0]['image'];
				$des = $res['description']; ?>

				<div class="company-list__item">
					<div class="company-list__item--logo">
						<?php if($logo) { ?>
							<img src="<?php echo $logo; ?>" alt="<?php echo $name . ' logo'; ?>">
						<?php } else {
							echo 'No image';
						} ?>

					</div>
					<div class="company-list__item--des">
						<div class="name"><?php echo $name; ?></div>
						<div class="des"><?php echo substr($des, 0, 100); ?></div>
					</div>
				</div>

			<?php }
			echo '</div>';
		} else {
			echo $args['before_title'] . $title . $args['after_title'];
			echo 'No company found';
		}
		echo $args['after_widget'];
	}

	public function form($instance)
	{

		if (isset($instance['title'])) {
			$title = $instance['title'];
		} else {
			$title = __('New title', 'uwc_widget_domain');
		}

		if (isset($instance['number_c'])) {
			$number_c = $instance['number_c'];
		} else {
			$number_c = __('3', 'uwc_widget_domain');
		}

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
				   name="<?php echo $this->get_field_name('title'); ?>" type="text"
				   value="<?php echo esc_attr($title); ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number_c'); ?>"><?php _e('Companies count:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('number_c'); ?>"
				   name="<?php echo $this->get_field_name('number_c'); ?>" type="number"
				   value="<?php echo esc_attr($number_c); ?>"/>
		</p>
		<?php
	}

	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title']		= (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
		$instance['number_c'] 	= (!empty($new_instance['number_c'])) ? $new_instance['number_c'] : '';
		return $instance;
	}
}