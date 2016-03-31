<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Department_Model_Department extends KD_Department_Model_Department_Collection
{
    protected $_name = 'department';
	protected $_pk = 'dept_id';
	
	protected $_tableField = array('dept_code'=>false,'dept_name'=>false,'dept_address1'=>false,'dept_address2'=>false,'dept_city'=>false,'dept_zip'=>false,'dept_state'=>false,'dept_country'=>false,'dept_phone1'=>false,'dept_phone2'=>false,'dept_phone3'=>false,'dept_mail1'=>false,'dept_mail2'=>false,'dept_expertise'=>false,'dept_capacity'=>false,'dept_ownerid'=>false,'dept_certificate'=>false,'dept_image'=>false,'date_of_creation'=>false,'created_by'=>false,'date_of_modification'=>false,'modified_by'=>false,'dept_status'=>false,'dept_municipality'=>false);
		
    public function __construct()
    {
        $this->_init('department/department','dept_id');
    }
    
    public function load($id, $field=null, $_tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }
		return parent::load($id,$field,$this->_tableField);
    }
		
    public function loadAll($tableField=array())
    {
        return parent::loadAll($this->_tableField);
    }
	public function loadList($type='active',$type='')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()->from('department',array('dept_id', 'dept_code', 'dept_name', 'dept_state', 'dept_city'));
		switch($type)
		{
			case 'archive': 
				$select = $select->where($adapter->quoteInto('dept_status = ?', 'no'));//->where($adapter->quoteInto('dept_delete_status = ?', 'no'));
			break;
			//case 'delete': 
			//	$select = $select->where($adapter->quoteInto('dept_delete_status = ?', 'yes'));
			//break;
			default:
				$select = $select->where($adapter->quoteInto('dept_status = ?', 'yes'));//->where($adapter->quoteInto('dept_delete_status = ?', 'no'));
			break;
		}
		#CODE FOR DEPT RESTRICTION
		if($type!='all')
		{
			$session = new Zend_Session_Namespace('Acl');
			if(isset($session->userDeptId) && !in_array('all',$session->userDeptId))
			{
				$select = $select->where($adapter->quoteInto('dept_id IN (?)', $session->userDeptId));
			}
		}
		#CODE FOR DEPT RESTRICTION
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}

	public function loadDeptBySearch($field=null)
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
			->from(array('d'=>'department'),array('dept_id', 'dept_code', 'dept_name', 'dept_state', 'dept_city'))
			->where($adapter->quoteInto('CONCAT(",",d.dept_name,",") LIKE ?', ','.$field.','))
			->where($adapter->quoteInto('d.dept_status = ?', 'yes'));
		$stmt = $select->query();

		$result = $stmt->fetchAll();

		if($this->decryptData($result,$this->_tableField)){
			return $this->decryptData($result,$this->_tableField);
		}
		return null;

	}
	public function getDepartment($id=0, $attribute='name')
	{
		if(isset($id) && $id > 0)
		{
			$format = KD::getModel('core/format');
			$department = $this->load($id);
			switch($attribute)
			{
				case 'name':
					return $department['dept_name'];
				break;
				default:
					if(isset($department[$attribute]))
						{
							return $department[$attribute];
						}
				break;
				
			}
		}
		else
		{
			return 'Invalid Department';
		} 
	}
	public function archiveList($updatedeptid='0',$val='no')
	{
		$data = array();
		$data['dept_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('dept_id in (?)', $updatedeptid);
		return parent::update($data,$where);
	}
	public function loadPageData($page=1, $type='active')
    {
		$select = $this->loadList($type);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
    public function loadListByCode($code='')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()->from('department',array('dept_code'))
								->where($adapter->quoteInto('dept_code like ?', '%'.$code.'%'))
								->order(array('dept_code DESC'));
		#CODE FOR DEPT RESTRICTION
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
    public function checkDepartment($code=0, $dept_id = 0, $action='insert')
    {
        if(strlen($code)>0)
        {
            return parent::checkDepartment($code, $dept_id, $action);
        }
        else
        {
            KD::throwException('Empty identifier field name');
        }
    }
	public function insert(array $dataPost)
    {
		$ende = KD::getModel('Core/Endecrypt');
		foreach($this->_tableField as $column=>$encrypt)
		{
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
					default:
						if(isset($dataPost[$column]) && $dataPost[$column]!='')
						{
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
		}
        return parent::insert($data);
    }
	public function update(array $dataPost, $key='dept_id',$val='')
    {
	  if($val!='')
	  {
	  	$ende = KD::getModel('Core/Endecrypt');
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
        return parent::update($data,$where);
	  }
	  else
	  {
	  	return 0;
	  }
    }
}