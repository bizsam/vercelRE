<?php


// DEFAULT USER COLUMNS
function asc_default_user_columns() {
  return array('dt_reg_date', 'dt_mod_date', 's_name', 's_username', 'user_description', 's_password', 's_secret', 's_email', 's_website', 's_phone_land', 's_phone_mobile', 'b_enabled', 'b_active', 's_country', 's_address', 's_zip', 's_region', 's_city', 's_city_area', 'd_coord_lat', 'd_coord_long', 'b_company', 'i_items', 'i_comments', 'dt_access_date', 's_access_ip', 's_profile_img');
}

// AVAILABLE USER COLUMNS DESCRIPTION
function asc_available_user_columns() {
  $data = array(
    'pk_i_id' => __('ID (PK)', 'account_sync'),
    'dt_reg_date' => __('Registration date', 'account_sync'),
    'dt_mod_date' => __('Modification date', 'account_sync'),
    's_name' => __('Name', 'account_sync'),
    'user_description' => __('User Description (Info/About)', 'account_sync'), 
    'user_business_profile' => __('User Business Profile (Plugin)', 'account_sync'), 
    's_username' => __('User name', 'account_sync'),
    's_password' => __('Password', 'account_sync'),
    's_secret' => __('Secret code', 'account_sync'),
    's_email' => __('Email', 'account_sync'),
    's_website' => __('Website URL', 'account_sync'),
    's_phone_land' => __('Land phone', 'account_sync'),
    's_phone_mobile' => __('Mobile phone', 'account_sync'),
    'b_enabled' => __('Is enabled', 'account_sync'),
    'b_active' => __('Is active', 'account_sync'),
    's_pass_code' => __('Pass code', 'account_sync'),
    's_pass_date' => __('Pass date', 'account_sync'),
    's_pass_ip' => __('Pass IP', 'account_sync'),
    'fk_c_country_code' => __('Country code (FK)', 'account_sync'),
    's_country' => __('Country name', 'account_sync'),
    's_address' => __('Address', 'account_sync'),
    's_zip' => __('ZIP', 'account_sync'),
    'fk_i_region_id' => __('Region ID (FK)', 'account_sync'),
    's_region' => __('Region name', 'account_sync'),
    'fk_i_city_id' => __('City ID (FK)', 'account_sync'),
    's_city' => __('City name', 'account_sync'),
    'fk_i_city_area_id' => __('City area ID (FK)', 'account_sync'),
    's_city_area' => __('City area name', 'account_sync'),
    'd_coord_lat' => __('Latitude', 'account_sync'),
    'd_coord_long' => __('Longitude', 'account_sync'),
    'b_company' => __('Is company', 'account_sync'),
    'i_items' => __('Item count', 'account_sync'),
    'i_comments' => __('Comments count', 'account_sync'),
    'dt_access_date' => __('Last access date', 'account_sync'),
    's_access_ip' => __('Last access IP', 'account_sync'),
    'i_login_fails' => __('Failed login count', 'account_sync'),
    'dt_login_fail_date' => __('Last failed login date', 'account_sync'),
    's_profile_img' => __('Profile image', 'account_sync'),
    's_country_native' => __('Native country name', 'account_sync'),
    's_region_native' => __('Native region name', 'account_sync'),
    's_city_native' => __('Native city name', 'account_sync'),
    'i_gdpr_tc' => __('Is GDPR Terms & Conditions checked', 'account_sync'),
    'i_gdpr_pp' => __('Is GDPR Privacy policy checked', 'account_sync'),
    'i_gdpr_nw' => __('Is GDPR Newsletter checked', 'account_sync')
  );
  
  $custom = array_unique(array_filter(array_map('strtolower', array_map('trim', explode(',', asc_param('custom_columns'))))));
  
  if(is_array($custom) && count($custom) > 0) {
    $data['custom_start'] = '----------------------------------------------------------';
    
    foreach($custom as $c) {
      if(!in_array($c, array_keys($data))) {
        $data[$c] = sprintf(__('Custom: %s', 'account_sync'), $c);
      }
    }
  }  
  
  return $data;
}


