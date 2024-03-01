<?php if (!defined('OC_ADMIN') || OC_ADMIN!==true) exit('Access is not allowed.');
  if(Params::getParam('plugin_action')=='done') {
    osc_set_preference('theme', Params::getParam('theme'), 'richedit', 'STRING');
    osc_set_preference('skin', Params::getParam('skin'), 'richedit', 'STRING');
    osc_set_preference('width', Params::getParam('width'), 'richedit', 'STRING');
    osc_set_preference('height', Params::getParam('height'), 'richedit', 'STRING');
    osc_set_preference('skin_variant', Params::getParam('skin_variant'), 'richedit', 'STRING');
    osc_set_preference('buttons1', Params::getParam('buttons1'), 'richedit', 'STRING');
    osc_set_preference('buttons2', Params::getParam('buttons2'), 'richedit', 'STRING');
    osc_set_preference('buttons3', Params::getParam('buttons3'), 'richedit', 'STRING');
    osc_set_preference('plugins', Params::getParam('plugins'), 'richedit', 'STRING');

    if(osc_version()<320) {
      echo '<div style="text-align:center; font-size:22px; background-color:#00bb00;"><p>' . __('Congratulations. The plugin is now configured', 'richedit') . '.</p></div>' ;
      osc_reset_preferences();
    } else {
      ob_get_clean();
      osc_add_flash_ok_message(__('Congratulations. The plugin is now configured', 'richedit'), 'admin');
      osc_admin_render_plugin( osc_plugin_folder(__FILE__) . 'conf.php');
    }
  }
?>
<script type="text/javascript" src="<?php echo osc_base_url().'oc-content/plugins/'.osc_plugin_folder(__FILE__);?>../tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
  tinyMCE.init({
    mode : "none",
    theme : "<?php echo osc_get_preference('theme', 'richedit'); ?>",
    skin: "<?php echo osc_get_preference('skin', 'richedit'); ?>",
    width: "<?php echo osc_get_preference('width', 'richedit'); ?>",
    height: "<?php echo osc_get_preference('height', 'richedit'); ?>",
    skin_variant : "<?php echo osc_get_preference('skin_variant', 'richedit'); ?>",
    theme_advanced_buttons1 : "<?php echo osc_get_preference('buttons1', 'richedit'); ?>",
    theme_advanced_buttons2 : "<?php echo osc_get_preference('buttons2', 'richedit'); ?>",
    theme_advanced_buttons3 : "<?php echo osc_get_preference('buttons3', 'richedit'); ?>",
    theme_advanced_toolbar_align : "left",
    theme_advanced_toolbar_location : "top",
    plugins : "<?php echo osc_get_preference('plugins', 'richedit'); ?>"
  });
  $(document).ready(function () {
    $("textarea[id^=description]").each(function(){
      tinyMCE.execCommand("mceAddControl", true, this.id);
    });
  });
</script>
<div id="settings_form" >
  <div>
    <div style="float: left; width: 100%;">
      <fieldset>
        <h2><?php _e('Rich edit options', 'richedit'); ?></h2>
        <form name="richedit_form" id="richedit_form" action="<?php echo osc_admin_base_url(true); ?>" method="POST" enctype="multipart/form-data" >
          <div style="float: left; width: 100%;">
          <input type="hidden" name="page" value="plugins" />
          <input type="hidden" name="action" value="renderplugin" />
          <input type="hidden" name="file" value="<?php echo osc_plugin_folder(__FILE__); ?>conf.php" />
          <input type="hidden" name="plugin_action" value="done" />
            <label><?php _e('Theme', 'richedit'); ?></label><br/><input type="text" name="theme" id="theme" value="<?php echo osc_get_preference('theme', 'richedit'); ?>" />
            <br/>
            <label><?php _e('Skin', 'richedit'); ?></label><br/><input type="text" name="skin" id="skin" value="<?php echo osc_get_preference('skin', 'richedit'); ?>" />
            <br/>
            <label><?php _e('Skin variant', 'richedit'); ?></label><br/><input type="text" name="skin_variant" id="skin_variant" value="<?php echo osc_get_preference('skin_variant', 'richedit'); ?>" />
            <br/>
            <label><?php _e('Width (need units, px or %)', 'richedit'); ?></label><br/><input type="text" name="width" id="width" value="<?php echo osc_get_preference('width', 'richedit'); ?>" />
            <br/>
            <label><?php _e('Height (need units, px or %)', 'richedit'); ?></label><br/><input type="text" name="height" id="height" value="<?php echo osc_get_preference('height', 'richedit'); ?>" />
            <br/>
            <label><?php _e('Line of buttons #1 (separated them by comma)', 'richedit'); ?></label><br/><input type="text" name="buttons1" id="buttons1" value="<?php echo osc_get_preference('buttons1', 'richedit'); ?>" />
            <br/>
            <label><?php _e('Line of buttons #2 (separated them by comma)', 'richedit'); ?></label><br/><input type="text" name="buttons2" id="buttons2" value="<?php echo osc_get_preference('buttons2', 'richedit'); ?>" />
            <br/>
            <label><?php _e('Line of buttons #3 (separated them by comma)', 'richedit'); ?></label><br/><input type="text" name="buttons3" id="buttons3" value="<?php echo osc_get_preference('buttons3', 'richedit'); ?>" />
            <br/>
            <label><?php _e('Plugins (separated them by comma)', 'richedit'); ?></label><br/><input type="text" name="plugins" id="plugins" value="<?php echo osc_get_preference('plugins', 'richedit'); ?>" />
            <br/>
            <label><?php echo sprintf(__('Plugins are located in %s. Feel free to add more plugins if you need it', 'richedit'), osc_plugins_path().osc_plugin_folder(__FILE__).'tiny_mce/plguins'); ?>.</label>
            <br/>
            <span style="float:left;margin-top:15px;"><button type="submit" class="btn btn-submit" style="float: left;"><?php _e('Update', 'richedit');?></button></span>
          </div>
          <br/>
          <div style="clear:both;"></div>
        </form>
      </fieldset>
      <br/>
      <fieldset>
        <legend><?php _e('Preview of the editor', 'richedit'); ?></legend>
        <div style="float: left; width: 100%;">
          <textarea id="description"><?php _e('This is a preview of how the rich editor will look like', 'richedit'); ?>.</textarea>
        </div>
        <div style="clear:both;"></div>
        <div>
          <?php echo sprintf(__('Learn more about the configuration of TinyMCE at %s', 'richedit'), '<a href="http://tinymce.moxiecode.com/wiki.php/Configuration">TinyMCE Wiki</a>');?>
        </div>
        <div style="clear:both;"></div>
      </fieldset>
    </div>
    <div style="clear: both;"></div>
  </div>
</div>
