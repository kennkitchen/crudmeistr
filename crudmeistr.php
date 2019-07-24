<?php
namespace VinlandMedia\CRUDMeistR;

/*
Plugin Name: CRUDMeistR
Plugin URI: https://vinland.tech/products/wp-post-stickies/
Description: CRUDMeistR provides add, update, list, and search capabilities based on your custom tables without the need for programming.
Version: 1.0.0
Author: Vinland Media
Author URI: http://vinlandmedia.com/
License: GPLv2
Tags:
Text Domain: tableInterfaceGenerator
*/

// includes
require_once('includes/ActDeactClass.php');
require_once('includes/Initializor.php');
require_once('includes/Enqueuer.php');
//require_once('includes/crudmr-queries.php');
require_once('includes/crudmr-utility-functions.php');

// public
//require_once('public/classes/QueryProcessor.php');

// admin
require_once('admin/crudmr-settings-options.php');
//require_once('admin/metaboxes.php');

//define('WP_DEBUG', true);

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/*
* Plugin Activation
*/
function crudmr_activate($network_wide) {
	$myActivator = new ActDeactClass();

	$myActivator->Activator($network_wide);
}
register_activation_hook(__FILE__, __NAMESPACE__ . '\\crudmr_activate');

/*
* Plugin Deactivation
*/
function crudmr_deactivate() {
	$myDeactivator = new ActDeactClass();

	$myDeactivator->Deactivator();

}
register_deactivation_hook(__FILE__, __NAMESPACE__ . '\\crudmr_deactivate');

function crudmr_init() {
	$myInitializor = new Initializor();

	$myInitializor->Initializor();

}
add_action('init', __NAMESPACE__.'\\crudmr_init');


function crudmeistr_admin_scripts() {
	$myEnqueuer = new Enqueuer();

	$myEnqueuer->crudmr_admin_enqueue();

}
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\crudmeistr_admin_scripts');

function crudmeistr_wp_scripts() {
	$myEnqueuer = new Enqueuer();

	$myEnqueuer->crudmr_public_enqueue();

}
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\crudmeistr_wp_scripts');

/**
 * CRUDMeistR Main
 *
 * This function is fired by the crudmeistr shortcode.
 *
 * CRUDmeistR Main reads the parameters of the shortcode and displays the
 * appropriate CRUD page.  A function parameter is always required; a table is
 * normally required unless the function is "search-criteria".  The passed table
 * name will be checked to see if it's in the database already.  After these
 * checks have passed, the function can move forward and perform specific
 * actions based upon the function passed.
 *
 * @param array $atts Passed attributes
 * @return string $results Page built by shortcode
 *
 * @author Vinland Media, LLC.
 * @package CRUDMeistR
 */
