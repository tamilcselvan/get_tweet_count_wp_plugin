<?php

add_shortcode( 'acs-twitter-milestone', 'acs_vc_milestone' );


function acs_vc_milestone( $atts, $content ) {
   extract( shortcode_atts( array(
	  'title' 		=> null,
	  'style' 		=> 'ewf-milestone-style-1',
	  'number' 		=> '1',
	  'icon' 		=> null,
	  'description' => null,
	  'symbol' 		=> null,
	  'speed' 		=> '2000',
	  'css' 		=> null
   ), $atts ));
 
	$number = intval($number);
	$class_extra = ' '.$css;
 
	ob_start();
	
	echo '<div class="milestone ' . esc_attr($class_extra) . '">';
		if ($icon){
			echo '<i class="' . esc_attr($icon) . '"></i>';
		}
	
		echo '<div class="milestone-content">';
			
			
			echo '<div class="milestone-description">';
				if ($title){
					echo $title;
				}
			echo '</div><!-- end .milestone-description -->';
				

			echo '<div class="milestone-value" data-speed="' . esc_attr($speed) . '" data-stop="' . esc_attr( ( int ) get_option ( 'acs_twitter_tweet_count' ) ) . '">';
			
			echo '</div>';
		echo '</div>';
	echo '</div>';
	
	return ob_get_clean();
}

add_action( 'vc_before_init', 'acs_twitter_integrateWithVC' );

function acs_twitter_integrateWithVC() {
	// function is exists
	if( function_exists('vc_map') ) {
		vc_map( array(
		   "name" => esc_html__("Twitter Milestone", 'quantum_wp'),
		   "base" => "acs-twitter-milestone",
		   "class" => "",
		   "icon" => "icon-wpb-ewf-milestone",
		   "description" => esc_html__("Shows milestones and numeric statistic with animated numbers", 'quantum_wp'),  
		   "category" => EWF_SETUP_VC_GROUP,
		   "params" => array(
			  array(
				 "type" => "textfield",
				 "holder" => "div",
				 "class" => "",
				 "heading" => esc_html__("Title", 'quantum_wp'),
				 "param_name" => "title",
				 "value" => null,
				 "description" => esc_html__("The milestone title", 'quantum_wp')
			  ),
			  array(
				 "type" => "textfield",
				 "holder" => "div",
				 "class" => "",
				 "heading" => esc_html__("Speed", 'quantum_wp'),
				 "param_name" => "speed",
				 "value" => 2000,
				 "description" => esc_html__("Specify the animation speed", 'quantum_wp')
			  ),
			  array( 
					"type" => "textfield", 
					"holder" => "div", 
					"class" => "", 
					"heading" => esc_html__("Extra CSS Class", 'quantum_wp'), 
					"param_name" => "css", 
					"value" => '', 
					"description" => esc_html__("Add and extra CSS class to the component", 'quantum_wp') 
				)
		   )
		));

	}
}
?>