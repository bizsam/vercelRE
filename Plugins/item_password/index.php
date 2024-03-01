<?php
/*
  Plugin Name: Item Password Plugin
  Plugin URI: https://osclasspoint.com/osclass-plugins/extra-fields-and-other/item-password-osclass-plugin-i182
  Description: Listing owner will get functionality to manage listings (edit/delete) using their custom password.
  Version: 1.1.1
  Author: MB Themes
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: item_password
  Plugin update URI: item-password
  Support URI: https://forums.osclasspoint.com/
  Product Key: bieyVwillf6RUKpr4hav
*/

require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelITP.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';


osc_enqueue_style('itp-user-style', osc_base_url() . 'oc-content/plugins/item_password/css/user.css?v=' . date('YmdHis'));
osc_register_script('itp-user', osc_base_url() . 'oc-content/plugins/item_password/js/user.js?v=' . date('YmdHis'), array('jquery'));
osc_enqueue_script('itp-user');


// INSTALL FUNCTION - DEFINE VARIABLES
function itp_call_after_install() {
  osc_set_preference('enable', 1, 'plugin-item_password', 'INTEGER');
  osc_set_preference('hook_edit', 1, 'plugin-item_password', 'INTEGER');
  osc_set_preference('hook_delete', 1, 'plugin-item_password', 'INTEGER');
  osc_set_preference('hook_form', 1, 'plugin-item_password', 'INTEGER');
  osc_set_preference('show_with_password', 0, 'plugin-item_password', 'INTEGER');
  osc_set_preference('show_with_user', 0, 'plugin-item_password', 'INTEGER');
  osc_set_preference('style_buttons', 1, 'plugin-item_password', 'INTEGER');
  osc_set_preference('password_type', 'text', 'plugin-item_password', 'STRING');

  ModelITP::newInstance()->install();
}


function itp_call_after_uninstall() {
  ModelITP::newInstance()->uninstall();
}


// ADMIN MENU
function itp_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/item_password/css/admin.css?v=' . date('YmdHis') . '" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/item_password/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/item_password/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/item_password/js/admin.js?v=' . date('YmdHis') . '"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/item_password/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/item_password/js/bootstrap-switch.js"></script>';

  if( $title == '') { $title = __('Configure', 'item_password'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>Item Password Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=item_password/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'item_password') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function itp_footer() {
  $pluginInfo = osc_plugin_get_info('item_password/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="https://osclasspoint.com"><img src="https://osclasspoint.com/favicon.ico" alt="OsclassPoint Market" /> OsclassPoint Market</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'item_password') . '</a>';
  $text .= '<a target="_blank" href="https://forums.osclasspoint.com/"><i class="fa fa-handshake-o"></i> ' . __('Support Forums', 'item_password') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'item_password') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  return $text;
}



// ADD MENU LINK TO PLUGIN LIST
function itp_admin_menu() {
echo '<h3><a href="#">Item Password Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Configure', 'item_password') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','itp_admin_menu', 1);



// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function itp_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/configure.php' );
}

osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'itp_conf' );	


// CALL WHEN PLUGIN IS ACTIVATED - INSTALLED
osc_register_plugin(osc_plugin_path(__FILE__), 'itp_call_after_install');

// SHOW UNINSTALL LINK
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'itp_call_after_uninstall');

?>