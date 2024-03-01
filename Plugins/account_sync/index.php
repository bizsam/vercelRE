<?php
/*
  Plugin Name: Account Synchronization Plugin
  Plugin URI: https://osclasspoint.com/osclass-plugins/extra-fields-and-other/account-synchronization-osclass-plugin-i173
  Description: Synchronize account/user information between different osclass installations
  Version: 1.0.1
  Author: MB Themes
  Author URI: http://osclasspoint.com
  Author Email: info@osclasspoint.com
  Short Name: account_sync
  Plugin update URI: account-synchronization
  Support URI: https://forums.osclasspoint.com/account-synchronization-plugin/
  Product Key: KFHwr9v7MXDv8z7Gaxqf
*/


require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'model/ModelASC.php';
require_once osc_plugins_path() . osc_plugin_folder(__FILE__) . 'functions.php';


// LISTENER
function asc_listener() {
  $log_type = 'FEED';
  $status = '';
  $message = '';
  $data = array();
  
  if(Params::getParam('ascSync') == 1) {
    $key = Params::getParam('ascApiKey');
    
    if(trim($key) == '') {
      $status = 'ERR';
      $message = __('API Key is missing', 'account_sync');
    } else {
      $account = ModelASC::newInstance()->getAccountByKey($key);

      if($account === false) { // && $key != asc_param('apikey')) {
        $status = 'ERR';
        $message = __('API Key is invalid - no account found', 'account_sync');
      }
    }
    
    // Authentication is OK
    if($status !== 'ERR') {
      // receiving user data updates from other domain
      // listening asc_do_user_post function from different domain
      if(Params::getParam('ascAction') == 'userStreamPost') {
        $log_type = 'FEED';
        $user = Params::getParam('ascData');
        $data = $user;  // for logs only
        
        // user record is valid, do update/insert
        if(isset($user['pk_i_id']) && isset($user['s_email']) && trim($user['s_email']) != '') {
          asc_update_insert_user($user, $account);
          
          $status = 'OK';
          $message = sprintf(__('Streamed user data received successfully (#%s, %s, %s)', 'account_sync'), $user['pk_i_id'], $user['s_email'], $user['s_name']);
          ModelASC::newInstance()->increaseAccountSyncFrom($account['pk_i_id'], 1);
        } else {
          $status = 'ERR';
          $message = __('Streamed user record invalid, pk_i_id or s_email is missing', 'account_sync');
        }
      }
      
      
      // receiving user data removal from other domain
      // listening asc_do_user_post_removed function from different domain
      if(Params::getParam('ascAction') == 'userStreamPostRemoval') {
        $log_type = 'FEED';
        $email = Params::getParam('ascData');
        $data[] = $email;

        $user = ModelASC::newInstance()->getUserByEmail($email);
        
        // user record is valid, do update/insert
        if(isset($user['pk_i_id']) && isset($user['s_email']) && trim($user['s_email']) != '') {
          asc_remove_user($user, $account);
          
          $status = 'OK';
          $message = sprintf(__('User data removal received successfully (#%s, %s, %s)', 'account_sync'), $user['pk_i_id'], $user['s_email'], $user['s_name']);
          ModelASC::newInstance()->increaseAccountSyncFrom($account['pk_i_id'], 1);
        } else {
          $status = 'ERR';
          $message = __('User record invalid, user has not been found (it might not exists on this domain or was already removed)', 'account_sync');
        }
      }
      
      
      // request to provide users list
      if(Params::getParam('ascAction') == 'userFeedRequest') {
        $log_type = 'STREAM';
        $last_user_id = (Params::getParam('ascLastUserId') > 0 ? Params::getParam('ascLastUserId') : 0);
        $limit = (asc_param('feed_limit') > 0 ? asc_param('feed_limit') : 200);
        $only_active = true;
        $data = ModelASC::newInstance()->getUsersFromId($last_user_id, $limit, $only_active);
        $count_remaining = ModelASC::newInstance()->countUsersFromId($last_user_id, $only_active);
        $count_all = ModelASC::newInstance()->countUsersFromId(-1, $only_active);
        
        $remaining = $count_remaining - count($data);
        $remaining = ($remaining <= 0 ? 0 : $remaining);
        $retrieved = $count_all - $remaining;
        $retrieved = ($retrieved <= 0 ? 0 : $retrieved);

        $progress = 100;
        if($count_all > 0) {
          $progress = round($retrieved/$count_all*100, 2);
        }
        
        $status = 'OK';
        $message = sprintf(__('Progress: %s (Total stats - Retrieved: %s, Remaining: %s, Total: %s / Last request stats - Retrieved : %s, Limit: %s, From user ID: %s)', 'account_sync'), number_format($progress, 2) . '%', $retrieved, $remaining, $count_all, count($data), $limit, $last_user_id);
        ModelASC::newInstance()->increaseAccountSyncTo($account['pk_i_id'], count($data));
      }
    }
    
    // Generate log
    if(isset($data['s_password'])) {
      $data['s_password'] = substr($data['s_password'], 0, 10) . '**********' . substr($data['s_password'], 20, 10) . '...';
    }
    
    $mask_data = array();
    if(is_array($data) && count($data) > 0) {
      foreach($data as $d) {
        if(isset($d['s_password'])) {
          $d['s_password'] = substr($d['s_password'], 0, 10) . '**********' . substr($d['s_password'], 20, 10) . '...';
        }
        
        $mask_data[] = $d;
      }
    }
    
    $data = $mask_data;
    
    ModelASC::newInstance()->insertLog(array(
      'fk_i_account_id' => isset($account['pk_i_id']) ? $account['pk_i_id'] : NULL,
      's_type' => $log_type,
      's_content' => substr(json_encode($data), 0, 4800),
      's_response' => $message,
      's_name' => isset($account['s_name']) ? $account['s_name'] : '-',
      's_key' => $key,
      's_ip' => asc_get_ip(),
      's_code' => $status,
      'dt_date' => date('Y-m-d H:i:s')
     ));
    
    echo json_encode(array('status' => $status, 'message' => $message, 'data' => $data));
    exit;  
  }
}

