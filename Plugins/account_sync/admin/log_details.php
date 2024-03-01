<?php
  // Create menu
  $title = __('Log details', 'account_sync');
  asc_menu($title);

  $id = Params::getParam('logId');
  $log = ModelASC::newInstance()->getLog($id);


  // REMOVE ACCOUNT
  if(Params::getParam('what') == 'delete') { 
    ModelASC::newInstance()->removeLog($id);
    osc_add_flash_ok_message(__('Log record successfully removed', 'account_sync'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=account_sync/admin/logs.php');
    exit;
  }
  
 
?>


<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  <div class="mb-box mb-log-detail">
    <div class="mb-head">
      <i class="fa fa-search"></i> <?php _e('Here you can find all details about log record', 'account_sync'); ?>
      
      <?php if(!asc_is_demo()) { ?>
        <a class="mb-btn mb-button-red mb-head-removelog" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=account_sync/admin/log_details.php&logId=<?php echo $id; ?>&what=delete" onclick="return confirm('<?php echo osc_esc_html(__('Are you sure you want to delete this log record?', 'account_sync')); ?>')"><i class="fa fa-trash"></i> <?php _e('Remove log', 'account_sync'); ?></a>
      <?php } ?>          
    </div>

    <div class="mb-inside">
        <div class="mb-notes">
          <div class="mb-line"><?php _e('Log details', 'account_sync'); ?></div>
        </div>


        <div class="mb-row">
          <label ><span><?php _e('ID', 'account_sync'); ?></span></label> 
          <input readonly size="10" type="text" value="<?php echo $log['pk_i_id']; ?>" />
        </div>

        <div class="mb-row">
          <label ><span><?php _e('Type', 'account_sync'); ?></span></label> 
          <input readonly size="20" type="text" value="<?php echo $log['s_type']; ?>" />
        </div>
        
        <div class="mb-row">
          <label ><span><?php _e('Account ID', 'account_sync'); ?></span></label> 
          <input readonly size="10" type="text" value="<?php echo $log['fk_i_account_id']; ?>" />
        </div>
        
        <div class="mb-row">
          <label ><span><?php _e('Account Name/Identifier', 'account_sync'); ?></span></label> 
          <input readonly size="50" type="text" value="<?php echo $log['s_name']; ?>" />
        </div>
        
        <div class="mb-row">
          <label ><span><?php _e('Account Key', 'account_sync'); ?></span></label> 
          <input readonly size="80" type="text" value="<?php echo $log['s_key']; ?>" />
        </div>
        
        <div class="mb-row">
          <label ><span><?php _e('IP Address', 'account_sync'); ?></span></label> 
          <input readonly size="20" type="text" value="<?php echo $log['s_ip']; ?>" />
        </div>
        
        <div class="mb-row">
          <label ><span><?php _e('Date', 'account_sync'); ?></span></label> 
          <input readonly size="20" type="text" value="<?php echo date('Y-m-d H:i:s', strtotime($log['dt_date'])); ?>" />
        </div>
        
        <div class="mb-row">
          <label ><span><?php _e('Status Code', 'account_sync'); ?></span></label> 
          <input readonly size="10" type="text" value="<?php echo $log['s_code']; ?>" />
        </div>
        
        <div class="mb-row">
          <label ><span><?php _e('Response', 'account_sync'); ?></span></label> 
          <textarea class="mb-has-code mb-res"><?php echo osc_esc_html($log['s_response']); ?></textarea>
        </div>
        
        <div class="mb-row">
          <label ><span><?php _e('Content', 'account_sync'); ?></span></label> 
          <textarea class="mb-has-code mb-con"><?php echo (trim($log['s_content']) <> '' ? osc_esc_html($log['s_content']) : __('- No content received -', 'account_sync')); ?></textarea>
        </div>
        

      </form>
    </div>
  </div>
  
</div>

<?php echo asc_footer(); ?>