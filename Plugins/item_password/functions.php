<?php

// EDIT BUTTON
function itp_button_edit($item = NULL) {
  if(itp_param('enable') == 0) {
    return false;
  }
  
  if($item === NULL) {
    $item = osc_item();
    $item_id = osc_item_id();
  } else {
    $item_id = (isset($item['pk_i_id']) ? $item['pk_i_id'] : 0);
  }
  
  if($item_id <= 0) {
    return false;
  }
  
  if(itp_param('show_with_password') == 1) {
    $row = ModelITP::newInstance()->getPassword($item_id);
    
    if(!isset($row['fk_i_item_id']) || trim($row['s_password']) == '' || trim($row['s_password_alt']) == '') {
      return false;
    }
  }
  
  if(itp_param('show_with_user') == 0) {    
    if(@$item['fk_i_user_id'] > 0) {
      return false;
    }
  } 
  ?>
  <div class="itp-button-wrap itp-body">
    <a href="#" class="itp-open-box itp-button itp-button-edit<?php if(itp_param('style_buttons') == 1) { ?> itp-styled<?php } ?>" data-type="edit"><?php _e('Edit', 'item_password'); ?></a>
    
    <div class="itp-box-content" style="display:none;">
      <form name="itp-edit" class="itp-form nocsrf" action="<?php echo osc_base_url(true); ?>?page=ajax&action=runhook&hook=itp_edit_manage" method="POST">
        <input type="hidden" name="itemId" value="<?php echo $item_id; ?>"/>

        <strong class="itp-header"><?php _e('Edit listing', 'item_password'); ?></strong>

        <div class="itp-row"><?php _e('Enter password to edit this listing.', 'item_password'); ?></div>

        <div class="itp-row">
          <div class="itp-input-box"><input type="text" required minlength="6" maxlength="50" name="sPassword" placeholder="<?php echo osc_esc_html(__('Enter your password', 'item_password')); ?>"/></div>
        </div>
        
        <div class="itp-row itp-button-row">
          <button type="submit" class="itp-btn"><?php _e('Submit', 'booking'); ?></button>
        </div>
      </form>
    </div>
  </div>
  <?php
}


// EDIT BUTTON
function itp_button_delete($item = NULL) {
  if(itp_param('enable') == 0) {
    return false;
  }

  if($item === NULL) {
    $item = osc_item();
    $item_id = osc_item_id();
  } else {
    $item_id = (isset($item['pk_i_id']) ? $item['pk_i_id'] : 0);
  }

  if($item_id <= 0) {
    return false;
  }

  if(itp_param('show_with_password') == 1) {
    $row = ModelITP::newInstance()->getPassword($item_id);
    
    if(!isset($row['fk_i_item_id']) || trim($row['s_password']) == '' || trim($row['s_password_alt']) == '') {
      return false;
    }
  }

  if(itp_param('show_with_user') == 0) {
    if(@$item['fk_i_user_id'] > 0) {
      return false;
    }
  }

  ?>
  <div class="itp-button-wrap itp-body">
    <a href="#" class="itp-open-box itp-button itp-button-delete<?php if(itp_param('style_buttons') == 1) { ?> itp-styled<?php } ?>" data-type="delete"><?php _e('Delete', 'item_password'); ?></a>
    
    <div class="itp-box-content" style="display:none;">
      <form name="itp-delete" class="itp-form itp-form-delete nocsrf" action="<?php echo osc_base_url(true); ?>?page=ajax&action=runhook&hook=itp_delete_manage" method="POST">
        <input type="hidden" name="itemId" value="<?php echo $item_id; ?>"/>

        <strong class="itp-header"><?php _e('Delete listing', 'item_password'); ?></strong>

        <div class="itp-row"><?php _e('Enter password to remove this listing.', 'item_password'); ?></div>

        <div class="itp-row">
          <div class="itp-input-box"><input type="text" required minlength="6" maxlength="50" name="sPassword" placeholder="<?php echo osc_esc_html(__('Enter your password', 'item_password')); ?>"/></div>
        </div>
        
        <div class="itp-row itp-row-confm"><?php echo sprintf(__('Please confirm removal by entering word "%s" into box bellow.', 'item_password'), '<strong>' . strtoupper(__('DELETE', 'item_password')) . '</strong>'); ?></div>

        <div class="itp-row itp-row-confi">
          <div class="itp-input-box"><input type="text" required minlength="1" name="sConfirm" placeholder="<?php echo str_repeat('_', strlen(__('DELETE', 'item_password'))); ?>"/></div>
        </div>

        <div class="itp-row itp-button-row">
          <button type="submit" class="itp-btn"><?php _e('Submit', 'booking'); ?></button>
        </div>
      </form>
    </div>
  </div>
  <?php
}


