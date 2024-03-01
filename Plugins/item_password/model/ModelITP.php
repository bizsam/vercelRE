<?php
class ModelITP extends DAO {
private static $instance;

public static function newInstance() {
  if( !self::$instance instanceof self ) {
    self::$instance = new self;
  }
  return self::$instance;
}

function __construct() {
  parent::__construct();
}

public function getTable_item() {
  return DB_TABLE_PREFIX.'t_itp_item';
}


public function import($file) {
  $path = osc_plugin_resource($file);
  $sql = file_get_contents($path);

  if(!$this->dao->importSQL($sql) ){
    throw new Exception("Error importSQL::ModelITP<br>" . $file . "<br>" . $this->dao->getErrorLevel() . " - " . $this->dao->getErrorDesc() );
  }
}


public function install($version = '') {
  if($version == '') {
    $this->import('item_password/model/struct.sql');
    osc_set_preference('version', 100, 'plugin-item_password', 'INTEGER');
  }

}


public function uninstall() {
  // DELETE ALL TABLES
  $this->dao->query(sprintf('DROP TABLE %s', $this->getTable_item()));


  // DELETE ALL PREFERENCES
  $db_prefix = DB_TABLE_PREFIX;
  $query = "DELETE FROM {$db_prefix}t_preference WHERE s_section = 'plugin-item_password'";
  $this->dao->query($query);
}



// GET ITEM PASSWORD
public function getPassword($item_id) {
  if($item_id <= 0) {
    return false;
  }
  
  $this->dao->select();
  $this->dao->from($this->getTable_item());
  $this->dao->where('fk_i_item_id', $item_id);
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->row();
    
    if(isset($data['fk_i_item_id'])) {
      return $data;
    }
  }
  
  return false;
}


public function insertPassword($data) {
  $this->dao->insert($this->getTable_item(), $data);
}


// UPDATE PASSWORD
public function updatePassword($id, $data) {
  $this->dao->update($this->getTable_item(), $data, array('fk_i_item_id' => $id));
}


// UPDATE PASSWORD
public function updateFailedAttempt($id, $count) {
  $this->dao->update($this->getTable_item(), array('i_failed_count' => $count, 'dt_date' => date('Y-m-d H:i:s')), array('fk_i_item_id' => $id));
}

}
?>