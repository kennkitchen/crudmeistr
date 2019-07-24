<?php

namespace VinlandMedia\CRUDMeistR;

/**
 * Created by PhpStorm.
 * User: kenkitchen
 * Date: 10/28/17
 * Time: 11:40 AM
 */
class ActDeactClass {

	public function Activator($network_wide) {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		global $wpdb;
		global $wp_version;

		if (version_compare($wp_version, '4.1', '<')) {
			wp_die('Requires version 4.8 or higher.');
		}

		if ((is_multisite()) && ($network_wide)) {
			$activated = array();

			$my_blogs = $wpdb->get_results('SELECT * FROM ' . $wpdb->blogs);

			foreach ($my_blogs as $my_blog) {
				switch_to_blog($my_blog->blog_id);
				$search_results_page = array(
					'post_title'    => 'Search Results',
					'post_content'  => '[crudmr function=results]',
					'post_status'   => 'publish',
					'post_type'   	=> 'page'
				);
				$search_results_page_id = wp_insert_post($search_results_page, $wp_error);
				$activated[] = $my_blogs->blog_id;
				restore_current_blog();
			}
		} else {
			$search_results_page = array(
				'post_title'    => 'Search Results',
				'post_content'  => '[crudmr function=results]',
				'post_status'   => 'publish',
				'post_type'   	=> 'page'
			);
			$search_results_page_id = wp_insert_post($search_results_page, $wp_error);
		}

		flush_rewrite_rules();

	}

	public function Deactivator() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		// delete_option('wpps_admin_only');

		// unregister_post_type('wpps-post-stickies');

		// flush rewrite cache
		flush_rewrite_rules();

	}
}