osc_add_hook('init', 'asc_listener', 1);




// STREAM USERS TO OTHER SITES
function asc_stream_users() {
  $ids = array_filter(array_unique(explode(',', asc_param('users_to_stream'))));
  $accounts = ModelASC::newInstance()->getAccounts();
  $display = '';

  if(is_array($ids) && count($ids) > 0 && count($accounts) > 0) {
    foreach($ids as $id) {
      $user = ModelASC::newInstance()->getUserData($id);
      
      if($user !== false && isset($user['pk_i_id']) && isset($user['s_email']) && trim($user['s_email']) != '') {
        foreach($accounts as $account) {
          $result = asc_do_user_post($account, $user);

          if(isset($result['httpcode']) && substr($result['httpcode'], 0, 1) == '2') {
            $status = $result['httpcode'];
            $message = sprintf(__('User data streamed successfully (#%s, %s, %s)', 'account_sync'), $user['pk_i_id'], $user['s_email'], $user['s_name']);
            ModelASC::newInstance()->increaseAccountSyncTo($account['pk_i_id'], 1);
          } else {
            $status = ($result['httpcode'] <> '0' ? $result['httpcode'] : 'ERR');
            $message = sprintf(__('User data stream failed (#%s, %s, %s) - %s', 'account_sync'), $user['pk_i_id'], $user['s_email'], $user['s_name'], $result['error']);
          }
          
          ModelASC::newInstance()->updateAccountStatus($account['pk_i_id'], $status, $message);

          // successful log
          ModelASC::newInstance()->insertLog(array(
            'fk_i_account_id' => $account['pk_i_id'],
            's_type' => 'STREAM',
            's_content' => substr($result['output'], 0, 4800),
            's_response' => $message,
            's_name' => $account['s_name'],
            's_key' => $account['s_key'],
            's_ip' => asc_get_ip(),
            's_code' => $status,
            'dt_date' => date('Y-m-d H:i:s')
          ));
          
          $userrec = '#' . $user['pk_i_id'] . ' ('  . substr($user['s_email'], 0, 3) . '*****' . substr($user['s_email'], -3) . ')';
          $display .= sprintf(__('SUCCESS: Streaming user update %s to account %s: %s', 'account_sync'), $userrec, trim($account['s_name']), trim($account['s_url'])) . PHP_EOL;
        }
      } else {
        $display .= sprintf(__('ERROR: Streaming of user update %s failed, user has been probably removed', 'account_sync'), $id) . PHP_EOL;
      }
    } 
  } else {
    //$display .= __('No users found for streaming', 'account_sync');
  }
  
  osc_set_preference('users_to_stream', '', 'plugin-account_sync');  
  
  return $display;
}

