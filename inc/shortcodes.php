<?php

	/**
	 * Clipit shortcode
	*/ 
	// [tech_pro post_id="" show_excerpt="" show_name="" show_title="" show_img="" show_email="" show_phone=""]	
	add_shortcode('tech_pro', 'shortcode_tech_pro');
	function shortcode_tech_pro($atts) {
		global $data;
		extract(shortcode_atts(array(
			'post_type' => 'profiles',
			'post_id' => '',
			'show_excerpt' => 'yes',
			'show_link' => 'yes',
			'show_img' => 'yes',
			'show_name' => 'yes',
			'show_title' => 'yes',
			'show_email' => 'yes',
			'profile_view' => 'vertical',
			'show_phone' => 'yes'
		), $atts));

		$html = '';
		wp_reset_query();
		$html .='<div class="tech-profile">';
		global $post; // Create and run custom loop 

		// WP_Query arguments
		$args = array (
			'p'         => $post_id,
			'post_type' => 'profiles',
		);

		// The Query
		$custom_posts = new WP_Query( $args );
		
		while ($custom_posts->have_posts()) : $custom_posts->the_post();
		
		$profile_att_title = get_post_meta($post->ID, 'profile_att_title', true);
		$profile_att_email = get_post_meta($post->ID, 'profile_att_email', true);
		$profile_att_phone = get_post_meta($post->ID, 'profile_att_phone', true);
		$profile_bio_name  = get_post_meta($post->ID, 'profile_bio_name', true);		

	
		if ($profile_view == 'vertical') {
			$html .='<div class="vert-profile">';
		}
		if ($profile_view == 'horizontal') {
			$html .='<div class="horiz-profile">';
		}
			if ($show_img == 'yes') {
			$html .= '<div class="tech-image-container">';	
				// Default, blog-size thumbnail
				if(has_post_thumbnail()) {                    
					$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' );
					 $html .='<a href="'. get_permalink() .'"><img alt="'.get_post_field('post_excerpt', get_post_thumbnail_id(get_the_ID())).'" id="image-slide" src="' . $image_src[0]  . '" style="height:auto; width:100%; margin:0; display:block;" class="img-responsive" /></a>';
				}
			$html .='</div>';
			}
			$html .='<div class="tech-desc">';
				$html .='<div class="tech-author">';
					$html .='<div class="tech-auth-wrapper">';
						$html .='<div class="person-name"><a href="'. get_permalink() .'">'. $profile_bio_name .'</a></div>';
						$html .='<div class="person-title">'. $profile_att_title .'</div>';					
					$html .='</div>';
					$html .='<div class="tech-auth-contact">';
						if (get_post_meta($post->ID, 'profile_att_email', true) || get_post_meta($post->ID, 'profile_att_phone', true)) {
							$html .='<ul>';
								if (get_post_meta($post->ID, 'profile_att_email', true) && $show_email == 'yes') {
								$html .='<li>';
									$html .='<div><a href="mailto:'. $profile_att_email .'">'. $profile_att_email .'</a></div>';
								$html .='</li>';
								}
								if (get_post_meta($post->ID, 'profile_att_phone', true) && $show_phone == 'yes') {
								$html .='<li>';
									$html .='<div><a href="tel:'. $profile_att_phone .'">'. $profile_att_phone .'</a></div>';
								$html .='</li>';
								}	
							$html .='</ul>';
						}							
					$html .='</div>';
					$html .='<div class="clear"></div>';
				$html .='</div>';
                                $html .='<div class="clear"></div>';
				if ($show_excerpt == 'yes') { 
				$html .='<div class="tech-content">'. do_shortcode(get_the_excerpt()). '</div>';
				}
                                $html .='<div class="clear"></div>'; 
				if ($show_link == 'yes') { 
				$html .='<a href="'. get_permalink( $post->ID ) .'">Read More...</a>';
				}
			$html .='</div>';
			$html .='</div>';
		$html .='</div>';
		endwhile;
		wp_reset_query();
		return $html;
	}	
?>