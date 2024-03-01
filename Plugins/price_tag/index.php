<?php
/*
  Plugin Name: Price Tag Plugin
  Plugin URI: https://osclasspoint.com/
  Description: Tag your listings with price label to bring more attraction
  Version: 1.0.1
  Author: MB Themes
  Author URI: http://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: price_tag
  Plugin update URI: price_tag
  Support URI: https://forums.osclasspoint.com/general-plugins-discussion/
  Product Key: 5vkOOrMEUgMI7oIXC7Rx
*/


require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelPRT.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';




// INSTALL FUNCTION - DEFINE VARIABLES
function prt_call_after_install() {
  $tags = array();
  
  for($i=1;$i<=4;$i++) {
    $tags[$i] = osc_base_url() . 'oc-content/plugins/price_tag/img/tag' . $i . '.png';
  }
  
  $tags = json_encode($tags, JSON_UNESCAPED_SLASHES);

  osc_set_preference('enabled', 1, 'plugin-price_tag', 'INTEGER');
  osc_set_preference('size', 'SMALL', 'plugin-price_tag', 'STRING');
  osc_set_preference('price_tags', $tags, 'plugin-price_tag', 'STRING');
  osc_set_preference('hook_item', 1, 'plugin-price_tag', 'INTEGER');
  osc_set_preference('hook_publish', 1, 'plugin-price_tag', 'INTEGER');
  
  
  
  ModelPRT::newInstance()->install();
}


function prt_call_after_uninstall() {
  ModelPRT::newInstance()->uninstall();
}



// ADMIN MENU
function prt_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/price_tag/css/admin.css?v=' . date('YmdHis') . '" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/price_tag/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/price_tag/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/price_tag/js/admin.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/price_tag/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/price_tag/js/bootstrap-switch.js"></script>';



  if($title == '') { $title = __('Configure', 'price_tag'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>Price Tag Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=price_tag/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'price_tag') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function prt_footer() {
  $pluginInfo = osc_plugin_get_info('price_tag/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="https://osclasspoint.com"><img src="https://osclasspoint.com/favicon.ico" alt="MB Themes" /> osclasspoint.com</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'price_tag') . '</a>';
  $text .= '<a target="_blank" href="https://forums.osclasspoint.com/"><i class="fa fa-comments"></i> ' . __('Support Forums', 'price_tag') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'price_tag') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  return $text;
}



// ADD MENU LINK TO PLUGIN LIST
function prt_admin_menu() {
echo '<h3><a href="#">Price Tag Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Configure', 'price_tag') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','prt_admin_menu', 1);



// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function prt_conf() {
  osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php');
}

osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'prt_conf');	


// CALL WHEN PLUGIN IS ACTIVATED - INSTALLED
osc_register_plugin(osc_plugin_path(__FILE__), 'prt_call_after_install');

// SHOW UNINSTALL LINK
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'prt_call_after_uninstall');

?>