osc_add_hook('cron_hourly', 'asc_stream_users');



// STREAM USERS TO OTHER SITES
function asc_stream_users_removed() {
  $emails = array_filter(array_unique(explode(',', asc_param('users_to_stream_removed'))));
  $accounts = ModelASC::newInstance()->getAccounts();
  $display = '';

  if(is_array($emails) && count($emails) > 0 && count($accounts) > 0) {
    foreach($emails as $email) {
      foreach($accounts as $account) {
        $result = asc_do_user_post_removed($account, $email);

        if(isset($result['httpcode']) && substr($result['httpcode'], 0, 1) == '2') {
          $status = $result['httpcode'];
          $message = sprintf(__('User data removal streamed successfully (%s)', 'account_sync'), $email);
          ModelASC::newInstance()->increaseAccountSyncTo($account['pk_i_id'], 1);
        } else {
          $status = ($result['httpcode'] <> '0' ? $result['httpcode'] : 'ERR');
          $message = sprintf(__('User data removal stream failed (%s) - %s', 'account_sync'), $email, $result['error']);
        }
        
        ModelASC::newInstance()->updateAccountStatus($account['pk_i_id'], $status, $message);

        // successful log
        ModelASC::newInstance()->insertLog(array(
          'fk_i_account_id' => $account['pk_i_id'],
          's_type' => 'STREAM',
          's_content' => substr($result['output'], 0, 4800),
          's_response' => $message,
          's_name' => $account['s_name'],
          's_key' => $account['s_key'],
          's_ip' => asc_get_ip(),
          's_code' => $status,
          'dt_date' => date('Y-m-d H:i:s')
        ));
        
        $userrec = substr($email, 0, 3) . '*****' . substr($email, -3);
        $display .= sprintf(__('SUCCESS: Streaming user removal (%s) to account %s: %s', 'account_sync'), $userrec, trim($account['s_name']), trim($account['s_url'])) . PHP_EOL;
      }
    }
  } else {
    //$display .= __('No users removal found for streaming', 'account_sync');
  }
  
  osc_set_preference('users_to_stream_removed', '', 'plugin-account_sync');  

  return $display;
}

osc_add_hook('cron_hourly', 'asc_stream_users_removed');


// INSTALL FUNCTION - DEFINE VARIABLES
function asc_call_after_install() {
  ModelASC::newInstance()->import('account_sync/model/struct.sql');
  
  // General settings
  osc_set_preference('apikey', mb_generate_rand_string(70), 'plugin-account_sync', 'STRING');
  osc_set_preference('custom_columns', '', 'plugin-account_sync', 'STRING');
  osc_set_preference('feed_limit', 200, 'plugin-account_sync', 'INTEGER');
  osc_set_preference('users_to_stream', '', 'plugin-account_sync', 'STRING');
  osc_set_preference('users_to_stream_removed', '', 'plugin-account_sync', 'STRING');
  
}


function asc_call_after_uninstall() {
  ModelASC::newInstance()->uninstall();
  osc_delete_preference('apikey', 'plugin-account_sync');
  osc_delete_preference('custom_columns', 'plugin-account_sync');
  osc_delete_preference('feed_limit', 'plugin-account_sync');
  osc_delete_preference('users_to_stream', 'plugin-account_sync');
  osc_delete_preference('users_to_stream_removed', 'plugin-account_sync');

}



