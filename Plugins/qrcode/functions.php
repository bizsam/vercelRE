<?php

// Generate QR code image
function qrc_generate_item_image($item_id) {
  require_once osc_base_path() . 'oc-content/plugins/qrcode/lib/qrlib.php';
  
  if($item_id <= 0) {
    return false;
  }
  
  $qr_img_path = osc_uploads_path() . 'qrcode/' . $item_id . '.png';
  
  if($item_id == osc_item_id()) {
    $item = osc_item();
  } else {
    $item = Item::newInstance()->findByPrimaryKey($item_id);
  }
  
  QRcode::png(osc_item_url_from_item($item), $qr_img_path, 'M', qrc_param('size'), QRC_OUTER_FRAME);
  
  osc_run_hook('qrcode_generated_image', $item_id, $qr_img_path);
  
  return true;
}


// Show item QR code
function qrc_show_image($item_id = NULL) {
  if($item_id === NULL) {
    $item_id = osc_item_id();
  }
  
  $img_url = '';
  
  if($item_id > 0) {
    // If does not exists, try to generate
    if(!file_exists(osc_uploads_path() . 'qrcode/' . $item_id . '.png')) {
      qrc_generate_item_image($item_id);
    }

    // show just when exists
    if(file_exists(osc_uploads_path() . 'qrcode/' . $item_id . '.png')) {
      $img_url = '<img src="' . osc_content_url() . 'uploads/qrcode/' . $item_id . '.png' . (qrc_param('asset_version') == 1 ? '?v=' . date('YmdHis') : '') . '" alt="' . osc_esc_html(__('QR code', 'qrcode')) . '" id="qrcode_' . $item_id . '" class="qrcode" />';
    }
  }
  
  echo osc_apply_filter('qrcode_resource_url', $img_url);
}


// Hook
if(trim((string)qrc_param('hook')) != '') {
  osc_add_hook(trim((string)qrc_param('hook')), function() { qrc_show_image(osc_item_id()); }, 3);  
}


// Retro-compatibility
function show_qrcode() {
  qrc_show_image();
}




// Delete QR code images related to item
function qrc_delete_item_images($item_id) {
  $files = glob(osc_uploads_path() . 'qrcode/' . $item_id . '.{jpg,png}', GLOB_BRACE);
  foreach($files as $f) {
    @unlink($f);
  }
}

osc_add_hook('delete_item', 'qrc_delete_item_images');


// Delete all QR code images
function qrc_delete_all_images() {
  $files = glob(osc_uploads_path() . 'qrcode/*.{jpg,png}', GLOB_BRACE);

  if(is_array($files) && count($files) > 0) {
    foreach($files as $f) {
      @unlink($f);
    }
  }
}


// Count all QR code images
function qrc_count_all_images() {
  $files = glob(osc_uploads_path() . 'qrcode/*.{jpg,png}', GLOB_BRACE);
  
  if(is_array($files)) {
    return count($files);
  }
  
  return 0;
}




// CORE FUNCTIONS
function qrc_param($name) {
  return osc_get_preference($name, 'plugin-qrcode');
}


if(!function_exists('mb_param_update')) {
  function mb_param_update( $param_name, $update_param_name, $type = NULL, $plugin_var_name = NULL ) {
  
  $val = '';
  if( $type == 'check') {

    // Checkbox input
    if( Params::getParam( $param_name ) == 'on' ) {
    $val = 1;
    } else {
    if( Params::getParam( $update_param_name ) == 'done' ) {
      $val = 0;
    } else {
      $val = ( osc_get_preference( $param_name, $plugin_var_name ) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
    }
    }
  } else {

    // Other inputs (text, password, ...)
    if( Params::getParam( $update_param_name ) == 'done' && Params::existParam($param_name)) {
    $val = Params::getParam( $param_name );
    } else {
    $val = ( osc_get_preference( $param_name, $plugin_var_name) != '' ) ? osc_get_preference( $param_name, $plugin_var_name ) : '';
    }
  }


  // If save button was pressed, update param
  if( Params::getParam( $update_param_name ) == 'done' ) {

    if(osc_get_preference( $param_name, $plugin_var_name ) == '') {
    osc_set_preference( $param_name, $val, $plugin_var_name, 'STRING');  
    } else {
    $dao_preference = new Preference();
    $dao_preference->update( array( "s_value" => $val ), array( "s_section" => $plugin_var_name, "s_name" => $param_name ));
    osc_reset_preferences();
    unset($dao_preference);
    }
  }

  return $val;
  }
}


// CHECK IF CACHE ENABLED
function qrc_cache_enabled() {
  // Disable in backoffice
  if(defined('OC_ADMIN') && OC_ADMIN) {
  return false;
  }
  
  if(qrc_param('enable_cache') == 1) {
  return true;
  }

  return false;
}


// GET LIFETIME OF CACHE
function qrc_cache_ttl() {
  return OSC_CACHE_TTL;
}

// CHECK IF RUNNING ON DEMO
function qrc_is_demo() {
  if(osc_logged_admin_username() == 'admin') {
  return false;
  } else if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'],'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'],'abprofitrade') !== false)) {
  return true;
  } else {
  return false;
  }
}


