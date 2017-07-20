<?php
/**
 *
 * The Tech Profiles Page 
 *
 */

get_header();
$options = get_option( 'lnb-tech-pro-options', true );
?>
		<div class="tech-profile content-area tech-profiles-category profile-category <?php echo $options['sprite'] ? 'tech-profiles-sprites' : ''; ?>">
			<?php if (have_posts()) : while (have_posts()) : the_post(); ?>	
			
				<profile id="post-<?php the_ID(); ?>">
					<div class="tech-container">	
						<div class="image-frame">
						<?php	
							// Default, blog-size thumbnail
							if(has_post_thumbnail()) {                    
								$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' );
								 echo '<a href="'.get_permalink().'" style="background-image:url('.$image_src[0].')">';
								 echo $options['sprite'] ? '<span class="profile-image-overlay" style="background-image:url('.$image_src[0].')"></span>' : null;
								 echo '</a>';
							} ?>
						</div>	
						<div class="caption-container">
							<?php
								$profile_att_title = get_post_meta($post->ID, 'profile_att_title', true);
								$profile_bio_name = get_post_meta($post->ID, 'profile_bio_name', true);
							?>
							<div class="caption"><a href="<?php the_permalink(); ?>"><?php echo( $profile_bio_name ); ?><br/>
							<?php echo( $profile_att_title ); ?></a></div>	
						</div>
					</div><!-- .tech-container -->
				</profile><!-- #post -->
			<?php endwhile; endif; ?>
		</div><!-- #tech-profiles -->
<?php get_footer(); ?>