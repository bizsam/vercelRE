<?php
class ModelPRT extends DAO {
private static $instance;

public static function newInstance() {
  if(!self::$instance instanceof self) {
    self::$instance = new self ;
  }
  return self::$instance ;
}

function __construct() {
  parent::__construct();
}

public function getTable_item() {
  return DB_TABLE_PREFIX.'t_item';
}

public function getTable_user() {
  return DB_TABLE_PREFIX.'t_user';
}

public function import($file) {
  $path = osc_plugin_resource($file);
  $sql = file_get_contents($path);
  if(!$this->dao->importSQL($sql)){ throw new Exception("Error importSQL::ModelPRT<br>".$file.'<br>'.$path.'<br><br>Please check your database for if there are no plugin tables. <br>If any of those tables exists in your database, drop them!');} 
}

public function install($version = '') {
  if($version == '') {
    //$this->import('price_tag/model/struct.sql');
    @$this->dao->query(sprintf('ALTER TABLE %s ADD i_prt_tag_id INT(3) DEFAULT NULL', $this->getTable_item()));

    osc_set_preference('version', 100, 'plugin-price_tag', 'INTEGER');
  }
}
 
public function uninstall() {
  //$this->dao->query(sprintf('DROP TABLE %s', $this->getTable_google()));
  $this->dao->query(sprintf('ALTER TABLE %s DROP COLUMN i_prt_tag_id', $this->getTable_item()));

  // DELETE ALL PREFERENCES
  $db_prefix = DB_TABLE_PREFIX;
  $query = "DELETE FROM {$db_prefix}t_preference WHERE s_section = 'plugin-price_tag'";
  $this->dao->query($query);
}

// INSERT TC/PP ON ITEM
public function updateItem($item_id, $tag_id) {
  if($item_id > 0) {
    $values = array(
      'i_prt_tag_id' => $tag_id
    );

    $this->dao->update($this->getTable_item(), $values, array('pk_i_id' => $item_id));
  }
}


}
?>