<?php
/*
Plugin Name: Tech Profiles
Plugin URI: http://leadsnearby.com
Description: Creates Tech Profiles with Nearby Now Plugin capability.
Version: 1.5.0
Author: Leads Nearby
Author URI: http://leadsnearby.com
License: GPLv2
*/

function techprofile_js_scripts()
{
	/* Register our script. */
   if (is_admin()) {
		wp_enqueue_media();
		wp_register_script('tech-commons', plugins_url('/lnb-tech-pro/js/commons.js'));
		wp_enqueue_script('tech-commons');
        
		/* Register Admin Styles */
        wp_register_style('tech-admin-styles', plugins_url('/lnb-tech-pro/css/tech-admin-styles.css'));
		wp_enqueue_style('tech-admin-styles');		
   }
}
add_action('admin_enqueue_scripts', 'techprofile_js_scripts');

function techprofile_css_styles()  
{ 
	if (!is_admin()) {
		wp_register_style('tech-styles', plugins_url('/lnb-tech-pro/css/tech-styles.min.css'));
		wp_enqueue_style('tech-styles');
	}		
}
add_action('wp_enqueue_scripts', 'techprofile_css_styles');

add_filter('the_excerpt', 'do_shortcode');

// Register post types: teams
add_action('init', 'tech_profile');
function tech_profile() {
	
	$labels = array(
		'name'               => _x( 'Tech Profiles', 'post type general name' ),
		'singular_name'      => _x( 'Tech Profiles', 'post type singular name' ),
		'add_new'            => _x( 'Add New', 'Profiles' ),
		'add_new_item'       => __( 'Add New Tech Profile' ),
		'edit_item'          => __( 'Edit Tech Profile' ),
		'new_item'           => __( 'New Tech Profile' ),
		'all_items'          => __( 'All Tech Profiles' ),
		'view_item'          => __( 'View Tech Profiles' ),
		'search_items'       => __( 'Search Profiles' ),
		'not_found'          => __( 'No Tech Profiles found' ),
		'not_found_in_trash' => __( 'No Tech Profiles found in the Trash' ), 
		'parent_item_colon'  => '',
		'menu_name'          => 'Tech Profiles'
	);	
	
	register_post_type(
		'profiles',
		array(
			'labels' => $labels,
			'public' => true,
			'show_in_nav_menus' => false,
			'menu_position' => 15,
			'menu_icon' => 'dashicons-groups',
			'supports' => array(
				'title',
				'thumbnail',
				'revisions',
			),
			'taxonomies' => array(  
				'post_tag',  
			),
			'show_ui' => true,
			'show_in_menu' => true,
			'publicly_queryable' => true,
			'has_archive' => true,
			'query_var' => true,
			'can_export' => true,
			'rewrite' => array('slug' => 'meet-the-team',),
			'capability_type' => 'post',
			'show_in_rest' => true, 				
		)
	);
	flush_rewrite_rules();
	register_taxonomy_for_object_type('tech', 'profiles');	
	
}

	// Build taxonomies for each post type
	add_action( 'init', 'tech_taxonomies');
	function tech_taxonomies() {   
		register_taxonomy(
		    'profiles_category',
			'profiles',    
			array(
				'public' => true,
				'show_in_nav_menus' => true,
				'hierarchical' => true,
				'label' => 'Profile Categories',
				'query_var' => true,
				'rewrite' => true 
			)  
		);
	}	

add_action("admin_init", "meta_box");
function meta_box(){
	
	//Profile
	add_meta_box("profile_bio", "Profile Bio", "profile_bio", "profiles", "normal", "high");
	add_meta_box("cert_images", "Certification Images - <em class='small'>The certification images should be no more than 250px wide</em>", "cert_images", "profiles", "normal", "high");
	add_meta_box("profile_attributes", "Profile Attributes", "profile_attributes", "profiles", "normal", "high");	
	add_meta_box("profile_nbn", "Nearby Now Attributes", "profile_nbn", "profiles", "normal", "high");	
	add_meta_box('postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', 'profiles', 'normal', 'high');
}

