<?php

namespace VinlandMedia\CRUDMeistR;

/**
 * Created by PhpStorm.
 * User: kenkitchen
 * Date: 10/28/17
 * Time: 12:00 PM
 */

class Enqueuer {
	public function crudmr_public_enqueue() {
		/*
		wp_register_style('w3_public_css', plugins_url('crudmeistr') . '/public/css/w3-lite.css');
		wp_enqueue_style('w3_public_css');
		*/

		wp_register_style('fontawesome-js', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', null);
		wp_enqueue_style('fontawesome-js');

		wp_register_style('crudmr_dt_css', plugins_url('crudmeistr') . '/public/datatables/datatables.min.css');
		wp_enqueue_style('crudmr_dt_css');

		wp_register_script('crudmr_dt_js', plugins_url('crudmeistr') . '/public/datatables/datatables.min.js', array('jquery'));
		wp_enqueue_script('crudmr_dt_js');

		wp_register_script('crudmr_js', plugins_url('crudmeistr') . '/public/js/crudmr-public.js', array('jquery'));
		wp_enqueue_script('crudmr_js');

	}

	public function crudmr_admin_enqueue() {
		wp_register_style('crudmr-admin-css', plugins_url('crudmeistr') . '/admin/css/crudmr-admin.css');
		wp_enqueue_style('crudmr-admin-css');

		wp_register_script('crudmr-admin-js', plugins_url('crudmeistr') . '/admin/js/crudmr-admin.js', array('jquery'));
		wp_localize_script('crudmr-admin-js', 'myAjax', array('ajaxurl' => admin_url('admin-ajax.php')));
		wp_enqueue_script('crudmr-admin-js');

		wp_enqueue_script('jquery-ui-sortable');

		wp_register_style('fontawesome-js', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', null);
		wp_enqueue_style('fontawesome-js');
	}
}