function crudmr_main($atts)
{
	global $wpdb;
	global $wp;

    //$panel_options = get_option('panel_options');

    $fetch_data_atts = shortcode_atts( array(
        'table' => '',
		'function' => '',
    ), $atts );

	// a function is always required
	if (!isset($fetch_data_atts['function']) || empty($fetch_data_atts['function'])) {
		return 'A function is required.';
	} else {
		// but if the function is 'results' we can send the parameters and get out
		if ('results' == $fetch_data_atts['function']) {
			$parameter_string = get_query_var('search-criteria', null);
			return return_row_list($parameter_string);
		}
	}

	// a table name is required if this isn't a 'results' function
	if (!isset($fetch_data_atts['table']) || empty($fetch_data_atts['table'])) {
		return 'A table name is required.';
	}

	// ...and the fuction should be valid
	if (!in_array($fetch_data_atts['function'] , array('add' , 'view', 'update', 'delete', 'list'))) {
		return 'A valid function (add, view, update, delete, or list) is required.';
	}

	// query the info schema for the table columns
	$results = get_table_columns($fetch_data_atts['table']);
	// if we don't get results, the table name must've been bogus
	if (!$results) {
		return 'Invalid table name supplied.';
	}

	// TODO does this line belong here?
	$search_results = get_query_var('search-results', null);

	$url = get_permalink();

	// table name without the WP prefix
//	$base_table_name = ltrim($fetch_data_atts['table'], $wpdb->prefix);
	$base_table_name = substr($fetch_data_atts['table'], strlen($wpdb->prefix));

	// TODO fix URL
	$redirect_url_prefix = home_url(add_query_arg(array(), $wp->request));

	$view_url = $redirect_url_prefix . '/' . $base_table_name . '/view-' . $base_table_name . '/';
	$update_url = $redirect_url_prefix . '/' .  $base_table_name . '/update-' . $base_table_name . '/';
	$delete_url = $redirect_url_prefix . '/'.  $base_table_name . '/delete-' . $base_table_name . '/';

	switch ($fetch_data_atts['function']) {
		case 'list':
			if (current_user_can('view_crudmr')) {
				// get all rows and all columns from table
				$datarows = $wpdb->get_results(
					'select * ' .
					'from ' . $fetch_data_atts['table'] . ' ');
				if (!$datarows) {
					return 'No data found in ' . $fetch_data_atts['table'];
				} else {
					return produce_datatable_output($results, $datarows, $base_table_name);
				}
			} else {
				return 'You are not authorized to view this page.';
			}
			break;

		case 'add':
			if (current_user_can('add_crudmr')) {
				// prepare a form for add
				$output = '<form method="POST" action="' . admin_url('admin-post.php') . '" name="crudmeistr-' . $fetch_data_atts['function'] . '">';
				$output .= '<input type="hidden" name="action" value="form_response">';

				// iterate columns
				foreach ($results as $result) {
					$output .= '<label for="' . $result->column_name . '" ' . restricted_fields($result->column_name, $fetch_data_atts['function']) . '>' . ucwords(str_replace('_', ' ', $result->column_name)) . '</label>';
					$output .= '<input class="w3-input" name="' . $result->column_name . '"' . restriction_types($result->column_name, $fetch_data_atts['function']) . '><br>';
				}

				// close the form
				$output .= '<button class="w3-button" value="submit" name="submit">Submit</button><br />';
				$output .= '<input type="hidden" name="table" value="' . $fetch_data_atts['table'] . '">';
				$output .= '<input type="hidden" name="function" value="' . $fetch_data_atts['function'] . '">';
				$output .= '</form>';
			} else {
				return 'You are not authorized to add rows.';
			}
			break;

		// view, update or delete
		default:
			// if view, update, or delete (actions for which a row must be found) and we have not
			// done this yet, we need to show the search box
			if (!$search_results) {
				// start the form
				$output = '<form method="POST" action="' . admin_url('admin-post.php') . '" name="crudmeistr-' . $fetch_data_atts['function'] . '">';
				$output .= '<input type="hidden" name="action" value="form_response">';

				// provide a search box
				$output .= '<label for="search_criteria">Search</label>';
				$output .= '<input class="w3-input" name="search_criteria" value=""><br>';

				// wrap up the form
				$output .= '<button class="w3-button" value="submit" name="submit">Submit</button><br />';
				$output .= '<input type="hidden" name="table" value="' . $fetch_data_atts['table'] . '">';
				$output .= '<input type="hidden" name="function" value="' . $fetch_data_atts['function'] . '">';
				$output .= '<input type="hidden" name="current_url" value="' . esc_url($url) . '">';
				$output .= '</form>';
			} else {
				if (('delete' == $fetch_data_atts['function'] && current_user_can('delete_crudmr')) ||
					('update' == $fetch_data_atts['function'] && current_user_can('update_crudmr')) ||
					('view' == $fetch_data_atts['function'] && current_user_can('view_crudmr'))) {

					// we have a datarow (the first part of the IF was previously true) so
					// we are starting a new form
					$output = '<form method="POST" action="' . admin_url('admin-post.php') . '" name="crudmeistr-' . $fetch_data_atts['function'] . '">';
					$output .= '<input type="hidden" name="action" value="form_response">';

					// split the results of the search into an array
					$results_as_array = explode(';', $search_results);

					// iterate the search results
					foreach ($results_as_array as $result) {
						// ONE result split into an array
						$result_as_array = explode(':', $result);
						if ($result_as_array[0] == 'id') {
							// save the ID key
							$current_key = $result_as_array[1];
						}
						// if we have a result...
						if (isset($result_as_array[0]) && !empty($result_as_array[0])) {
							// TODO comment this line once you remember what it does!
							$output .= '<label for="' . $result_as_array[0] . '" ' . restricted_fields($result_as_array[0], $fetch_data_atts['function']) . '>' . ucwords(str_replace('_', ' ', $result_as_array[0])) . '</label>';
							$output .= '<input class="w3-input" name="' . $result_as_array[0] . '" value="' . $result_as_array[1] . '" ' . restricted_fields($result_as_array[0], $fetch_data_atts['function'])  . '><br>';
						}
					}
					if ('delete' == $fetch_data_atts['function']) {
						// add a confirmation checkbox for deletes
						$output .= '<label for="delete_checkbox">Check to Confirm Deletion of this row! </label>';
						$output .= ' <input class="w3-check" type="checkbox" name="delete_checkbox" value="remove">';
					}
					// for view (which does nothing) dim out the Submit button
					if ('view' != $fetch_data_atts['function']) {
						$output .= '<button class="w3-button" value="submit" name="submit">Submit</button><br />';
					} else {
						$output .= '<button class="w3-button" value="submit" name="submit" disabled>Submit</button><br />';
					}
					// wrap up the form
					$output .= '<input type="hidden" name="key" value="' . $current_key . '">';
					$output .= '<input type="hidden" name="table" value="' . $fetch_data_atts['table'] . '">';
					$output .= '<input type="hidden" name="function" value="' . $fetch_data_atts['function'] . '">';
					$output .= '<input type="hidden" name="current_url" value="' . esc_url($url) . '">';
					$output .= '</form>';
					$output .= '<br />';

					$child_table = has_children($fetch_data_atts['table']);
					if (!empty($child_table)) {
						$output .= display_child_rows($child_table, $fetch_data_atts['table'], $current_key);
					} else {
						$output .= '(this table does not have children)';
					}
				} else {
				$output = 'You are not authorized to perform the requested function.';
			}
			break;
		}
	}

	// this is the whole page!
    return $output;
}
add_shortcode('crudmr', __NAMESPACE__ . '\\crudmr_main');

