<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'LeadsNearby_Tech_Profiles_Migrator' ) ) :

	class LeadsNearby_Tech_Profiles_Migrator {

		protected static $instance;

		private $old = array(
			'profile_bio_name', // deprecate
			'profile_bio_hometown',
			'profile_bio_college',
			'profile_bio_cert',
			'profile_bio_fav',
			'profile_bio_hobbies',
			'profile_bio_role',
			'profile_bio_facts',
			'profile_bio_advice',
			'profile_att_title',
			'profile_att_email',
			'profile_att_phone',
			'profile_nbn_email',
			'profile_nbn_count', // deprecate
		);

		private $map = array(
			'about' => array(
				'title' => 'profile_att_title',
				'certifications' => 'profile_bio_cert',
				'hometown' => 'profile_bio_hometown',
				'college' => 'profile_bio_college',
				'fav' => 'profile_bio_fav',
				'hobbies' => 'profile_bio_hobbies',
				'role' => 'profile_bio_role',
				'facts' => 'profile_bio_facts',
				'advice' => 'profile_bio_advice',
				'bio' => null,
			),
			'nearby-now' => array(
				'nn-email' => 'profile_nbn_email',
				'nn-count' => 'profile_nbn_count'
			),
		);

		private $profiles = array();

		private function __construct() { }
		private function __clone() { }
		private function __wakeup() { }

		public static function getInstance() {

			if( ! isset( self::$instance ) ) {

				self::$instance = new self();

			}

			return self::$instance;

		}

		function getStarted() {

			$this->get_profiles();

			$this->migrate_meta();

			$this->delete_meta();

			$this->finishUp();
			
		}

		private function get_profiles() {

			$query = new WP_Query( [ 'post_type' => LeadsNearby_Tech_Profiles::$post_type, 'posts_per_page' => -1  ] );

			while( $query->have_posts() ) :
				
				$query->the_post();

				$profiles[] = array(
					'id' => get_the_id(),
					'title' => get_the_title(),
				);

			endwhile;

			$this->profiles = $profiles;

		}

		public function migrate_meta() {

			foreach( $this->profiles as $properties ) {

				$migration;

				foreach( $this->map as $section => $fields ) {

					foreach( $this->map[$section] as $new => $old ) {

						if( $new == 'bio' ) {

							$migration[$section][$new] = get_the_excerpt( $properties['id'] );

						} else {

							$migration[$section][$new] = get_post_meta( $properties['id'], $old, true );

						}
					}

				}

				update_post_meta( $properties['id'], LeadsNearby_Tech_Profiles_Metaboxes::$meta_key, $migration );

			}

		}

		public function delete_meta() {

			foreach( $this->profiles as $properties ) {

				foreach( $this->old as $old_key ) {

					delete_post_meta( $properties['id'], $old_key );

				}

			}

		}

		public function finishUp() {

			update_option( 'lnb-tech-pro-options-migrated', true );

		}

	}

endif;

?>