<?php
  // Create menu
  $title = __('Configure', 'qrcode');
  qrc_menu($title);

  // GET & UPDATE PARAMETERS
  // $variable = mb_param_update( 'param_name', 'form_name', 'input_type', 'plugin_var_name' );
  // input_type: check or value

  $size = mb_param_update('size', 'plugin_action', 'value', 'plugin-qrcode');
  $hook = mb_param_update('hook', 'plugin_action', 'value', 'plugin-qrcode');
  $asset_version = mb_param_update('asset_version', 'plugin_action', 'check', 'plugin-qrcode');


  if(Params::getParam('plugin_action') == 'done') {
    osc_add_flash_ok_message(__('Settings were successfully saved.', 'qrcode'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=qrcode/admin/configure.php');
    exit;
  }
  
  if(Params::getParam('what') == 'cleancache') {
    qrc_delete_all_images();
    osc_add_flash_ok_message(__('QR code images removed successfully.', 'qrcode'), 'admin');

    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=qrcode/admin/configure.php');
    exit;
  }
  
  
  $count = qrc_count_all_images();

?>

<div class="mb-body">
  <form name="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
    <?php if(!qrc_is_demo()) { ?>
      <input type="hidden" name="page" value="plugins" />
      <input type="hidden" name="action" value="renderplugin" />
      <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
      <input type="hidden" name="plugin_action" value="done" />
    <?php } ?>


    <!-- CONFIGURE SECTION -->
    <div class="mb-box">
      <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Configure', 'qrcode'); ?></div>

      <div class="mb-inside">

        <div class="mb-row">
          <label for="size" class=""><span><?php _e('QR Code Image Size', 'qrcode'); ?></span></label> 
          <select name="size" id="size">
            <?php for($i=1;$i<=10;$i++) { ?>
              <option value="<?php echo $i; ?>" <?php if($i == $size) { ?>selected="selected"<?php } ?>>
                <?php echo $i; ?>
                <?php if($i == 1) { echo ' - ' . __('Smallest', 'qrcode'); } ?>
                <?php if($i == 10) { echo ' - ' . __('Largest', 'qrcode'); } ?>
              </option>
            <?php } ?>
          </select>
          
          <div class="mb-explain">
            <div class="mb-line"><?php _e('Size of generated QR code image. Do not use too large image until required. Default: 3', 'qrcode'); ?></div>
            <div class="mb-line"><?php _e('Size change does not impact existing QR code images. To force regeneration, clear all existing QR code images.', 'qrcode'); ?></div>
          </div>
        </div>


        <div class="mb-row">
          <label for="asset_version" class=""><span><?php _e('Asset Versioning', 'qrcode'); ?></span></label> 
          <input name="asset_version" type="checkbox" class="element-slide" <?php echo ($asset_version == 1 ? 'checked' : ''); ?> />

          <div class="mb-explain"><?php _e('When enabled, ?v=YYYYMMDDHHIISS is added to image URL that will block image caching by browser & server. Used mainly for testing & development.', 'qrcode'); ?></div>
        </div>
        

        <div class="mb-row">
          <label for="hook" class=""><span><?php _e('Item Page Hook', 'qrcode'); ?></span></label> 
          <input name="hook" type="text" size="50" value="<?php echo osc_esc_html($hook); ?>" />

          <div class="mb-explain"><?php _e('Use hook on listing page or listing loop to show image. Example: item_detail. Keep blank to disable.', 'qrcode'); ?></div>
        </div>
        
        <hr/>
       
        
        <div class="mb-row">
          <label for="clean_cache"><span>&nbsp;</span></label> 
          <a href="<?php echo osc_admin_base_url(true); ?>?page=plugins&action=renderplugin&file=qrcode/admin/configure.php&what=cleancache" class="mb-button-blue mb-add" onclick="return confirm('Are you sure you want to remove all QR code images? Action cannot be undone');">
            <i class="fa fa-trash"></i> <?php _e('Clean all QR code images', 'qrcode'); ?>
          </a>

          <div class="mb-explain">
            <div class="mb-line"><?php echo sprintf(__('You have %d QR code images.', 'qrcode'), $count); ?></div>
          </div>
        </div>
        

        
        
        <div class="mb-row">&nbsp;</div>

        <div class="mb-foot">
          <?php if(qrc_is_demo()) { ?>
            <a class="mb-button mb-has-tooltip disabled" onclick="return false;" style="cursor:not-allowed;opacity:0.5;" title="<?php echo osc_esc_html(__('This is demo site', 'qrcode')); ?>"><?php _e('Save', 'qrcode');?></a>
          <?php } else { ?>
            <button type="submit" class="mb-button"><?php _e('Save', 'qrcode');?></button>
          <?php } ?>
        </div>
      </div>
    </div>
  </form>


</div>


<?php echo qrc_footer(); ?>