/**
 * Restricted Fields
 *
 * This function hides and disables form fields.
 *
 * Depending on the form being displayed, some or all of the fields should be
 * un-editable or, in some cases, hidden completely.  This function is called
 * in-line as fields are being added to a form and returns the appropriate
 * keyword ("hidden," "disabled") to the correct spot in the HTML.
 *
 * @param string $field_name The current field name
 * @param string $function The current function
 * @return string ' disabled', ' hidden', or null
 *
 * @author Vinland Media, LLC.
 * @package CRUDMeistR
 */
function restricted_fields($field_name = null, $function = null) {
	// for delete and view, none of the fields should be keyable
	if (in_array($function, array('delete', 'view'))) {
		return ' disabled';
	}
	// for other fuctions, decide what should be hidden or disabled
	if (in_array($field_name, array('id' , 'created' , 'modified'))) {
		if (in_array($function, array('add'))) {
			return ' hidden';
		} else {
			return ' disabled';
		}
	} else {
		return null;
	}
}

function restriction_types($field_name = null, $function = null) {
	// for delete and view, none of the fields should be keyable
	if (in_array($function, array('delete', 'view'))) {
		return ' type="disabled"';
	}
	// for other functions, decide what should be hidden or disabled
	if (in_array($field_name, array('id' , 'created' , 'modified'))) {
		if (in_array($function, array('add'))) {
			return ' type="hidden"';
		} else {
			return ' type="disabled"';
		}
	} else {
		return null;
	}
}

/**
 * Translate Foreign Key
 *
 * This function translates IDs into names.
 *
 * Foreign keys should always be defined as tablename_id, where "tablename" is
 * the singular form of a tablename (which is plural). Based upon this convention,
 * this function looks at any field that ends in "_id" and looks for a table that
 * matches. If it finds such a table, it looks for a column named "name" and, if
 * one is found, it will use that column ("name") instead of the ID.
 *
 * @param string $field_name The current field name
 * @param string $value The current field value
 * @param string $function The current function
 * @return string $column_value->$foreign_column or $value
 *
 * @author Vinland Media, LLC.
 * @package CRUDMeistR
 */
