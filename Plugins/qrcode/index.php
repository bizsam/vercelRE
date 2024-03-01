<?php
/*
  Plugin Name: QR Code Generator Plugin
  Plugin URI: https://osclasspoint.com/
  Description: Generate QR code images for listings those contain link to listing
  Version: 2.0.0
  Author: MB Themes
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: qrcode
  Plugin update URI: qrcode
  Support URI: https://forums.osclasspoint.com/
  Product Key: RvR9IqCQA0IVv57n7PFx
*/

define('QRC_VERSION_ID', 100);            // Version of DB state
define('QRC_OUTER_FRAME', 2);             // 1-4


require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelQRC.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';



// INSTALL FUNCTION - DEFINE VARIABLES
function qrc_call_after_install() {
  osc_set_preference('size', 3, 'plugin-qrcode', 'INTEGER');
  osc_set_preference('hook', '', 'plugin-qrcode', 'STRING');
  osc_set_preference('asset_version', 0, 'plugin-qrcode', 'INTEGER');

  osc_set_preference('version', QRC_VERSION_ID, 'plugin-qrcode', 'INTEGER');

  ModelQRC::newInstance()->install();
}



// AUTOMATIC PLUGIN UPDATE
// Version ID is number greater than 100 and reference to "version of database state" for plugin
function qrc_install_plugin_update() {
  $plugin = 'qrcode';
  
  if(!in_array(Params::getParam('action'), array('widget','add_post','add','enable','disable','install','uninstall')) && !in_array(Params::getParam('page'), array('ajax','login','market','upgrade','appearance'))) { 
    $installed_version = (int)qrc_param('version');
    $current_version = (int)QRC_VERSION_ID;
    
    if($installed_version > 0 && $current_version > $installed_version) {
      $ignore_error = (Params::getParam('forceupdateplugin') == $plugin ? true : false);
      qrc_update_version($ignore_error);
    }
  }
}

osc_add_hook('init_admin', 'qrc_install_plugin_update', 10);


// PLUGIN UPDATE
function qrc_update_version($ignore_error = false) {
  $result = ModelQRC::newInstance()->versionUpdate($ignore_error);
  
  // if failed, do not update version and try DB update again
  if($result !== false || $ignore_error !== false) {
    osc_set_preference('version', QRC_VERSION_ID, 'plugin-qrcode', 'INTEGER');
    osc_reset_preferences();
  }
  
  // ignore error and force version update of plugin
  if($ignore_error === true) {
    osc_add_flash_ok_message(sprintf(__('Force update of "%s" completed! Verify plugin functionality, in case of problems reinstall plugin.', 'qrcode'), __('QR Code Generator Plugin', 'qrcode')), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins');
    exit;
  }
}

osc_add_hook(osc_plugin_path(__FILE__) . '_enable', 'qrc_update_version');


// UNINSTALL PLUGIN
function qrc_call_after_uninstall() {
  ModelQRC::newInstance()->uninstall();
  qrc_delete_all_images();
  @rmdir(osc_uploads_path() . 'qrcode/');
}


// ADMIN MENU
function qrc_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/qrcode/css/admin.css?v=' . date('YmdHis') . '" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/qrcode/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/qrcode/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/qrcode/js/admin.js?v=' . date('YmdHis') . '"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/qrcode/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/qrcode/js/bootstrap-switch.js"></script>';



  if( $title == '') { $title = __('Configure', 'qrcode'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>QR Code Generator Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=qrcode/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'qrcode') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function qrc_footer() {
  $pluginInfo = osc_plugin_get_info('qrcode/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="https://osclasspoint.com"><img src="https://osclasspoint.com/favicon.ico" alt="OsclassPoint Market" /> OsclassPoint Market</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'qrcode') . '</a>';
  $text .= '<a target="_blank" href="https://forums.osclasspoint.com/"><i class="fa fa-handshake-o"></i> ' . __('Support Forums', 'qrcode') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'qrcode') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  return $text;
}


// ADD MENU LINK TO PLUGIN LIST
function qrc_admin_menu() {
echo '<h3><a href="#">QR Code Generator Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Configure', 'qrcode') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','qrc_admin_menu', 1);



// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function qrc_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/configure.php' );
}

osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'qrc_conf' );	


// CALL WHEN PLUGIN IS ACTIVATED - INSTALLED
osc_register_plugin(osc_plugin_path(__FILE__), 'qrc_call_after_install');

// SHOW UNINSTALL LINK
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'qrc_call_after_uninstall');

?>