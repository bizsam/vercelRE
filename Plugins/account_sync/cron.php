<?php
  define('ABS_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
  require_once ABS_PATH . 'oc-load.php';

  $print = Params::getParam('print');
  $output = asc_stream_users();

  if($print == 'all' || $print == '') {
    if($output != '') {
      echo '<pre>';
      echo __('UPDATE STREAM', 'account_sync') . PHP_EOL;
      echo '--------------------------------------------' . PHP_EOL;
      echo $output;
      echo '</pre>';
    } else {
      //echo '*NO UPDATE DATA*';
    }
    
    $output2 = asc_stream_users_removed();

    if($output2 != '') {
      if($output != '') {
        echo PHP_EOL . PHP_EOL;
      }
      
      echo '<pre>';
      echo __('REMOVAL STREAM', 'account_sync') . PHP_EOL;
      echo '--------------------------------------------' . PHP_EOL;
      echo $output2;
      echo '</pre>';
    } else {
      //echo '*NO UPDATE DATA*';
    }
  }
  
  if($output == '' && $output2 == '' && $print == 'all') {
    echo __('*NO USER RECORDS UPDATED OR REMOVED*', 'account_sync') . PHP_EOL;
  }
?>