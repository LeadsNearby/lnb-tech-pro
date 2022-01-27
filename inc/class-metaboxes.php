<?php

namespace lnb\techprofiles;

if (!defined('ABSPATH')) {
  exit;
}
// Exit if accessed directly

if (!class_exists('Metaboxes')):

  class Metaboxes {

    protected static $instance;
    public static $meta_key = 'tech_profile_meta';

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

    public static function getInstance() {

      if (!isset(self::$instance)) {

        self::$instance = new self();

      }

      return self::$instance;

    }

    public function init() {

      add_action('add_meta_boxes', [$this, 'add_custom_meta_boxes']);

    }

    public function add_custom_meta_boxes() {

      add_meta_box(
        self::$meta_key,
        'Tech Information',
        array($this, 'display_meta_boxes'),
        'profiles',
        'normal',
        'high'
      );

    }

    public function display_meta_boxes($post, $metabox) {

      $options = LeadsNearby_Tech_Profiles::$options;
      $fields = get_post_meta($post->ID, self::$meta_key, true);

      wp_nonce_field(plugin_basename(__FILE__), self::$meta_key);

      ob_start();?>

<div id='tech-profiles-meta-tabs' class="lnb-tabs">
  <div class="tab-nav-group">
    <a data-tab="1" class="dashicons-before dashicons-businessman tab-nav-item active"
      href="#tech-profiles-meta-tabs-about"><?php echo $options['about']['title']; ?></a></li>
    <a data-tab="2" class="dashicons-before dashicons-awards tab-nav-item"
      href="#tech-profiles-meta-tabs-certifications"><?php echo $options['certifications']['title']; ?></a>
    <?php if (is_plugin_active('nn-reviews/nn-reviews.php')) {?>
    <a data-tab="3" class="dashicons-before dashicons-star-filled tab-nav-item"
      href="#tech-profiles-meta-tabs-nn">Nearby Now</a>
    <?php }?>
  </div>
  <div class="tabs-group">
    <div data-tab="1" id="tech-profiles-meta-tabs-about" class="tab active">
      <?php foreach ($options['about']['fields'] as $i => $value) {?>
      <div class="tech-profiles-meta-field">
        <label for="tech_profile_meta_<?php echo $i; ?>"><?php echo $value['title']; ?></label>
        <?php if ($value['desc']) {?>
        <span><?php echo $value['desc']; ?></span>
        <?php }?>
        <?php if ($value['type'] && $value['type'] == 'textarea') {?>
        <textarea rows="8" id="tech_profile_meta_<?php echo $i; ?>" class="profile-bio"
          name="tech_profile_meta[about][<?php echo $i; ?>]"><?php echo $fields['about'][$i] ?></textarea>
        <?php } else {?>
        <input id="tech_profile_meta_<?php echo $i; ?>" type="text" class="profile-bio"
          name="tech_profile_meta[about][<?php echo $i; ?>]" value="<?php echo $fields['about'][$i] ?>">
        <?php }?>
      </div>
      <?php }?>
    </div>
    <div data-tab="2" id="tech-profiles-meta-tabs-certifications" class="tab">
      <div id="certification-container" class="tech-profiles-meta-field-group">
        <?php foreach ($fields['certifications'] as $i => $sub_options) {?>
        <div data-index="<?php echo $i; ?>" id="certification_<?php echo $i; ?>"
          class="tech-profiles-meta-field tech-profiles-meta-field-certification">
          <?php if ($i != 0) {?>
          <span data-index="<?php echo $i; ?>" class="delete-row dashicons dashicons-no-alt"></span>
          <?php }?>
          <input placeholder="Name of certification" type="text"
            name="tech_profile_meta[certifications][<?php echo $i; ?>][name]"
            value="<?php echo $fields['certifications'][$i]['name']; ?>">
          <input data-image="<?php echo $i; ?>" type="hidden"
            name="tech_profile_meta[certifications][<?php echo $i; ?>][image]"
            value="<?php echo $fields['certifications'][$i]['image']; ?>">
          <?php $image_placeholder = wp_get_attachment_image_src($fields['certifications'][$i]['image'], 'thumbnail');?>
          <img data-image-placeholder="<?php echo $i; ?>" src="<?php echo $image_placeholder[0]; ?>" />
          <a data-selector="<?php echo $i; ?>" class="upload_image_button" href="#">Select image</a>
        </div>
        <?php }?>
      </div>
      <a onclick="new_certification_container()" href="javascript:void(0)">A new certification</a>
    </div>
    <?php if (is_plugin_active('nn-reviews/nn-reviews.php')) {?>
    <div data-tab="3" id="tech-profiles-meta-tabs-nn" class="tab">
      <?php foreach ($options['nearby-now']['fields'] as $i => $value) {?>
      <div class="tech-profiles-meta-field">
        <label for="tech_profile_meta_<?php echo $i; ?>"><?php echo $value['title']; ?></label>
        <?php if ($value['desc']) {?>
        <span><?php echo $options['desc']; ?></span>
        <?php }?>
        <input id="tech_profile_meta_<?php echo $i; ?>" type="text" class="profile-bio"
          name="tech_profile_meta[nn][<?php echo $i; ?>]"
          value="<?php echo $fields['nn'][$i] ? $fields['nn'][$i] : ''; ?>">
      </div>
      <?php }?>
    </div>
    <?php }?>
  </div>
</div>

<?php echo ob_get_clean();

    }

    public function save_custom_metadata($id) {

      // Check the nonce to make sure it matches
      if (!wp_verify_nonce($_POST[self::$meta_key], plugin_basename(__FILE__))) {

        return $id;

      }

      // Make sure post isn't being autosaved
      if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

        return $id;

      }

      if ('profiles' == $_POST['post_type']) { // Exit if it's not the right post type

        if (!current_user_can('edit_page', $id)) {

          return $id;

        }

      } else { // Else exit if current user doesn't have perms to edit page

        if (!current_user_can('edit_page', $id)) {

          return $id;

        }
      }

      if (!empty($_POST[self::$meta_key])) {

        update_post_meta($id, self::$meta_key, $_POST[self::$meta_key]);

      }

    }

  }

endif;

?>