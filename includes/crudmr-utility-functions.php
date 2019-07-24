<?php

namespace VinlandMedia\CRUDMeistR;

/**
 * Created by PhpStorm.
 * User: kenkitchen
 * Date: 10/27/17
 * Time: 8:27 AM
 */
/**
 * Get Table Columns
 *
 * This function returns all of the column names in a table.
 *
 * This plugin needs to get columns so many times, it needed to be a function to
 * help keep things DRY.  Very straight-forward.
 *
 * @param string $table_name The table from which columns are being requested
 * @return object $results An object containing the table column information
 *
 * @author Vinland Media, LLC.
 * @package crudmeistr
 */
function get_table_columns($table_name = null) {
	global $wpdb;

	$results = $wpdb->get_results(
		'select t.table_name, c.column_name, c.data_type ' .
		'from information_schema.columns c, information_schema.tables t ' .
		'where t.table_name = c.table_name ' .
		'and t.table_name = "' . $table_name . '"');

	if (!$results || empty($results)) {
		return null;
	} else {
		return $results;
	}

}

function strip_db_prefix($table_name) {
	global $wpdb;

	return substr($table_name, strlen($wpdb->prefix));

}


function find_in_multi_array($needle, $haystack) {
	// TODO this is a stub

	return true;

}