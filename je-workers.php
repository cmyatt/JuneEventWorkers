<?php

/*
 * Plugin Name: June Event Workers
 * Description: Worker management system for the June Event.
 * Version:     0.1
 * Author:      Chris Myatt
*/

require_once('constants.php');
require_once('debug.php');
require_once('applicant.php');
require_once('worker.php');
require_once('settings.php');

// Menu pages
define('MENU_PAGE', ABSPATH.'wp-content/plugins/je-workers/menu-pages/');
require_once(MENU_PAGE.'applicants.php');
require_once(MENU_PAGE.'edit_applicant.php');
require_once(MENU_PAGE.'workers.php');
require_once(MENU_PAGE.'edit_worker.php');
require_once(MENU_PAGE.'settings.php');
require_once(MENU_PAGE.'debug.php');

/*
 * Want to be able to integrate this plugin with je-ticketing so that when a worker
 * signs in to the site they see their worker status as well as their ticket status.
 * Can then automatically ask worker if they want a refund for their ticket(s), or
 * if they want them re-named (provide a web interface for this so no emailing ticketing
 * necessary).
*/

// Hook registration
register_activation_hook(__FILE__, 'activate');
add_action('admin_init', 'register_scripts');
add_action('admin_menu', 'create_menu');

/* Called on activation.
 * Don't just call install() directly - there may be other
 * work we want to do here in the future
*/
function activate() {
	require_once(ABSPATH . 'wp-content/plugins/je-workers/install.php');
	install();
}

function register_scripts() {
	// CSS
	wp_register_style(
		'JEWorkersSettingsStyleSheet',
		WP_PLUGIN_URL.'/je-workers/includes/css/settings.css'
		);
	wp_register_style(
		'JEWorkersDebugStyleSheet',
		WP_PLUGIN_URL.'/je-workers/includes/css/debug.css'
		);

	// JS
	wp_register_script(
		'JEWorkersSettingsScript',
		WP_PLUGIN_URL.'/je-workers/includes/js/settings.js'
		);
	wp_register_script(
		'JEWorkersDebugScript',
		WP_PLUGIN_URL.'/je-workers/includes/js/debug.js'
		);
}

function create_menu() {
	/* Workers
	 *   +------> Applicants 		// interface for applicants table
	 *   +------> Edit Applicant 	// edit info for individual applicants
	 *   +------> Workers 			// interface for workers table
	 *   +------> Edit Worker 		// edit info for individual workers
	 *   +------> Settings 			// interface for settings object
	 *   +------> Debug 			// hidden when Debug->show is false
	*/
	// Top-level 'Workers' menu
	$title_tag = 'Workers';
	$menu_name = 'Workers';
	$capability = 'edit_pages';	// Editor, Admin and SuperAdmin
	$main_menu_id = 'je_workers';
	$function_name = 'workers_page';
	add_object_page($title_tag, $menu_name, $capability, $main_menu_id, $function_name);
	
	// 'Edit Worker' sub-menu
	$menu_name = 'Edit Worker';
	$app_menu_id = 'je_workers_edit_worker';
	$function_name = 'edit_worker_page';
	add_submenu_page($main_menu_id, $title_tag, $menu_name, $capability, $app_menu_id, $function_name);

	// 'Applicants' sub-menu
	$menu_name = 'Applicants';
	$app_menu_id = 'je_workers_applicants';
	$function_name = 'applicants_page';
	add_submenu_page($main_menu_id, $title_tag, $menu_name, $capability, $app_menu_id, $function_name);
	
	// 'Edit Applicant' sub-menu
	$menu_name = 'Edit Applicant';
	$app_menu_id = 'je_workers_edit_applicant';
	$function_name = 'edit_applicant_page';
	add_submenu_page($main_menu_id, $title_tag, $menu_name, $capability, $app_menu_id, $function_name);

	// Settings sub-menu
	$menu_name = 'Settings';
	$app_menu_id = 'je_workers_settings';
	$function_name = 'settings_page';
	$page = add_submenu_page($main_menu_id, $title_tag, $menu_name, $capability, $app_menu_id, $function_name);
	
	// Add debug stylesheet and script to the Settings page only
	add_action('admin_print_styles-'.$page, 'queue_settings_scripts');
	add_action('admin_print_scripts-'.$page, 'queue_settings_scripts');

	// Debug sub-menu
	$menu_name = 'Debug';
	$app_menu_id = 'je_workers_debug';
	$function_name = 'debug_page';
	$page = add_submenu_page($main_menu_id, $title_tag, $menu_name, $capability, $app_menu_id, $function_name);

	// Add debug stylesheet and script to the Debug page only
	add_action('admin_print_styles-'.$page, 'queue_debug_scripts');
	add_action('admin_print_scripts-'.$page, 'queue_debug_scripts');
}

function queue_settings_scripts() {
	wp_enqueue_style('JEWorkersSettingsStyleSheet');
	wp_enqueue_script('JEWorkersSettingsScript');
}

function queue_debug_scripts() {
	wp_enqueue_style('JEWorkersDebugStyleSheet');
	wp_enqueue_script('JEWorkersDebugScript');
}

?>
