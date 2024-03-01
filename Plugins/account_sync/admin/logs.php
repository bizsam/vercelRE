<?php
  // Create menu
  $title = __('Synchronization Logs', 'account_sync');
  asc_menu($title);


  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value

  
  $params = Params::getParamsAsArray();
  $logs = ModelASC::newInstance()->getLogsSearch($params);
?>


<div class="mb-body">

  <!-- LIST SECTION -->
  <div class="mb-box mb-bp">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Synchronization Logs', 'account_sync'); ?></div>

    <div class="mb-inside">
      <div class="mb-notes">
        <div class="mb-line"><?php _e('Log types: PROCESS - processing of single user record; FEED - feeding of data from other domains/accounts; STREAM - streaming of data to other domains/accounts.', 'account_sync'); ?></div>
        <div class="mb-line"><?php _e('By default, last 200 logs are shown.', 'account_sync'); ?></div>
      </div>      


      <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=account_sync/admin/logs.php" method="POST" enctype="multipart/form-data" >
        <input type="hidden" name="logSearch" value="1"/>
        
        <div id="mb-search-table">
          <div class="mb-col-3">
            <label for="type"><?php _e('Type', 'account_sync'); ?></label>
            <select name="type" value="<?php echo Params::getParam('type'); ?>">
              <option value="" <?php if(Params::getParam('type') == '') { ?>selected="selected"<?php } ?>><?php _e('All', 'account_sync'); ?></option>
              <option value="FEED" <?php if(Params::getParam('type') == 'FEED') { ?>selected="selected"<?php } ?>><?php _e('Feed', 'account_sync'); ?></option>
              <option value="STREAM" <?php if(Params::getParam('type') == 'STREAM') { ?>selected="selected"<?php } ?>><?php _e('Stream', 'account_sync'); ?></option>
              <option value="PROCESS" <?php if(Params::getParam('type') == 'PROCESS') { ?>selected="selected"<?php } ?>><?php _e('Process', 'account_sync'); ?></option>
            </select>
          </div>
          
          <div class="mb-col-4">
            <label for="account"><?php _e('Account/Feeder', 'account_sync'); ?></label>
            <input type="text" name="account" value="<?php echo Params::getParam('account'); ?>" />
          </div>
          
          <div class="mb-col-6">
            <label for="content"><?php _e('Content', 'account_sync'); ?></label>
            <input type="text" name="content" value="<?php echo Params::getParam('content'); ?>" />
          </div>
          
          <div class="mb-col-3">
            <label for="response"><?php _e('Response', 'account_sync'); ?></label>
            <input type="text" name="response" value="<?php echo Params::getParam('response'); ?>" />
          </div>
          
          <div class="mb-col-2">
            <label for="status"><?php _e('Status', 'account_sync'); ?></label>
            <input type="text" name="status" value="<?php echo Params::getParam('status'); ?>" />
          </div>
          
          <div class="mb-col-2">
            <label for="date"><?php _e('Date', 'account_sync'); ?></label>
            <input type="text" name="date" value="<?php echo Params::getParam('date'); ?>" placeholder="yyyy-mm-dd"/>
          </div>
          
          <div class="mb-col-2">
            <label for="limit"><?php _e('Limit', 'account_sync'); ?></label>
            <input type="text" name="limit" value="<?php echo (Params::getParam('limit') > 0 ? Params::getParam('limit') : 200); ?>"/>
          </div>
          
          <div class="mb-col-2">
            <label for="">&nbsp;</label>
            <button type="submit" class="mb-button mb-button-black"><i class="fa fa-search"></i> <?php _e('Search', 'account_sync'); ?></button>
          </div>
        </div>
      </form>
      

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
        <?php } ?>
      </div>
    </div>
  </div>

</div>

<?php echo asc_footer(); ?>