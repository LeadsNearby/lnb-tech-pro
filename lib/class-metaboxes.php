<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'LeadsNearby_Tech_Profiles_Metaboxes' ) ) :

	class LeadsNearby_Tech_Profiles_Metaboxes {

		protected static $instance;
		public static $meta_key = 'tech_profile_meta';

		private function __construct() { }
		private function __clone() { }
		private function __wakeup() { }

		public static function getInstance() {

			if( ! isset( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;

		}

		public function init() {

			add_action( 'add_meta_boxes', [ $this, 'add_custom_meta_boxes' ] );

		}

		public function add_custom_meta_boxes() {

			add_meta_box(
        		self::$meta_key,
        		'Tech Information',
        		array( $this, 'display_meta_boxes' ),
        		'profiles',
        		'normal',
        		'high'
    		);

		}

		public function display_meta_boxes( $post, $metabox ) {

			wp_nonce_field( plugin_basename(__FILE__), self::$meta_key );

			ob_start(); ?>

			<div id='tech-profiles-meta-tabs' class="lnb-tabs">
				<div class="tab-nav-group">
					<a class="dashicons-before dashicons-businessman tab-nav-item active" href="#tech-profiles-meta-tabs-about">About Tech</a></li>
					<a class="dashicons-before dashicons-awards tab-nav-item" href="#tech-profiles-meta-tabs-certifications">Certifications</a>
				</div>
				<?php
					$options = LeadsNearby_Tech_Profiles::$options;
					$fields = get_post_meta( $post->ID, self::$meta_key, true );
				?>
				<div class="tabs-group">
					<div id="tech-profiles-meta-tabs-about" class="tab active">
						<?php foreach( $options['about'] as $option => $options ) { ?>
							<div class="tech-profiles-meta-field">
								<label for="tech_profile_meta_<?php echo $option; ?>"><?php echo $options['title']; ?></label>
								<input id="tech_profile_meta_<?php echo $option; ?>" type="text" class="profile-bio" name="tech_profile_meta[about][<?php echo $option; ?>]" >
							</div>
						<?php } ?>
						</pre>
					</div>
					<div id="tech-profiles-meta-tabs-certifications" class="tab">
						<pre>
							<?php print_r( $fields['certifications'] ); ?>
						</pre>
					</div>
				</div>
			</div>

			<?php echo ob_get_clean();

		}

		public function save_custom_metadata( $id ) {

			// Check the nonce to make sure it matches
		    if( ! wp_verify_nonce( $_POST[ self::$meta_key ], plugin_basename( __FILE__ ) ) ) {

		    	return $id;

		    }
	       
	       	// Make sure post isn't being autosaved
		   	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) {

		    	return $id;

		    }
			
			if( 'profiles' == $_POST['post_type'] ) { // Exit if it's not the right post type

		    	if( ! current_user_can( 'edit_page', $id ) ) {

		    		return $id;

		      	}

		    } else { // Else exit if current user doesn't have perms to edit page

		    	if( ! current_user_can( 'edit_page', $id ) ) {

		        	return $id;

		        }
		    }

		    if( ! empty( $_POST[ self::$meta_key ] ) ) {

		    	update_post_meta( $id, self::$meta_key , $_POST[ self::$meta_key ] );

		    }
	     
		}

	}

endif;

?>