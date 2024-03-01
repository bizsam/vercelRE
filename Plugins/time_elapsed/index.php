<?php
/*
  Plugin Name: Time Elapsed Plugin
  Plugin URI: https://osclasspoint.com/
  Description: Plugin shows the times takes to render each page
  Version: 1.1.0
  Author: OsclassPoint
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: time_elapsed
  Plugin update URI: time_elapsed
  Support URI: https://forums.osclasspoint.com/free-plugins/
  Product Key: DS9mru7ri7zFBwdxxzQW
*/


$timer = null;

function time_elapsed_header() {
	global $timer;
	$timer = microtime();
}

function time_elapsed_footer() {
	global $timer;
	echo '<!-- time to load: ', microtime() - $timer , ' -->', PHP_EOL;
}

osc_register_plugin(osc_plugin_path(__FILE__), '');
osc_add_hook('footer', 'time_elapsed_footer', 10);
osc_add_hook('init', 'time_elapsed_header', 1);