// REMOVE USER RECORD
function asc_remove_user($user, $account) {
  $status = false;
  
  if(isset($user['pk_i_id'])) {
    $status = User::newInstance()->deleteUser($user['pk_i_id']);
  }
  
  if($status) {
    // Successful log
    ModelASC::newInstance()->insertLog(array(
      'fk_i_account_id' => $account['pk_i_id'],
      's_type' => 'PROCESS',
      's_content' => json_encode($user),
      's_response' => sprintf(__('REMOVAL: User record successfully removed %s', 'account_sync'), (isset($user['pk_i_id']) ? '(ID: ' . $user['pk_i_id'] . ')' : '')),
      's_name' => $account['s_name'],
      's_key' => $account['s_key'],
      's_code' => 'OK',
      'dt_date' => date('Y-m-d H:i:s')
     ));
  } else {
    // Failed log
    ModelASC::newInstance()->insertLog(array(
      'fk_i_account_id' => $account['pk_i_id'],
      's_type' => 'PROCESS',
      's_content' => json_encode($user),
      's_response' => sprintf(__('REMOVAL: User record removal failed %s', 'account_sync'), (isset($user['pk_i_id']) ? '(ID: ' . $user['pk_i_id'] . ')' : '')),
      's_name' => $account['s_name'],
      's_key' => $account['s_key'],
      's_code' => 'OK',
      'dt_date' => date('Y-m-d H:i:s')
     ));
  }
     
  return $status;
}


