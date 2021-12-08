<article id="post-<?php the_ID();?>">
  <div class="tech-container" style="margin-bottom: 3em">
    <div class="image-frame">
      <?php
// Default, blog-size thumbnail
if (has_post_thumbnail()) {
  $image_src = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
  $template = strtolower(wp_get_theme()->template);
  if ($template == 'hypercore') {
    echo '<a class="hyper-lazyload-bg" href="' . get_permalink() . '" data-bg-image="' . $image_src[0] . '">';
  } else {
    echo '<a href="' . get_permalink() . '" style="background-image: url(' . $image_src[0] . ')">';
  }
  echo $sprite ? '<span class="profile-image-overlay" style="background-image:url(' . $image_src[0] . ')"></span>' : null;
  echo '</a>';
}?>
    </div>
    <div class="caption-container">
      <?php
$profile_att_title = get_post_meta($post->ID, 'profile_att_title', true);
$profile_bio_name = get_post_meta($post->ID, 'profile_bio_name', true);
?>
      <div class="caption"><a href="<?php the_permalink();?>"><?php echo ($profile_bio_name); ?><br />
          <?php echo ($profile_att_title); ?></a></div>
    </div>
  </div><!-- .tech-container -->
</article><!-- #post -->