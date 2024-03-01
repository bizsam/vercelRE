<?php
  // Create menu
  $title = __('Configure', 'price_tag');
  prt_menu($title);


  $enabled = prt_param_update('enabled', 'plugin_action', 'check', 'plugin-price_tag');
  $size = prt_param_update('size', 'plugin_action', 'value', 'plugin-price_tag');
  $price_tags = prt_param_update('price_tags', 'plugin_action', 'array', 'plugin-price_tag');
  $hook_item = prt_param_update('hook_item', 'plugin_action', 'check', 'plugin-price_tag');
  $hook_publish = prt_param_update('hook_publish', 'plugin_action', 'check', 'plugin-price_tag');
 
  
  if(Params::getParam('plugin_action') == 'done') {
    osc_add_flash_ok_message(__('Settings were successfully saved.', 'price_tag'), 'admin');
    header('Location:' . osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=price_tag/admin/configure.php');
    exit;
  }
?>


<div class="mb-body">
  <div class="mb-notes">
    <div class="mb-line"><?php _e('You must upload price tag images to be able to use them in plugin.', 'price_tag'); ?></div>
  </div>


  <!-- CONFIGURE SECTION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-cog"></i> <?php _e('Configure', 'price_tag'); ?></div>

    <div class="mb-inside">
      <form name="promo_form" id="promo_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
        <?php if(!prt_is_demo()) { ?>
        <input type="hidden" name="page" value="plugins" />
        <input type="hidden" name="action" value="renderplugin" />
        <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>configure.php" />
        <input type="hidden" name="plugin_action" value="done" />
        <?php } ?>
        
        <div class="mb-row">
          <label for="enabled" class=""><span><?php _e('Enable Price Tags', 'price_tag'); ?></span></label> 
          <input name="enabled" id="enabled" type="checkbox" class="element-slide" <?php echo ($enabled == 1 ? 'checked' : ''); ?> />
          <div class="mb-explain"><?php _e('When enabled, users can select price tags on their items.', 'price_tag'); ?></div>
        </div>

        <div class="mb-row">
          <label for="hook_item" class=""><span><?php _e('Enable Price Tags', 'price_tag'); ?></span></label> 
          <input name="hook_item" id="hook_item" type="checkbox" class="element-slide" <?php echo ($hook_item == 1 ? 'checked' : ''); ?> />
          <div class="mb-explain"><?php _e('When enabled, hook price tag automatically on item page.', 'price_tag'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="hook_publish" class=""><span><?php _e('Enable Price Tags', 'price_tag'); ?></span></label> 
          <input name="hook_publish" id="hook_publish" type="checkbox" class="element-slide" <?php echo ($hook_publish == 1 ? 'checked' : ''); ?> />
          <div class="mb-explain"><?php _e('When enabled, hook price tag automatically on publish page.', 'price_tag'); ?></div>
        </div>
        
        
        <div class="mb-row">
          <label for="size" class=""><span><?php _e('Price Tag Size', 'price_tag'); ?></span></label> 
          <select name="size" id="size">
            <option value="SMALL" <?php echo ($size == 'SMALL' ? 'selected="selected"' : ''); ?>><?php _e('Small', 'price_tag'); ?></option>
            <option value="MEDIUM" <?php echo ($size == 'MEDIUM' ? 'selected="selected"' : ''); ?>><?php _e('Medium', 'price_tag'); ?></option>
            <option value="LARGE" <?php echo ($size == 'LARGE' ? 'selected="selected"' : ''); ?>><?php _e('Large', 'price_tag'); ?></option>
            <option value="XLARGE" <?php echo ($size == 'XLARGE' ? 'selected="selected"' : ''); ?>><?php _e('Extra Large', 'price_tag'); ?></option>
            <option value="XXLARGE" <?php echo ($size == 'XXLARGE' ? 'selected="selected"' : ''); ?>><?php _e('Extra-Extra Large', 'price_tag'); ?></option>
          </select> 
          
          <div class="mb-explain"><?php _e('Size used on item page.', 'price_tag'); ?></div>
        </div>
        
        <div class="mb-row">
          <label for="price_tags" class=""><span><?php _e('Price Tags URLs', 'price_tag'); ?></span></label> 

          <div style="float:left;width:60%;">
            <?php for($i=1;$i<=12;$i++) { ?>
              <div class="mb-line mb-price_tags mb-spec">
                <div class="mb-src">
                  <input type="text" size="100" name="price_tags[<?php echo $i; ?>]" value="<?php echo @$price_tags[$i];  ?>" placeholder="https://domain.com/tag1.png"/>
                </div>
              </div>
            <?php } ?>
          </div>
        </div>
        
        <div class="mb-row">&nbsp;</div>
        
        <?php if(!prt_is_demo()) { ?>
          <div class="mb-foot">
            <button type="submit" class="mb-button"><?php _e('Save', 'price_tag');?></button>
          </div>
        <?php } ?>
      </form>
    </div>
  </div>

  
  <!-- PLUGIN INTEGRATION -->
  <div class="mb-box">
    <div class="mb-head"><i class="fa fa-wrench"></i> <?php _e('Plugin Setup', 'price_tag'); ?></div>

    <div class="mb-inside">
      <div class="mb-row"><?php _e('No theme modification are required to use all functions of plugin. Here is list of available functions for customization.', 'price_tag'); ?></div>
      <div class="mb-row">
        <strong class="mb-line"><?php _e('Add price tag selection to publish page', 'price_tag'); ?></strong>
        <span class="mb-code">&lt;?php if(function_exists('prt_publish_form')) { prt_publish_form(); } ?&gt;</span>
      </div>

      <div class="mb-row">
        <strong class="mb-line"><?php _e('Add price tag to item or search page', 'price_tag'); ?></strong>
        <span class="mb-code">&lt;?php if(function_exists('prt_tag_item')) { prt_tag_item(osc_item_id()); } ?&gt;</span>
      </div>
      
    </div>
  </div>
</div>

<?php echo prt_footer(); ?>