function translate_foreign_key($field_name = null, $value = null, $function = null) {
	global $wpdb;

	// does the current field end with "_id"?
	if ('_id' == substr($field_name, strlen($field_name)-3)) {
		// derive foreign table name from column
		$foreign_table = $wpdb->prefix . substr($field_name, 0, strlen($field_name)-3) . 's';
		$foreign_column = null;

		// get the column names from the foreign table
		$results = get_table_columns($foreign_table);

		// iterate columns until you find one named "name"
		foreach ($results as $result) {
			if ($foreign_table == $result->table_name && 'name' == $result->column_name) {
				$foreign_column = $result->column_name;
				break;
			}

		}
		// return the original value if no "name" column is found
		if (!$foreign_column || empty($foreign_column)) {
			return $value;
		} else {
			// if "name" column found, get its value
			$column_value = $wpdb->get_row(
				'select ' . $foreign_column . ' ' .
				'from ' . $foreign_table . ' ' .
				'where id = "' . $value . '"'
			);

			// return the value of "name" column
			return $column_value->$foreign_column;
		}

	} else {
		// return the original value
		return $value;
	}
}


/**
 * Has Children
 *
 * Determine if a table has a child table.
 *
 * Checks the information_schema to see if there are any tables that have a field
 * called $table_name (singular) + "_id".  If found, return the child table name.
 *
 * @param string $table_name  The (potentially) parent table
 * @return object $found_table  The child table name (if there is one)
 *
 * @author Vinland Media, LLC.
 * @package CRUDMeistR
 */
function has_children($table_name) {
	global $wpdb;

	// relies on the convention having been followed:
	// i.e. table = items, foreign key = item_id
	$results = $wpdb->get_results(
		'select t.table_name, c.column_name, c.data_type ' .
		'from information_schema.columns c, information_schema.tables t ' .
		'where t.table_name = c.table_name ' .
		'and t.table_name like "' . $wpdb->prefix . '%" ' .
		'and c.column_name = "' . substr($table_name, strlen($wpdb->prefix), -1) . '_id' . '"'
	);

	// find FIRST table name and return it -- doesn't handled more than one
	// child table at this point
	if ($results) {
		foreach ($results as $result) {
			$found_table = $result->table_name;
			break;
		}
		return $found_table;
	} else {
		return null;
	}
}

/**
 * Display Child Rows
 *
 * Display the rows of a child table
 *
 * This function gets the datarows of a child table and then calls the function
 * that turns it into a datatables table.
 *
 * @param string $table_name  The name of the child table
 * @param string $parent_table_name  The name of the parent table
 * @param string $parent_key_value  The ID of the parent
 * @return string ...
 *
 * @author Vinland Media, LLC.
 * @package CRUDMeistR
 */
function display_child_rows($table_name, $parent_table_name, $parent_key_value) {
	global $wpdb;

	$datarows = $wpdb->get_results(
		'select * ' .
		'from ' . $table_name . ' ' .
		'where ' . substr($parent_table_name, strlen($wpdb->prefix), -1) . '_id' . ' = ' . $parent_key_value
	);

	if (!$datarows) {
		return 'No child rows exist for this ' . $table_name . ' row.';
	} else {
		// query the info schema for the table columns
		$results = get_table_columns($table_name);

		// table name without the WP prefix
		$base_table_name = ltrim($table_name, $wpdb->prefix);
		return produce_datatable_output($results, $datarows, $base_table_name);
	}
}


/**
 * Produce Datatable Output
 *
 * Creates an output table of selected rows from a single table
 *
 * DataPages uses the open-source product DataTables (no relation) to create
 * robust output results when multiple rows are being displayed.  This can
 * occur both on a page where the "list" function has been employed and also
 * when there are child rows to be displayed on a view/update/delete screen.
 *
 * @param object $results  Column names for the table in question
 * @param object $datarows  The data output to be displayed
 * @param string $base_table_name  Table name w/o prefix and "s"
 * @return string $output  HTML formatted table
 *
 * @author Vinland Media, LLC.
 * @package CRUDMeistR
 */
