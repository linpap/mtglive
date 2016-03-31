<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */ 
class KD_Vaktrapport_Model_Vaktrapport extends KD_Vaktrapport_Model_Vaktrapport_Collection
{
	protected $_name = 'vaktrap';
   	protected $_tableField = array('vaktrap_deptID'=>false,'vaktrap_patientID'=>false,'vaktrap_userID'=>false,'vaktrap_teamID'=>false,'vaktrap_observationdesc'=>false,'vaktrap_maaldesc'=>true,'vaktrap_counters'=>false,'vaktrap_vaktrapIds'=>false,'vaktrap_from_date'=>false,'vaktrap_to_date'=>false,'vaktrap_year'=>false,'vaktrap_period'=>false,'vaktrap_status'=>false,'vaktrap_type'=>false,'vaktrap_tilspan_from_date'=>false,'vaktrap_tilspan_to_date'=>false,'vaktrap_maalpan_from_date'=>false,'vaktrap_maalpan_to_date'=>false,'vaktrap_fremgang'=>true,'vaktrap_hendelser'=>true,'vaktrap_aarsak'=>true,'vaktrap_merknader'=>true,'vaktrap_oppsumering'=>true,'vaktrap_genogram'=>false,'vaktrap_previousID'=>false,'vaktrap_lock_date'=>false,'vaktrap_freeze_date'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
	
    public function __construct()
    {
        $this->_init('vaktrapport/vaktrapport','vaktrap_id');
    }
 
    public function load($id, $field=null, $_tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }

        return parent::load($id,$field,$this->_tableField);
    }   
	// Get Vaktrapport With all Necessary Data of User, Department And patient for View
	public function getVaktrap($id=0)
	{
		if(isset($id) && $id > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('v'=>'vaktrap'))
						->join(array('u'=>'user'),'v.vaktrap_userID=u.user_id',array('user_fname','user_mname','user_lname'))
						->join(array('d'=>'department'),'v.vaktrap_deptID=d.dept_id',array('dept_name'))
						->join(array('p'=>'patient'),'v.vaktrap_patientID=p.patient_id',array('patient_fname','patient_mname','patient_lname','patient_legal'))
						->where($adapter->quoteInto('v.vaktrap_id = ?', $id))
						->order(array('vaktrap_id DESC'))
						->setIntegrityCheck(false);
			
			$tableField = $this->_tableField;
			$tableField['user_fname'] = true;
			$tableField['user_mname'] = true;
			$tableField['user_lname'] = true;
			$tableField['patient_fname'] = true;
			$tableField['patient_mname'] = true;
			$tableField['patient_lname'] = true;
			$tableField['patient_legal'] = true;
			
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		else
		{
			return 'Invalid vaktrapport';
		} 
	}
	// Get Current Report Which is currently Active
	public function getCurrentVaktrap($patientId = 0)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('v'=>'vaktrap'),array('vaktrap_id'))
						->where($adapter->quoteInto('vaktrap_status = ?', 'yes'))
						->where($adapter->quoteInto('vaktrap_patientID = ?', $patientId))
						->where($adapter->quoteInto('vaktrap_type = ?', 'enkel'))
						->order(array('vaktrap_id DESC'))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			$result = $stmt->fetch();
			return $result;
		}
		return false;
	}
	// Get One Previous Report Which is CLosed Recently
	public function getPreviousVaktrap($patientId = 0)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('v'=>'vaktrap'),array('vaktrap_id','vaktrap_from_date','vaktrap_to_date'))
						->where($adapter->quoteInto('vaktrap_status = ?', 'no'))
						->where($adapter->quoteInto('vaktrap_patientID = ?', $patientId))
						->where($adapter->quoteInto('vaktrap_type = ?', 'enkel'))
						->order(array('vaktrap_id DESC'))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			$result = $stmt->fetch();
			return $result;
		}
		return false;
	}
    public function loadAll($_tableField=array())
    {
        return parent::loadAll($this->_tableField);
    } 
	// Load All Vaktrapport Depending Upon Pasient ID
	public function loadList($patientId = 0, $type='active', $reportType='enkel', $filter='')
	{	
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('v'=>'vaktrap'),array('vaktrap_id', 'vaktrap_from_date', 'vaktrap_to_date', 'vaktrap_counters','vaktrap_status'))
						->join(array('u'=>'user'),'v.vaktrap_userID=u.user_id',array('user_fname','user_mname','user_lname',))
						->join(array('d'=>'department'),'v.vaktrap_deptID=d.dept_id',array('dept_name'))
						->where($adapter->quoteInto('vaktrap_patientID = ?', $patientId))
						->where($adapter->quoteInto('vaktrap_type = ?', $reportType))
						->order(array('vaktrap_id DESC'))
						->setIntegrityCheck(false);
			
			$switchFl = substr($filter,0,1);
			$maxVak = new DateTime();
			$minVak = clone $maxVak;
			switch($switchFl)
			{
				case 'M':
					$switchCnt = substr($filter,1);
					if($switchCnt>0)
					{
						$minVak = $minVak->sub(new DateInterval('P'.$switchCnt.'M'));
					}
					else
					{
						$minVak = $minVak->sub(new DateInterval('P1M'));
					}
					//$select = $select->where($adapter->quoteInto('vaktrap_from_date <= ?', $maxVak->format('Y-m-d')));
					$select = $select->where($adapter->quoteInto('vaktrap_from_date >= ?', $minVak->format('Y-m-d')));
				break;
				case 'Y':
					$switchCnt = substr($filter,1);
					if($switchCnt>0)
					{
						$minVak = $minVak->sub(new DateInterval('P'.$switchCnt.'Y'));
					}
					else
					{
						$minVak = $minVak->sub(new DateInterval('P1Y'));
					}
					//$select = $select->where($adapter->quoteInto('vaktrap_from_date <= ?', $maxVak->format('Y-m-d')));
					$select = $select->where($adapter->quoteInto('vaktrap_from_date >= ?', $minVak->format('Y-m-d')));
				break;
				case 'A':
				break;
			}
			
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('vaktrap_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('vaktrap_status = ?', 'yes'));
				break;
				case 'all':
				break;
				default:
				break;
			}
			
			$tableField = $this->_tableField;
			$tableField['user_fname'] = true;
			$tableField['user_mname'] = true;
			$tableField['user_lname'] = true;
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return array();
	}
	// Load All Vaktrapport Depending Upon Pasient ID in Paging Manner
	public function loadPageData($patientId, $page=1, $type='all', $sizeList=0, $filter='M1', $reportType = 'enkel')
    {
		if($filter=='A')
		{
			$select = $this->loadList($patientId, $type, $reportType);
		}
		else
		{
			$select = $this->loadList($patientId, $type, $reportType ,$filter);
		}
		if($sizeList<1000)
		{
			$result = $this->loadPage($this->_tableField, $select, $page, $sizeList);
			return $result;
		}
		return $select;
    }
	
	// Load All Vaktrapport Depending Upon Pasient ID in Paging Manner
	/*public function loadPageDataMK($patientId, $page=1, $type='all', $reportType = 'maaned')
    {
		if($reportType=='kvartal')
		{
			$select = $this->loadList($patientId, $type, $reportType);
		}
		else
		{
			$select = $this->loadList($patientId, $type, $reportType);
		}
		
		return $result = $this->loadPage($this->_tableField, $select, $page);
		
    }*/
	
	// Load All Vaktrapport Depending Upon Department and patient
	public function loadAllVaktrap($type='active',$reportType='enkel')
	{


		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
        $latest_report = '(SELECT count(vaktrap_id) from vaktrap where vaktrap_patientid = p.patient_id AND patient_delete_status = \'no\' AND vaktrap_type = \'enkel\')';
		$select = $db->select()
					->from(array('v'=>'vaktrap'),array('vaktrap_id', 'vaktrap_from_date', 'vaktrap_to_date','vaktrap_patientID', 'last_report' => new Zend_Db_Expr($latest_report)))
					->join(array('p'=>'patient'),'v.vaktrap_patientID=p.patient_id',array('patient_fname','patient_mname','patient_lname','patient_birk_no','patient_date_of_joining'))
					->join(array('d'=>'department'),'v.vaktrap_deptID=d.dept_id',array('dept_name'))
					->where($adapter->quoteInto('vaktrap_type = ?', $reportType))
					->where($adapter->quoteInto('patient_status = ?', 'yes'))
					->where($adapter->quoteInto('patient_delete_status = ?', 'no'))
					->order(array('vaktrap_id DESC'))
					->setIntegrityCheck(false);

		switch($type)
		{
			case 'archive': 
				$select = $select->where($adapter->quoteInto('vaktrap_status = ?', 'no'));
			break;
			case 'active': 
				$select = $select->where($adapter->quoteInto('vaktrap_status = ?', 'yes'));
			break;
			default:
				
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
				$queryParts[] = sprintf($adapter->quoteInto('v.vaktrap_deptID = ?', $deptID));
			}
			$select = $select->where(implode(' OR ', $queryParts));
		}
		#CODE FOR DEPT RESTRICTION
		$tableField = $this->_tableField;
		$tableField['patient_fname'] = true;
		$tableField['patient_mname'] = true;
		$tableField['patient_lname'] = true;
		$tableField['patient_birk_no'] = true;
		
		$stmt = $select->query();


		$result = $stmt->fetchAll();

		return $this->decryptData($result,$tableField);
	}
	// Check Vaktrapport Depending Year, Type & Period
	public function checkVaktrapMonthQuartal($patientId=0, $type, $period, $year)
	{
		if($patientId>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('v'=>'vaktrap'))
						->where($adapter->quoteInto('vaktrap_patientID = ?', $patientId))
						->where($adapter->quoteInto('vaktrap_type = ?', $type))
						->where($adapter->quoteInto('vaktrap_period = ?', $period))
						->where($adapter->quoteInto('vaktrap_year = ?', $year))
						->order(array('vaktrap_id DESC'))
						->setIntegrityCheck(false);
			
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		else
		{
			return -1;
		}
	}
	// Check Vaktrapport Depending Year, Type & Period
	public function getVaktrapMonthQuartal($patientId=0, $type, $period=0, $year=0)
	{
		if($patientId>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('v'=>'vaktrap'))
						->join(array('p'=>'patient'),'v.vaktrap_patientID=p.patient_id',array('patient_fname','patient_mname','patient_lname','patient_birk_no','patient_date_of_joining','patient_legal'))
						->join(array('u'=>'user'),'v.vaktrap_userID=u.user_id',array('user_fname','user_mname','user_lname'))
						->where($adapter->quoteInto('v.vaktrap_type = ?', 'enkel'))
						->where($adapter->quoteInto('v.vaktrap_patientID = ?', $patientId))
						->order(array('vaktrap_id ASC'))
						->setIntegrityCheck(false);
			if(isset($period, $year) && $period>0 && $year>0)
			{
				if($type=='maaned')
				{
					$startDateObj = new DateTime($year.'-'.$period.'-01');
					$endDateObj = clone $startDateObj;
					$endDateObj->add(new DateInterval('P1M'));
				}
				elseif($type=='kvartal')
				{
					if($period==1)
					{
						$startDateObj = new DateTime(($year-1).'-12-01'); 
					}
					else
					{
						$startDateObj = new DateTime($year.'-'.(($period-1)*3).'-01'); 
					}
					$endDateObj = clone $startDateObj;
					$endDateObj->add(new DateInterval('P3M'));
				}
				$startDate = $startDateObj->format('Y-m-d');
				$endDateObj->sub(new DateInterval('P1D'));
				$endDate = $endDateObj->format('Y-m-d');
				
				$tableField = $this->_tableField;
				$tableField['patient_fname'] = true;
				$tableField['patient_mname'] = true;
				$tableField['patient_lname'] = true;
				$tableField['user_fname'] = true;
				$tableField['user_mname'] = true;
				$tableField['user_lname'] = true;
				$tableField['patient_birk_no'] = true;
				$tableField['patient_legal'] = true;
				$select =	$select->where($adapter->quoteInto('vaktrap_from_date >= ?', $startDate))
						  			->where($adapter->quoteInto('vaktrap_from_date <= ?', $endDate));
				$stmt = $select->query();
				$result = $stmt->fetchAll();
				return $this->decryptData($result,$tableField);
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
	// Check Vaktrapport Depending Year, Type & Period
	public function getVaktrapMonths($patientId=0, $year=0, $type='maaned')
	{
		if($patientId>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('v'=>'vaktrap'))
						->join(array('p'=>'patient'),'v.vaktrap_patientID=p.patient_id',array('patient_fname','patient_mname','patient_lname','patient_birk_no','patient_date_of_joining','patient_legal'))
						->join(array('u'=>'user'),'v.vaktrap_userID=u.user_id',array('user_fname','user_mname','user_lname'))
						->where($adapter->quoteInto('v.vaktrap_type = ?', 'maaned'))
						->where($adapter->quoteInto('v.vaktrap_year = ?', $year))
						->where($adapter->quoteInto('v.vaktrap_patientID = ?', $patientId))
						->order(array('vaktrap_id ASC'))
						->setIntegrityCheck(false);
				$stmt = $select->query();
				$result = $stmt->fetchAll();
				return $this->decryptData($result,$this->_tableField);
		}
		else
		{
			return false;
		}
	}
	// Check Vaktrapport Depending Year, Type & Period $isDept = ture is used for department data
	public function getVaktrapFromDate($patientId=0, $startDate='', $endDate='',$isDept=false)
	{
		if($patientId>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('v'=>'vaktrap'))
						->where($adapter->quoteInto('vaktrap_type = ?', 'enkel'))
						->order(array('vaktrap_id ASC'))
						->setIntegrityCheck(false);
			// if $isDept = true Get Data for Deparment and Consider  patientId as dept ID
			if($isDept)
			{
				$select = $select->where($adapter->quoteInto('vaktrap_deptID = ?', $patientId));
			}
			else
			{
				$select = $select->where($adapter->quoteInto('vaktrap_patientID = ?', $patientId));
			}
			if(isset($startDate, $endDate) && $startDate!='' && $endDate!='')
			{
				$select =	$select->where($adapter->quoteInto('vaktrap_from_date >= ?', $startDate))
						  			->where($adapter->quoteInto('vaktrap_from_date <= ?', $endDate));
				$stmt = $select->query();
				$result = $stmt->fetchAll();
				return $this->decryptData($result,$this->_tableField);
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
	// Get Vaktrap List from vaktrap_id list comma seperated 
	public function getVaktrapList($vaktrapIds)
	{		
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('v'=>'vaktrap'))
					->where($adapter->quoteInto('vaktrap_id in (?)', $vaktrapIds))
					->setIntegrityCheck(false);
		
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);

	}
	// Load All Vaktrapport Depending Upon Department in Paging Manner
    public function loadAllVaktrapPageData($page=1, $type='')
    {
		$select = $this->loadAllVaktrap($type);


		$result = $this->loadPage($this->_tableField, $select, $page);

		return $result;
    }
	public function archiveList($vaktrapid,$val='no')
	{
		$data = array();
		$data['vaktrap_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('vaktrap_id in (?)', $vaktrapid);
		return parent::update($data,$where);
	}
	public function update(array $dataPost, $key='vaktrap_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);exit();
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'vaktrap_from_date':
				case 'vaktrap_to_date':
				case 'vaktrap_tilspan_from_date':
				case 'vaktrap_tilspan_to_date':
				case 'vaktrap_maalpan_from_date':
				case 'vaktrap_maalpan_to_date':
					$pdoj = '';
					if(isset($dataPost[$column]))
					{
						$pdoj = $dataPost[$column];
						$data[$column] = $format->PrepareDateDB($pdoj);
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
		//print_r($dataPost);exit();
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'vaktrap_from_date':
				case 'vaktrap_to_date':
				case 'vaktrap_tilspan_from_date':
				case 'vaktrap_tilspan_to_date':
				case 'vaktrap_maalpan_from_date':
				case 'vaktrap_maalpan_to_date':
					$pdoj = '';
					if(isset($dataPost[$column]))
					{
						$pdoj = $dataPost[$column];
						$data[$column] = $format->PrepareDateDB($pdoj);
					}
				break;
				case 'date_of_creation':
					$data[$column] = date("Y-m-d H:i:s");
				break;
				case 'vaktrap_userID':
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