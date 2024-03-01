<?php
class ModelASC extends DAO {
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


public function getTable_account() {
  return DB_TABLE_PREFIX.'t_asc_account';
}

public function getTable_log() {
  return DB_TABLE_PREFIX.'t_asc_log';
}

public function getTable_user() {
  return DB_TABLE_PREFIX.'t_user';
}

public function getTable_user_desc() {
  return DB_TABLE_PREFIX.'t_user_description';
}

public function getTable_user_business_profile() {
  return DB_TABLE_PREFIX.'t_user_business_profile';
}


public function import($file) {
  $path = osc_plugin_resource($file);
  $sql = file_get_contents($path);
  if(!$this->dao->importSQL($sql)){ throw new Exception("Error importSQL::ModelASC<br>".$file.'<br>'.$path.'<br><br>Please check your database for if there are no plugin tables. <br>If any of those tables exists in your database, drop them!');} 
}
 
public function uninstall() {
  $this->dao->query('DROP TABLE '. $this->getTable_account());
  $this->dao->query('DROP TABLE '. $this->getTable_log());
}


// GET LOG BY ID
public function getLog($id) {
  $this->dao->select();
  $this->dao->from($this->getTable_log());
  $this->dao->where('pk_i_id', $id);
  $result = $this->dao->get();
  
  if($result) {
    return $result->row();
  }
  
  return false;
}


// GET ACCOUNT BY ID
public function getAccount($id) {
  $this->dao->select();
  $this->dao->from($this->getTable_account());
  $this->dao->where('pk_i_id', $id);
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->row();
    
    if(isset($data['pk_i_id']) && $data['pk_i_id'] > 0) {
      return $data;
    }
  }
  
  return false;
}

// GET ACCOUNT BY API KEY
public function getAccountByKey($key) {
  $this->dao->select();
  $this->dao->from($this->getTable_account());
  $this->dao->where('s_key', $key);
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->row();
    
    if(isset($data['pk_i_id']) && $data['pk_i_id'] > 0) {
      return $data;
    }
  }
  
  return false;
}


// GET USER BY EMAIL
public function getUserByEmail($email) {
  $this->dao->select();
  $this->dao->from($this->getTable_user());
  $this->dao->where('s_email', $email);
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->row();
    
    if(isset($data['pk_i_id']) && $data['pk_i_id'] > 0) {
      return $data;
    }
  }
  
  return false;
}


// GET USER BY ID
public function getUserData($id) {
  $this->dao->select();
  $this->dao->from($this->getTable_user());
  $this->dao->where('pk_i_id', $id);
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->row();
    $data['user_description'] = $this->getUserDescription($id);
    $data['user_business_profile'] = $this->getUserBusinessProfile($id);
    return $data;
  }
  
  return false;
}



// GET USER DESCRIPTION
public function getUserDescription($id) {
  $this->dao->select();
  $this->dao->from($this->getTable_user_desc());
  $this->dao->where('fk_i_user_id', $id);
  $this->dao->where('trim(s_info) <> ""');
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->result();
    return $data;    
  }
  
  return array();
}



// GET USER BUSINESS PROFILE
public function getUserBusinessProfile($id) {
  if(Plugins::isEnabled('business_profile/index.php')) {
    $this->dao->select();
    $this->dao->from($this->getTable_user_business_profile());
    $this->dao->where('fk_i_user_id', $id);
    $result = $this->dao->get();
    
    if($result) {
      $data = $result->row();
      return $data;    
    }
  }
  
  return array();
}


// GET USERS FROM PARTICULAR USER ID
public function getUsersFromId($last_user_id = 0, $limit = 100, $active_only = true) {
  $this->dao->select();
  $this->dao->from($this->getTable_user());

  if($last_user_id > 0) {
    $this->dao->where('pk_i_id > ' . $last_user_id);
  }
  
  if($active_only) {
    $this->dao->where('b_enabled', 1);
    $this->dao->where('b_active', 1);

  }

  $this->dao->limit($limit);
  $this->dao->orderby('pk_i_id ASC');
  
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->result();

    if(is_array($data) && count($data) > 0) {
      $output = array();
      
      foreach($data as $d) {
        $output[$d['pk_i_id']] = $d;
        $output[$d['pk_i_id']]['user_description'] = $this->getUserDescription($d['pk_i_id']);
        $output[$d['pk_i_id']]['user_business_profile'] = $this->getUserBusinessProfile($d['pk_i_id']);
      }
      
      return $output;
    }
  }
  
  return array();
}



// COUNT USERS FROM PARTICULAR USER ID
public function countUsersFromId($last_user_id = 0, $active_only = true) {
  $this->dao->select('count(*) as i_count');
  $this->dao->from($this->getTable_user());

  if($last_user_id > 0) {
    $this->dao->where('pk_i_id > ' . $last_user_id);
  }
  
  if($active_only) {
    $this->dao->where('b_enabled', 1);
    $this->dao->where('b_active', 1);
  }

  $this->dao->orderby('pk_i_id ASC');
  
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->row();

    if(isset($data['i_count']) && $data['i_count'] > 0) {
      return $data['i_count'];
    }
  }
  
  return 0;
}



// GET LOGS
public function getLogs($limit = 500, $account_id = NULL, $type = NULL, $code = NULL) {
  $this->dao->select();
  $this->dao->from($this->getTable_log());

  if($account_id !== NULL) {
    $this->dao->where('fk_i_account_id', $account_id);
  }
  
  if($type !== NULL) {
    $this->dao->where('s_type', $type);
  }
  
  if($code !== NULL) {
    $this->dao->where('s_code', $code);
  }

  $this->dao->limit($limit);
  $this->dao->orderby('pk_i_id DESC');
  
  $result = $this->dao->get();
  
  if($result) {
    return $result->result();
  }
  
  return array();
}


