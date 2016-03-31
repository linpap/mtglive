<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_User_Model_User_Collection extends KD_Core_Model_Abstract
{
    protected $_resourceName;
    protected $_resourceCollectionName;
    protected $_idFieldName;
	protected $db;
	
    
    protected function _init($resourceModel,$idFieldName=null) {
        if($idFieldName==null)
        {
            KD::throwException('Empty identifier field name For Class => '.$resourceModel);
        }
        parent::_init($resourceModel, $idFieldName);
    }
    
    public function getIdFieldName()
    {
        if (empty($this->_idFieldName)) {
            KD::throwException('Empty identifier field name');
        }
        return $this->_idFieldName;
    }
    
    protected function _getResource()
    {
        if (empty($this->_resourceName)) {
            KD::throwException('Resource is not set.');
        }

        return KD::getResourceSingleton($this->_resourceName);
    }
    protected function checkUser($code=0, $user_id = 0, $action='insert')
    {
		if($action=='insert')
		{
			$result =$this->load($code,'user_code');
			if($result!=NULL)
			{	
				return $result;
			}
		}
		else
		{
			$adapter = $this->_getResource()->getAdapter();
			$select = $this->_getResource()->getAdapter()->select()->from('user')->where($adapter->quoteInto('user_code = ?', $code))->where($adapter->quoteInto('user_id <> ?',$user_id));
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			if($result!=NULL)
			{	
				return $result;
			}
		}

        return NULL;
    }
	
	protected function checkUserEmail($email=0, $user_id = 0, $action='insert')
    {
		
		if($action=='insert')
		{
			$result =$this->load($email,'user_login');
			if($result!=NULL)
			{	
				return $result;
			}
		}
		else
		{
			$adapter = $this->_getResource()->getAdapter();
			$select = $this->_getResource()->getAdapter()->select()->from('user')->where($adapter->quoteInto('user_email = ?', $email))->where($adapter->quoteInto('user_id <> ?',$user_id));
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			if($result!=NULL)
			{	
				return $result;
			}
		}

        return NULL;
    }	

	public function insert(array $data)
    {
		$db = $this->_getResource();
        return $db->insert($data);
    }
	public function update(array $data, $where='')
    {	
		$db = $this->_getResource();
        return $db->update($data, $where);
	}	
	#SUNIL CODE
   /* protected function getAllowedUserOrg($username)
    {  
        $db = $this->_getResource();
        $select = $db->select()
        ->from(array('u' => 'admin_user'),array('username' => 'username'))
		->join(array('ur' => 'admin_user_role'), 'u.user_id = ur.user_id')
		->join(array('r' => 'admin_rule'), 'ur.role_id = r.role_id')
        ->where('u.username = ?', $username)
        ->setIntegrityCheck(false); // ADD This Line
		
        $query = $db->fetchAll($select)->toArray();
		$resource = $query[0]['resource_id'];
		$role = $query[0]['username'];
		if($query){
		$i=0;
		foreach ($query as $record) {
				$i++;
		}		
		}
		else{
		}
        return $query;
    }
	
    
    public function getAllowedRoles()
    {  
        $db = $this->_getResource();
        $select = $db->select()
        ->from(array('u' => 'admin_role'),array('role_type' => 'role_type'))       
        ->setIntegrityCheck(false);	
        $query = $db->fetchAll($select)->toArray();
        return $query;
    }
    

    public function getUserDepartment($username)
    {  
        $db = $this->_getResource();
        $select = $db->select()
        ->from(array('u' => 'user'),array('user_deptid' => 'user_deptid')) 
		->where('u.user_login = ?', $username)      
        ->setIntegrityCheck(false);
        $result = $db->fetchAll($select)->toArray();
        return $result;
    }
		
    public function getAllowedUserResources($username)
    {  
        $db = $this->_getResource();
        $select = $db->select()
        ->from(array('u' => 'user'),array('user_fname' => 'user_fname','user_role' => 'user_role'))
        ->join(array('a' => 'admin_rule'), 'a.role_type = u.user_role')
		->join(array('r' => 'admin_resource'), 'r.resource_id = a.resource_id',array('resource_id' => 'resource_id','resource_name' => 'resource_name'))
        ->where('u.user_login = ?', $username)
        ->setIntegrityCheck(false); // ADD This Line
        $result = $db->fetchAll($select)->toArray();
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
        return $query;
    }
    	*/
}