function produce_datatable_output($results, $datarows, $base_table_name) {
	global $wp;

	// initialize fields
	$column_headers = array();
	$url_parameters = '';
	$url_parameter_template = '';

	// TODO fix URL
	$redirect_url_prefix = home_url(add_query_arg(array(), $wp->request));

	$view_url = $redirect_url_prefix . '/' . $base_table_name . '/view-' . $base_table_name . '/';
	$update_url = $redirect_url_prefix . '/' . $base_table_name . '/update-' . $base_table_name . '/';
	$delete_url = $redirect_url_prefix . '/' . $base_table_name . '/delete-' . $base_table_name . '/';

	// table headers
	$output = '<table id="dt-default">';
	$output .= '<thead><tr>';

	// iterate the table columns
	foreach ($results as $result) {
		// turn column headers into pretty names for output
		$output .= '<th>' . ucwords(str_replace('_', ' ', $result->column_name)) . '</th>';
		// put the columns into an array for later user
		array_push($column_headers, $result->column_name);
		// build a template for the URL parameter output
		$url_parameter_template .= $result->column_name . ':%' . $result->column_name . '%;';
	}

	// actions column header
	$output .= '<th>';
	$output .= 'Actions';
	$output .= '</th>';

	// start the table body
	$output .= '</tr></thead>';
	$output .= '<tbody>';

	// iterate data
	foreach ($datarows as $datarow) {
		$output .= '<tr>';
		$url_parameters = $url_parameter_template;

		// use column header array to identify data columns
		for ($i=0; $i < count($column_headers); $i++) {
			$output .= '<td>';
			// output the datarow with optional special processing for foreign keys
			$output .= translate_foreign_key($column_headers[$i], $datarow->{$column_headers[$i]}, 'list');
			// this really only needs to happen once! -- getting the link for the actions
			$url_parameters = str_replace('%'.$column_headers[$i].'%', $datarow->{$column_headers[$i]}, $url_parameters);
			$output .= '</td>';
		}

		// finish up table with action column
		$output .= '<td>';
		$output .= '<a href="' . $view_url . '?search-results=' . $url_parameters . '"><i class="fa fa-eye"></i></a> / ';
		$output .= '<a href="' . $update_url . '?search-results=' . $url_parameters . '"><i class="fa fa-pencil"></i></a> / ';
		$output .= '<a href="' . $delete_url . '?search-results=' . $url_parameters . '"><i class="fa fa-trash"></i></a>';
		$output .= '</td>';

		$output .= '</tr>';
	}

	// close table
	$output .= '</tbody>';
	$output .= '</table>';

	return $output;
}


/**
 * Return Row List
 *
 * This function is called when the function is "result".
 *
 * This plugin uses a special page called "Search Results" ("search-results")
 * for searches that result in more than one returned row.  This page contains
 * the crudmeistr shortcode with no table and a function of "result".  This is
 * the function that produces the dynamic content of the "Search Results" page
 * when it is being displayed.
 *
 * @param string $parameter_string  The submitted search parameters
 * @return string $output  Formatted HTML data for Search Results page
 *
 * @author Vinland Media, LLC.
 * @package CRUDMeistR
 */
