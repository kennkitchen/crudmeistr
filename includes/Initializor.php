<?php

namespace VinlandMedia\CRUDMeistR;

/**
 * Created by PhpStorm.
 * User: kenkitchen
 * Date: 10/28/17
 * Time: 11:47 AM
 */

class Initializor {
	
	public function Initializor() {
		add_role(
			'crudmr_admin',
			'CRUDMeistR - Administrator',
			[
				'administer_crudmr'  => true,
				'delete_crudmr'      => true,
				'add_crudmr'         => true,
				'update_crudmr'      => true,
				'view_crudmr'        => true,
			]
		);

		add_role(
			'crudmr_superuser',
			'CRUDMeistR - Superuser',
			[
				'delete_crudmr'      => true,
				'add_crudmr'         => true,
				'update_crudmr'      => true,
				'view_crudmr'        => true,
			]
		);

		add_role(
			'crudmr_user',
			'CRUDMeistR - User',
			[
				'add_crudmr'         => true,
				'update_crudmr'      => true,
				'view_crudmr'        => true,
			]
		);

		add_role(
			'crudmr_viewer',
			'CRUDMeistR - Viewer',
			[
				'view_crudmr'        => true,
			]
		);

		$current_user = wp_get_current_user();
		$current_user->add_role('crudmr_admin');

		// TODO premium only
//		register_post_type('crudmr-queries', register_query_cpt());
//		register_post_type('crudmr-templates', register_template_cpt());
//		register_post_type('crudmr-functions', register_function_cpt());

		// flush rewrite cache
		flush_rewrite_rules();
	}

	private function register_query_cpt() {
		//register the query custom post type
		$labels = array(
			'name'               => __( 'CMR Queries', 'crudmeistr' ),
			'singular_name'      => __( 'CMR Query', 'crudmeistr' ),
			'add_new'            => __( 'Add New', 'crudmeistr' ),
			'add_new_item'       => __( 'Add New CMR Query', 'crudmeistr' ),
			'edit_item'          => __( 'Edit CMR Query', 'crudmeistr' ),
			'new_item'           => __( 'New CMR Query', 'crudmeistr' ),
			'all_items'          => __( 'All CMR Queries', 'crudmeistr' ),
			'view_item'          => __( 'View CMR Query', 'crudmeistr' ),
			'search_items'       => __( 'Search CMR Queries', 'crudmeistr' ),
			'not_found'          =>  __( 'No CMR Queries found', 'crudmeistr' ),
			'not_found_in_trash' => __( 'No CMR Queries found in Trash', 'crudmeistr' ),
			'menu_name'          => __( 'CMR Queries', 'crudmeistr' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			//'show_in_menu'       => 'crudmeistr_main_menu',
			'menu_icon'			 => 'dashicons-search',
			'query_var'          => true,
			'rewrite'            => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'rest_base'          => 'crudmeistr-queries',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'           => array('title', 'comments', 'editor'),
//		'taxonomies'         => array('category')
		);

		return $args;
	}

	private function register_template_cpt() {
		//register the query custom post type
		$labels = array(
			'name'               => __( 'CMR Templates', 'crudmeistr' ),
			'singular_name'      => __( 'CMR Template', 'crudmeistr' ),
			'add_new'            => __( 'Add New', 'crudmeistr' ),
			'add_new_item'       => __( 'Add New CMR Template', 'crudmeistr' ),
			'edit_item'          => __( 'Edit CMR Template', 'crudmeistr' ),
			'new_item'           => __( 'New CMR Template', 'crudmeistr' ),
			'all_items'          => __( 'All CMR Templates', 'crudmeistr' ),
			'view_item'          => __( 'View CMR Template', 'crudmeistr' ),
			'search_items'       => __( 'Search CMR Templates', 'crudmeistr' ),
			'not_found'          =>  __( 'No CMR Templates found', 'crudmeistr' ),
			'not_found_in_trash' => __( 'No CMR Templates found in Trash', 'crudmeistr' ),
			'menu_name'          => __( 'CMR Templates', 'crudmeistr' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			//'show_in_menu'       => 'crudmeistr_main_menu',
			'menu_icon'			 => 'dashicons-layout',
			'query_var'          => true,
			'rewrite'            => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'rest_base'          => 'crudmeistr-templates',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'           => array('title', 'comments', 'editor'),
//		'taxonomies'         => array('category')
		);

		return $args;
	}

	private function register_function_cpt() {
		//register the query custom post type
		$labels = array(
			'name'               => __( 'CMR Functions', 'crudmeistr' ),
			'singular_name'      => __( 'CMR Function', 'crudmeistr' ),
			'add_new'            => __( 'Add New', 'crudmeistr' ),
			'add_new_item'       => __( 'Add New CMR Function', 'crudmeistr' ),
			'edit_item'          => __( 'Edit CMR Function', 'crudmeistr' ),
			'new_item'           => __( 'New CMR Function', 'crudmeistr' ),
			'all_items'          => __( 'All CMR Functions', 'crudmeistr' ),
			'view_item'          => __( 'View CMR Function', 'crudmeistr' ),
			'search_items'       => __( 'Search CMR Functions', 'crudmeistr' ),
			'not_found'          =>  __( 'No CMR Functions found', 'crudmeistr' ),
			'not_found_in_trash' => __( 'No CMR Functions found in Trash', 'crudmeistr' ),
			'menu_name'          => __( 'CMR Functions', 'crudmeistr' )
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			//'show_in_menu'       => 'crudmeistr_main_menu',
			'menu_icon'			 => 'dashicons-chart-line',
			'query_var'          => true,
			'rewrite'            => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'show_in_rest'       => true,
			'rest_base'          => 'crudmeistr-functions',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'supports'           => array('title', 'comments', 'editor'),
//		'taxonomies'         => array('category')
		);

		return $args;
	}

}