// UPDATE OR INSERT USER RECORD
function asc_update_insert_user($user, $account) {
  $errors = array();
  $location_fk_checks = true;
  $columns = trim($account['s_columns']) != '' ? $account['s_columns'] : asc_default_user_columns();
  $columns = array_map('strtolower', array_unique(array_filter(array_map('trim', explode(',', $columns)))));
  
  if(!isset($user['s_email']) || trim($user['s_email']) == '') {
    ModelASC::newInstance()->insertLog(array(
      'fk_i_account_id' => $account['pk_i_id'],
      's_type' => 'PROCESS',
      's_content' => json_encode($user),
      's_response' => __('FAILED: User email is missing or empty', 'account_sync'),
      's_name' => $account['s_name'],
      's_key' => $account['s_key'],
      's_code' => 'ERR',
      'dt_date' => date('Y-m-d H:i:s')
     ));
    
    return false;
  }
  
  $check_user = ModelASC::newInstance()->getUserByEmail($user['s_email']);
  
  
  // prepare data based on columns definition
  $data = array();
  $desc = array();
  if(is_array($columns) && count($columns) > 0) {
    foreach($columns as $c) {
      if($c == 'user_description' || $c == 'user_business_profile') {
        // do nothing
        // will be processed once we have user ID
        
      } else if($c == 'fk_c_country_code' && isset($user[$c]) && $user[$c] <> '' && $location_fk_checks) {
        $check_country = Country::newInstance()->findByCode($user[$c]);
        
        // Country FK check
        if(isset($check_country['pk_c_code'])) {
          if(!isset($user['s_country']) || @$user['s_country'] == '' || (isset($user['s_country']) && $check_country['s_name'] == $user['s_country'])) {
            $data[$c] = $user[$c];
          }
        }
        
      } else if($c == 'fk_i_region_id' && isset($user[$c]) && $user[$c] > 0 && $location_fk_checks) {
        $check_region = Region::newInstance()->findByPrimaryKey($user[$c]);
        
        // Region FK check
        if(isset($check_region['pk_i_id'])) {
          if(!isset($user['s_region']) || @$user['s_region'] == '' || (isset($user['s_region']) && $check_region['s_name'] == $user['s_region'])) {
            $data[$c] = $user[$c];
          }
        }

      } else if($c == 'fk_i_city_id' && isset($user[$c]) && $user[$c] > 0 && $location_fk_checks) {
        $check_city = City::newInstance()->findByPrimaryKey($user[$c]);
        
        // City FK check
        if(isset($check_city['pk_i_id'])) {
          if(!isset($user['s_city']) || @$user['s_city'] == '' || (isset($user['s_city']) && $check_city['s_name'] == $user['s_city'])) {
            $data[$c] = $user[$c];
          }
        } 
        
      } else if($c == 's_profile_img' && isset($user[$c]) && trim($user[$c]) <> '') {
        $img_url = $account['s_url'] . 'oc-content/uploads/user-images/' . $user[$c];
        $target_path = osc_base_path() . 'oc-content/uploads/user-images/' . $user[$c];
        $res = asc_download_file($img_url, $target_path);
        
        // we were able to download user profile picture
        if($res === true) {
          $data[$c] = $user[$c];
        } else {
          $errors[] = sprintf(__('Image download failed: %s / %s', 'account_sync'), $img_url, $target_path);
        }
        
      } else {
        if(isset($user[$c])) {
          $data[$c] = $user[$c];
        }
      }
    }
  } else {
    return false; 
  }
  
  $model = ModelASC::newInstance();
  
  // user does not exists
  if($check_user === false) {
    $id = $model->insertUser($data);
    $optype = __('INSERT', 'account_sync');
    
  // user exists
  } else {
    $id = $check_user['pk_i_id'];
    $model->updateUser($id, $data);
    $optype = __('UPDATE', 'account_sync');

  }
  
  
  $error_code = $model->dao->getErrorLevel();
  $error_desc = $model->dao->getErrorDesc();
  
  // SQL Processing error occured while inserting record
  if($id <= 0 || ($error_code != '' && $error_code != 0)) {
    ModelASC::newInstance()->insertLog(array(
      'fk_i_account_id' => $account['pk_i_id'],
      's_type' => 'PROCESS',
      's_content' => json_encode($user),
      's_response' => sprintf(__('SQL Processing error!!! Make sure all columns defined in Account setting exists in your t_user table (ID: %s). %s: %s', 'account_sync'), $id, PHP_EOL . $error_code, $error_desc),
      's_name' => $account['s_name'],
      's_key' => $account['s_key'],
      's_code' => 'ERR',
      'dt_date' => date('Y-m-d H:i:s')
     ));

    return false;
  }
  
  $description = asc_prepare_user_desc($id, $user);
  
  if(!empty($description) && count($description) > 0) {
    foreach($description as $desc_line) {
      $model->updateUserDescription($desc_line);
      
      $error_code = $model->dao->getErrorLevel();
      $error_desc = $model->dao->getErrorDesc();
      
      if($error_code != '' && $error_code != 0) {
        $errors[] = __('Description update', 'account_sync') . ' - ' . $error_code . ': ' . $error_desc; 
      }
    }
  }
  
  $business_profile = asc_prepare_user_business_profile($id, $user);
  
  if(!empty($business_profile) && isset($business_profile['fk_i_user_id'])) {
    $model->updateUserBusinessProfile($business_profile);
    
    $error_code = $model->dao->getErrorLevel();
    $error_desc = $model->dao->getErrorDesc();
    
    if($error_code != '' && $error_code != 0) {
      $errors[] = __('Business profile update', 'account_sync') . ' - ' . $error_code . ': ' . $error_desc; 
    }
  }
  
  
  // Mask password before saving it into logs
  if(isset($user['s_password'])) {
    $user['s_password'] = substr($user['s_password'], 0, 10) . '**********' . substr($user['s_password'], 20, 10) . '...';
  }
  
  $errors = implode('; ', array_filter($errors));
  
  // Successful log
  ModelASC::newInstance()->insertLog(array(
    'fk_i_account_id' => $account['pk_i_id'],
    's_type' => 'PROCESS',
    's_content' => json_encode($user),
    's_response' => sprintf(__('%s: User record successfully processed %s %s', 'account_sync'), $optype, (isset($check_user['pk_i_id']) ? '(ID: ' . $check_user['pk_i_id'] . ')' : ''), $errors != '' ? '[' . $errors . ']' : ''),
    's_name' => $account['s_name'],
    's_key' => $account['s_key'],
    's_code' => 'OK',
    'dt_date' => date('Y-m-d H:i:s')
   ));
     
  return $id;
}

// PREPARE USER DESCRIPTION
function asc_prepare_user_desc($id, $user) {
  $output = array();
  $locales = OSCLocale::newInstance()->listAll();
  $locale_codes = array();
  
  foreach($locales as $l) {
    $locale_codes[] = $l['pk_c_code'];
  }
    
  if(isset($user['user_description']) && is_array($user['user_description']) && count($user['user_description']) > 0) {
    foreach($user['user_description'] as $d) {
      if(in_array($d['fk_c_locale_code'], $locale_codes)) {
        if(trim($d['s_info']) != '') {
          $output[] = array(
            'fk_i_user_id' => $id,
            'fk_c_locale_code' => $d['fk_c_locale_code'],
            's_info' => $d['s_info']
          );
        }
      }
    }
  }
  
  return $output;
}