osc_add_hook('item_detail', function($item) {
  if((itp_param('hook_edit') == 1 || itp_param('hook_delete') == 1) && itp_param('enable') == 1) {
  ?>
  <div id="itp-buttons" class="itp-item-buttons-wrap">
    <?php
      if(itp_param('hook_edit') == 1) {
        itp_button_edit($item);
      }
      
      if(itp_param('hook_delete') == 1) {
        itp_button_delete($item);
      }
    ?>
  </div>
  <?php
  }
});


// MANAGE ITEM EDIT PASSWORD INPUT
function itp_edit_manage() {
  $item_id = osc_esc_html(Params::getParam('itemId'));
  $pass = osc_esc_html(trim(Params::getParam('sPassword')));
  $redirect_url = osc_base_url();
  
  if($pass != '' && $item_id > 0) {
    $row = ModelITP::newInstance()->getPassword($item_id);
    
    if(isset($row['fk_i_item_id'])) {
      if(itp_check_attempts($row)) {
        osc_add_flash_error_message(__('Too many failed attempts. Try again in few minutes.', 'item_password'));
        header('Location:' . osc_item_url_ns($item_id));
        exit;
      }
      
      $p1 = trim($row['s_password']);
      $p2 = trim($row['s_password_alt']);
      
      if($p1 != '' && $pass == $p1 || $p2 != '' && $pass == $p2) {
        $item = Item::newInstance()->findByPrimaryKey($item_id);
        itp_update_failed_attempt($item_id, 0);

        header('Location:' . osc_item_edit_url($item['s_secret'], $item_id));
        exit;
        
      } else {
        $failed_count = (@$row['i_failed_count'] > 0 ? $row['i_failed_count'] + 1 : 1);
        $failed_count = ($failed_count > 5 ? 1 : $failed_count); // if we got there, we should reset counter
        
        itp_update_failed_attempt($item_id, $failed_count);
          
        $redirect_url = osc_item_url_ns($item_id);
        osc_add_flash_error_message(__('Password does not match!', 'item_password'));
      }
    } else {
      $redirect_url = osc_item_url_ns($item_id);
      osc_add_flash_error_message(__('Password to edit item has not been defined yet!', 'item_password'));
    }
  } else {
    osc_add_flash_error_message(__('Password is empty or listing does not exist!', 'item_password'));
  }

  header('Location:' . $redirect_url);
  exit;
}

osc_add_hook('ajax_itp_edit_manage', 'itp_edit_manage');