function return_row_list($parameter_string = null) {
	global $wpdb;
	global $wp;

	// $parameter_string is in the format ?search-criteria=(n):table_name:n1;[n2; ...]
	// where n is the number of rows returns and n1, n2, etc. are the keys found in
	// the preceding search.
	//
	// Here we split out the number of search hits, the table name, and the
	// returned keys
	$passed_search_array = explode(':', $parameter_string);
	// and then we separate the returned keys themselves
	$passed_search_keys = explode(';', $passed_search_array[2]);

	// TODO fix URLs
	$base_table_name = strip_db_prefix($passed_search_array[1]);
	$base_url = home_url(add_query_arg(array(), $wp->request));

	$view_url = $base_url . $base_table_name . '/view-' . $base_table_name . '/';
	$update_url = $base_url . $base_table_name . '/update-' . $base_table_name . '/';
	$delete_url = $base_url . $base_table_name . '/delete-' . $base_table_name . '/';

	// build start of query
	// the "where id = 0" is a dummy line so that "where" is out of the way
	// and we can just add "or" lines
	$query_string = 'select * ' .
			'from ' . $passed_search_array[1] . ' ' .
			'where id = 0 ';

	// for each key that was passed, add "or id = n1," "or id = n2," etc. until
	// we have them all
	foreach ($passed_search_keys as $passed_search_key) {
		if (!empty($passed_search_key)) {
			$query_string .= 'or id = ' . $passed_search_key . ' ';
		}
	}
	// init some variables
	$column_headers = array();
	$url_parameters = '';
	$url_parameter_template = '';

	// get the column names
	$results = get_table_columns($passed_search_array[1]);
	// get the data
	$datarows = $wpdb->get_results($query_string);

	if ((!$results) || (!$datarows)) {
		$output = 'No data found that matches query parameters';

	} else {

		// Start the table
		$output = '<table class="w3-table w3-bordered w3-hoverable w3-card-4">';
		$output .= '<tbody>';

		// do column headers
		$output .= '<thead><tr>';

		foreach ($results as $result) {
			$output .= '<th>' . ucwords(str_replace('_', ' ', $result->column_name)) . '</th>';
			array_push($column_headers, $result->column_name);
			$url_parameter_template .= $result->column_name . ':%' . $result->column_name . '%;';
		}

		// add "select" header
		$output .= '<th>';
		$output .= 'Select';
		$output .= '</th>';

		$output .= '</tr></thead><tbody>';

		// load table with data items
		foreach ($datarows as $datarow) {
			$output .= '<tr>';
			$url_parameters = $url_parameter_template;

			for ($i=0; $i < count($column_headers); $i++) {
				$output .= '<td>';
				$output .= translate_foreign_key($column_headers[$i], $datarow->{$column_headers[$i]}, 'list');
				$url_parameters = str_replace('%'.$column_headers[$i].'%', $datarow->{$column_headers[$i]}, $url_parameters);
				$output .= '</td>';
			}

			// add the FA icon actions (view, update, delete)
			$output .= '<td>';
			$output .= '<a href="' . $view_url . '?search-results=' . $url_parameters . '"><i class="fa fa-eye"></i></a> / ';
			$output .= '<a href="' . $update_url . '?search-results=' . $url_parameters . '"><i class="fa fa-pencil"></i></a> / ';
			$output .= '<a href="' . $delete_url . '?search-results=' . $url_parameters . '"><i class="fa fa-trash"></i></a>';
			$output .= '</td>';

			$output .= '</tr>';
		}

		// close out tables
		$output .= '</tbody>';
		$output .= '</table>';

	}

	// return the HTML formatted output
	return $output;
}

/**
 * Do Form Response
 *
 * This function handles the CRUD form responses
 *
 * This...
 *
 * @return string $output  Formatted HTML data for Search Results page
 *
 * @author Vinland Media, LLC.
 * @package CRUDMeistR
 */
