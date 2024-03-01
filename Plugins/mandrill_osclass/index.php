<?php
/*
  Plugin Name: Mandrill Plugin
  Plugin URI: https://osclasspoint.com/
  Description: Mandrill Osclass Plugin
  Version: 1.1.0
  Author: OsclassPoint
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: mandrill_osclass
  Plugin update URI: mandrill_osclass
  Support URI: https://forums.osclasspoint.com/free-plugins/
  Product Key: X2JXV9xLTaCvC2Og5CUm
*/

function mandrill_osclass_menu() {
  osc_add_admin_submenu_page(
    'plugins',
    __('Mandrill settings', 'mandrill_osclass'),
    osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/settings.php'),
    'mandrill_settings',
    'moderator'
  );
}
osc_add_hook('init_admin', 'mandrill_osclass_menu');

function mandrill_osclass_init_admin_actions() {
  if( Params::getParam('file') != 'mandrill_osclass/admin/settings.php' ) {
    return '';
  }

  $option = Params::getParam('mandrill_osclass_hidden');
  if( $option == 'configuration' ) {
    osc_set_preference('mandrill_osclass_username', Params::getParam('mandrill_osclass_username'), 'mandrill_osclass');
    osc_set_preference('mandrill_osclass_password', Params::getParam('mandrill_osclass_password'), 'mandrill_osclass');

    osc_add_flash_ok_message(__('Mandrill settings have been updated', 'amazonses'), 'admin');
    header('Location: ' . osc_admin_render_plugin_url('mandrill_osclass/admin/settings.php')); exit;
  }
}
osc_add_hook('init_admin', 'mandrill_osclass_init_admin_actions');

function mandrill_osclass_phpmailer_init($mail, $params) {

  $mandrill_osclass_username = osc_get_preference('mandrill_osclass_username', 'mandrill_osclass');
  $mandrill_osclass_password = osc_get_preference('mandrill_osclass_password', 'mandrill_osclass');


  if( $mandrill_osclass_username === '' || $mandrill_osclass_password === '' ) {
    return $mail;
  }

  $mail->Host = 'smtp.mandrillapp.com';         // Specify main and backup server
  $mail->Port = 587;                  // Set the SMTP port
  $mail->SMTPAuth = true;                 // Enable SMTP authentication
  $mail->Username = $mandrill_osclass_username;     // SMTP username
  $mail->Password = $mandrill_osclass_password;     // SMTP password
  $mail->SMTPSecure = 'tls';              // Enable encryption, 'ssl' also accepted
  $mail->IsSMTP();
  return $mail;
}
osc_add_filter('pre_send_mail', 'mandrill_osclass_phpmailer_init');
?>