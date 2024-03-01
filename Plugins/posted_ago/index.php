<?php
/*
  Plugin Name: Posted Ago
  Plugin URI: https://osclasspoint.com/osclass-plugins/design-and-appearance/posted-ago-plugin-i193
  Description: Show the published date, modified date, premium date of the Ad in Real Time.
  Version: 1.2.0
  Author: Community
  Author URI: https://osclasspoint.com
  Short Name: posted_ago
  Plugin update URI: posted_ago
  Support URI: https://forums.osclasspoint.com/
  Product Key: n821LlcdX8vUdHSGTD3o
*/

function posted_ago($date, $mode = "posted", $granularity = 2) {
  $date = strtotime($date);
  $difference = time() - $date;
  $periods = array(__('Decade', 'posted_ago') => 315360000,
      __('Year', 'posted_ago') => 31536000,
      __('Month', 'posted_ago') => 2628000,
      __('Week', 'posted_ago') => 604800,
      __('Day', 'posted_ago') => 86400,
      __('Hour', 'posted_ago') => 3600,
      __('Minute', 'posted_ago') => 60,
      __('Second', 'posted_ago') => 1);

  $retval = ""; 
  foreach ($periods as $key => $value) {
    if ($difference >= $value) {
      $time = floor($difference / $value);
      $difference %= $value;
      $retval .= (isset($retval)? ' ' : '');
      $retval .= $time.' ';
      $retval .= (($time > 1) ? $key . __('s', 'posted_ago') : $key);
      $granularity--;
    }
    if ($granularity == '0') {
      break;
    }
  }

  switch (osc_current_user_locale()) {
    case "es_ES":
      if ($mode == "modified") {
        return __('Modified', 'posted_ago') . '&nbsp;' . __('Ago', 'posted_ago') . '&nbsp;' . $retval . '&nbsp;';
      } else {
        return __('Posted', 'posted_ago') . '&nbsp;' . __('Ago', 'posted_ago') . '&nbsp;' . $retval . '&nbsp;';
      }
      break;
    default:
      if ($mode == "modified") {
        return __('Modified', 'posted_ago') . '&nbsp;' . $retval . '&nbsp;' . __('Ago', 'posted_ago');
      } else {
        return __('Posted', 'posted_ago') . '&nbsp;' . $retval . '&nbsp;' . __('Ago', 'posted_ago');
      }
      break;
  }
}

function posted_ago_install() {
  $conn = getConnection();
  $conn->osc_dbExec(sprintf("REPLACE INTO %st_preference VALUES ('osclass', 'dateFormat', 'Y-m-d', 'STRING')", DB_TABLE_PREFIX));
  $conn->osc_dbExec(sprintf("REPLACE INTO %st_preference VALUES ('osclass', 'timeFormat', 'H:i:s', 'STRING')", DB_TABLE_PREFIX));
  $conn->commit();
}

function posted_ago_configure() {
  osc_admin_render_plugin(osc_plugin_path(dirname(__FILE__)) . '/help.php');
}

osc_add_hook(osc_plugin_path(__FILE__) . '_configure', 'posted_ago_configure');
osc_register_plugin(osc_plugin_path(__FILE__), 'posted_ago_install');
osc_add_hook(osc_plugin_path(__FILE__) . '_uninstall', '');