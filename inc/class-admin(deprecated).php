<?php

if (!defined('ABSPATH')) {
  exit;
}
// Exit if accessed directly

exit;

if (!class_exists('LeadsNearby_Tech_Profiles_Admin')):

  class LeadsNearby_Tech_Profiles_Admin {

    protected static $instance;

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
      add_action('admin_init', [$this, 'check_migration']);
      add_action('admin_menu', [$this, 'create_settings_page']);
      add_action('admin_init', [$this, 'register_settings']);
      add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
      add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
      add_action('admin_init', [$this, 'load_metaboxes']);
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

      add_submenu_page('edit.php?post_type=profiles', 'Tech Profiles Settings', 'Settings', 'edit_posts', 'tech-pro-settings', [$this, 'render_settings_page']);

      register_setting('lnb-tech-pro-group', 'lnb-tech-pro-options', [$this, 'sanitize_settings']);
    }

    public function sanitize_settings($data) {

      $data['slug'] = sanitize_title_with_dashes($data['slug']);

      return $data;

    }

    public function render_settings_page() {

      require_once plugin_dir_path(__FILE__) . '/lib/templates/admin-settings.php';

    }

    public function register_settings() {

      $data = get_option('lnb-tech-pro-options', true);

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
          'value'       => esc_attr($data['sprite']),
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
        $args['value'] == 'on' ? 'checked' : '',
        $args['placeholder']
      );
    }

    public function load_metaboxes() {

      LeadsNearby_Tech_Profiles_Metaboxes::getInstance()->init();

    }

    public function check_migration() {

      $migrated = get_option('lnb-tech-pro-options-migrated');

      if (!$migrated) {

        LeadsNearby_Tech_Profiles_Migrator::getInstance()->getStarted();

      }

    }

  }

endif;

?>