// MANAGE ITEM EDIT PASSWORD INPUT
function itp_delete_manage() {
  $item_id = osc_esc_html(Params::getParam('itemId'));
  $confirm = osc_esc_html(strtoupper(trim(Params::getParam('sConfirm'))));
  $pass = osc_esc_html(trim(Params::getParam('sPassword')));
  $redirect_url = osc_base_url();
  
  if($pass != '' && $item_id > 0) {
    $row = ModelITP::newInstance()->getPassword($item_id);
    
    if(isset($row['fk_i_item_id'])) {
      if(itp_check_attempts($row)) {
        osc_add_flash_error_message(__('Too many failed attempts. Try again in few minutes.', 'item_password'));
        header('Location:' . osc_item_url_ns($item_id));
        exit;
      }
      
      $p1 = trim($row['s_password']);
      $p2 = trim($row['s_password_alt']);
      
      if($confirm == strtoupper(__('DELETE', 'item_password'))) {
        if($p1 != '' && $pass == $p1 || $p2 != '' && $pass == $p2) {
          $item = Item::newInstance()->findByPrimaryKey($item_id);
          
          if(itl_is_demo()) {
            $manager = new ItemActions(false);
            $manager->delete($item['s_secret'], $item_id);
            osc_add_flash_ok_message(__('Listing has been successfully removed', 'item_password'));
          } else { 
            osc_add_flash_warning_message(__('This is demo site, you cannot remove listing there!', 'item_password'));
          }
          
          header('Location:' . osc_base_url());
          exit;
          
        } else {
          $failed_count = (@$row['i_failed_count'] > 0 ? $row['i_failed_count'] + 1 : 1);
          $failed_count = ($failed_count > 5 ? 1 : $failed_count); // if we got there, we should reset counter
          
          itp_update_failed_attempt($item_id, $failed_count);
          
          $redirect_url = osc_item_url_ns($item_id);
          osc_add_flash_error_message(__('Password does not match!', 'item_password'));
        }
      } else {
        $redirect_url = osc_item_url_ns($item_id);
        osc_add_flash_error_message(sprintf(__('Confirmation word not correct. Entered: %s, expected: %s', 'item_password'), $confirm, strtoupper(__('DELETE', 'item_password'))));
      }
    } else {
      $redirect_url = osc_item_url_ns($item_id);
      osc_add_flash_error_message(__('Password to delete item has not been defined yet!', 'item_password'));
    }
  } else {
    osc_add_flash_error_message(__('Password is empty or listing does not exist!', 'item_password'));
  }

  header('Location:' . $redirect_url);
  exit;
}

osc_add_hook('ajax_itp_delete_manage', 'itp_delete_manage');


// INSERT PASSWORD ON PUBLISH LISTING
function itp_item_published($item) {
  $item_id = (isset($item['pk_i_id']) ? $item['pk_i_id'] : 0);
  
  if($item_id > 0) {
    $data = array('fk_i_item_id' => $item_id);
    
    if(Params::existParam('sItpPassword')) {
      $data['s_password'] = osc_esc_html(trim(Params::getParam('sItpPassword')));
    }
    
    if(Params::existParam('sItpPasswordAlt')) {
      $data['s_password_alt'] = osc_esc_html(trim(Params::getParam('sItpPasswordAlt')));
    }
    
    ModelITP::newInstance()->insertPassword($data);
  }
}

osc_add_hook('posted_item', 'itp_item_published');


// UPDATE PASSWORD ON EDIT LISTING
function itp_item_edited($item) {
  $item_id = (isset($item['pk_i_id']) ? $item['pk_i_id'] : 0);
  
  $check = ModelITP::newInstance()->getPassword($item_id);
  
  if($item_id > 0) {
    $data = array();
    
    if(Params::existParam('sItpPassword')) {
      $data['s_password'] = osc_esc_html(trim(Params::getParam('sItpPassword')));
    }
    
    if(Params::existParam('sItpPasswordAlt')) {
      $data['s_password_alt'] = osc_esc_html(trim(Params::getParam('sItpPasswordAlt')));
    }
    
    if($check !== false) {
      if(!empty($data)) {
        ModelITP::newInstance()->updatePassword($item_id, $data);
      }
    } else {
      $data['fk_i_item_id'] = $item_id;
      ModelITP::newInstance()->insertPassword($data);
    }
  }
}

osc_add_hook('edited_item', 'itp_item_edited');


