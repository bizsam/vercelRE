<h2 class="render-title"><?php _e('Mandrill email service', 'mandrill_osclass'); ?></h2>

<br/>

<?php if( php_sapi_name() == 'cgi-fcgi' || php_sapi_name() == 'cgi' ) { ?>
<div style="margin-left: 60px;margin-bottom: 30px;" class="flashmessage flashmessage-inline warning">
  <p><?php _e("Cannot be sure that Apache Module <b>mod_ssl</b> is loaded.", 'mandrill_osclass'); ?></p>
</div>
<?php } else if( !@apache_mod_loaded('mod_ssl') ) { ?>
<div style="margin-left: 60px;margin-bottom: 30px;" class="flashmessage flashmessage-inline warning">
  <p><?php _e("Apache Module <b>mod_ssl</b> is not loaded", 'mandrill_osclass'); ?></p>
</div>
<?php } ?>


<form action="<?php echo osc_admin_render_plugin_url('mandrill_osclass/admin/settings.php'); ?>" method="post">
  <input type="hidden" name="mandrill_osclass_hidden" value="configuration" />
  <fieldset>
    <div class="form-horizontal">
      <div class="form-row">
        <div class="form-label"><?php _e('SMTP username', 'mandrill_osclass') ?></div>
        <div class="form-controls"><input type="text" class="xlarge" name="mandrill_osclass_username" value="<?php echo osc_esc_html( osc_get_preference('mandrill_osclass_username', 'mandrill_osclass') ); ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-label"><?php _e('SMTP password', 'mandrill_osclass') ?></div>
        <div class="form-controls"><input type="text" class="xlarge" name="mandrill_osclass_password" value="<?php echo osc_esc_html( osc_get_preference('mandrill_osclass_password', 'mandrill_osclass') ); ?>"></div>
      </div>
      <div class="form-row">
        <div class="form-controls">
          <p><?php _e('You can create a Mandrill account here', 'mandrill_osclass'); ?> <a href="http://mandrill.com/">http://mandrill.com/</a></p>
          <p><?php _e('NOTE: Free, up to 12k emails per month.', 'mandrill_osclass'); ?></p>
        </div>
      </div>
      <div class="form-actions">
        <input type="submit" value="<?php echo osc_esc_html(__('Save changes', 'mandrill_osclass')); ?>" class="btn btn-submit">
      </div>
    </div>
  </fieldset>
</form>