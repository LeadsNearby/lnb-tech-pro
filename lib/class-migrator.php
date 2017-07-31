<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'LeadsNearby_Tech_Profiles_Migrator' ) ) :

	class LeadsNearby_Tech_Profiles_Migrator {

		private $old = array();
		private $new = array();

		function __construct() {

			$this->old = array(
				'profile_bio_name',
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
				'profile_att_phone'
			);

			$this->new = LeadsNearby_Tech_Profiles::options;

		}

	}

endif;

?>