<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Info_Model_Info extends KD_Info_Model_Info_Collection
{
    protected $_name = 'info';
	protected $_pk = 'info_id';

	protected $_tableField = array('info_type'=>false,'info_title'=>true,'info_desc'=>true,'info_deptIDs'=>false,'date_of_creation'=>false,'created_by'=>false,'date_of_modification'=>false,'modified_by'=>false,'user_date_joining'=>false);
		
    public function __construct()
    {
        $this->_init('info/info','info_id');

    }
    
    public function load($id, $field=null, $_tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }

        return parent::load($id,$field,$this->_tableField);
    }
    public function loadAll($_tableField=array())
    {
        return parent::loadAll($this->_tableField);
    }
	public function loadList($type='cur')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('i'=>'info'))
					->where($adapter->quoteInto('info_status = ?', 'yes'))
					->order(array('info_id DESC'))
					->setIntegrityCheck(false);

		switch($type)
		{
			case 'main': 
				$select = $select->where($adapter->quoteInto('info_type = ?', 'main'));
			break;
			case 'cur': 
				$select = $select->where($adapter->quoteInto('info_type = ?', 'cur'));
			break;
		}
		
		#CODE FOR DEPT RESTRICTION
		$session = new Zend_Session_Namespace('Acl');
		if(isset($session->userDeptId) && $session->userDeptId!='all')
		{
			$select = $select->where($adapter->quoteInto('CONCAT(",",i.info_deptIDs,",") LIKE ?', '%,'.$session->userDeptId.',%'));
		}
		#CODE FOR DEPT RESTRICTION
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
	
    public function loadPageData($page=1, $type='cur')
    {
        //$db = $this->_getResource();
		$select = $this->loadList($type);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
	public function archiveList($updatedepartmentid='0',$val='no')
	{
		$data = array();
		$data['info_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('info_id IN (?)', $updatedepartmentid);
		return parent::update($data,$where);
	}
	public function insert(array $dataPost)
    {
		$ende = KD::getModel('core/endecrypt');
		$format = KD::getModel('core/format');
		$passwordSalt = KD::getHelper('user')->getPasswordSalt();
		$tmpPassword = KD::getHelper('user')->getPassword();
		$password = sha1($passwordSalt.$tmpPassword);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'date_of_creation':
					$data[$column] = date("Y-m-d H:i:s");
				break;
				case 'created_by':
					$data[$column] = $_SESSION['Acl']['userID'];
				break;
				case 'user_password':
					$data[$column] = $password;
				break;
				case 'password_salt':
					$data[$column] = $passwordSalt;
				break;
				default:
					if(isset($dataPost[$column]) && $dataPost[$column]!='')
					{
						if($column=='user_email') $data['user_login'] = $dataPost[$column];
						if($encrypt)
						{
							$data[$column] = $ende->getEnc($dataPost[$column]);
						}
						else
						{
							$data[$column] = $dataPost[$column];
						}
					}
				break;
				
			}
			
		}
		//send password using email
        return parent::insert($data);
    }

	public function update(array $dataPost, $key='user_id',$val='')
    {
	  if($val!='')
	  {
	  	//print_r($dataPost);exit();
		//echo '<pre>';
		$ende = KD::getModel('Core/Endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			
			switch($column)
			{
				case 'date_of_modification':
					$data[$column] = date("Y-m-d H:i:s");
				break;
				case 'modified_by':
					$data[$column] = $_SESSION['Acl']['userID'];
				break;
				default:
					if(isset($dataPost[$column]) && $dataPost[$column]!='')
					{
						if($column=='user_email') $data['user_login'] = $dataPost[$column];
						if($encrypt)
						{
							$data[$column] = $ende->getEnc($dataPost[$column]);
						}
						else
						{
							$data[$column] = $dataPost[$column];
						}
					}
				break;
				
			}
		}
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto(' '.$key .' = ? ', $val);
		//print_r($data);exit();
        return parent::update($data,$where);
	  }
	  else
	  {
	  	return 0;
	  }
    }	
    public function checkUser($code=0, $user_id = 0, $action='insert')
    {
        if(strlen($code)>0)
        {
            return parent::checkUser($code, $user_id, $action);
        }
        else
        {
            KD::throwException('Empty identifier field name');
        }
    }
	# SUNIL CODE
    public function getAllowedUser($username)
    {  
        $db = $this->_getResource();
        $select = $db->select()
					->from(array('u' => 'user'),array('user_fname','user_role'))
					->join(array('a' => 'admin_rule'), 'a.role_type = u.user_role')
					->where('u.user_login = ?', $username)
					->setIntegrityCheck(false); // ADD This Line
		
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
    }
	
	public function getUserRoleType($username)
    {  
        $db = $this->_getResource();
        $select = $db->select()
					->from(array('u' => 'user'),array('user_login','user_role')) 
					->where('u.user_login = ?', $username)      
					->setIntegrityCheck(false);
					$query = $db->fetchAll($select)->toArray();
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
    }
	
    public function getAllResources()
    {  
        $db = $this->_getResource();
		$select = $db->select()
				->from(array('a' => 'admin_resource'),array('resource_id'))	
				->setIntegrityCheck(false); // ADD This Line
		$query = $db->fetchAll($select)->toArray();
        $stmt = $select->query();
		$result = $stmt->fetchAll();
		return $result;
    }	
	
	public function getAllowedUserResources($username)
    {  
        $db = $this->_getResource();
        $select = $db->select()
					->from(array('u' => 'user'),array('user_fname','user_role'))
					->join(array('a' => 'admin_rule'), 'a.role_type = u.user_role')
					->join(array('r' => 'admin_resource'), 'r.resource_id = a.resource_id',array('resource_id','resource_name'))
					->where('u.user_login = ?', $username)
					->setIntegrityCheck(false); // ADD This Line
        $query = $db->fetchAll($select)->toArray();
        $stmt = $select->query();
		$result = $stmt->fetchAll();
		return $result;
    }	
	public function getAllowedUserResourcesName($resourcename)
    {  
        $db = $this->_getResource();
        $select = $db->select()
					->from(array('a' => 'admin_resource'),array('resource_id' => 'resource_id','resource_name' => 'resource_name'))	
					->where('a.resource_name = ?', $resourcename)
					->setIntegrityCheck(false); // ADD This Line
		$query = $db->fetchAll($select)->toArray();
        $stmt = $select->query();
		$result = $stmt->fetchAll();
		return $result;
    }
}