function profile_bio(){
	global $post;
	$custom = get_post_custom($post->ID);
	$profile_bio_name = $custom["profile_bio_name"][0];
	$profile_bio_hometown = $custom["profile_bio_hometown"][0];
	$profile_bio_college = $custom["profile_bio_college"][0];
	$profile_bio_cert = $custom["profile_bio_cert"][0];
	$profile_bio_fav = $custom["profile_bio_fav"][0];
	$profile_bio_hobbies = $custom["profile_bio_hobbies"][0];
	$profile_bio_role = $custom["profile_bio_role"][0];
	$profile_bio_facts = $custom["profile_bio_facts"][0];
	$profile_bio_advice = $custom["profile_bio_advice"][0];
	?>
	<p><label>Tech Name</label><br />
	<input class="profile-bio" type="text" size="" name="profile_bio_name" value="<?php echo $profile_bio_name; ?>" /> 
	</p>
	<p><label>Tech Hometown</label><br />
	<input class="profile-bio" type="text" size="" name="profile_bio_hometown" value="<?php echo $profile_bio_hometown; ?>" /> 
	</p>
	<p><label>Tech College</label><br />
	<input class="profile-bio" type="text" size="" name="profile_bio_college" value="<?php echo $profile_bio_college; ?>" /> 
	</p>
	<p><label>Tech Certifications</label><br />
	<em>Separate certifications with a comma</em>
	<input class="profile-bio" type="text" size="" name="profile_bio_cert" value="<?php echo $profile_bio_cert; ?>" /> 
	</p>
	<p><label>Tech Favorite Aspect of my job:</label><br />
	<input class="profile-bio" type="text" size="" name="profile_bio_fav" value="<?php echo $profile_bio_fav; ?>" /> 
	</p>
	<p><label>Tech Hobbies</label><br />
	<input class="profile-bio" type="text" size="" name="profile_bio_hobbies" value="<?php echo $profile_bio_hobbies; ?>" /> 
	</p>
	<p><label>Tech Role Model</label><br />
	<input class="profile-bio" type="text" size="" name="profile_bio_role" value="<?php echo $profile_bio_role; ?>" /> 
	</p>
	<p><label>Tech Interesting Fact about me</label><br />
	<input class="profile-bio" type="text" size="" name="profile_bio_facts" value="<?php echo $profile_bio_facts; ?>" /> 
	</p>
	<p><label>Tech Best Advice to customers</label><br />
	<input class="profile-bio" type="text" size="" name="profile_bio_advice" value="<?php echo $profile_bio_advice; ?>" /> 
	</p>
	<?php
}

function profile_attributes(){
	global $post;
	$custom = get_post_custom($post->ID);
	$profile_att_title = $custom["profile_att_title"][0];
	$profile_att_email = $custom["profile_att_email"][0];
	$profile_att_phone = $custom["profile_att_phone"][0];
	?>
	<p><label>Tech Title</label><br />
	<input class="profile-bio" type="text" size="" name="profile_att_title" value="<?php echo $profile_att_title; ?>" /> 
	</p>
	<p><label>Tech Contact Email Address</label><br />
	<input class="profile-bio" type="text" size="" name="profile_att_email" value="<?php echo $profile_att_email; ?>" /> 
	</p>
	<p><label>Tech Phone Number</label><br />
	<input class="profile-bio" type="text" size="" name="profile_att_phone" value="<?php echo $profile_att_phone; ?>" /> 
	</p>
	<?php
	
}

function profile_nbn(){
	global $post;
	$custom = get_post_custom($post->ID);
	$profile_nbn_email = $custom["profile_nbn_email"][0];
	$profile_nbn_count = $custom["profile_nbn_count"][0];
	
	/**
	 * Detect plugin. For use in Admin area only.
	 */
	if ( is_plugin_active( 'nn-reviews/nn-reviews.php' ) ) { ?>	
	<p><label>Nearby Now Email Address</label><br />
	<input class="profile-bio" type="text" size="" name="profile_nbn_email" value="<?php echo $profile_nbn_email; ?>" /> 
	</p>
	<p><label>Nearby Now Review Count</label><br />
	<input class="profile-bio" type="text" size="20" name="profile_nbn_count" value="<?php echo $profile_nbn_count; ?>" /> 
	</p>
	<?php }else {?>
	<p style="color:#ff0000;"><strong>NN REVIEWS PLUGIN NOT ACTIVE:</strong> Please active the NN Reviews Plugin to display these fields.</p>    
	<?php }?>
	<?php	
}