if(!function_exists('message_ok')) {
  function message_ok( $text ) {
  $final  = '<div class="flashmessage flashmessage-ok flashmessage-inline">';
  $final .= $text;
  $final .= '</div>';
  echo $final;
  }
}


if(!function_exists('message_error')) {
  function message_error( $text ) {
  $final  = '<div class="flashmessage flashmessage-error flashmessage-inline">';
  $final .= $text;
  $final .= '</div>';
  echo $final;
  }
}


if( !function_exists('osc_is_contact_page') ) {
  function osc_is_contact_page() {
  $location = Rewrite::newInstance()->get_location();
  $section = Rewrite::newInstance()->get_section();
  if( $location == 'contact' ) {
    return true ;
  }

  return false ;
  }
}


// COOKIES WORK
if(!function_exists('mb_set_cookie')) {
  function mb_set_cookie($name, $val) {
  Cookie::newInstance()->set_expires( 86400 * 30 );
  Cookie::newInstance()->push($name, $val);
  Cookie::newInstance()->set();
  }
}


if(!function_exists('mb_get_cookie')) {
  function mb_get_cookie($name) {
  return Cookie::newInstance()->get_value($name);
  }
}

if(!function_exists('mb_drop_cookie')) {
  function mb_drop_cookie($name) {
  Cookie::newInstance()->pop($name);
  }
}



// CATEGORIES WORK
function qrc_cat_tree($list = array()) {
  if(!is_array($list) || empty($list)) {
  $list = Category::newInstance()->listAll();
  }

  $array = array();
  //$root = Category::newInstance()->findRootCategoriesEnabled();

  foreach($list as $c) {
  if($c['fk_i_parent_id'] <= 0) {
    $array[$c['pk_i_id']] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name']);
    $array[$c['pk_i_id']]['sub'] = qrc_cat_sub($list, $c['pk_i_id']);
  }
  }

  return $array;
}

function qrc_cat_sub($list, $parent_id) {
  $array = array();
  //$cats = Category::newInstance()->findSubcategories($id);

  if(count($list) > 0) {
  foreach($list as $c) {
    if($c['fk_i_parent_id'] == $parent_id) {
    $array[$c['pk_i_id']] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name']);
    $array[$c['pk_i_id']]['sub'] = qrc_cat_sub($list, $c['pk_i_id']);
    }
  }
  }
    
  return $array;
}

function qrc_cat_list($selected = array(), $categories = '', $level = 0) {
  if($categories == '' || $level == 0) {
  $categories = qrc_cat_tree($categories);
  }

  if($level == 0) {
  echo '<option value="0" ' . (in_array(0, $selected) ? 'selected="selected"' : '') . '>' . __('All categories', 'qrcode') . '</option>';
  }
  
  foreach($categories as $c) {
  echo '<option value="' . $c['pk_i_id'] . '" ' . (in_array($c['pk_i_id'], $selected) ? 'selected="selected"' : '') . '>' . str_repeat('-', $level) . ($level > 0 ? ' ' : '') . $c['s_name'] . '</option>';

  if(@count($c['sub']) > 0) {
    qrc_cat_list($selected, $c['sub'], $level + 1);
  }
  }
}


