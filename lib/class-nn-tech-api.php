<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'NN_Tech_API' ) ) :

	class NN_Tech_API {

		function __construct() {

			$this->get_nn_data();

		}

		/**
		* Returns array of scraped NearbyNow data
		*
		* @return array  $data  NearbyNow data.
		*/
		public static function get_nn_data() {

			$data = get_transient( 'nn_tech_data' );

			if( ! $data ) {

				$data = array();

				$args = array(
					'post_type' => 'profiles',
					'meta_query' => array(
		        		array(
		          			'key' => 'profile_nbn_email',
		          			'value' => '',
		          			'compare' => '!='
		        			)
		        		)
					);

				$tech_query = get_transient( 'nn_tech_data_query' );

				if( ! $tech_query ) {

					$tech_query = new WP_Query( $args );

					set_transient( 'nn_tech_data_query', $tech_query, 30*24*60*60 );

				}

				if( $tech_query->have_posts() ) :
					while( $tech_query->have_posts() ) : $tech_query->the_post();
						$id = get_the_id();
						$tech_email = get_post_meta( $id, 'profile_nbn_email', true );

						$url = 'https://api.sidebox.com/plugin/nearbyserviceareareviewcombo/?storefronttoken=2726e4a0-b8e6-43de-9ad8-84437a3e1ad4&reviewcount=0&checkincount=0&techemail='.$tech_email;

						$response = file_get_contents( $url );

						$string = preg_match_all( '/<span itemprop="(.*?)">(.*?)<\/span>/', $response, $matches );

						$string_cities = preg_match_all( '/<a href="cityurl">(.*?)<\/a>/', $response, $matches_cities );

						$data[basename( get_the_permalink() )] = array(
							'rating' => $matches[2][0],
							'count' => $matches[2][1],
						);
					endwhile;

					wp_reset_postdata();

				endif;

				set_transient( 'nn_tech_data', $data, 24*60*60 );

				return $data;

			} else {

				return $data;

			}
		}

		/**
		* Clears transient stored by get_nn_data()
		*/
		public static function clear_data() {

			delete_transient( 'nn_tech_data' );
			delete_transient( 'nn_tech_data_query' );

		}

	}

endif;

add_action( 'save_post_profiles', array( 'NN_Tech_API', 'clear_data' ) );
add_action( 'wp_update_nav_menu', array( 'NN_Tech_API', 'clear_data' ) );
add_action( 'after_rocket_clean_cache_dir', array( 'NN_Tech_API', 'clear_data' ) );