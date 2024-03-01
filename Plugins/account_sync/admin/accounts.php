<?php
  // Create menu
  $title = __('Accounts', 'account_sync');
  asc_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value

  if(Params::getParam('what') == 'sync' && Params::getParam('accountId') > 0) {
    $id = Params::getParam('accountId');
    $output = asc_feed_users($id);
   
    $message  = '<p>' . __('Synchronization has finished.', 'account_sync') . '</p>';
    $message .= '<p class="mb-light"><strong class="' . ($output['status'] == 'ERROR' ? 'mb-status4' : 'mb-status2') . '">' . $output['status'] . '</strong> ' . $output['message'] . '</p>';
    
    osc_add_flash_ok_message($message, 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=account_sync/admin/accounts.php');
    exit;
  }
  
  $accounts = ModelASC::newInstance()->getAccounts(true);
?>


<div class="mb-body">

  <!-- LIST SECTION -->
  <div class="mb-box mb-bp">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Accounts', 'account_sync'); ?></div>

    <div class="mb-inside">
      <div class="mb-notes">
        <div class="mb-line"><?php _e('Here you can define accounts - other domains/websites with those you want to synchronize user accounts of this website. This installation will send user data to bellow defined accounts as well this installation can feed user data from bellow defined accounts.', 'account_sync'); ?></div>
        <div class="mb-line"><?php _e('Press "Feed now" button to start feeding data from account/domain. This may be repeated several times until all user records are downloaded. Progress is reported in response message.', 'account_sync'); ?></div>
        <div class="mb-line"><?php _e('Osclass CRON setup is essential for proper functionality of this plugin, otherwise new user records will not be streamed to bellow mentioned accounts.', 'account_sync'); ?></div>
        <div class="mb-line"><?php _e('For best performance, we strongly recommend to create CRON command to execute following URL ideally every 1-5 minutes:', 'account_sync'); ?> <a href="<?php echo osc_base_url(); ?>oc-content/plugins/account_sync/cron.php"><?php echo osc_base_url(); ?>oc-content/plugins/account_sync/cron.php</a></div>
      </div>      

      <div class="mb-table mb-table-accounts">
        <div class="mb-table-head">
          <div class="mb-col-1"><?php _e('ID', 'account_sync');?></div>
          <div class="mb-col-3 mb-align-left"><?php _e('Name', 'account_sync'); ?></div>
          <div class="mb-col-5 mb-align-left"><?php _e('URL', 'account_sync');?></div>
          <div class="mb-col-3"><?php _e('Synced feed/stream', 'account_sync'); ?></div>
          <div class="mb-col-2"><?php _e('Status', 'account_sync'); ?></div>
          <div class="mb-col-4 mb-align-left"><?php _e('Message', 'account_sync'); ?></div>
          <div class="mb-col-2 mb-align-left"><?php _e('Last sync', 'account_sync'); ?></div>
          <div class="mb-col-4">&nbsp;</div>
        </div>

        <?php if(count($accounts) <= 0) { ?>
          <div class="mb-table-row mb-row-empty">
            <i class="fa fa-warning"></i><span><?php _e('No accounts has been found', 'account_sync'); ?></span>
          </div>
        <?php } else { ?>
          <?php foreach($accounts as $a) { ?>
            <?php
              $logs = '';
              
              if(isset($a['logs']) && is_array($a['logs']) && count($a['logs']) > 0) {
                foreach($a['logs'] as $log) {
                  $logs .= '#' . $log['pk_i_id'] . ' - ' . $log['s_type'] . ' - ' . $log['s_name'] . ': ' . $log['s_response'] . PHP_EOL;
                }
              }
            ?>
            
            <div class="mb-table-row">
              <div class="mb-col-1"><?php echo $a['pk_i_id']; ?></div>
              <div class="mb-col-3 mb-align-left"><?php echo $a['s_name']; ?></div>
              <div class="mb-col-5 mb-align-left"><span class="mb-has-tooltip-light" title="<?php echo osc_esc_html($a['s_url']); ?>"><?php echo osc_highlight($a['s_url'], 40); ?></div>
              <div class="mb-col-3"><?php echo ($a['i_synced_from'] > 0 ? $a['i_synced_from'] : 0); ?>x / <?php echo ($a['i_synced_to'] > 0 ? $a['i_synced_to'] : 0); ?>x</div>

              <div class="mb-col-2">
                <?php if(trim($a['s_status']) !== '') { ?>
                  <span class="<?php if($a['s_status'] == 'OK' || substr($a['s_status'], 0, 1) == '2') { ?>mb-status2<?php } else { ?>mb-status4<?php } ?>"><?php echo $a['s_status']; ?></span>
                <?php } ?>
              </div>

              <div class="mb-col-4 mb-align-left"><span class="mb-has-tooltip-light mb-mes" title="<?php echo htmlentities($a['s_message']); ?>"><?php echo osc_highlight($a['s_message'], 50); ?></div>
              <div class="mb-col-2 mb-align-left">
                <?php if($a['dt_last_sync'] != '') { ?>
                  <span class="mb-has-tooltip-light" title="<?php echo date('Y-m-d H:i:s', strtotime($a['dt_last_sync'])); ?>"><?php echo date('Y-m-d', strtotime($a['dt_last_sync'])); ?></span>
                <?php } else { ?>
                  <?php echo '-'; ?>
                <?php } ?>
              </div>
              
              <div class="mb-col-4 mb-align-right">
                <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=account_sync/admin/accounts.php&what=sync&accountId=<?php echo $a['pk_i_id']; ?>" class="mb-btn mb-button-green mb-has-tooltip-light" title="<?php echo osc_esc_html(__('Initiate feed and download user data from this account/domain. Do this just in case of first setup or you are sure this account has more accurate user information.', 'account_sync')); ?>"><i class="fa fa-download"></i> <?php _e('Feed now', 'account_sync'); ?></a>
                <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=account_sync/admin/account_edit.php&accountId=<?php echo $a['pk_i_id']; ?>" class="mb-btn mb-button-blue"><i class="fa fa-pencil"></i> <?php _e('Edit', 'account_sync'); ?></a>

                <?php if(!asc_is_demo()) { ?>
                  <a class="mb-btn mb-button-red" href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=account_sync/admin/account_edit.php&accountId=<?php echo $a['pk_i_id']; ?>&what=delete" onclick="return confirm('<?php echo osc_esc_html(__('Are you sure you want to delete this account?', 'account_sync')); ?>')"><i class="fa fa-trash"></i></a>
                <?php } ?>
              </div>
            </div>
          <?php } ?>
        <?php } ?>
      </div>

      <div class="mb-row"></div>
      
      <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=account_sync/admin/account_edit.php" class="mb-button-green mb-add"><i class="fa fa-plus-circle"></i><?php _e('Create a new account', 'account_sync'); ?></a>
    </div>
  </div>
  
  
  <!-- STREAM STATUS SECTION -->
  <div class="mb-box mb-bp">
    <div class="mb-head"><i class="fa fa-upload"></i> <?php _e('Streaming status (pending data to be streamed)', 'account_sync'); ?></div>

    <div class="mb-inside">
      <div class="mb-notes">
        <div class="mb-line"><?php _e('List of changes (updates/removal) made on this domain, those will be streamed using upcoming CRON call to all above listed accounts.', 'account_sync'); ?></div>
      </div>      

      <div class="mb-row mb-streams">
        <strong class="mb-line"><?php _e('User updates to be streamed:', 'account_sync'); ?></strong>
        <div class="mb-row mb-stream-output mb-code"><?php 
            $data = array_filter(array_unique(explode(',', asc_param('users_to_stream')))); 
          
            if(is_array($data) && count($data) > 0) {
              foreach($data as $id) { 
                $user = User::newInstance()->findByPrimaryKey($id); 
              
                if(isset($user['pk_i_id']) && $user['pk_i_id'] > 0) {
                  echo __('UPDATE', 'account_sync') . ': ' . '#' . str_pad($id, 6, ' ') . ' - ' . str_pad(osc_highlight($user['s_email'], 42), 50, ' ') . ' - ' . $user['s_name'] . PHP_EOL;
                } else {
                  echo __('UPDATE', 'account_sync') . ': ' . '#' . str_pad($id, 6, ' ') . ' - ' . __('User record not found, it was probably removed meanwhile', 'account_sync') . PHP_EOL;
                }
              }
            } else {
              echo __('No user records updated from last CRON execution', 'account_sync'); 
            }
          ?></div>
      </div>
      
      <div class="mb-row mb-streams">
        <strong class="mb-line"><?php _e('User removals to be streamed:', 'account_sync'); ?></strong>
        <div class="mb-row mb-stream-output mb-code"><?php 
            $data = array_filter(array_unique(explode(',', asc_param('users_to_stream_removed'))));
          
            if(is_array($data) && count($data) > 0) {
              foreach($data as $email) { 
                echo __('REMOVAL', 'account_sync') . ': ' . $email . PHP_EOL;
              }
            } else {
              echo __('No user records removed from last CRON execution', 'account_sync'); 
            }
          ?></div>
      </div>
      
      <div class="mb-row"></div>
      
      <a href="<?php echo osc_base_url(); ?>oc-content/plugins/account_sync/cron.php?print=all" class="mb-button-green mb-add" target="_blank"><i class="fa fa-upload"></i><?php _e('Execute Stream now!', 'account_sync'); ?></a>
    </div>
  </div>       
</div>


<?php echo asc_footer(); ?>