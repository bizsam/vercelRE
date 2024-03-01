<?php
/*
  Plugin Name: Registered Users Only Plugin
  Plugin URI: https://osclasspoint.com/
  Description: Plugin blocks non-registered users from accessing your website
  Version: 1.1.0
  Author: OsclassPoint
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: registered_users_only
  Plugin update URI: registered_users_only
  Support URI: https://forums.osclasspoint.com/free-plugins/
  Product Key: 9xLZ82VNsdC2cPp14iZh
*/

function login_necessary( ) {
  $location = Rewrite::newInstance()->get_location() ;
  $section = Rewrite::newInstance()->get_section() ;
  
  switch($location) {
    case('login'):
    case('register'):
    break;
    default: 
      if(!osc_is_web_user_logged_in()) {
        osc_add_flash_info_message(__('Only registered users can enter to this site. Please register or login', 'registered_users_only')) ;
        header('Location: ' . osc_register_account_url()); 
        exit;
      }
      break;
  }
}

osc_register_plugin(osc_plugin_path(__FILE__), '');
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", '');

osc_add_hook('before_html', 'login_necessary');
?>