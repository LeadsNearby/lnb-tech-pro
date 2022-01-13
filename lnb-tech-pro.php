<?php
/*
Plugin Name: LeadsNearby Tech Profiles
Plugin URI: http://leadsnearby.com
Description: Creates Tech Profiles with Nearby Now Plugin capability.
Version: 2.0
Author: Leads Nearby
Author URI: http://leadsnearby.com
License: GPLv2
 */

namespace lnb\techprofiles;

//Load Additional Files
define('TechPro_MAIN', plugin_dir_path(__FILE__));

// // Load Custom Shortcodes
require_once TechPro_MAIN . '/inc/shortcodes.php';
require_once TechPro_MAIN . 'inc/class-nn-tech-api.php';

if (!class_exists('TechProfiles')) {

  class TechProfiles {

    public $post_type = 'profiles';
    public $options = array();
    private $styles;
    private $scripts;

    public function __construct() {

      $this->styles = array(
        array(
          'handle'  => 'tech-styles',
          'src'     => plugins_url('/dist/css/tech-styles.min.css', __FILE__),
          'deps'    => array(),
          'version' => null,
          'media'   => 'all',
        ),
        array(
          'handle'  => 'tech-admin-styles',
          'src'     => plugins_url('/dist/css/tech-admin-styles.min.css', __FILE__),
          'deps'    => array(),
          'version' => null,
          'media'   => 'all',
        ),
      );

      $this->scripts = array(
        array(
          'handle'  => 'tech-common-js',
          'src'     => plugins_url('/dist/js/commons-min.js', __FILE__),
          'deps'    => array(),
          'version' => null,
          'footer'  => true,
        ),
      );

      add_filter('the_excerpt', 'do_shortcode');

      // Register Styles Scripts
      add_action('wp_enqueue_scripts', [$this, 'register_styles']);
      add_action('wp_enqueue_scripts', [$this, 'register_scripts']);
      add_action('wp_enqueue_scripts', [$this, 'enqueue_public_styles']);

      // Register Settings
      add_action('admin_menu', [$this, 'create_settings_page']);
      add_action('admin_init', [$this, 'register_settings']);
      add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
      add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);

      // Register Tech Profiles Post Type
      add_action('init', [$this, 'tech_profile']);
      add_action('init', [$this, 'tech_taxonomies']);
      add_action('admin_init', [$this, 'meta_boxes']);
      add_action('save_post_profiles', [$this, 'save_meta']);
      add_action('admin_menu', [$this, 'remove_tech_excerpt_fields']);
      add_filter('template_include', [$this, 'profiles_template_function'], 1);

      //Add to Rest API
      add_filter('rest_api_init', [$this, 'lnb_tech_pro_api_fields']);

    }

    public function register_styles() {
      foreach ($this->styles as $style) {
        wp_register_style($style['handle'], $style['src'], $style['deps'], $style['version'], $style['media']);
      }
    }

    public function register_scripts() {
      foreach ($this->scripts as $script) {
        wp_register_script($script['handle'], $script['src'], $script['deps'], $script['version'], $script['footer']);
      }
    }

    public function enqueue_public_styles() {
      if (get_post_type() == $this->post_type) {
        wp_enqueue_style('tech-styles');
      }
    }

    public function enqueue_admin_styles($hook_suffix) {
      $cpt = 'profiles';
      if (in_array($hook_suffix, array('post.php', 'post-new.php'))) {
        $screen = get_current_screen();
        if (is_object($screen) && $cpt == $screen->post_type) {
          wp_enqueue_style('tech-admin-styles');
        }
      }
    }

    public function enqueue_admin_scripts($hook_suffix) {
      $cpt = 'profiles';
      if (in_array($hook_suffix, array('post.php', 'post-new.php'))) {
        $screen = get_current_screen();
        if (is_object($screen) && $cpt == $screen->post_type) {
          wp_enqueue_script('tech-common-js');
        }
      }
    }

    public function create_settings_page() {
      add_submenu_page(
        'edit.php?post_type=profiles',
        'Tech Profiles Settings',
        'Settings',
        'edit_posts',
        'tech-pro-settings',
        [$this, 'render_settings_page']
      );
      register_setting(
        'lnb-tech-pro-group',
        'lnb-tech-pro-options',
        [$this, 'sanitize_settings']
      );
    }

    public function sanitize_settings($data) {
      $data['slug'] = sanitize_title_with_dashes($data['slug']);
      return $data;
    }

    public function render_settings_page() {

      require_once plugin_dir_path(__FILE__) . '/inc/templates/admin-settings.php';

    }

    public function register_settings() {

      $data = get_option('lnb-tech-pro-options');

      add_settings_section(
        'section_general',
        'General Settings',
        [$this, 'render_settings_section'],
        'lnb-tech-pro-settings'
      );

      add_settings_field(
        'section_general_slug',
        'Tech Profiles Slug',
        [$this, 'render_text_input'],
        'lnb-tech-pro-settings',
        'section_general',
        array(
          'label_for'   => 'section_general_slug',
          'name'        => 'slug',
          'value'       => esc_attr($data['slug']),
          'option_name' => 'lnb-tech-pro-options',
          'desc'        => 'If nothing is specified, "profiles" will be used',
        )
      );

      add_settings_field(
        'section_general_sprite',
        'Using Sprites',
        [$this, 'render_checkbox_input'],
        'lnb-tech-pro-settings',
        'section_general',
        array(
          'label_for'   => 'section_general_sprite',
          'name'        => 'sprite',
          'value'       => esc_attr(isset($data['sprite']) ? $data['sprite'] : false),
          'option_name' => 'lnb-tech-pro-options',
        )
      );

    }

    public function render_settings_section() {
      return;
    }

    public function render_text_input($args) {
      printf('<input name="%1$s[%2$s]" id="%3$s" value="%4$s" class="regular-text"><p><em class="small">%5$s</em></p>',
        $args['option_name'],
        $args['name'],
        $args['label_for'],
        $args['value'],
        $args['desc']
      );
    }

    public function render_checkbox_input($args) {
      printf('<input type="checkbox" name="%1$s[%2$s]" id="%3$s" %4$s>',
        $args['option_name'],
        $args['name'],
        $args['label_for'],
        $args['value'] == 'on' ? 'checked' : ''
      );
    }

    public function tech_profile() {

      $labels = array(
        'name'               => _x('Tech Profiles', 'post type general name'),
        'singular_name'      => _x('Tech Profile', 'post type singular name'),
        'add_new'            => _x('Add New', 'Profiles'),
        'add_new_item'       => __('Add New Tech Profile'),
        'edit_item'          => __('Edit Tech Profile'),
        'new_item'           => __('New Tech Profile'),
        'all_items'          => __('All Tech Profiles'),
        'view_item'          => __('View Tech Profiles'),
        'search_items'       => __('Search Profiles'),
        'not_found'          => __('No Tech Profiles found'),
        'not_found_in_trash' => __('No Tech Profiles found in the Trash'),
        'parent_item_colon'  => '',
        'menu_name'          => 'Tech Profiles',
      );

      $options = get_option('lnb-tech-pro-options', true);

      register_post_type(
        'profiles',
        array(
          'labels'             => $labels,
          'public'             => true,
          'show_in_nav_menus'  => false,
          'menu_position'      => 15,
          'menu_icon'          => 'dashicons-groups',
          'supports'           => array(
            'title',
            'thumbnail',
            'revisions',
          ),
          'taxonomies'         => array(
            'post_tag',
          ),
          'show_ui'            => true,
          'show_in_menu'       => true,
          'publicly_queryable' => true,
          'has_archive'        => true,
          'query_var'          => true,
          'can_export'         => true,
          'rewrite'            => array('slug' => $options['slug'] ? $options['slug'] : 'profiles', 'with_front' => false),
          'capability_type'    => 'post',
          'show_in_rest'       => true,
        )
      );

      flush_rewrite_rules();

      register_taxonomy_for_object_type('tech', 'profiles');

    }

    public function meta_boxes() {
      add_meta_box('profile_bio', 'Profile Bio', [$this, 'profile_bio'], 'profiles', 'normal', 'high');
      add_meta_box('cert_images', 'Certification Images - <em class="small">The certification images should be no more than 250px wide</em>', [$this, 'cert_images'], 'profiles', 'normal', 'high');
      add_meta_box('profile_attributes', 'Profile Attributes', [$this, 'profile_attributes'], 'profiles', 'normal', 'high');
      add_meta_box('profile_nbn', 'Nearby Now Attributes', 'profile_nbn', [$this, 'profiles'], 'normal', 'high');
      add_meta_box('postexcerpt', __('Excerpt'), 'post_excerpt_meta_box', [$this, 'profiles'], 'normal', 'high');

    }

    public function tech_taxonomies() {
      register_taxonomy(
        'profiles_category',
        'profiles',
        array(
          'public'            => true,
          'show_in_nav_menus' => false,
          'hierarchical'      => false,
          'label'             => 'Profile Categories',
          'query_var'         => true,
        )
      );
    }

    public function profile_bio() {
      global $post;
      $custom = get_post_custom($post->ID);
      $profile_bio_name = $custom['profile_bio_name'][0];
      $profile_bio_hometown = $custom['profile_bio_hometown'][0];
      $profile_bio_college = $custom['profile_bio_college'][0];
      $profile_bio_cert = $custom['pr   ofile_bio_cert'][0];
      $profile_bio_fav = $custom['profile_bio_fav'][0];
      $profile_bio_hobbies = $custom['profile_bio_hobbies'][0];
      $profile_bio_role = $custom['profile_bio_role'][0];
      $profile_bio_facts = $custom['profile_bio_facts'][0];
      $profile_bio_advice = $custom['profile_bio_advice'][0];
      ?>
<p><label>Tech Name</label><br />
  <input class="profile-bio" type="text" size="" name="profile_bio_name" value="<?php echo $profile_bio_name; ?>" />
</p>
<p><label>Tech Hometown</label><br />
  <input class="profile-bio" type="text" size="" name="profile_bio_hometown"
    value="<?php echo $profile_bio_hometown; ?>" />
</p>
<p><label>Tech College</label><br />
  <input class="profile-bio" type="text" size="" name="profile_bio_college"
    value="<?php echo $profile_bio_college; ?>" />
</p>
<p><label>Tech Certifications</label><br />
  <em>Separate certifications with a comma</em>
  <input class="profile-bio" type="text" size="" name="profile_bio_cert" value="<?php echo $profile_bio_cert; ?>" />
</p>
<p><label>Tech Favorite Aspect of my job:</label><br />
  <input class="profile-bio" type="text" size="" name="profile_bio_fav" value="<?php echo $profile_bio_fav; ?>" />
</p>
<p><label>Tech Hobbies</label><br />
  <input class="profile-bio" type="text" size="" name="profile_bio_hobbies"
    value="<?php echo $profile_bio_hobbies; ?>" />
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

    public function profile_attributes() {
      global $post;
      $custom = get_post_custom($post->ID);
      $profile_att_title = $custom['profile_att_title'][0];
      $profile_att_email = $custom['profile_att_email'][0];
      $profile_att_phone = $custom['profile_att_phone'][0];

      ?> <p><label>Tech Title</label><br />
  <input class='profile-bio' type='text' size='' name='profile_att_title' value='<?php echo $profile_att_title; ?>' />
</p>
<p><label>Tech Contact Email Address</label><br />
  <input class='profile-bio' type='text' size='' name='profile_att_email' value='<?php echo $profile_att_email; ?>' />
</p>
<p><label>Tech Phone Number</label><br />
  <input class='profile-bio' type='text' size='' name='profile_att_phone' value='<?php echo $profile_att_phone; ?>' />
</p>
<?php
}

    public function profile_nbn() {
      global $post;
      $custom = get_post_custom($post->ID);
      $profile_nbn_email = $custom['profile_nbn_email'][0];
      $profile_nbn_count = $custom['profile_nbn_count'][0];

      /**
       * Detect plugin. For use in Admin area only.
       */
      if (is_plugin_active('nearby-now/main.php')) {?>
<p><label>Nearby Now Email Address</label><br />
  <input class='profile-bio' type='text' size='' name='profile_nbn_email' value='<?php echo $profile_nbn_email; ?>' />
</p>
<p><label>Nearby Now Review Count</label><br />
  <input class='profile-bio' type='text' size='20' name='profile_nbn_count' value='<?php echo $profile_nbn_count; ?>' />
</p>
<?php } else {?>
<p style='color:#ff0000;'><strong>NN REVIEWS PLUGIN NOT ACTIVE:</strong> Please active the NN Reviews Plugin to display
  these fields.</p>
<?php }?>
<?php
}

    public function cert_images() {
      global $post;
      $custom = get_post_custom($post->ID);
      $cert_images_one = $custom['cert_images_one'][0];
      $cert_images_two = $custom['cert_images_two'][0];
      $cert_images_three = $custom['cert_images_three'][0];
      $cert_images_four = $custom['cert_images_four'][0];
      ?>
<p><label>Certification Image 1:</label><br />
  <input class='upload_image wp-media-buttons profile-bio' type='text' size='' name='cert_images_one'
    value='<?php echo $cert_images_one; ?>' />
  <span class='wp-media-buttons'><a title='Add Media' data-editor='cert_images_one'
      class='button upload_image_button add_media' href='#'><span class='wp-media-buttons-icon'></span> Add
      Media</a></span>
<div class='clear'></div>
</p>
<p><label>Certification Image 2:</label><br />
  <input class='upload_image wp-media-buttons profile-bio' type='text' size='' name='cert_images_two'
    value='<?php echo $cert_images_two; ?>' />
  <span class='wp-media-buttons'><a title='Add Media' data-editor='cert_images_two'
      class='button upload_image_button add_media' href='#'><span class='wp-media-buttons-icon'></span> Add
      Media</a></span>
<div class='clear'></div>
</p>
<p><label>Certification Image 3:</label><br />
  <input class='upload_image wp-media-buttons profile-bio' type='text' size='' name='cert_images_three'
    value='<?php echo $cert_images_three; ?>' />
  <span class='wp-media-buttons'><a title='Add Media' data-editor='cert_images_three'
      class='button upload_image_button add_media' href='#'><span class='wp-media-buttons-icon'></span> Add
      Media</a></span>
<div class='clear'></div>
</p>
<p><label>Certification Image 4:</label><br />
  <input class='upload_image wp-media-buttons profile-bio' type='text' size='' name='cert_images_four'
    value='<?php echo $cert_images_four; ?>' />
  <span class='wp-media-buttons'><a title='Add Media' data-editor='cert_images_four'
      class='button upload_image_button add_media' href='#'><span class='wp-media-buttons-icon'></span> Add
      Media</a></span>
<div class='clear'></div>
</p>
<?php
}

    public function save_meta() {
      global $post;
      update_post_meta($post->ID, 'profile_nbn_email', $_POST['profile_nbn_email']);
      update_post_meta($post->ID, 'profile_nbn_count', $_POST['profile_nbn_count']);
      update_post_meta($post->ID, 'profile_att_title', $_POST['profile_att_title']);
      update_post_meta($post->ID, 'profile_att_email', $_POST['profile_att_email']);
      update_post_meta($post->ID, 'profile_att_phone', $_POST['profile_att_phone']);
      update_post_meta($post->ID, 'profile_bio_name', $_POST['profile_bio_name']);
      update_post_meta($post->ID, 'profile_bio_hometown', $_POST['profile_bio_hometown']);
      update_post_meta($post->ID, 'profile_bio_college', $_POST['profile_bio_college']);
      update_post_meta($post->ID, 'profile_bio_cert', $_POST['profile_bio_cert']);
      update_post_meta($post->ID, 'profile_bio_fav', $_POST['profile_bio_fav']);
      update_post_meta($post->ID, 'profile_bio_hobbies', $_POST['profile_bio_hobbies']);
      update_post_meta($post->ID, 'profile_bio_role', $_POST['profile_bio_role']);
      update_post_meta($post->ID, 'profile_bio_facts', $_POST['profile_bio_facts']);
      update_post_meta($post->ID, 'profile_bio_advice', $_POST['profile_bio_advice']);
      update_post_meta($post->ID, 'cert_images_one', $_POST['cert_images_one']);
      update_post_meta($post->ID, 'cert_images_two', $_POST['cert_images_two']);
      update_post_meta($post->ID, 'cert_images_three', $_POST['cert_images_three']);
      update_post_meta($post->ID, 'cert_images_four', $_POST['cert_images_four']);
    }

    public function remove_tech_excerpt_fields() {
      remove_meta_box('postexcerpt', 'post', 'normal');
    }

    // Adds Template files when plugin is activated.
    public function profiles_template_function($template_path) {
      if (get_post_type() == 'profiles') {
        if (is_single()) {
          // checks if the file exists in the theme first,
          // otherwise serve the file from the plugin
          if ($theme_file = locate_template(array('single-profiles.php'))) {
            $template_path = $theme_file;
          } else {
            $template_path = plugin_dir_path(__FILE__) . 'single-profiles.php';
          }
        }
        if (is_archive()) {
          // checks if the file exists in the theme first,
          // otherwise serve the file from the plugin
          if ($theme_file = locate_template(array('taxonomy-profiles.php'))) {
            $template_path = $theme_file;
          } else {
            $template_path = plugin_dir_path(__FILE__) . 'taxonomy-profiles.php';
          }
        }
      }
      return $template_path;
    }

    public function lnb_tech_pro_api_fields() {
      register_rest_field('profiles',
        'info',
        array(
          'get_callback' => [$this, 'lnb_tech_pro_api_fields_get_cb'],
        )
      );
    }

    public function lnb_tech_pro_api_fields_get_cb($object, $field, $request) {
      $tech_review_data = lnb\techprofiles\NNTechAPI::get_nn_data();
      $name = get_post_meta($object['id'], 'profile_bio_name', true);
      $image = get_the_post_thumbnail_url($object['id']);
      $title = get_post_meta($object['id'], 'profile_att_title', true);
      $hometown = get_post_meta($object['id'], 'profile_bio_hometown', true);
      $college = get_post_meta($object['id'], 'profile_bio_college', true);
      $certifications = get_post_meta($object['id'], 'profile_bio_cert', true);
      $certificationImages = array(
        get_post_meta($object['id'], 'cert_images_one', true),
        get_post_meta($object['id'], 'cert_images_two', true),
        get_post_meta($object['id'], 'cert_images_three', true),
        get_post_meta($object['id'], 'cert_images_four', true),
      );
      $favoriteAspect = get_post_meta($object['id'], 'profile_bio_fav', true);
      $hobbies = get_post_meta($object['id'], 'profile_bio_hobbies', true);
      $roleModel = get_post_meta($object['id'], 'profile_bio_role', true);
      $interestingFact = get_post_meta($object['id'], 'profile_bio_facts', true);
      $bestAdvice = get_post_meta($object['id'], 'profile_bio_advice', true);
      $bio = get_the_excerpt($object['id']);
      $reviewRating = $tech_review_data ? $tech_review_data[$object['slug']]['rating'] : null;
      $reviewCount = $tech_review_data ? $tech_review_data[$object['slug']]['count'] : null;

      $response = array(
        'name'                => $name ? $name : null,
        'image'               => $image ? $image : null,
        'jobTitle'            => $title ? $title : null,
        'hometown'            => $hometown ? $hometown : null,
        'college'             => $college ? $college : null,
        'certifications'      => $certifications ? $certifications : null,
        'certificationImages' => array(
          $certificationImages[0] ? $certificationImages[0] : null,
          $certificationImages[1] ? $certificationImages[1] : null,
          $certificationImages[2] ? $certificationImages[2] : null,
          $certificationImages[3] ? $certificationImages[3] : null,
        ),
        'favoriteAspect'      => $favoriteAspect ? $favoriteAspect : null,
        'hobbies'             => $hobbies ? $hobbies : null,
        'roleModel'           => $roleModel ? $roleModel : null,
        'interestingFact'     => $interestingFact ? $interestingFact : null,
        'bestAdvice'          => $bestAdvice ? $bestAdvice : null,
        'bio'                 => $bio ? $bio : null,
        'reviews'             => array(
          'rating' => $reviewRating ? $reviewRating : null,
          'count'  => $reviewCount ? $reviewCount : null,
        ),
      );

      return $response;
    }
  }

}

new TechProfiles;