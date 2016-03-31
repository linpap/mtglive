<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
 
/*user_id user_code user_loginuser_password user_firstname user_middlename user_lastname user_address1 user_address2 	user_city 	user_zip user_state user_countryuser_phone 	user_mobile user_email user_msisdn user_deptid user_status user_role user_sms_service user_sortby user_expertise user_education user_relatives user_image user_date_created user_created_by user_date_modified 	
user_modified_by 	
user_date_joining */
 
 
class KD_User_Model_User extends KD_User_Model_User_Collection
{
    protected $_name = 'user';
	protected $_pk = 'user_id';

	protected $_tableField = array('user_code'=>false,'user_login'=>false,'user_password'=>false,'password_salt'=>false,'user_fname'=>true,'user_mname'=>true,'user_lname'=>true,'user_position'=>true,'user_address1'=>true,'user_address2'=>true,'user_city'=>true,'user_zip'=>true,'user_state'=>true,'user_country'=>true,'user_phone'=>true,'user_mobile'=>true,'user_email'=>true,'user_msisdn'=>true,'user_deptid'=>false,'user_status'=>false,'user_delete_status'=>false,'user_role'=>false,'user_sms_service'=>true,'user_sortby'=>false,'user_expertise'=>true,'user_education'=>true,'user_relatives'=>true,'user_image'=>false,'date_of_creation'=>false,'created_by'=>false,'date_of_modification'=>false,'modified_by'=>false,'user_date_joining'=>false);
		
    public function __construct()
    {
        $this->_init('user/user','user_id');

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
	public function loadList($type='active')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('u'=>'user'),array('user_id', 'user_code', 'user_fname', 'user_mname', 'user_lname', 'user_phone', 'user_deptid', 'user_mobile','user_email','user_date_joining'))
					->joinLeft(array('d'=>'department'),'d.dept_id=u.user_deptid',array('dept_name'))
					->joinLeft(array('ar'=>'admin_role'),'ar.role_type=u.user_role',array('role_name'))
					->setIntegrityCheck(false);
					