function do_form_response() {
	global $wpdb;

//	$wpdb->show_errors();

	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		//
		if (isset($_POST["table"]) && isset($_POST["function"])) {
			$url = '/' . substr($_POST["table"], strlen($wpdb->prefix));
			$column_headers = array();

			$results = get_table_columns($_POST["table"]);

			foreach ($results as $result) {
				array_push($column_headers, $result->column_name);
			}

			if (isset($_POST["search_criteria"]) && !empty($_POST["search_criteria"])) {
				$search_results = do_search($_POST["search_criteria"], $_POST["table"], $column_headers);
				$url = home_url() . '/search-results/?search-criteria=' . $search_results;

				wp_safe_redirect($url);
				exit;
			}


			switch ($_POST["function"]) {
				case 'add':
					$insert_contents = array();

					for ($i=0; $i < count($column_headers); $i++) {
						if (isset($_POST[$column_headers[$i]]) && !empty($_POST[$column_headers[$i]])) {
							$insert_contents[$column_headers[$i]] = $_POST[$column_headers[$i]];
						}
					}
					$insert_contents['created'] = date("Y-m-d h:i:sa");

					$wpdb->insert(
					    $_POST["table"],
						$insert_contents
					);
					$record_id = $wpdb->insert_id;
					wp_redirect($url);
//					$wpdb->print_error();
					exit;

					break;

				case 'update':
					$update_contents = array();

					$post_id = (int)$_POST["key"];

					for ($i=0; $i < count($column_headers); $i++) {
						if (isset($_POST[$column_headers[$i]]) && !empty($_POST[$column_headers[$i]])) {
							if (!restricted_fields($_POST[$column_headers[$i]], 'update')) {
								$update_contents[$column_headers[$i]] = $_POST[$column_headers[$i]];
							}
						}
					}
					$update_contents['modified'] = date("Y-m-d h:i:sa");

					$update_result = $wpdb->update(
						$_POST["table"],
						$update_contents,
						array('ID' => '%d'),
						null,
						$post_id
					);

					if (!$update_result) {
						print_r($update_contents);
						$wpdb->print_error();
						//die();
					}

					wp_safe_redirect($url);
					exit;

					break;

				case 'delete':
					$post_id = (int)$_POST["key"];

					if ($_POST["delete_checkbox"]) {
						// don't do it!!
					}


					$delete_result = $wpdb->delete(
						$_POST["table"],
						array('ID' => $post_id)
					);

					if (!$delete_result) {
						$wpdb->print_error();
						//die();
					}

					wp_safe_redirect($url);
					exit;

					break;

				default:
					wp_safe_redirect($url);
					exit;

					break;
			}
		}
	}

}
add_action( 'admin_post_nopriv_form_response', __NAMESPACE__ . '\\do_form_response' );
add_action( 'admin_post_form_response', __NAMESPACE__ . '\\do_form_response' );

/**
 * Do Search
 *
 * Returns search results
 *
 * This...
 *
 * @param string $search_criteria  Passed search criteria
 * @param string $table_name  Table to be searched
 * @param array $column_headers  Table column names
 * @return string $result_id  One or more ID values from output of search
 *
 * @author Vinland Media, LLC.
 * @package CRUDMeistR
 */
function do_search($search_criteria = null, $table_name = null, $column_headers = null) {
	global $wpdb;
	global $wp;

	$select_statement = 'select * from ' . $table_name . ' where ';

	for ($i=0; $i < count($column_headers); $i++) {
		if (0 != $i) {
			$select_statement .= 'or ';
		}
		$select_statement .= $column_headers[$i] . ' like "%' . $search_criteria . '%" ';
	}

	$results = $wpdb->get_results($select_statement);

	if (!$results || empty($results)) {
		return 'No data found.';
	} else {
		$output = '';
		$result_count = 0;
		$result_id = $table_name . ':';

		// for ($i=0; $i < count($column_headers); $i++) {
		// 	$output .= '<th>' . $column_header[$i])) . '</th>';
		// }
		//
		foreach ($results as $result_key => $result_value) {

			for ($i=0; $i < count($column_headers); $i++) {
				if ('id' == $column_headers[$i]) {
					$result_id .= $result_value->{$column_headers[$i]} . ';';
				}
				$output .= $column_headers[$i] . ':' . $result_value->$column_headers[$i] . ';';
			}
			$result_count++;
		}

		//if (1 == $result_count) {
		//	return '(1)' . $output;
		//} else {
			//$url = home_url() . '/search-results/' . '?search-results=' . $table_name . ':' . $result_id;
			return '(' . $result_count . '):' . $result_id;
		//}
	}
}

function do_find_result() {
	global $wp;

}
add_action('admin_post_nopriv_find_result', __NAMESPACE__ . '\\do_find_result');
add_action('admin_post_find_result', __NAMESPACE__ . '\\do_find_result');


function add_query_vars_filter($vars) {
  $vars[] = "search-results";
  $vars[] = "search-criteria";
  return $vars;
}
add_filter('query_vars', __NAMESPACE__ . '\\add_query_vars_filter');
