<?php
  // Create menu
  $title = __('Account', 'account_sync');
  asc_menu($title);

  $id = Params::getParam('accountId');

  if(Params::getParam('plugin_action') == 'done') {
    $url = Params::getParam('s_url');
    $url = rtrim($url, '/') . '/';

    
    $data = array(
      's_name' => Params::getParam('s_name'),
      's_url' => $url,
      's_key' => Params::getParam('s_key'),
      's_columns' => Params::getParam('s_columns'),
      'i_last_user_id' => Params::getParam('i_last_user_id'),
      'i_synced_from' => (Params::getParam('i_synced_from') > 0 ? Params::getParam('i_synced_from') : 0),
      'i_synced_to' => (Params::getParam('i_synced_to') > 0 ? Params::getParam('i_synced_to') : 0)
    );
    
    if($id == '' || $id == 0) {
      $id = ModelASC::newInstance()->insertAccount($data);
      osc_add_flash_ok_message(__('Account successfully created.', 'account_sync'), 'admin');
    } else {
      ModelASC::newInstance()->updateAccount($id, $data);
      osc_add_flash_ok_message(__('Account successfully updated.', 'account_sync'), 'admin');
    }

    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=account_sync/admin/account_edit.php&accountId=' . $id);
    exit;
  }


  $avl_columns = asc_available_user_columns();
  $def_columns = asc_default_user_columns();
  
  if($id <> '' && $id > 0) {
    $account = ModelASC::newInstance()->getAccount($id);
    $acols = $account['s_columns'];
    $acols_array = explode(',', $acols);
    $logs = ModelASC::newInstance()->getLogs(200, $id);
    
  } else {
    $acols = implode(',', $def_columns);
    $acols_array = $def_columns;
    $logs = array();
  }


  // REMOVE ACCOUNT
  if(Params::getParam('what') == 'delete') { 
    ModelASC::newInstance()->removeAccount($id);
    osc_add_flash_ok_message(__('Account successfully removed', 'account_sync'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=account_sync/admin/accounts.php');
    exit;
  }
  
 
?>


<div class="mb-body">
  <!-- CONFIGURE SECTION -->
  <div class="mb-box mb-account-detail">
    <div class="mb-head">
      <i class="fa fa-key"></i> <?php _e('Account configuration', 'account_sync'); ?>
      
      <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=account_sync/admin/accounts.php&what=sync&accountId=<?php echo $id; ?>" class="mb-btn mb-button-green mb-head-sync mb-has-tooltip-light" title="<?php echo osc_esc_html(__('Initiate feed and download user data from this account/domain. Do this just in case of first setup or you are sure this account has more accurate user information.', 'account_sync')); ?>"><i class="fa fa-download"></i> <?php _e('Feed now', 'account_sync'); ?></a>
    </div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>account_edit.php" />
        <input type="hidden" name="plugin_action" value="done" />
        <input type="hidden" name="accountId" value="<?php echo $id; ?>" />
        
        <div class="mb-notes">
          <div class="mb-line"><?php _e('Account identifies other website/instance from that you want to feed data and to that you want to stream new data.', 'account_sync'); ?></div>
        </div>

        <?php if($id > 0) { ?>
          <div class="mb-row">
            <label for="pk_i_id"><span><?php _e('Account ID', 'account_sync'); ?></span></label> 
            <input size="10" disabled id="pk_i_id" type="text" value="<?php echo $id; ?>" />

            <div class="mb-explain"><?php _e('Total number of user records streamed/synced to this account. Field is not editable.', 'account_sync'); ?></div>
          </div>
        <?php } ?>

        <div class="mb-row">
          <label for="s_name"><span><?php _e('Name', 'account_sync'); ?></span></label> 
          <input required size="50" name="s_name" id="s_name" type="text" value="<?php echo isset($account['s_name']) ? $account['s_name'] : ''; ?>" />

          <div class="mb-explain"><?php _e('Name or identifier of this account. Example: my.domain.com', 'account_sync'); ?></div>
        </div>

        <div class="mb-row">
          <label for="s_url"><span><?php _e('URL', 'account_sync'); ?></span></label> 
          <input required size="100" name="s_url" id="s_url" type="url" value="<?php echo isset($account['s_url']) ? $account['s_url'] : ''; ?>" />

          <div class="mb-explain">
            <div class="mb-line"><?php _e('URL to another osclass installation from where / to which you want to get / receive data.', 'account_sync'); ?></div>
            <div class="mb-line"><?php _e('<b>WARNING:</b> URL must end with "/" sign. Example: https://mysite.com/, http://sub.mysite.com/', 'account_sync'); ?></div>
          </div>
        </div>
        
        <div class="mb-row">
          <label for="s_key"><span><?php _e('API Key', 'account_sync'); ?></span></label> 
          <input required size="100" name="s_key" id="s_key" type="text" value="<?php echo isset($account['s_key']) ? $account['s_key'] : ''; ?>" />

          <div class="mb-explain"><?php _e('API Key of target installation. Can be found at Configure page of this plugin on target domain.', 'account_sync'); ?></div>
        </div>

        <div class="mb-row mb-row-select-multiple">
          <label for="s_columns_multiple"><span><?php _e('Columns', 'account_sync'); ?></span></label> 

          <input type="hidden" name="s_columns" id="s_columns" value="<?php echo $acols; ?>"/>
          <select id="s_columns_multiple" name="s_columns_multiple" multiple>
            <?php foreach($avl_columns as $cid => $cname) { ?>
              <option value="<?php echo $cid; ?>" <?php if($cid == 'custom_start') { ?>disabled<?php } ?> <?php if(in_array($cid, $acols_array)) { ?>selected="selected"<?php } ?>><?php echo $cname; ?> <?php echo $cid != 'custom_start' ? '[' . $cid . ']' : ''; ?></option>
            <?php } ?>
          </select>

          <div class="mb-explain">
            <div class="mb-line"><?php _e('Select list of columns those will be synchronized against this account. Custom columns can be defined at Configure page.', 'account_sync'); ?></div>
            <div class="mb-line"><?php _e('<b>WARNING:</b> Carefuly with IDs - Foreign/Primary keys (FK/PK)!!! If keys are missing in this installation, synchronization will fail !!!', 'account_sync'); ?></div>
          </div>
        </div>

        <div class="mb-row">
          <label for="i_last_user_id"><span><?php _e('Last User ID', 'account_sync'); ?></span></label> 
          <input size="15" name="i_last_user_id" id="i_last_user_id" type="text" value="<?php echo isset($account['i_last_user_id']) ? $account['i_last_user_id'] : ''; ?>" />

          <div class="mb-explain"><?php _e('Last user ID fed from this account. Edit to 0 if you want to restart synchronization from this account.', 'account_sync'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="i_synced_from"><span><?php _e('Synced from', 'account_sync'); ?></span></label> 
          <input size="12" id="i_synced_from" name="i_synced_from" type="text" value="<?php echo isset($account['i_synced_from']) ? $account['i_synced_from'] : 0; ?>" />

          <div class="mb-explain"><?php _e('Total number of user records fed/synced from this account. Set to 0 to reset statistics.', 'account_sync'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="i_synced_to"><span><?php _e('Synced to', 'account_sync'); ?></span></label> 
          <input size="12" id="i_synced_to" name="i_synced_to" type="text" value="<?php echo isset($account['i_synced_to']) ? $account['i_synced_to'] : 0; ?>" />

          <div class="mb-explain"><?php _e('Total number of user records streamed/synced to this account. Set to 0 to reset statistics.', 'account_sync'); ?></div>
        </div>
        
        
        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <?php if($id == '' || $id == 0) { ?>
            <button type="submit" class="mb-button"><?php _e('Create', 'account_sync');?></button>
          <?php } else { ?>
            <button type="submit" class="mb-button"><?php _e('Update', 'account_sync');?></button>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>
  
  
  
  <!-- SYNC DETAILS SECTION -->
  <?php if($id > 0) { ?>
    <div class="mb-box">
      <div class="mb-head"><i class="fa fa-search"></i> <?php _e('Synchronization details', 'account_sync'); ?></div>

      <div class="mb-inside">
        <div class="mb-notes">
          <div class="mb-line"><?php _e('Here you can find details about last synchronization, but also sync statistics.', 'account_sync'); ?></div>
        </div>
        

        <div class="mb-row">
          <label for="s_status"><span><?php _e('Status', 'account_sync'); ?></span></label> 
          <input size="10" disabled id="s_status" type="text" value="<?php echo isset($account['s_status']) ? $account['s_status'] : ''; ?>" />

          <div class="mb-explain"><?php _e('Status of last synchronization. Field is not editable.', 'account_sync'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="s_message"><span><?php _e('Message', 'account_sync'); ?></span></label> 
          <textarea disabled id="s_status"><?php echo isset($account['s_message']) ? $account['s_message'] : ''; ?></textarea>

          <div class="mb-explain"><?php _e('Message from last synchronization. Field is not editable.', 'account_sync'); ?></div>
        </div>

        <div class="mb-row">
          <label for="dt_last_sync"><span><?php _e('Synchronization date', 'account_sync'); ?></span></label> 
          <input size="20" disabled id="dt_last_sync" type="text" value="<?php echo isset($account['dt_last_sync']) ? date('Y-m-d H:i:s', strtotime($account['dt_last_sync'])) : ''; ?>" />

          <div class="mb-explain"><?php _e('Timestamp of last synchronization. Field is not editable.', 'account_sync'); ?></div>
        </div> 

      </div>
    </div>
  <?php } ?>
  
  
  <!-- LOGS SECTION -->
  <?php if($id > 0) { ?>
    <div class="mb-box">
      <div class="mb-head"><i class="fa fa-search"></i> <?php _e('Account logs', 'account_sync'); ?></div>

      <div class="mb-inside">
        <div class="mb-notes">
          <div class="mb-line"><?php _e('Last 200 logs related to this Account', 'account_sync'); ?></div>
        </div>
        
        <div class="mb-table mb-table-logs">
          <div class="mb-table-head">
            <div class="mb-col-1"><?php _e('ID', 'account_sync');?></div>
            <div class="mb-col-2 mb-align-left"><?php _e('Type', 'account_sync'); ?></div>
            <div class="mb-col-3 mb-align-left"><?php _e('Account/Feeder', 'account_sync');?></div>
            <div class="mb-col-7 mb-align-left"><?php _e('Content', 'account_sync'); ?></div>
            <div class="mb-col-5 mb-align-left"><?php _e('Response', 'account_sync'); ?></div>
            <div class="mb-col-1"><?php _e('Status', 'account_sync'); ?></div>
            <div class="mb-col-2 mb-align-left"><?php _e('Date', 'account_sync'); ?></div>
            <div class="mb-col-3">&nbsp;</div>
          </div>

          <?php if(count($logs) <= 0) { ?>
            <div class="mb-table-row mb-row-empty">
              <i class="fa fa-warning"></i><span><?php _e('No logs has been found', 'account_sync'); ?></span>
            </div>
          <?php } else { ?>
            <div class="mb-table-content-wrap">
              <?php foreach($logs as $l) { ?>
                <?php
                  $account_details = array_filter(array_map('trim', array($l['s_name'], $l['s_ip'])));
                  $account_details = implode(' / ', $account_details);
                  $content_short = osc_highlight($l['s_content'], 160);
                ?>
                
                <div class="mb-table-row">
                  <div class="mb-col-1"><?php echo $l['pk_i_id']; ?></div>
                  <div class="mb-col-2 mb-align-left"><?php echo $l['s_type']; ?></div>
                  <div class="mb-col-3 mb-align-left"><span class="mb-has-tooltip-light" title="<?php echo osc_esc_html(__('API key', 'account_sync') . ': ' . osc_highlight($l['s_key'], 20)); ?>"><?php echo $account_details; ?></div>
                  <div class="mb-col-7 mb-align-left"><span class="mb-has-tooltip-long" title="<?php echo osc_esc_html($l['s_content']); ?>"><?php echo ($content_short <> '' ? $content_short : '<em>' . __('- No content received -', 'account_sync') . '</em>'); ?></span></div>
                  <div class="mb-col-5 mb-align-left"><span class="mb-has-tooltip-long" title="<?php echo osc_esc_html($l['s_response']); ?>"><?php echo $l['s_response']; ?></span></div>
                  <div class="mb-col-1"><span class="mb-status<?php echo substr($l['s_code'], 0, 1); ?>"><?php echo $l['s_code']; ?></span></div>
                  <div class="mb-col-2 mb-align-left"><span class="mb-has-tooltip-light date" title="<?php echo date('Y-m-d H:i:s', strtotime($l['dt_date'])); ?>"><?php echo date('Y-m-d H:i:s', strtotime($l['dt_date'])); ?></span></div>
                  <div class="mb-col-3 mb-align-right">
                    <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=account_sync/admin/log_details.php&logId=<?php echo $l['pk_i_id']; ?>" class="mb-btn mb-button-blue"><i class="fa fa-search"></i> <?php _e('View', 'account_sync'); ?></a>
          
                    <?php if(!asc_is_demo()) { ?>
                      <a class="mb-btn mb-button-red" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=account_sync/admin/log_details.php&logId=<?php echo $l['pk_i_id']; ?>&what=delete" onclick="return confirm('<?php echo osc_esc_html(__('Are you sure you want to delete this log record?', 'account_sync')); ?>')"><i class="fa fa-trash"></i></a>
                    <?php } ?>          
                  </div>
                </div>
              <?php } ?>
            </div>
          <?php } ?>
        </div>
      
      </div>
    </div>
  <?php } ?>
</div>

<?php echo asc_footer(); ?>