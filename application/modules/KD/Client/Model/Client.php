<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Client extends KD_Client_Model_Client_Collection
{
    protected $_name = 'patient';

	protected $_tableField = array('patient_code'=>false,'patient_person_no'=>true,'patient_birk_no'=>true,'patient_image'=>false,'patient_fname'=>true,'patient_mname'=>true,'patient_lname'=>true,'patient_address1'=>true,'patient_address2'=>true,'patient_city'=>true,'patient_zip'=>true,'patient_state'=>true,'patient_country'=>true,'patient_phone'=>true,'patient_mobile1'=>true,'patient_mobile2'=>true,'patient_email1'=>true,'patient_email2'=>true,'patient_msisdn'=>true,'patient_primary_userID'=>false,'patient_deptID'=>false,'patient_description'=>true,'patient_family'=>true,'patient_network'=>true,'patient_purpose_of_stay'=>true,'patient_actionplan'=>true,'patient_resource'=>true,'patient_location'=>true,'patient_contracting'=>true,'patient_address_list'=>true,'patient_network_check'=>false,'patient_status'=>false,'patient_freeze_status'=>false,'patient_delete_status'=>false,'patient_legal'=>true,'patient_genogram'=>false,'patient_date_of_joining'=>false,'patient_date_of_leaving'=>false,'patient_date_of_vedtak_plan'=>false,'patient_date_of_tiltak_plan'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/client','patient_id');
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
					->from(array('p'=>'patient'),array('patient_id', 'patient_code', 'patient_fname', 'patient_mname', 'patient_lname', 'patient_person_no', 'patient_deptID', 'patient_date_of_joining'))
					->join(array('d'=>'department'),'d.dept_id=p.patient_deptID',array('dept_name'))
					->setIntegrityCheck(false);
		switch($type)
		{
			case 'archive': 
				$select = $select->where($adapter->quoteInto('patient_status = ?', 'no'))->where($adapter->quoteInto('patient_delete_status = ?', 'no'));
			break;
			case 'delete': 
				$select = $select->where($adapter->quoteInto('patient_delete_status = ?', 'yes'));
			break;
			case 'active': 
				$select = $select->where($adapter->quoteInto('patient_status = ?', 'yes'))->where($adapter->quoteInto('patient_delete_status = ?', 'no'));
			break;
			default:
				//You can get List of all Client List
			break;
		}
		#CODE FOR DEPT RESTRICTION
		$session = new Zend_Session_Namespace('Acl');
		
		if(isset($session->userDeptId) && !in_array('all',$session->userDeptId))
		{
			
			if(isset($session->userID) && $session->userID>0 && $session->userRole=='N')
			{
			       
				$select = $select->where($adapter->quoteInto('p.patient_deptID IN (?)', $session->userDeptId));
								 //->where($adapter->quoteInto('p.patient_primary_userID = ?', $session->userID));
								 
								
			}
			else
			{
			        
				$select = $select->where($adapter->quoteInto('p.patient_deptID IN (?)', $session->userDeptId));
			}
		}
		#CODE FOR DEPT RESTRICTION
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}

	public function loadClientBySearch($id=0,$field=null, $_tableField=array())
	{


		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();

		$select = $db->select()
			->from(array('p'=>'patient'),array('patient_id', 'patient_code', 'patient_fname', 'patient_mname', 'patient_lname', 'patient_person_no', 'patient_deptID', 'patient_date_of_joining'))
			->where($adapter->quoteInto('CONCAT(",",p.patient_fname,",") LIKE ?', ','.$field.',') ." OR ". $adapter->quoteInto('CONCAT(",",p.patient_mname,",") LIKE ?', ','.$field.',') ." OR ". $adapter->quoteInto('CONCAT(",",p.patient_lname,",") LIKE ?', ','.$field.','))
			->where($adapter->quoteInto('p.patient_status = ?', 'yes'))
			->where($adapter->quoteInto('p.patient_delete_status = ?', 'no'));
		$stmt = $select->query();
		$result = $stmt->fetchAll();

		if($this->decryptData($result,$this->_tableField)){
			return $this->decryptData($result,$this->_tableField);
		}
		return null;
	}
	public function getClient($id=0, $attribute='name')
	{
		if(isset($id) && $id > 0)
		{
			$format = KD::getModel('core/format');
			$client = $this->load($id);
			switch($attribute)
			{
				case 'name':
					return $format->FormatName($client['patient_fname'],$client['patient_mname'],$client['patient_lname']);
				break;
				case 'patient_date_of_joining':
				case 'patient_date_of_leaving':
				case 'patient_date_of_vedtak_plan':
				case 'patient_date_of_tiltak_plan':
				case 'date_of_creation':
				case 'date_of_modification':
					if(isset($client[$attribute]))
						{
							return $format->FormatDate($client[$attribute]);
						}
				break;
				default:
					if(isset($client[$attribute]))
						{
							return $client[$attribute];
						}
				break;
				
			}
		}
		else
		{
			return 'Invalid Client';
		} 
	}
	
	// Load Client Depending Upon department
	public function loadClientByDept($id='0',$type='active')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('p'=>'patient'),array('patient_id', 'patient_code', 'patient_fname', 'patient_mname', 'patient_lname', 'patient_person_no', 'patient_deptID', 'patient_date_of_joining'))
					->where($adapter->quoteInto('p.patient_deptID = ?', $id));
					//->where($adapter->quoteInto('p.patient_delete_status = ?', 'no'));
		switch($type)
		{
			case 'archive': 
				$select = $select->where($adapter->quoteInto('p.patient_status = ?', 'no'))->where($adapter->quoteInto('patient_delete_status = ?', 'no'));
			break;
			case 'delete': 
				$select = $select->where($adapter->quoteInto('patient_delete_status = ?', 'yes'));
			break;
			case 'active': 
				$select = $select->where($adapter->quoteInto('p.patient_status = ?', 'yes'))->where($adapter->quoteInto('patient_delete_status = ?', 'no'));
			break;
			default:
				//You can get List of all Client List
			break;
		}
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
	// Load Client whose vaktrapport is not active Depending Upon department 
	public function loadClientByDeptHasNoVakt($id='0')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		//this $innerselect find which user did not have active vaktrapport and Used as sub query for following select statement
		$innerselect = $db->select()
					->from(array('v'=>'vaktrap'),array('vaktrap_patientID'))
					->where($adapter->quoteInto('v.vaktrap_status = ?', 'yes'))
					->setIntegrityCheck(false);
		$select = $db->select()
					->from(array('p'=>'patient'),array('patient_id', 'patient_code', 'patient_fname', 'patient_mname', 'patient_lname', 'patient_person_no', 'patient_deptID', 'patient_date_of_joining'))
					->where($adapter->quoteInto('p.patient_deptID = ?', $id))
					->where($adapter->quoteInto('p.patient_status = ?', 'yes'))
					->where($adapter->quoteInto('p.patient_id NOT IN ?', $innerselect))
					->where($adapter->quoteInto('p.patient_delete_status = ?', 'no'))
					->setIntegrityCheck(false);
		
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
    public function loadPageData($page=1, $type='active')
    {
		$select = $this->loadList($type);

		$result = $this->loadPage($this->_tableField, $select, $page);


		
		return $result;
    }
	public function archiveList($updatepatientid,$val='no')
	{
		$data = array();
		$data['patient_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('patient_id in (?)', $updatepatientid);
		return parent::update($data,$where);
	}
	public function deleteList($updatepatientid,$val='yes')
	{
		$data = array();
		$data['patient_delete_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('patient_id in (?)', $updatepatientid);
		return parent::update($data,$where);
	}
    public function checkClient($code=0, $patient_id = 0, $action='insert')
    {
		$encrypt = KD::getModel('core/endecrypt');
		$code = $encrypt->getEnc($code);
        if(strlen($code)>0)
        {
            return parent::checkClient($code, $patient_id, $action);
        }
        else
        {
            KD::throwException('Empty identifier field name');
        }
    }	
	

	public function update(array $dataPost, $key='patient_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			
			switch($column)
			{
				case 'patient_date_of_joining':
				case 'patient_date_of_vedtak_plan':
				case 'patient_date_of_tiltak_plan':
				case 'patient_date_of_leaving':
					$pdoj = '';
					if(isset($dataPost[$column]))
					{
						$pdoj = $dataPost[$column];
						/*$pdoj = substr($pdoj,6).'-'.substr($pdoj,3,2).'-'.substr($pdoj,0,2);
						$date = new DateTime($pdoj);
						$data[$column] = $date->format('Y-m-d H:i:s');*/
						$data[$column] = $format->PrepareDateDB($pdoj);//$date->format('Y-m-d H:i:s');
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
	
	public function insert(array $dataPost)
    {
		$ende = KD::getModel('core/endecrypt');
		$format = KD::getModel('core/format');
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'patient_date_of_joining':
				case 'patient_date_of_vedtak_plan':
				case 'patient_date_of_tiltak_plan':
				case 'patient_date_of_leaving':
					$pdoj = '';
					if(isset($dataPost[$column]))
					{
						$pdoj = $dataPost[$column];
						//$pdoj = substr($pdoj,6).'-'.substr($pdoj,3,2).'-'.substr($pdoj,0,2);
						//$date = new DateTime($pdoj);
						$data[$column] = $format->PrepareDateDB($pdoj);//$data[$column] = $date->format('Y-m-d H:i:s');
					}
				break;
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
        return parent::insert($data);
    }
}