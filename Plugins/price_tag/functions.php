<?php

// SHOW PRICE TAG OPTIONS ON PUBLISH PAGE
function prt_publish_form($item_id = null) {
  if(prt_param('enabled') != 1) {
    return false;
  }
  
  $active_id = 0;

  if((int)$item_id <= 0) {
    $item_id = (osc_item_id() > 0 ? osc_item_id() : Params::getParam('id'));
  }
  
  if((int)$item_id > 0) {
    $item = Item::newInstance()->findByPrimaryKey($item_id);
    $active_id = @$item['i_prt_tag_id'];
  }
  
  $list = prt_get_tags();

  if($list !== false) {
  ?>
    <div id="prt-box">
      <input type="hidden" name="i_prt_tag_id" value="<?php echo $active_id; ?>"/>
      <strong><?php _e('Select a price tag', 'price_tag'); ?></strong>
      <div class="prt-list">
        <?php foreach($list as $id => $tag_url) { ?>
          <img class="prt-tag <?php if($id == $active_id) { ?>prt-active<?php } ?>" data-prt-id="<?php echo $id; ?>" src="<?php echo $tag_url; ?>"/>
        <?php } ?>
      </div>
    </div>
  <?php
  }
}

osc_add_hook('item_form', function() {
  if(prt_param('hook_publish') == 1) {
    prt_publish_form();
  }
});

osc_add_hook('item_edit', function($cat_id, $item_id) {
  if(prt_param('hook_publish') == 1) {
    prt_publish_form($item_id);
  }
});


// SHOW PRICE TAG ON ITEM
function prt_tag_item($item_id) {
  if(prt_param('enabled') != 1) {
    return false;
  }
  
  $list = prt_get_tags();

  if($list !== false) {
    $item = Item::newInstance()->findByPrimaryKey($item_id);
    
    if(isset($item['i_prt_tag_id']) && $item['i_prt_tag_id'] > 0) {
      $tag_id = $item['i_prt_tag_id'];
      
      if(isset($list[$tag_id])) {
        prt_create_tag_img($tag_id, $list[$tag_id]);
      }
    }
  }
}

osc_add_hook('item_detail', function($item) {
  if(prt_param('hook_item') == 1) {
    prt_tag_item($item['pk_i_id']);
  }
});


// GET PRICE TAG OPTIONS
function prt_get_tags() {
  $list = @json_decode(prt_param('price_tags'), true);
  $output = array();
  
  if(is_array($list) && count($list) > 0) {
    $i = 1;
    foreach($list as $link) {
      if($link != '') {
        $output[$i] = $link;
      }
      
      $i++;
    }
      
    return $output;
  }
  
  return false;
}


// CREATE IMAGE
function prt_create_tag_img($id, $tag) {
  $size = strtolower(prt_param('size'));
  $url = $tag;
?>
  <img class="prt-tag prt-size-<?php echo $size; ?>" src="<?php echo $url; ?>" alt="<?php echo osc_esc_html(__('Price tag', 'price_tag')); ?>"/>
<?php
}


// STYLESHEET
function prt_css_js() {
  if(prt_param('enabled') != 1) {
    return false;
  }
?>
<style>
  .prt-tag {display:inline-block;width:auto;height:auto;max-width:100%;}
  .prt-tag.prt-size-small {height:32px;}
  .prt-tag.prt-size-medium {height:48px;}
  .prt-tag.prt-size-large {height:64px;}
  .prt-tag.prt-size-xlarge {height:128px;}
  .prt-tag.prt-size-xxlarge {height:256px;}
  #prt-box {display:inline-block;width:100%;margin:0 0 20px 0;border:1px solid #ccc;padding:12px 12px 2px 12px;background:#fff;}
  #prt-box strong {width:100%;display:inline-block;margin:0 0 10px 0;}
  .prt-list .prt-tag {border:2px solid transparent;border-radius:4px;padding:5px;background:#fff;margin:0 10px 10px 0;height:64px;cursor:pointer;}
  .prt-list .prt-tag:hover, .prt-list .prt-tag.prt-active {border-color:blue;}
</style>

<script>
  $(document).ready(function() {
    $('body').on('click', '.prt-list .prt-tag', function(e) {
      e.preventDefault();
      $('.prt-list .prt-tag').removeClass('prt-active');
      $(this).addClass('prt-active');
      $('input[name="i_prt_tag_id"]').val($(this).attr('data-prt-id'));
    });
  });
</script>
<?php
}

osc_add_hook('footer', 'prt_css_js', 1);
osc_add_hook('admin_footer', 'prt_css_js', 1);


// UPDATE TC/PP ON ITEM
function prt_item_post($item) {
  if(prt_param('enabled') != 1) {
    return false;
  }
  
  $item_id = @$item['pk_i_id'];

  if($item_id > 0) {
    $tag_id = Params::getParam('i_prt_tag_id');

    ModelPRT::newInstance()->updateItem($item_id, $tag_id);
  }
}

osc_add_hook('posted_item', 'prt_item_post');
osc_add_hook('edited_item', 'prt_item_post');


// CHECK IF RUNNING ON DEMO
function prt_is_demo($ignore_admin = false) {
  if(!$ignore_admin && osc_logged_admin_username() == 'admin') {
    return false;
  } else if(isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'mb-themes') !== false || strpos($_SERVER['HTTP_HOST'], 'abprofitrade') !== false)) {
    return true;
  } else {
    return false;
  }
}


// CORE FUNCTIONS
function prt_param($name) {
  return osc_get_preference($name, 'plugin-price_tag');
}


if(!function_exists('prt_param_update')) {
  function prt_param_update( $param_name, $update_param_name, $type = NULL, $plugin_var_name = NULL ) {
  
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
        $val = ( osc_get_preference( $param_name, $plugin_var_name) != '' ) ? ($type == 'array' ? json_decode(osc_get_preference($param_name, $plugin_var_name), true) : osc_get_preference($param_name, $plugin_var_name)) : ($type == 'array' ? array() : '');
      }
    }


    // If save button was pressed, update param
    if( Params::getParam( $update_param_name ) == 'done' ) {

      if(osc_get_preference( $param_name, $plugin_var_name ) == '') {
        osc_set_preference( $param_name, ($type == 'array' ? json_encode($val, JSON_UNESCAPED_SLASHES) : $val), $plugin_var_name, 'STRING');  
      } else {
        $dao_preference = new Preference();
        $dao_preference->update( array( "s_value" => ($type == 'array' ? json_encode($val, JSON_UNESCAPED_SLASHES) : $val)), array( "s_section" => $plugin_var_name, "s_name" => $param_name ));
        osc_reset_preferences();
        unset($dao_preference);
      }
    }

    return $val;
  }
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


if(!function_exists('message_ok')) {
  function message_ok($text) {
    $final  = '<div class="flashmessage flashmessage-ok flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}


if(!function_exists('message_error')) {
  function message_error($text) {
    $final  = '<div class="flashmessage flashmessage-error flashmessage-inline">';
    $final .= $text;
    $final .= '</div>';
    echo $final;
  }
}

if(!function_exists('osc_content_url')) {
  function osc_content_url() {
    if(!defined('CONTENT_WEB_PATH')) {
      $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
      return $protocol . $_SERVER['HTTP_HOST'] . '/oc-content/';
    } else {
      return CONTENT_WEB_PATH;
    }
  }
}

if(!function_exists('osc_content_path')) {
  function osc_content_path() {
    if(!defined('CONTENT_PATH')) {
      return ABS_PATH . 'oc-content/';
    } else {    
      return CONTENT_PATH;
    }
  }
}

?>