function cert_images() {
	global $post;
	$custom = get_post_custom($post->ID);
	$cert_images_one = $custom["cert_images_one"][0];
	$cert_images_two = $custom["cert_images_two"][0];
	$cert_images_three = $custom["cert_images_three"][0];
	$cert_images_four = $custom["cert_images_four"][0];
	?>
	<p><label>Certification Image 1:</label><br />
	<input class="upload_image wp-media-buttons profile-bio" type="text" size="" name="cert_images_one" value="<?php echo $cert_images_one; ?>" /> 
	<span class="wp-media-buttons"><a title="Add Media" data-editor="cert_images_one" class="button upload_image_button add_media" href="#"><span class="wp-media-buttons-icon"></span> Add Media</a></span>
	<div class="clear"></div>
	</p>
	<p><label>Certification Image 2:</label><br />
	<input class="upload_image wp-media-buttons profile-bio" type="text" size="" name="cert_images_two" value="<?php echo $cert_images_two; ?>" />
	<span class="wp-media-buttons"><a title="Add Media" data-editor="cert_images_two" class="button upload_image_button add_media" href="#"><span class="wp-media-buttons-icon"></span> Add Media</a></span>
	<div class="clear"></div>
	</p>
	<p><label>Certification Image 3:</label><br />
	<input class="upload_image wp-media-buttons profile-bio" type="text" size="" name="cert_images_three" value="<?php echo $cert_images_three; ?>" />
	<span class="wp-media-buttons"><a title="Add Media" data-editor="cert_images_three" class="button upload_image_button add_media" href="#"><span class="wp-media-buttons-icon"></span> Add Media</a></span>
	<div class="clear"></div>
	</p>
	<p><label>Certification Image 4:</label><br />
	<input class="upload_image wp-media-buttons profile-bio" type="text" size="" name="cert_images_four" value="<?php echo $cert_images_four; ?>" />
	<span class="wp-media-buttons"><a title="Add Media" data-editor="cert_images_four" class="button upload_image_button add_media" href="#"><span class="wp-media-buttons-icon"></span> Add Media</a></span>
	<div class="clear"></div>
	</p>
	<?php
}

add_action('save_post', 'save_meta');
function save_meta(){
	global $post;
	update_post_meta($post->ID, "profile_nbn_email", $_POST["profile_nbn_email"]);
	update_post_meta($post->ID, "profile_nbn_count", $_POST["profile_nbn_count"]);
	update_post_meta($post->ID, "profile_att_title", $_POST["profile_att_title"]);
	update_post_meta($post->ID, "profile_att_email", $_POST["profile_att_email"]);
	update_post_meta($post->ID, "profile_att_phone", $_POST["profile_att_phone"]);
	update_post_meta($post->ID, "profile_bio_name", $_POST["profile_bio_name"]);
	update_post_meta($post->ID, "profile_bio_hometown", $_POST["profile_bio_hometown"]);
	update_post_meta($post->ID, "profile_bio_college", $_POST["profile_bio_college"]);
	update_post_meta($post->ID, "profile_bio_cert", $_POST["profile_bio_cert"]);
	update_post_meta($post->ID, "profile_bio_fav", $_POST["profile_bio_fav"]);
	update_post_meta($post->ID, "profile_bio_hobbies", $_POST["profile_bio_hobbies"]);
	update_post_meta($post->ID, "profile_bio_role", $_POST["profile_bio_role"]);
	update_post_meta($post->ID, "profile_bio_facts", $_POST["profile_bio_facts"]);
	update_post_meta($post->ID, "profile_bio_advice", $_POST["profile_bio_advice"]);
	update_post_meta($post->ID, "cert_images_one", $_POST["cert_images_one"]);
	update_post_meta($post->ID, "cert_images_two", $_POST["cert_images_two"]);
	update_post_meta($post->ID, "cert_images_three", $_POST["cert_images_three"]);
	update_post_meta($post->ID, "cert_images_four", $_POST["cert_images_four"]);
}

