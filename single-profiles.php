<?php 

	/*
	Template Name: Tech Profile Taxonomy
	*/

get_header(); ?>
<div id="" class="tech-profiles tech-profiles-individual">

	<?php
		$profile_nbn_email = get_post_meta($post->ID, 'profile_nbn_email', true);
		$profile_nbn_count = get_post_meta($post->ID, 'profile_nbn_count', true);
		$profile_att_title = get_post_meta($post->ID, 'profile_att_title', true);
		$profile_att_email = get_post_meta($post->ID, 'profile_att_email', true);
		$profile_att_phone = get_post_meta($post->ID, 'profile_att_phone', true);

		$profile_bio_name = get_post_meta($post->ID, 'profile_bio_name', true);
		$profile_bio_hometown = get_post_meta($post->ID, 'profile_bio_hometown', true);
		$profile_bio_college = get_post_meta($post->ID, 'profile_bio_college', true);
		$profile_bio_cert = get_post_meta($post->ID, 'profile_bio_cert', true);
		$profile_bio_fav = get_post_meta($post->ID, 'profile_bio_fav', true);
		$profile_bio_hobbies = get_post_meta($post->ID, 'profile_bio_hobbies', true);
		$profile_bio_role = get_post_meta($post->ID, 'profile_bio_role', true);
		$profile_bio_facts = get_post_meta($post->ID, 'profile_bio_facts', true);
		$profile_bio_advice = get_post_meta($post->ID, 'profile_bio_advice', true);

		$profile_bio_array = array(
			// array('Hometown',$profile_bio_hometown ),
			// array('College',$profile_bio_college ),
			// array('Certifications',$profile_bio_cert ),
			// array('Favorite Aspect of My Job',$profile_bio_fav ),
			// array('Hobbies',$profile_bio_hobbies ),
			// array('Role Model',$profile_bio_role ),
			// array('Interesting Fact About Me',$profile_bio_facts ),
			// array('Best Advice to Customers',$profile_bio_advice ),
			// );
			'Name' => $profile_bio_name,
			'Hometown' => $profile_bio_hometown,
			'College' => $profile_bio_college,
			'Certifications' => $profile_bio_cert,
			'Favorite Aspect of My Job' => $profile_bio_fav,
			'Hobbies' => $profile_bio_hobbies,
			'Role Model' => $profile_bio_role,
			'Interesting Fact About Me' => $profile_bio_facts,
			'Best Advice to Customers' => $profile_bio_advice,
		);

		$cert_images_one = get_post_meta($post->ID, 'cert_images_one', true);
		$cert_images_two = get_post_meta($post->ID, 'cert_images_two', true);
		$cert_images_three = get_post_meta($post->ID, 'cert_images_three', true);
		$cert_images_four = get_post_meta($post->ID, 'cert_images_four', true);
	?>		
	
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>	
		
			<div class="tech-profile" id="profile-<?php the_ID(); ?>">	
				<div class="tech-info">
				    <div class="tech-profile-image-wrapper">
						<?php	
						// Default, blog-size thumbnail
						if(has_post_thumbnail()) {                    
							$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(),'full' );
							 echo '<span class="tech-profile-image" style="background-image:url(' . $image_src[0]  . ')" />';
						} ?>
					</div>
				
					<div id="tech-bio">
						<div id="profile-title" class="grid">
							<div class="profile-title-container">
								<h2 class="tech-name"><?php the_title(); ?></h2>
								<?php if (get_post_meta($post->ID, 'profile_att_title', true)) : ?>
									<h3 class="tech-title"><?php echo( $profile_att_title ); ?></h3>
								<?php endif; ?>
							</div>
							<div class="profile-nn-stats-container"> 
								<?php if ($profile_nbn_email) : ?>
									<p><span id="profile-tech-rating"></span><span>Rating</span></p>
									<p><span id="profile-tech-reviews-num"></span><span>Reviews</span></p>
								<?php endif; ?>
							</div>
						</div>

						<?php /* <div id="certification" class="grid">
							<div class="col-1-1">
								<ul>
									<?php if (get_post_meta($post->ID, 'cert_images_one', true)) { ?>
									<li>
										<img src="<?php echo( $cert_images_one ); ?>" alt="Certification Image 1" />
									</li>
									<?php } ?>		
									<?php if (get_post_meta($post->ID, 'cert_images_two', true)) { ?>
									<li>								
										<img src="<?php echo( $cert_images_two ); ?>" alt="Certification Image 2" />
									</li>
									<?php } ?>		
									<?php if (get_post_meta($post->ID, 'cert_images_three', true)) { ?>
									<li>																		
										<img src="<?php echo( $cert_images_three ); ?>" alt="Certification Image 3" />
									</li>
									<?php } ?>	
									<?php if (get_post_meta($post->ID, 'cert_images_four', true)) { ?>
									<li>																		
										<img src="<?php echo( $cert_images_four ); ?>" alt="Certification Image 4" />
									</li>
									<?php } ?>
									<div class="clear"></div>
								</ul>	
							</div>
						</div> */ ?>
						<?php if ($profile_att_email && $profile_att_phone) : ?>
						    <div class="tech-contact-info">
								<?php if ($profile_att_email) : ?>
									<p><strong>Contact Email:</strong> <?php echo( $profile_att_email ); ?></p>
								<?php endif; ?>	
								<?php if ($profile_att_phone) : ?>
									<p><strong>Phone Number:</strong> <?php echo( $profile_att_phone ); ?></p>
								<?php endif; ?>	
							</div>
						<?php endif; ?>

						<div class="entry">
							<?php the_excerpt(); ?>
						</div>

						<div class="tech-bio-atts">
						<?php foreach ($profile_bio_array as $bio_item_name => $bio_item_value ) {
							if( $bio_item_value ) {
								echo '<p><span class="bio-att-name"><strong>'.$bio_item_name.':</strong></span> <span class="bio-att-value">'.$bio_item_value.'</span></p>';
							}
						} ?>
						</div>
						
					</div>
				</div>
				
				<?php if($profile_nbn_email) : ?>
					<div class="nearby-now">
						<?php if (get_post_meta($post->ID, 'profile_nbn_email', true)) { ?>	
							<?php echo do_shortcode('[serviceareareviewcombo showmap="no" techemail="'.$profile_nbn_email.'" count="'.$profile_nbn_count.'"]'); ?>
						<?php } ?>
					</div>
				<?php endif; ?>
			</div><!-- .post -->
		<?php endwhile;
		endif; ?>
			
</div><!-- #tech-content -->
<script>
(function() {
	const holder = document.querySelector('.nn-review-inner-cont > div');
	if(!holder) {
		return;
	}
	const nnRating = parseFloat(holder.children[1].innerText);
	const nnReviewCount = holder.children[2].innerText;
	if (!nnReviewCount) {
		document.querySelector('.profile-nn-stats-container').remove();
	}
	// $('#profile-tech-rating').text(parseFloat(nnRating).toFixed(1));
	document.querySelector('#profile-tech-rating').innerText = (Math.floor(nnRating * 10) / 10).toFixed(1);
	document.querySelector('#profile-tech-reviews-num').innerText = nnReviewCount;
})();
</script>

<?php get_footer(); ?>