// GET LOGS
public function getLogsSearch($params) {
  $this->dao->select();
  $this->dao->from($this->getTable_log());

  if(isset($params['content']) && $params['content'] !== '') {
    $this->dao->like('s_content', $params['content']);
  }
  
  if(isset($params['type']) && $params['type'] !== '') {
    $this->dao->like('s_type', $params['type']);
  }
  
  if(isset($params['date']) && $params['date'] !== '') {
    $this->dao->like('dt_date', $params['date']);
  }
  
  if(isset($params['response']) && $params['response'] !== '') {
    $this->dao->like('s_response', $params['response']);
  }

  if(isset($params['status']) && $params['status'] !== '') {
    $this->dao->like('s_code', $params['status']);
  }
  
  if(isset($params['account']) && $params['account'] !== '') {
    $cond = sprintf('(s_name like "%%%s%%" OR s_domain like "%%%s%%" OR s_ip like "%%%s%%" OR s_key like "%%%s%%")', $params['account'], $params['account'], $params['account'], $params['account']);
    $this->dao->where($cond);
  }
  
  if(isset($params['limit']) && $params['limit'] > 0) {
    $this->dao->limit(intval($params['limit']));
  } else {
    $this->dao->limit(200);
  }
  
  $this->dao->orderby('pk_i_id DESC');
  
  $result = $this->dao->get();
  
  if($result) {
    return $result->result();
  }
  
  return array();
}


// GET ACCOUNTS
public function getAccounts($with_logs = false) {
  $this->dao->select();
  $this->dao->from($this->getTable_account());
  $this->dao->orderby('pk_i_id ASC');
  
  $result = $this->dao->get();
  
  if($result) {
    $data = $result->result();
    
    if($with_logs === true) {
      $output = array();
      
      if(is_array($data) && count($data) > 0) {
        foreach($data as $d) {
          $d['logs'] = $this->getLogs(20, $d['pk_i_id']);
          $output[] = $d;
        }
        
        return $output;
      }
    }
    
    return $data;
  }
  
  return array();
}


// INSERT TRANSACTION LOG
public function insertLog($data) {
  $this->dao->insert($this->getTable_log(), $data);
  $log_id = $this->dao->insertedId();
  
  return $log_id;
}


// INSERT USER
public function insertUser($data) {
  $this->dao->insert($this->getTable_user(), $data);
  return $this->dao->insertedId();
}

// UPDATE USER
public function updateUser($id, $data) {
  $this->dao->update($this->getTable_user(), $data, array('pk_i_id' => $id));
}

// UPDATE USER DESCRIPTION
public function updateUserDescription($desc) {
  $this->dao->replace($this->getTable_user_desc(), $desc);
}

// UPDATE USER BUSINESS PROFILE
public function updateUserBusinessProfile($profile) {
  $this->dao->replace($this->getTable_user_business_profile(), $profile);
}


// UPDATE ACCOUNT
public function updateAccount($id, $data) {
  if($id > 0) {
    $this->dao->update($this->getTable_account(), $data, array('pk_i_id' => $id));
    return $id;
  } 
} 


// INSERT ACCOUNT
public function insertAccount($data) {
  $this->dao->insert($this->getTable_account(), $data);
  return $this->dao->insertedId();
}   



// UPDATE ACCOUNT SYNC FROM
public function increaseAccountSyncFrom($id, $count = 1) {
  if($id > 0) {
    return $this->dao->query('UPDATE '.$this->getTable_account() . ' SET i_synced_from=coalesce(i_synced_from, 0)+(' . $count . ') WHERE pk_i_id=' . $id);
  }
}

// UPDATE ACCOUNT SYNC TO
public function increaseAccountSyncTo($id, $count = 1) {
  if($id > 0) {
    return $this->dao->query('UPDATE '.$this->getTable_account() . ' SET i_synced_to=coalesce(i_synced_to, 0)+(' . $count . ') WHERE pk_i_id=' . $id);
  }
}

// UPDATE ACCOUNT LAST TIMESTAMP
public function updateAccountLastSyncDate($id) {
  if($id > 0) {
    $this->dao->update($this->getTable_account(), array('dt_last_sync' => date('Y-m-d H:i:s')), array('pk_i_id' => $id));
  }
}

// UPDATE ACCOUNT STATUS
public function updateAccountStatus($id, $status, $message) {
  if($id > 0) {
    $this->dao->update($this->getTable_account(), array('s_status' => $status, 's_message' => $message, 'dt_last_sync' => date('Y-m-d H:i:s')), array('pk_i_id' => $id));
  }
}

// UPDATE ACCOUNT LAST USER ID
public function updateAccountLastUserId($id, $last_user_id) {
  if($id > 0) {
    $this->dao->update($this->getTable_account(), array('i_last_user_id' => $last_user_id), array('pk_i_id' => $id));
  }
}

public function removeAccount($id) {
  $this->dao->query('DELETE FROM '. $this->getTable_account() . ' WHERE pk_i_id = ' . $id);
}

public function removeLog($id) {
  $this->dao->query('DELETE FROM '. $this->getTable_log() . ' WHERE pk_i_id = ' . $id);
}


// End of DAO Class
}
?>