add_action( 'admin_menu' , 'remove_tech_excerpt_fields' );
function remove_tech_excerpt_fields() {
	remove_meta_box( 'postexcerpt', 'post', 'normal' );
}


// Template Functions
	// Adds Template files when plugin is activated.
	add_filter( 'template_include', 'profiles_template_function', 1 );
	function profiles_template_function( $template_path ) {
		if ( get_post_type() == 'profiles' ) {			
			if ( is_single() ) {
				// checks if the file exists in the theme first,
				// otherwise serve the file from the plugin
				if ( $theme_file = locate_template( array ( 'single-profiles.php' ) ) ) {
					$template_path = $theme_file;
				} else {
					$template_path = plugin_dir_path( __FILE__ ) . 'single-profiles.php';
				}
			}
			if ( is_archive() ) {
				// checks if the file exists in the theme first,
				// otherwise serve the file from the plugin
				if ( $theme_file = locate_template( array ( 'taxonomy-profiles.php' ) ) ) {
					$template_path = $theme_file;
				} else {
					$template_path = plugin_dir_path( __FILE__ ) . 'taxonomy-profiles.php';
				}
			}			
		}
		return $template_path;
	}
	
	//Load Additional Files
	define('TechPro_MAIN', plugin_dir_path( __FILE__ ));
	
	// Load Custom Shortcodes
	require_once(TechPro_MAIN . '/shortcode.php');

	require_once(TechPro_MAIN . '/class-nn-tech-api.php');

add_filter('rest_api_init', 'test_tech_profiles_api_fields');

function test_tech_profiles_api_fields() {
	register_rest_field( 'profiles',
   'info',
   array(
      'get_callback'    => 'test_profiles_api_fields_cb',
   )
	);
}

function test_profiles_api_fields_cb($object, $field, $request) {
	$tech_review_data = NN_Tech_API::get_nn_data();
	$response['name'] = get_post_meta( $object['id'], 'profile_bio_name', true);
	$response['image'] = get_the_post_thumbnail_url( $object['id'] );
	$response['hometown'] = get_post_meta( $object['id'], 'profile_bio_hometown', true);
	$response['college'] = get_post_meta( $object['id'], 'profile_bio_college', true);
	$response['certifications'] = get_post_meta( $object['id'], 'profile_bio_cert', true);
	$response['certificationImages'] = array(
		get_post_meta( $object['id'], 'cert_images_one', true) ? get_post_meta( $object['id'], 'cert_images_one', true) : null,
		get_post_meta( $object['id'], 'cert_images_two', true) ? get_post_meta( $object['id'], 'cert_images_two', true) : null,
		get_post_meta( $object['id'], 'cert_images_three', true) ? get_post_meta( $object['id'], 'cert_images_three', true) : null,
		get_post_meta( $object['id'], 'cert_images_four', true) ? get_post_meta( $object['id'], 'cert_images_four', true) : null,
		);
	$response['favoriteAspect'] = get_post_meta( $object['id'], 'profile_bio_fav', true);
	$response['hobbies'] = get_post_meta( $object['id'], 'profile_bio_hobbies', true);
	$response['roleModel'] = get_post_meta( $object['id'], 'profile_bio_role', true);
	$response['interestingFact'] = get_post_meta( $object['id'], 'profile_bio_facts', true);
	$response['bestAdvice'] = get_post_meta( $object['id'], 'profile_bio_advice', true);
	$response['bio'] = get_the_excerpt( $object['id'] );
	$response['reviews'] = $tech_review_data[$object['slug']];
	return $response;
}

?>