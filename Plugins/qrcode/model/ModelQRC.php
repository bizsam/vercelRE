<?php
class ModelQRC extends DAO {
private static $instance;

public static function newInstance() {
  if(!self::$instance instanceof self) {
    self::$instance = new self;
  }
  return self::$instance;
}

function __construct() {
  parent::__construct();
}


public function getTable_item() {
  return DB_TABLE_PREFIX.'t_item';
}

// public function getTable_translation() {
  // return DB_TABLE_PREFIX.'t_qrc_translation';
// }



public function import($file) {
  $path = osc_plugin_resource($file);
  $sql = file_get_contents($path);
  
  $sql = str_replace('/*LOCALE_CODE*/', osc_language(), $sql);

  if(!$this->dao->importSQL($sql) ){
    throw new Exception("Error importSQL::ModelQRD<br>" . $file . "<br>" . $this->dao->getErrorLevel() . " - " . $this->dao->getErrorDesc() );
  }
}


public function install() {
  $this->import('qrcode/model/struct.sql');
}


public function uninstall() {
  // DELETE ALL TABLES
  // $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_translation()));
  
  // DELETE ALL PREFERENCES
  $db_prefix = DB_TABLE_PREFIX;
  $query = "DELETE FROM {$db_prefix}t_preference WHERE s_section = 'plugin-qrcode'";
  $this->dao->query($query);
}


// EXECUTE QUERIES ON VERSION UPDATE
public function versionUpdate($ignore_error = false) {
  $version = (int)qrc_param('version');     // v100 is initial
  $version = ($version >= 100 ? $version : 0);
  $plugin = 'qrcode';
  
  // Version not yet available - it's installation process now
  if($version == 0) {
    return true;
  }
  
  // $queries = array(
    // array('version' => 101, 'query' => sprintf("CREATE TABLE %st_qrc_values (pk_i_id INT NOT NULL AUTO_INCREMENT, fk_c_locale_code CHAR(5) NOT NULL, s_type VARCHAR(20), s_name VARCHAR(100) NOT NULL, PRIMARY KEY(pk_i_id, fk_c_locale_code)) ENGINE=InnoDB DEFAULT CHARACTER SET 'UTF8' COLLATE 'UTF8_GENERAL_CI';", DB_TABLE_PREFIX)),
    // array('version' => 102, 'query' => sprintf("ALTER TABLE %st_user_qrcode ADD COLUMN s_gallery VARCHAR(2000);", DB_TABLE_PREFIX)),
  // );
  
  $queries = array();
  
  if(is_array($queries) && count($queries) > 0) {
    foreach($queries as $query) {
      if($version < $query['version'] && $query['version'] <= QRC_VERSION_ID) {
        $result = $this->dao->query($query['query']);
        
        if($result === false && $ignore_error !== true) {
          $message  = sprintf(__('Update of plugin "%s" failed on DB version "%s". Please enable %s to see error details. Failed query is listed below.', 'qrcode'), __('Business Profile Plugin', 'qrcode'), $query['version'], '<a href="https://docs.osclasspoint.com/debug-mode" target="_blank">' . __('DB debug mode', 'qrcode') . '</a>');
          $message .= '<pre style="font-size:11px;">' . $query['query'] . '</pre>';
          $message .= '<a href="' . osc_admin_base_url(true) . '?page=plugins&forceupdateplugin=' . $plugin . '">' . __('Ignore error and force plugin update', 'qrcode') . '</a>. ';
          $message .= __('Never force update plugin until you are sure that your database structure match to model/struct.sql file! It may lead to unexpected plugin functionality. Try to reinstall plugin.', 'qrcode');

          osc_add_flash_error_message($message, 'admin');
          return false;
        }
      }
    }
  }
  
  return true;
}



// NOTHING HERE



}
?>