// PREPARE LOCALE OBJECT - USER DESCRIPTION
function asc_prepare_user_business_profile($id, $user) {
  $output = array();
  
  if(Plugins::isEnabled('business_profile/index.php')) {
    if(isset($user['user_business_profile']) && isset($user['user_business_profile']['fk_i_user_id']) && $user['user_business_profile']['fk_i_user_id'] > 0) {
      $output = $user['user_business_profile'];
      $output['fk_i_user_id'] = $id;
    }
  }
  
  return $output;
}


// FEED USERS FROM OTHER SITES
function asc_feed_users($account_id) {
  $account = ModelASC::newInstance()->getAccount($account_id);
  
  $response_data = asc_do_user_feed($account);
  $httpcode = $response_data['httpcode'];
  $json = $response_data['output'];
  $response = json_decode($json, true);
  
  $users = isset($response['data']) ? $response['data'] : array();
  $users_count = count($users);

  if($users_count > 0) {
    ModelASC::newInstance()->increaseAccountSyncFrom($account_id, $users_count);
  }
  
  $last_id = NULL;
  if(is_array($users) && count($users) > 0) {
    foreach($users as $user) {
      // insert or update user record
      asc_update_insert_user($user, $account);
      $last_id = $user['pk_i_id'];
    }
    
    ModelASC::newInstance()->updateAccountLastUserId($account_id, $last_id);
  }
  
  $stat = isset($response['status']) ? $response['status'] : 'ERROR';
  
  $mes = '';
  
  if(substr($httpcode, 0, 1) != '2') {
    $mes .= '[' . $httpcode . '] ';
    $mes .= $response_data['error'] . '. ';
  }
  
  $mes .= isset($response['message']) ? $response['message'] : sprintf(__('Invalid data received, probably incorrect configuration. (JSON output: %s)', 'account_sync'), htmlentities(substr($json, 0, 2000)));
  
  ModelASC::newInstance()->updateAccountStatus($account_id, $stat, $mes);

  // successful log
  ModelASC::newInstance()->insertLog(array(
    'fk_i_account_id' => $account['pk_i_id'],
    's_type' => 'FEED',
    's_content' => substr($json, 0, 4800),
    's_response' => $stat . ': ' . $mes,
    's_name' => $account['s_name'],
    's_key' => $account['s_key'],
    's_ip' => asc_get_ip(),
    's_code' => ($httpcode <> '0' ? $httpcode : 'ERR'),
    'dt_date' => date('Y-m-d H:i:s')
   ));
   
  return array(
    'status' => $stat, 
    'message' => $mes
  );
}



// DO FEED FROM OTHER SITE
function asc_do_user_feed($account) {
  if(isset($account['pk_i_id'])) {
    $url = $account['s_url'];
    $key = $account['s_key'];
   
    $fields = [
      'ascSync' => 1,
      'ascApiKey' => asc_param('apikey'),
      'ascAction' => 'userFeedRequest',
      'ascLastUserId' => $account['i_last_user_id']
    ];

    $fields_string = http_build_query($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch); 

    curl_close($ch);

    return array(
      'httpcode' => $httpcode,
      'error' => $error,
      'output' => $result
    );
  }
  
  return false;
}


function asc_download_file($source_url, $target_path) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $source_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);

  $result = curl_exec($ch);

  curl_close($ch);

  if($file = fopen($target_path, "w+")) {
    if(fputs($file, $result)) {
      return true;
    }
    
    fclose($file);
  }
  
  return false;
}
  




// DO POST TO OTHER SITE
function asc_do_user_post($account, $user) {
  if(isset($account['pk_i_id']) && isset($user['pk_i_id'])) {
    $url = $account['s_url'];
    $key = $account['s_key'];
    
    $fields = [
      'ascSync' => 1,
      'ascApiKey' => asc_param('apikey'),
      'ascAction' => 'userStreamPost',
      'ascData' => $user
    ];

    $fields_string = http_build_query($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch); 

    curl_close($ch);

    return array(
      'httpcode' => $httpcode,
      'error' => $error,
      'output' => $result
    );
  }
}



// DO POST TO OTHER SITE
function asc_do_user_post_removed($account, $email) {
  if(isset($account['pk_i_id']) && trim($email) != '') {
    $url = $account['s_url'];
    $key = $account['s_key'];
    
    $fields = [
      'ascSync' => 1,
      'ascApiKey' => asc_param('apikey'),
      'ascAction' => 'userStreamPostRemoval',
      'ascData' => $email
    ];

    $fields_string = http_build_query($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $result = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch); 

    curl_close($ch);

    return array(
      'httpcode' => $httpcode,
      'error' => $error,
      'output' => $result
    );
  }
}