// ADMIN MENU
function asc_menu($title = NULL) {
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/account_sync/css/admin.css?v=' . date('YmdHis') . '" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/account_sync/css/bootstrap-switch.css" rel="stylesheet" type="text/css" />';
  echo '<link href="' . osc_base_url() . 'oc-content/plugins/account_sync/css/tipped.css" rel="stylesheet" type="text/css" />';
  echo '<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/account_sync/js/admin.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/account_sync/js/tipped.js"></script>';
  echo '<script src="' . osc_base_url() . 'oc-content/plugins/account_sync/js/bootstrap-switch.js"></script>';



  if( $title == '') { $title = __('Configure', 'account_sync'); }

  $text  = '<div class="mb-head">';
  $text .= '<div class="mb-head-left">';
  $text .= '<h1>' . $title . '</h1>';
  $text .= '<h2>Account Synchronization Plugin</h2>';
  $text .= '</div>';
  $text .= '<div class="mb-head-right">';
  $text .= '<ul class="mb-menu">';
  $text .= '<li><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=account_sync/admin/configure.php"><i class="fa fa-wrench"></i><span>' . __('Configure', 'account_sync') . '</span></a></li>';
  $text .= '<li><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=account_sync/admin/accounts.php"><i class="fa fa-key"></i><span>' . __('Accounts', 'account_sync') . '</span></a></li>';
  $text .= '<li><a href="' . osc_base_url() . 'oc-admin/index.php?page=plugins&action=renderplugin&file=account_sync/admin/logs.php"><i class="fa fa-database"></i><span>' . __('Logs', 'account_sync') . '</span></a></li>';
  $text .= '</ul>';
  $text .= '</div>';
  $text .= '</div>';

  echo $text;
}



// ADMIN FOOTER
function asc_footer() {
  $pluginInfo = osc_plugin_get_info('account_sync/index.php');
  $text  = '<div class="mb-footer">';
  $text .= '<a target="_blank" class="mb-developer" href="https://osclasspoint.com"><img src="https://osclasspoint.com/favicon.ico" alt="MB Themes" /> osclasspoint.com</a>';
  $text .= '<a target="_blank" href="' . $pluginInfo['support_uri'] . '"><i class="fa fa-bug"></i> ' . __('Report Bug', 'account_sync') . '</a>';
  $text .= '<a target="_blank" href="https://forums.osclasspoint.com/"><i class="fa fa-comments"></i> ' . __('Support Forums', 'account_sync') . '</a>';
  $text .= '<a target="_blank" class="mb-last" href="mailto:info@osclasspoint.com"><i class="fa fa-envelope"></i> ' . __('Contact Us', 'account_sync') . '</a>';
  $text .= '<span class="mb-version">v' . $pluginInfo['version'] . '</span>';
  $text .= '</div>';

  return $text;
}



// ADD MENU LINK TO PLUGIN LIST
function asc_admin_menu() {
echo '<h3><a href="#">Account Synchronization Plugin</a></h3>
<ul> 
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/configure.php') . '">&raquo; ' . __('Configure', 'account_sync') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/accounts.php') . '">&raquo; ' . __('Accounts', 'account_sync') . '</a></li>
  <li><a style="color:#2eacce;" href="' . osc_admin_render_plugin_url(osc_plugin_path(dirname(__FILE__)) . '/admin/logs.php') . '">&raquo; ' . __('Logs', 'account_sync') . '</a></li>
</ul>';
}


// ADD MENU TO PLUGINS MENU LIST
osc_add_hook('admin_menu','asc_admin_menu', 1);



// DISPLAY CONFIGURE LINK IN LIST OF PLUGINS
function asc_conf() {
  osc_admin_render_plugin( osc_plugin_path( dirname(__FILE__) ) . '/admin/configure.php' );
}

osc_add_hook( osc_plugin_path( __FILE__ ) . '_configure', 'asc_conf' );	


// CALL WHEN PLUGIN IS ACTIVATED - INSTALLED
osc_register_plugin(osc_plugin_path(__FILE__), 'asc_call_after_install');

// SHOW UNINSTALL LINK
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', 'asc_call_after_uninstall');

?>