<?php
  // Create menu
  $title = __('Configure', 'account_sync');
  asc_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value

  $apikey = mb_param_update('apikey', 'plugin_action', 'value', 'plugin-account_sync');
  $custom_columns = mb_param_update('custom_columns', 'plugin_action', 'value', 'plugin-account_sync');
  $feed_limit = mb_param_update('feed_limit', 'plugin_action', 'value', 'plugin-account_sync');



  if(Params::getParam('plugin_action') == 'done') {
    message_ok( __('Settings were successfully saved', 'account_sync') );
  }
?>



<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-cog"></i> <?php _e('Configure', 'account_sync'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_action" value="done" />

        <div class="mb-notes" style="font-weight:bold;">
          <div class="mb-line"><?php _e('This plugin has documentation included, please ready it first:', 'account_sync'); ?> <a style="text-decoration:underline;" href="<?php echo osc_base_url(); ?>oc-content/plugins/account_sync/documentation.pdf"><?php _e('Download documentation (PDF)', 'account_sync'); ?></a></div>
        </div>
        
        <div class="mb-notes">
          <div class="mb-line"><?php _e('Enter name/identificator of your site and API key, that is used by other osclass installations to feed data from this website.', 'account_sync'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="apikey" class=""><span><?php _e('API Key', 'account_sync'); ?></span></label> 
          <input size="110" name="apikey" id="apikey" type="text" value="<?php echo $apikey; ?>" />

          <div class="mb-explain"><?php _e('API key is used by other osclass installations to feed account data from this website.', 'account_sync'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="custom_columns" class=""><span><?php _e('Custom user columns', 'account_sync'); ?></span></label> 
          <input size="110" name="custom_columns" id="custom_columns" type="text" value="<?php echo $custom_columns; ?>" />

          <div class="mb-explain"><?php _e('Enter custom column names from user table, if pre-defined ones are not sufficient or you are missing any of them.', 'account_sync'); ?></div>
        </div>

        <div class="mb-row">
          <label for="feed_limit" class=""><span><?php _e('Feed limit', 'account_sync'); ?></span></label> 
          <input size="6" name="feed_limit" id="feed_limit" type="text" value="<?php echo $feed_limit; ?>" />

          <div class="mb-explain"><?php _e('Limit number of items those can be fed in 1 call. Default is 200. Powerful server can handle thousands.', 'account_sync'); ?></div>
        </div>
        
        <?php if(!asc_is_demo()) { ?>
          <div class="mb-foot">
            <button type="submit" class="mb-button"><?php _e('Save', 'account_sync');?></button>
          </div>
        <?php } ?>
      </form>
    </div>
  </div>



  <!-- PLUGIN INTEGRATION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Plugin Setup', 'account_sync'); ?></div>

    <div class="mb-inside">
      <div class="mb-row"><?php _e('Plugin does not require any modifications in theme files.', 'account_sync'); ?></div>
      <div class="mb-row"><?php _e('Osclass CRON setup is essential for proper functionality of this plugin, otherwise new user records will not be streamed to bellow mentioned accounts.', 'account_sync'); ?></div>
      <div class="mb-row"><?php _e('For best performance, we strongly recommend to create CRON command to execute following URL ideally every 1-5 minutes:', 'account_sync'); ?> <a href="<?php echo osc_base_url(); ?>oc-content/plugins/account_sync/cron.php"><?php echo osc_base_url(); ?>oc-content/plugins/account_sync/cron.php</a></div>
    </div>
  </div>


</div>

<?php echo asc_footer(); ?>