// USER HAS BEEN UPDATED
// we can get $user as ID or as user object
function asc_user_updated($user) {
  if(is_array($user) && isset($user['pk_i_id'])) {
    $id = $user['pk_i_id'];
  } else {
    $id = $user; 
  }
  
  $ids = explode(',', asc_param('users_to_stream'));
  $ids[] = $id;
  $ids = array_unique(array_filter($ids));
  $ids = implode(',', $ids);
  osc_set_preference('users_to_stream', $ids, 'plugin-account_sync');  
}


osc_add_hook('user_register_completed', 'asc_user_updated');
osc_add_hook('user_edit_completed', 'asc_user_updated');
osc_add_hook('activate_user', 'asc_user_updated');
osc_add_hook('deactivate_user', 'asc_user_updated');
osc_add_hook('enable_user', 'asc_user_updated');
osc_add_hook('disable_user', 'asc_user_updated');



// USER HAS BEEN REMOVED
// we can get $user as ID or as user object
function asc_user_removed($user) {
  if(is_array($user) && isset($user['pk_i_id'])) {
    $id = $user['pk_i_id'];
  } else {
    $id = $user; 
  }
  
  $user = User::newInstance()->findByPrimaryKey($id);
  $emails = explode(',', asc_param('users_to_stream_removed'));
  $emails[] = $user['s_email'];
  $emails = array_unique(array_filter($emails));
  $emails = implode(',', $emails);
  osc_set_preference('users_to_stream_removed', $emails, 'plugin-account_sync');  
}

osc_add_hook('delete_user', 'asc_user_removed');


// GET IP FUNCTION
function asc_get_ip() {
  if(function_exists('osc_get_ip')) {
    return osc_get_ip();
  } else {
    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
      $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
      $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }
    
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = @$_SERVER['REMOTE_ADDR'];

    if(filter_var($client, FILTER_VALIDATE_IP)) {
      $ip = $client;
    } elseif(filter_var($forward, FILTER_VALIDATE_IP)) {
      $ip = $forward;
    } else {
      $ip = $remote;
    }

    return $ip;
  }
}


// CHECK IF RUNNING ON DEMO
function asc_is_demo($ignore_admin = false) {
  if(!$ignore_admin && osc_logged_admin_username() == 'admin') {
    return false;
  } else if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'],'abprofitrade') !== false)) {
    return true;
  } else {
    return false;
  }
}

// CORE FUNCTIONS
function asc_param($name) {
  return osc_get_preference($name, 'plugin-account_sync');
}


if(!function_exists('mb_param_update')) {
  function mb_param_update( $param_name, $update_param_name, $type = NULL, $plugin_var_name = NULL ) {
  
    $val = '';
    if( $type == 'check') {

      // Checkbox input
      if( Params::getParam( $param_name ) == 'on' ) {
        $val = 1;
      } else {
        if( Params::getParam( $update_param_name ) == 'done' ) {
          $val = 0;
        } else {
          $val = ( osc_get_preference( $param_name, $plugin_var_name ) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
        }
      }
    } else {

      // Other inputs (text, password, ...)
      if( Params::getParam( $update_param_name ) == 'done' && Params::existParam($param_name)) {
        $val = Params::getParam( $param_name );
      } else {
        $val = ( osc_get_preference( $param_name, $plugin_var_name) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
      }
    }


    // If save button was pressed, update param
    if( Params::getParam( $update_param_name ) == 'done' ) {

      if(osc_get_preference( $param_name, $plugin_var_name ) == '') {
        osc_set_preference( $param_name, $val, $plugin_var_name, 'STRING');  
      } else {
        $dao_preference = new Preference();
        $dao_preference->update( array( "s_value" => $val ), array( "s_section" => $plugin_var_name, "s_name" => $param_name ));
        osc_reset_preferences();
        unset($dao_preference);
      }
    }

    return $val;
  }
}

if(!function_exists('mb_generate_rand_int')) {
  function mb_generate_rand_int($length = 18) {
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}


if(!function_exists('mb_generate_rand_string')) {
  function mb_generate_rand_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
    }

    return $randomString;
  }
}


if(!function_exists('message_ok')) {
  function message_ok( $text ) {
    $final  = '<div class="flashmessage flashmessage-ok flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


if(!function_exists('message_error')) {
  function message_error( $text ) {
    $final  = '<div class="flashmessage flashmessage-error flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


?>