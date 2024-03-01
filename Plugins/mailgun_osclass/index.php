<?php
/*
  Plugin Name: MailGun Plugin
  Plugin URI: https://osclasspoint.com/
  Description: MailGun for Osclass
  Version: 1.1.0
  Author: OsclassPoint
  Author URI: https://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: mailgun_osclass
  Plugin update URI: mailgun_osclass
  Support URI: https://forums.osclasspoint.com/free-plugins/
  Product Key: KPmC9399OcTmjOMQYjGp
*/


function mailgun_osclass_menu() {
  osc_add_admin_submenu_page(
    'plugins',
    __('Mailgun settings', 'mailgun_osclass'),
    osc_admin_render_plugin_url(osc_plugin_folder(__FILE__) . 'admin/settings.php'),
    'mailgun_settings',
    'moderator'
  );
}
osc_add_hook('init_admin', 'mailgun_osclass_menu');

function mailgun_osclass_init_admin_actions() {
  if( Params::getParam('file') != 'mailgun_osclass/admin/settings.php' ) {
    return '';
  }

  $option = Params::getParam('mailgun_osclass_hidden');
  if( $option == 'configuration' ) {
    osc_set_preference('mailgun_osclass_username', Params::getParam('mailgun_osclass_username'), 'mailgun_osclass');
    osc_set_preference('mailgun_osclass_password', Params::getParam('mailgun_osclass_password'), 'mailgun_osclass');

    osc_add_flash_ok_message(__('Mailgun settings have been updated', 'amazonses'), 'admin');
    header('Location: ' . osc_admin_render_plugin_url('mailgun_osclass/admin/settings.php')); exit;
  }
}
osc_add_hook('init_admin', 'mailgun_osclass_init_admin_actions');

function mailgun_osclass_phpmailer_init($mail, $params) {

  $mailgun_osclass_username = osc_get_preference('mailgun_osclass_username', 'mailgun_osclass');
  $mailgun_osclass_password = osc_get_preference('mailgun_osclass_password', 'mailgun_osclass');


  if( $mailgun_osclass_username === '' || $mailgun_osclass_password === '' ) {
    return $mail;
  }

  $mail->Host = 'smtp.mailgun.org';           // Specify main and backup SMTP servers
  $mail->SMTPAuth = true;                 // Enable SMTP authentication
  $mail->Username = $mailgun_osclass_username;     // SMTP username
  $mail->Password = $mailgun_osclass_password;     // SMTP password
  $mail->SMTPSecure = 'tls';              // Enable encryption, only 'tls' is accepted
  $mail->IsSMTP();
  return $mail;
}
osc_add_filter('pre_send_mail', 'mailgun_osclass_phpmailer_init');
?>