function qrc_list_values_ol($values) {
 if(count($values) > 0 && is_array($values)) {
  foreach($values as $v) {
    ?>

    <li class="mb-val" id="val_<?php echo $v['pk_i_id']; ?>">
    <?php qrc_div_value($v); ?>
    
    <ol>
      <?php 
      if(isset($v['values']) && count($v['values']) > 0) { 
        qrc_list_values_ol($v['values']); 
      }
      ?>
    </ol>
    </li>
  <?php
  }
  }
}


// CATEGORIES WORK FLAT
function qrc_cat_tree_flat($list = array()) {
  if(!is_array($list) || empty($list)) {
  $list = Category::newInstance()->listAll();
  }

  $array = array();
  $level = 0;
  //$root = Category::newInstance()->findRootCategoriesEnabled();

  foreach($list as $c) {
  if($c['fk_i_parent_id'] <= 0) {
    $array[] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name'], 'i_level' => $level);
    $array = array_merge($array, qrc_cat_sub_flat($list, $c['pk_i_id'], $level + 1));
  }
  }

  return $array;
}

function qrc_cat_sub_flat($list, $parent_id, $level = 0) {
  $array = array();
  //$cats = Category::newInstance()->findSubcategories($id);

  if(count($list) > 0) {
  foreach($list as $c) {
    if($c['fk_i_parent_id'] == $parent_id) {
    $array[] = array('pk_i_id' => $c['pk_i_id'], 's_name' => $c['s_name'], 'i_level' => $level);
    $array = array_merge($array, qrc_cat_sub_flat($list, $c['pk_i_id'], $level + 1));
    }
  }
  }

  return $array;
}



// GENERATE PAGINATION
function qrc_admin_paginate($file, $page_id, $per_page, $count_all, $class = '', $params = '') {
  $html = '';
  $page_id = (int)$page_id;
  $page_id = ($page_id <= 0 ? 1 : $page_id);
  $base_link = osc_admin_base_url(true) . '?page=plugins&action=renderplugin&file=' . $file . $params;

  if($per_page < $count_all) {
  $html .= '<div id="mb-pagination" class="' . $class . '">';
  $html .= '<div class="mb-pagination-wrap">';
  $html .= '<div>' . __('Page:', 'qrcode') . '</div>';

  $pages = ceil($count_all/$per_page); 
  $page_actual = ($page_id == '' ? 1 : $page_id);

  if($pages > 6) {

    // Too many pages to list them all
    if($page_id == 1) { 
    $ids = array(1,2,3, $pages);

    } else if ($page_id > 1 && $page_id < $pages) {
    $ids = array(1,$page_id-1, $page_id, $page_id+1, $pages);

    } else {
    $ids = array(1, $page_id-2, $page_id-1, $page_id);
    }

    $old = -1;
    $ids = array_unique(array_filter($ids));

    foreach($ids as $i) {
    $url = $base_link . '&pageId=' . $i;
    
    if($old <> -1 && $old <> $i - 1) {
      $html .= '<span>&middot;&middot;&middot;</span>';
    }

    $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="mb-active"' : '') . '>' . $i . '</a>';
    $old = $i;
    }

  } else {

    // List all pages
    for ($i = 1; $i <= $pages; $i++) {
    $url = $base_link . '&pageId=' . $i;
    $html .= '<a href="' . $url . '" ' . ($page_actual == $i ? 'class="mb-active"' : '') . '>' . $i . '</a>';
    }
  }

  $html .= '</div>';
  $html .= '</div>';
  }

  return $html;
}


if(!function_exists('mb_generate_rand_int')) {
  function mb_generate_rand_int($length = 18) {
  $characters = '0123456789';
  $charactersLength = strlen($characters);
  $randomString = '';

  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }

  return $randomString;
  }
}


if(!function_exists('mb_generate_rand_string')) {
  function mb_generate_rand_string($length = 10) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';

  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }

  return $randomString;
  }
}


if(!function_exists('osc_get_current_user_locations_native')) {
  function osc_get_current_user_locations_native() {
  return false;
  }
}

if(!function_exists('osc_location_native_name_selector')) {
  function osc_location_native_name_selector($array, $column = 's_name') {
  return @$array[$column];
  }
}

?>