// CREATE INPUT IN PUBLISH/EDIT FORM
function itp_item_post($item = NULL) {
  if(isset($item['pk_i_id'])) {
    $item_id = $item['pk_i_id'];
  } else {
    $item_id = (Params::getParam('itemId') > 0 ? Params::getParam('itemId') : Params::getParam('id'));
  }

  $pass = ModelITP::newInstance()->getPassword($item_id);

  $is_ocadmin = false;
  if(defined('OC_ADMIN') && OC_ADMIN === true) {
    $is_ocadmin = true;
  }
  
  $value = ($pass !== false ? $pass['s_password'] : '');
  
  if($is_ocadmin) {
    $value = ($pass !== false ? $pass['s_password_alt'] : '');
  }
  ?>

  <?php if($is_ocadmin) { ?>
    <div class="form-row itp-pass">
      <label class="form-label" for="sItpPasswordAlt"><?php _e('Edit/Delete password', 'item_password'); ?></label>
      <div class="form-controls">
        <input id="sItpPasswordAlt" type="<?php echo itp_password_input_type(); ?>" name="sItpPasswordAlt" value="<?php echo osc_esc_html($value); ?>" minlength="6" maxlength="50">
        <p class="help-inline"><?php _e('Admin password will not change/remove password entered by customer.', 'item_password'); ?></p>
      </div>
    </div>  
  <?php } else if(itp_param('hook_form') == 1) { ?>
    <?php if(!osc_is_web_user_logged_in() || itp_param('show_with_user') == 1) { ?>
      <div class="control-group itp-pass">
        <label class="control-label" for="sItpPassword"><?php _e('Edit/Delete password', 'item_password'); ?></label>
        <div class="controls">
          <input id="sItpPassword" type="<?php echo itp_password_input_type(); ?>" name="sItpPassword" value="<?php echo osc_esc_html($value); ?>" minlength="6" maxlength="50">
        </div>
      </div>
    <?php } ?>
  <?php } ?>
  <?php
}

osc_add_hook('item_form', 'itp_item_post', 3);
osc_add_hook('item_edit', 'itp_item_post', 3);


// UPDATE FAILED ATTEMPT
function itp_update_failed_attempt($item_id, $count = 0) {
  if($item_id > 0) {
    ModelITP::newInstance()->updateFailedAttempt($item_id, $count);
  }
}


// CHECK IF THERE IS NOT TOO MANY FAILED ATTEMPTS
function itp_check_attempts($row) {
  if(isset($row['i_failed_count']) && $row['i_failed_count'] >= 5 && $row['dt_date'] != '' && strtotime($row['dt_date']) > strtotime('-5 minutes')) {
    return true;
  }

  return false;  
}


// CHECK IF ELIGIBLE TO SHOW BOX TO ITEMS WITH USER
function itp_user_items_eligible() {
  $result = version_compare2('8.0.1', OSCLASS_VERSION);
  
  if ($result == 0 || $result == -1) {    // A <= B
    return true;
  }
  
  return false;
}


// GET PASSWORD INPUT TYPE
function itp_password_input_type() {
  $type = strtolower(trim(itp_param('password_type')));
  
  if($type == 'text' || $type == 'password') {
    return $type;
  }
  
  return 'text';
}


// CORE FUNCTIONS
function itp_param($name) {
  return osc_get_preference($name, 'plugin-item_password');
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


// CHECK IF RUNNING ON DEMO
function itp_is_demo() {
  if(osc_logged_admin_username() == 'admin') {
    return false;
  } else if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'],'abprofitrade') !== false)) {
    return true;
  } else {
    return false;
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



// COOKIES WORK
if(!function_exists('mb_set_cookie')) {
  function mb_set_cookie($name, $val) {
    Cookie::newInstance()->set_expires( 86400 * 30 );
    Cookie::newInstance()->push($name, $val);
    Cookie::newInstance()->set();
  }
}


if(!function_exists('mb_get_cookie')) {
  function mb_get_cookie($name) {
    return Cookie::newInstance()->get_value($name);
  }
}

if(!function_exists('mb_drop_cookie')) {
  function mb_drop_cookie($name) {
    Cookie::newInstance()->pop($name);
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

?>