					//->where($adapter->quoteInto('user_status = ?', 'yes'))
					//->where($adapter->quoteInto('user_delete_status = ?', 'no'));
		switch($type)
		{
			case 'archive': 
				$select = $select->where($adapter->quoteInto('user_status = ?', 'no'))->where($adapter->quoteInto('user_delete_status = ?', 'no'));
			break;
			case 'delete': 
				$select = $select->where($adapter->quoteInto('user_delete_status = ?', 'yes'));
			break;
			default:
				$select = $select->where($adapter->quoteInto('user_status = ?', 'yes'))->where($adapter->quoteInto('user_delete_status = ?', 'no'));
			break;
		}
		#CODE FOR DEPT RESTRICTION
		$session = new Zend_Session_Namespace('Acl');
		if(isset($session->userDeptId) && !in_array('all',$session->userDeptId))
		{
			$count = 0;
			$queryParts = array();
			foreach($session->userDeptId as $deptID)
			{
				$check = 'CONCAT(",",u.user_deptid,",")';
				$DEPT = '$$,'.$deptID.',$$';
				$queryParts[] = str_replace('u.user_deptid',$check,str_replace('$$','%',sprintf($adapter->quoteInto('u.user_deptid LIKE ?', $DEPT))));
			}
			$select = $select->where(implode(' OR ', $queryParts));
			
			
			if(in_array($session->userRole,array('L','N')))
			{
				$select = $select->where($adapter->quoteInto('u.user_role IN (?)', array('L','N')));
			}
		}
		#CODE FOR DEPT RESTRICTION
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
	public function loadUserByDept($id='0')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('u'=>'user'),array('user_id', 'user_code', 'user_fname', 'user_mname', 'user_lname', 'user_deptid', 'user_phone', 'user_mobile', 'user_email', 'user_date_joining'))
					->where($adapter->quoteInto('CONCAT(",",u.user_deptid,",") LIKE ?', ','.$id.','))
					->where($adapter->quoteInto('user_status = ?', 'yes'))
					->where($adapter->quoteInto('user_delete_status = ?', 'no'));
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
	
	public function loadListByCode($code='')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('u'=>'user'),array('user_code'))
					->where($adapter->quoteInto('user_code LIKE ?', '%'.$code.'%'))
					->setIntegrityCheck(false)
					->order(array('user_code DESC'));
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
	public function getUser($id=0, $attribute='name')
	{
		if(isset($id) && $id > 0)
		{
			$format = KD::getModel('core/format');
			$user = $this->load($id);
			switch($attribute)
			{
				case 'name':
					return $format->FormatName($user['user_fname'],$user['user_mname'],$user['user_lname']);
				break;
				case 'user_date_joining':
				case 'date_of_creation':
				case 'date_of_modification':
					if(isset($user[$attribute]))
						{
							return $format->FormatDate($user[$attribute]);
						}
				break;
				default:
					if(isset($user[$attribute]))
						{
							return $user[$attribute];
						}
				break;
				
			}
		}
		else
		{
			return 'Invalid User';
		} 
	}
	public function archiveList($updatedepartmentid='0',$val='no')
	{
		$data = array();
		$data['user_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('user_id in (?)', $updatedepartmentid);
		return parent::update($data,$where);
	}
	public function deleteList($updatedepartmentid='0',$val='no')
	{
		$data = array();
		$data['user_delete_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('user_id in (?)', $updatedepartmentid);
		return parent::update($data,$where);
	}
    public function loadPageData($page=1, $type='active')
    {
        //$db = $this->_getResource();
		$select = $this->loadList($type);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
    /*public function getUserDetail($id)
    {
        if((int)$id>0)
        {
            return parent::getUserDetail($id);
        }
        else
        {
            KD::throwException('Empty identifier field name');
        }
    }*/
	
	public function changepassword($userId = 0)
    {
	  if(isset($userId) && $userId>0)
	  {
		$ende = KD::getModel('core/endecrypt');
		$format = KD::getModel('core/format');
		$passwordSalt = KD::getHelper('user')->getPasswordSalt();
		$tmpPassword = KD::getHelper('user')->getPassword();
		$password = sha1($passwordSalt.$tmpPassword);
		$data = array();$data['user_password'] = $password;$data['password_salt'] = $passwordSalt;
		$updateFlag = $this->update($data,'user_id',$userId);
		if($updateFlag)
		{
			//send password using email
			$data['user_password'] = $tmpPassword;
        	return $data;
		}
		else
		{
			return false;
		}
	  }
	  else
	  {
	    return false;
	  }
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
				case 'user_date_joining':
					$udj = '';
					if(isset($dataPost[$column]) && strpos($dataPost[$column],'/'))
					{
						$udj = $dataPost[$column];
						//$udj = substr($udj,6).'-'.substr($udj,3,2).'-'.substr($udj,0,2);
						//$date = new DateTime($udj);
						$data[$column] = $format->PrepareDateDB($udj);//$date->format('Y-m-d H:i:s');
					}
				break;
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
		
        $insertFlag =  parent::insert($data);
		if($insertFlag)
		{
			$dataRes = array();
			$dataRes['user_password'] = $tmpPassword;
			$dataRes['user_login'] = $data['user_login'];
			return $dataRes;
		}
		else
		{
			return false;
		}
    }

	public function update(array $dataPost, $key='user_id',$val='')
    {
	  if($val!='')
	  {
		//echo '<pre>';
		$ende = KD::getModel('Core/Endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'user_date_joining':
					$udj = '';
					if(isset($dataPost[$column]) && strpos($dataPost[$column],'/'))
					{
						$udj = $dataPost[$column];
						$udj = substr($udj,6).'-'.substr($udj,3,2).'-'.substr($udj,0,2);
						$date = new DateTime($udj);
						$data[$column] = $date->format('Y-m-d H:i:s');
					}
				break;
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
	public function checkUserEmail($email=0, $user_id = 0, $action='insert')
    {
        if(strlen($email)>0)
        {
            return parent::checkUserEmail($email, $user_id, $action);
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