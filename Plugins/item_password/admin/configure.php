<?php
  // Create menu
  $title = __('Configure', 'item_password');
  itp_menu($title);
 

  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value
  $enable = mb_param_update('enable', 'plugin_action', 'check', 'plugin-item_password');
  $hook_edit = mb_param_update('hook_edit', 'plugin_action', 'check', 'plugin-item_password');
  $hook_delete = mb_param_update('hook_delete', 'plugin_action', 'check', 'plugin-item_password');
  $hook_form = mb_param_update('hook_form', 'plugin_action', 'check', 'plugin-item_password');
  $show_with_password = mb_param_update('show_with_password', 'plugin_action', 'check', 'plugin-item_password');
  $show_with_user = mb_param_update('show_with_user', 'plugin_action', 'check', 'plugin-item_password');
  $password_type = mb_param_update('password_type', 'plugin_action', 'value', 'plugin-item_password');
  $style_buttons = mb_param_update('style_buttons', 'plugin_action', 'check', 'plugin-item_password');


  if(Params::getParam('plugin_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'item_password') );
  }
?>


<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  
  <div class="mb-notes">
    <div class="mb-line"><?php _e('Plugin stores 2 passwords - one entered by customer and one entered by admin, so admin does not see and change customer password and vice versa.', 'item_password'); ?></div>
  </div>
  
  <div class="mb-box">
    <div class="mb-head">
      <i class="fa fa-wrench"></i> <?php _e('Configure', 'item_password'); ?>
    </div>

    <div class="mb-inside">
      <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_action" value="done" />


        <div class="mb-row">
          <label for="enable"><span><?php _e('Enable Item Password', 'item_password'); ?></span></label> 
          <input name="enable" id="enable" type="checkbox" class="element-slide" <?php echo ($enable == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('When enabled, users can define their edit/remove password for listing.', 'item_password'); ?></div>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="hook_edit"><span><?php _e('Hook Edit Button', 'item_password'); ?></span></label> 
          <input name="hook_edit" id="hook_edit" type="checkbox" class="element-slide" <?php echo ($hook_edit == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('When enabled, edit listing button is automatically hooked to item page (item_detail).', 'item_password'); ?></div>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="hook_delete"><span><?php _e('Hook Delete Button', 'item_password'); ?></span></label> 
          <input name="hook_delete" id="hook_delete" type="checkbox" class="element-slide" <?php echo ($hook_delete == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('When enabled, delete listing button is automatically hooked to item page (item_detail).', 'item_password'); ?></div>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="hook_form"><span><?php _e('Hook Input to Form', 'item_password'); ?></span></label> 
          <input name="hook_form" id="hook_form" type="checkbox" class="element-slide" <?php echo ($hook_form == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('When enabled, password input is automatically hooked to item publish & edit page.', 'item_password'); ?></div>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="show_with_password"><span><?php _e('On Items with Password Only', 'item_password'); ?></span></label> 
          <input name="show_with_password" id="show_with_password" type="checkbox" class="element-slide" <?php echo ($show_with_password == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('When enabled, edit & delete buttons will be shown only on items those has password defined by users.', 'item_password'); ?></div>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="show_with_user"><span><?php _e('On Items with User', 'item_password'); ?></span></label> 
          <input name="show_with_user" id="show_with_user" type="checkbox" class="element-slide" <?php echo ($show_with_user == 1 ? 'checked' : ''); ?> <?php echo (!itp_user_items_eligible() ? 'disabled' : ''); ?>/>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('When enabled, edit & delete buttons are shown also on items those were created by registered users.', 'item_password'); ?></div>
            <div class="mb-line"><strong><?php _e('Warning!', 'item_password'); ?></strong> <?php _e('This feature is available only on Osclass v8.0.1 and later.', 'item_password'); ?></div>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="password_type"><span><?php _e('Password Input Type', 'item_password'); ?></span></label>
          
          <select name="password_type">
            <option value="text" <?php if($password_type == 'text' || $password_type == '') { ?>selected="selected"<?php } ?>><?php _e('Text (unmasked)', 'item_password'); ?></option>
            <option value="password" <?php if($password_type == 'password') { ?>selected="selected"<?php } ?>><?php _e('Password (masked)', 'item_password'); ?></option>
          </select>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('Select type on input that will be used for password. Text type is not masked and password is exposed in front. Password type is masked and password is not shown.', 'item_password'); ?></div>
          </div>
        </div>    

        <div class="mb-row">
          <label for="style_buttons"><span><?php _e('Style Buttons', 'item_password'); ?></span></label> 
          <input name="style_buttons" id="style_buttons" type="checkbox" class="element-slide" <?php echo ($style_buttons == 1 ? 'checked' : ''); ?>/>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('When enabled, edit & delete buttons will be styled (formatted) by plugin. Otherwise, only basic links will be shown with no formatting.', 'item_password'); ?></div>
          </div>
        </div>



        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <?php if(itp_is_demo()) { ?>
            <a class="mb-button mb-has-tooltip disabled" onclick="return false;" style="cursor:not-allowed;opacity:0.5;" title="<?php echo osc_esc_html(__('This is demo site', 'item_password')); ?>"><?php _e('Save', 'item_password');?></a>
          <?php } else { ?>
            <button type="submit" class="mb-button"><?php _e('Save', 'item_password');?></button>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>



  <!-- PLUGIN INTEGRATION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Plugin Setup', 'item_password'); ?></div>

    <div class="mb-inside">
      <div class="mb-row"><?php _e('No theme modification are required to use all functions of plugin, but in order to provide more integrated view of plugin, followimg functions are available.', 'item_password'); ?></div>
      <div class="mb-row">
        <strong><?php _e('Edit button code:', 'item_password'); ?></strong>
        <span class="mb-code">&lt;?php itp_button_edit($item = NULL); ?&gt;</span>

        <span class="mb-line">&nbsp;</span>
        
        <strong><?php _e('Delete button code:', 'item_password'); ?></strong>
        <span class="mb-code">&lt;?php itp_button_delete($item = NULL); ?&gt;</span>

        <span class="mb-line">&nbsp;</span>
        
        <strong><?php _e('Custom password input on publish/edit page:', 'item_password'); ?></strong>
        <span class="mb-code">&lt;input type="text" name="sItpPassword" value="" /&gt;</span>

      </div>
    </div>
  </div>
</div>


<?php echo itp_footer(); ?>