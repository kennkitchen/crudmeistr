<?php

namespace VinlandMedia\CRUDMeistR;

/**
 * Created by PhpStorm.
 * User: kenkitchen
 * Date: 10/28/17
 * Time: 1:57 PM
 */
function crudmr_main_menu_page() {
	?>
	<div class="wrap cm-panel">
		<p><img src="<?= plugins_url('crudmeistr') ?>/admin/img/CRUDMeistR-img-logo-half.png"></p>
		<h2>About</h2>
		<p>CRUDMeistR allows you to create new tables inside your WordPress database and then generate "CRUD" (<strong>C</strong>reate, <strong>R</strong>ead, <strong>U</strong>pdate, and <strong>D</strong>elete) screens around those tables.  CRUDmeistR allows you to create mini-applications that live in WordPress without any programming knowledge.</p>
		<h3>How it Works</h3>
		<p>There are a lot of good form-building tools for WordPress which focus on helping you create nice ways to collect data. CRUDMeistR is a little different in that it thinks in terms of the data first, and automatically builds the "forms" on top of your table(s).</p>
		<p>In order for this to work, certain conventions MUST be observed. PLEASE READ.</p>
		<h3>Tables</h3>
		<p>In a database, a table is the place where data is stored. As someone who works with WordPress, you're likely familiar with the type of table that you use in a page or post. It's very simlar; database tables are made up of rows and columns.</p>
		<p>WordPress uses a prefix for all of its table names. Even if you happen to know what your prefix is, DON'T USE IT with CrudMeistR. We want you to enter your table names without the prefix because</p>
		<ul>
			<li>a) that's how people refer to their data (it's easier to say "the recipies table" than "the W-P-underscore-recipies table"; and...</li>
			<li>b) we put the prefix in for you anyway.</li>
		</ul>
		<p>The other thing you need to know about table naming is that you should use a plural name. Specifically, the name should end in "s" even if it's bad spelling.</p>
		<h4>Correct Table Name Examples</h4>
		<ul>
			<li>-books</li>
			<li>-cars</li>
			<l1>-commoditys -OR- commodities (depending on whether or not you care most about how the plural or the singluar name looks)</l1>
		</ul>
		<p>In the examples above, the singluar names would come out as "book," "car," "commodity," or "commoditie."  (The latter is because we haven't had time to add a dicitonary to our app.)</p>
		<p>Also, it's best if you stick to all lowercase when you name a table.</p>
		<h4>Parents and Children</h4>
		<p>If you're familiar with databases, you may know about "parent-child" relationships. The cool thing about CRUDMeistR (well, <em>one</em> of the cool things) is that we take care of the mechanics for you. So if you happen to know about databases and you know what a "foreign key" is, please don't give it another thought; we take care of that too.</p>
		<p>If the previous paragraph is Greek to you (and assuming you don't actually speak or read Greek), just ignore it. The take-away from this section is: sometimes you have one type of thing to store in a database and another type of thing that relates to the first. For example, you might have "shelves" and, on each shelf, a lot of different "products". In this example, the "shelves" table would be the parent of the "products" table. All you need to know is your data; when used properly, CRUDMeistR handles the hinky stuff.</p>
		<h2>Get Started</h2>
		<p>(Finally, right?)</p>
		<p>Go to <a href="/wp-admin/admin.php?page=crudmr_submenu1">Create a Table</a> and, well, create a table.</p>
		<p>Next, go to <a href="/wp-admin/admin.php?page=crudmr_submenu2">Scaffolding</a> and enter your new table name (no WP prefix, remember?) and press "Go".</p>
		<p>You'll have new pages in your WP Admin "Pages" section: ...</p>
	</div>
	<?php
}


/*
* Queries page
*/
function crudmr_submenu1_page() {
	global $wpdb;
	?>
	<div class="wrap cm-panel">
		<img src="<?= plugins_url('crudmeistr') ?>/admin/img/CRUDMeistR-logo-MD.png">
		<h3>Create a Table</h3>
		<p>Use this screen to create new tables. Give the table a plural name (ending in "s" even if it's bad spelling! Valid table names might be "books" or "cars" or "accessorys".) Wordpress uses a table prefix that is defined in wp_config.php; please DO NOT use the prefix here.</p>
		<p>If you happen to know a bit about databases, you probably know about keys or even foreign keys; don't use there here. We have the keys covered for you.</p>
		<p>Enter a table name and press and add as many columns (data fields) as you need.</p>
		<form method="POST" action="<?= admin_url('admin-post.php') ?>" name="crudmr-database">
			<input type="hidden" name="action" value="database_response">
			<label for="table-name">Table Name</label>
			<input type=text name="table-name" pattern="^[a-zA-Z_]+$" title="letters and underscores only"><br>
			<br />

			<div class="repeat">
				<table class="wrapper" width="100%">
					<thead>
					<tr>
						<td width="10%" colspan="4"><span class="add"><i class="fa fa-plus" aria-hidden="true"></i> Add Columns</span></td>
					</tr>
					</thead>
					<tbody class="container">
					<tr class="template row">
						<td width="10%"><span class="move"><i class="fa fa-arrows" aria-hidden="true"></i></span></td>

						<td width="20%">
							<label for="column-name">Name: </label>
							<input type="text" name="column-name[{{row-count-placeholder}}]" pattern="^[a-zA-Z_]+$" title="letters and underscores only" />
						</td>
						<td width="15%">
							<label for="column-type">Data Type: </label>

							<select name="column-type[{{row-count-placeholder}}]" onchange="selectChange(this, {{row-count-placeholder}})">
								<option value="varchar">varchar</option>
								<option value="text">text</option>
								<option value="int">integer</option>
								<option value="decimal">decimal</option>
								<option value="date">date</option>
								<option value="time">time</option>
								<option value="datetime">datetime</option>
								<option value="year">year</option>
							</select>

						</td>
						<td width="15%">
							<label for="column-length" id="lengthLabel[{{row-count-placeholder}}]">Length: </label>
							<input type="text" id="lengthColumn[{{row-count-placeholder}}]" name="column-length[{{row-count-placeholder}}]" value="50" pattern="^\d+$" title="integer values only" />
						</td>
						<td width="15%">
							<label for="decimal-places" style="display:none;" id="decimalLabel[{{row-count-placeholder}}]">Decimal Places: </label>
							<input type="text" style="display:none;" id="decimalColumn[{{row-count-placeholder}}]" name="decimal-places[{{row-count-placeholder}}]" value="2" pattern="^\d+$" title="integer values only" />
						</td>
						<td width="15%">
							<label for="column-pattern">Validate as: </label>

							<select name="column-pattern[{{row-count-placeholder}}]">
								<option value="none">none</option>
								<option value="^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$">email address</option>
							</select>

						</td>
						<td width="10%"><span class="remove"><i class="fa fa-minus" aria-hidden="true"></i> Remove Column</span></td>
					</tr>
					</tbody>
				</table>
			</div><br />
			<div>
				<h3>Select a Parent Table (optional)</h3>
				<p>Parent tables come into play when you have two tables with a relationship to one another where there are normally multiple rows of one that relates to a single row of another.</p>
				<p>A parent table is a table that "owns" another table. For instance, a table named "authors" might be the parent of a table of "books". Or "departments" might be the parent of "employees".</p>
				<p>A parent table is NOT required; it's just handy if you have tables where such relationships can exist. With CRUDMeistR, the parent table should be created first; then come back to this screen and add a the child and pick the parent from the dropdown.</p>
				<?php
				$existing_tables = $wpdb->get_results(
					'select t.table_name ' .
					'from information_schema.tables t ' .
					'where t.table_name like "' . $wpdb->prefix . '%" ' .
					'and t.table_name not in ("' . $wpdb->prefix . 'commentmeta", "' .
					$wpdb->prefix . 'comments", "' . $wpdb->prefix . 'links", "' .
					$wpdb->prefix . 'options", "' . $wpdb->prefix . 'postmeta", "' .
					$wpdb->prefix . 'posts", "' . $wpdb->prefix . 'term_relationships", "' .
					$wpdb->prefix . 'term_taxonomy", "' . $wpdb->prefix . 'termmeta", "' .
					$wpdb->prefix . 'terms", "' . $wpdb->prefix . 'usermeta", "' .
					$wpdb->prefix . 'users") '
				);

				echo create_select_box(
					$title = 'Parent Table? ',
					$html_name = 'parent-table',
					$current_value = null,
					$dataset = $existing_tables,
					$static_values = null);
				?>
			</div>


			<br />
			<button value="submit" name="submit">Create Table</button><br />
		</form>
		<hr />

		<hr>
	</div>
	<script>
        jQuery(function() {
            jQuery('.repeat').each(function() {
                jQuery(this).repeatable_fields();
            });
        });
        function selectChange(obj, rowCount) {

            var selectBox = obj;
            var selected = selectBox.options[selectBox.selectedIndex].value;

            var lengthLabel = document.getElementById("lengthLabel[" + rowCount + "]");
            var lengthColumn = document.getElementById("lengthColumn[" + rowCount + "]");

            var decimalLabel = document.getElementById("decimalLabel[" + rowCount + "]");
            var decimalColumn = document.getElementById("decimalColumn[" + rowCount + "]");

            if (selected === 'decimal') {
                lengthLabel.style.display = "block";
                lengthColumn.style.display = "block";

                decimalLabel.style.display = "block";
                decimalColumn.style.display = "block";
            }
            else if (selected === 'varchar' || selected === 'int') {
                lengthLabel.style.display = "block";
                lengthColumn.style.display = "block";
            } else {
                lengthLabel.style.display = "none";
                lengthColumn.style.display = "none";

                decimalLabel.style.display = "none";
                decimalColumn.style.display = "none";
            }
        }
	</script>
	<?php
}


/*
* Databases page
*/
function crudmr_submenu2_page() {
	global $wpdb;
	?>
	<div class="wrap cm-panel">
		<img src="<?= plugins_url('crudmeistr') ?>/admin/img/CRUDMeistR-logo-MD.png">
		<h3>Scaffolding</h3>
		<p>Use the box below if you would like to generate pages with shortcodes for your new table.</p>
		<p>This will create a page with the same name as your table containing the shortcode to display the list of all items in the table. Subhordinate to this page, it will also create Add, Update, View, and Delete pages which also contain the appropriate shortcodes for your table and its functions. You can then add these pages to a menu for easy access.</p>
		<p>This is an optional feature; you can skip it entirely and add the shortcodes manually if you wish.</p>
		<p>Enter a table name and press "Go":</p>
		<form method="POST" action="<?= admin_url('admin-post.php') ?>" name="crudmr-scaffolding">
			<input type="hidden" name="action" value="scaffold_response">

			<?php
			$existing_tables = $wpdb->get_results(
				'select t.table_name ' .
				'from information_schema.tables t ' .
				'where t.table_name like "' . $wpdb->prefix . '%" ' .
				'and t.table_name not in ("' . $wpdb->prefix . 'commentmeta", "' .
				$wpdb->prefix . 'comments", "' . $wpdb->prefix . 'links", "' .
				$wpdb->prefix . 'options", "' . $wpdb->prefix . 'postmeta", "' .
				$wpdb->prefix . 'posts", "' . $wpdb->prefix . 'term_relationships", "' .
				$wpdb->prefix . 'term_taxonomy", "' . $wpdb->prefix . 'termmeta", "' .
				$wpdb->prefix . 'terms", "' . $wpdb->prefix . 'usermeta", "' .
				$wpdb->prefix . 'users") '
			);

			echo create_select_box(
				$title = 'Table Name ',
				$html_name = 'table-name',
				$current_value = null,
				$dataset = $existing_tables,
				$static_values = null);
			?>

			<button value="submit" name="submit">Go</button><br />
		</form>
		<hr />
		<hr>
	</div>
	<?php
}

/*
* Settings page
*/
function crudmr_settings_page() {
	?>
	<div class="wrap cm-panel">
		<img src="<?= plugins_url('crudmeistr') ?>/admin/img/CRUDMeistR-logo-MD.png">
		<h3>Options</h3>
		<p>
			Suspendisse blandit, velit ut rhoncus ullamcorper, nisi lacus accumsan ligula, id venenatis odio nulla et metus. Fusce ligula lacus, tincidunt ut auctor et, luctus non enim. Praesent iaculis ante in egestas suscipit. Aliquam euismod lorem sit amet felis lacinia egestas. Duis sem ante, rutrum sed finibus quis, molestie ac metus. Praesent pulvinar pretium lacinia. Cras volutpat nec neque vel vulputate. Phasellus nec posuere libero. Maecenas condimentum massa nec luctus accumsan. Ut in magna efficitur, dapibus neque quis, pretium mi. Suspendisse iaculis diam nibh, vel condimentum enim pretium et. Integer fermentum auctor ex non efficitur. Nullam hendrerit scelerisque ex, eget maximus lacus mollis ut. Sed ultricies egestas fermentum. Nam sed tristique dui, at mattis sem.
		</p>
		<h3>Settings</h3>
		<p>
			Suspendisse blandit, velit ut rhoncus ullamcorper, nisi lacus accumsan ligula, id venenatis odio nulla et metus. Fusce ligula lacus, tincidunt ut auctor et, luctus non enim. Praesent iaculis ante in egestas suscipit. Aliquam euismod lorem sit amet felis lacinia egestas. Duis sem ante, rutrum sed finibus quis, molestie ac metus. Praesent pulvinar pretium lacinia. Cras volutpat nec neque vel vulputate. Phasellus nec posuere libero. Maecenas condimentum massa nec luctus accumsan. Ut in magna efficitur, dapibus neque quis, pretium mi. Suspendisse iaculis diam nibh, vel condimentum enim pretium et. Integer fermentum auctor ex non efficitur. Nullam hendrerit scelerisque ex, eget maximus lacus mollis ut. Sed ultricies egestas fermentum. Nam sed tristique dui, at mattis sem.
		</p>
	</div>
	<?php
}


function crudmr_admin_menus() {
	add_menu_page('CRUDMeistR', 'CRUDMeistR', 'crudmr_admin', 'crudmr_main_menu',
		__NAMESPACE__ . '\\crudmr_main_menu_page');

	add_submenu_page('crudmr_main_menu', 'CRUDMeistR Table Creator',
		'Table Creator', 'crudmr_admin', 'crudmr_submenu1', __NAMESPACE__ . '\\crudmr_submenu1_page');

	add_submenu_page('crudmr_main_menu', 'CRUDMeistR Scaffolding',
		'Scaffolding', 'crudmr_admin', 'crudmr_submenu2', __NAMESPACE__ . '\\crudmr_submenu2_page');

	add_submenu_page('crudmr_main_menu', 'CRUDMeistR Settings',
		'Settings', 'crudmr_admin', 'crudmr_settings', __NAMESPACE__ . '\\crudmr_settings_page');
}
add_action('admin_menu', __NAMESPACE__ . '\\crudmr_admin_menus');

function create_select_box($title = null, $html_name = null, $current_value = null, $dataset = null, $static_values = null) {

	$select_box = '<td>' .__($title, 'crudmr').':</td>';

	$select_box .= '<td><select name="' . esc_html($html_name) . '">';
	if ($current_value == '') {
		$select_box .= '<option value="" selected>(none)</option>';
	} else {
		$select_box .= '<option value="">None</option>';
	}
	if ($dataset) {
		foreach($dataset as $datarow) {
			$select_box .=  '<option value="' . esc_html($datarow->table_name) . '" ' . selected($datarow->table_name, esc_attr($current_value), false) . '>' . esc_html($datarow->table_name) .
			                '</option>';
		}
	} elseif ($static_values) {
		foreach($static_values as $static_value) {
			$select_box .=  '<option value="' . esc_html($static_value['post_name']) . '" ' . selected($static_value['post_name'], esc_attr($current_value), false) . '>' . esc_html($static_value['post_title']) . ' (' . esc_html($static_value['post_name']) . ')' .
			                '</option>';
		}
	} else {
		$select_box .= '<option value="">(Nothing to display.)</option>';
	}
	$select_box .= '</select ' . is_disabled() . '></td>';

	return $select_box;
}

function is_disabled() {
	if (!user_can(wp_get_current_user(), 'transition_tasks')) {
		return 'disabled';
	} else {
		return null;
	}
}

function do_scaffold_response() {
	global $wpdb;

	$wp_error = null;

	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		if (isset($_POST["table-name"]) && !empty($_POST["table-name"])) {
			$my_table_name = sanitize_text_field($_POST["table-name"]);

			// remove the database table prefix
			if (substr($my_table_name, 0, strlen($wpdb->prefix)) == $wpdb->prefix) {
				$my_table_name = substr($my_table_name, strlen($wpdb->prefix)); // ltrim($my_table_name, $wpdb->prefix);
			}

			$top_page = get_page_by_title('CRUDmeistR for Wordpress Home');

			$table_page = array(
				'post_title'    => ucwords($my_table_name),
				'post_content'  => '[crudmr table=' . $wpdb->prefix . $my_table_name . ' function=list]',
				'post_status'   => 'publish',
				'post_type'   	=> 'page',
				'post_parent'	=> $top_page->ID
			);
			$table_page_id = wp_insert_post($table_page, $wp_error);

			$add_page = array(
				'post_title'    => 'Add ' . ucwords($my_table_name),
				'post_content'  => '[crudmr table=' . $wpdb->prefix . $my_table_name . ' function=add]',
				'post_status'   => 'publish',
				'post_type'   	=> 'page',
				'post_parent'	=> $table_page_id
			);
			$add_page_id = wp_insert_post($add_page, $wp_error);

			$update_page = array(
				'post_title'    => 'Update ' . ucwords($my_table_name),
				'post_content'  => '[crudmr table=' . $wpdb->prefix . $my_table_name . ' function=update]',
				'post_status'   => 'publish',
				'post_type'   	=> 'page',
				'post_parent'	=> $table_page_id
			);
			$update_page_id = wp_insert_post($update_page, $wp_error);

			$view_page = array(
				'post_title'    => 'View ' . ucwords($my_table_name),
				'post_content'  => '[crudmr table=' . $wpdb->prefix . $my_table_name . ' function=view]',
				'post_status'   => 'publish',
				'post_type'   	=> 'page',
				'post_parent'	=> $table_page_id
			);
			$view_page_id = wp_insert_post($view_page, $wp_error);

			$delete_page = array(
				'post_title'    => 'Delete ' . ucwords($my_table_name),
				'post_content'  => '[crudmr table=' . $wpdb->prefix . $my_table_name . ' function=delete]',
				'post_status'   => 'publish',
				'post_type'   	=> 'page',
				'post_parent'	=> $table_page_id
			);
			$delete_page_id = wp_insert_post($delete_page, $wp_error);

		}

		wp_safe_redirect('edit.php?post_type=page');
	} else {
		wp_safe_redirect('admin.php?page=crudmr_submenu1');
	}

}
add_action('admin_post_nopriv_scaffold_response', __NAMESPACE__ . '\\do_scaffold_response');
add_action('admin_post_scaffold_response', __NAMESPACE__ . '\\do_scaffold_response');

function do_database_response() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$columns = array();

	if (isset($_POST["table-name"]) && !empty($_POST["table-name"])) {

		for ($i=0; $i < 99; $i++) {
			if (check_row($_POST["column-name"][$i], $_POST["column-type"][$i], $_POST["column-length"][$i], $_POST["decimal-places"][$i])) {
				array_push($columns, [
					'name' => sanitize_text_field($_POST["column-name"][$i]),
					'type' => sanitize_text_field($_POST["column-type"][$i]),
					'length' => intval($_POST["column-length"][$i]),
					'decimal_places' => intval($_POST["decimal-places"][$i]),
				]);
			} else {
				break;
			}
		}

		if ($i > 0) {
			if (substr($_POST["table-name"], strlen($_POST["table-name"])-1, 1) == 's') {
				$table_name = sanitize_text_field($_POST["table-name"]);
			} else {
				$table_name = sanitize_text_field($_POST["table-name"]) . 's';
			}

			// if prefix was entered, remove it
			if (substr($table_name, 0, strlen($wpdb->prefix)) == $wpdb->prefix) {
				$table_name = ltrim($table_name, $wpdb->prefix);
			}

			$sql = 'CREATE TABLE ' . $wpdb->prefix . $table_name .
			       ' (id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY, ';

			foreach ($columns as $column) {

				if ($column['type'] === 'decimal') {
					$sql .= $column['name'] . ' ' . $column['type'] . '(' . $column['length'] . '),' . ' ';
				}
				elseif ($column['type'] === 'varchar' || $column['type'] === 'int') {
					$sql .= $column['name'] . ' ' . $column['type'] . '(' . $column['length'] . '),' . ' ';
				} else {
					$sql .= $column['name'] . ' ' . $column['type'] . ',' . ' ';
				}

			}

			if ($_POST["parent-table"] != '') {
				$sql .= substr(sanitize_text_field($_POST["parent-table"]), strlen($wpdb->prefix), -1) . '_id INT, ';
			}

			$sql .= 'created DATETIME DEFAULT NULL, ' .
                'modified DATETIME DEFAULT NULL) ' .
                $charset_collate . ';';

			if ( ! function_exists('dbDelta') ) {
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			}

			dbDelta($sql);

		}

	}

	wp_safe_redirect('admin.php?page=crudmr_submenu2');

}

add_action('admin_post_nopriv_database_response', __NAMESPACE__ . '\\do_database_response');
add_action('admin_post_database_response', __NAMESPACE__ . '\\do_database_response');

function check_row($name = null, $type = null, $length = null, $decimal_places = null) {
	if ((!isset($name) || empty($name)) || (!isset($type))) {
		return false;
	}

	if (('varchar' == $type || 'int' == $type) && (!isset($length) || empty($length))) {
		return false;
	} elseif (('decimal' == $type) && (!isset($decimal_places) || empty($decimal_places))) {
		return false;
	}

	return true;

}
