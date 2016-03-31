<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Maalplan extends KD_Client_Model_Maalplan_Collection
{
    protected $_name = 'maalplan';

	protected $_tableField = array('maalplan_patientID'=>false,'maalplan_userID'=>false,'maalplan_deptID'=>false,'maalplan_location'=>true,'maalplan_actionplan'=>true,'maalplan_resource'=>true,'maalplan_advance'=>true,'maalplan_from_date'=>false,'maalplan_to_date'=>false,'maalplan_tiltak_fromDate'=>false,'maalplan_tiltak_toDate'=>false,'maalplan_maalsty_fromDate'=>false,'maalplan_maalsty_toDate'=>false,'maalplan_status'=>false,'maalplan_lock_date'=>false,'maalplan_lock_by'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/maalplan','maalplan_id');
    }
 
    public function load($id, $field=null, $_tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }

        return parent::load($id,$field,$this->_tableField);
    } 
	
	public function getMaalPlanForNew($patientId)
    {
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('mp'=>'maalplan'))
						//->where($adapter->quoteInto('maalplan_status = ?', 'no'))
						->order(array('maalplan_id DESC'))
						->limit(1)
						->setIntegrityCheck(false);
						
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
	}
	public function getActiveMaalPlan($patientId)
    {
		$maalPlanArray = $this->loadMaalPlanList($patientId,'active');
		$patientArray = KD::getModel('client/client')->load($patientId);
		if(is_array($maalPlanArray) && count($maalPlanArray)>0)
		{
	        $maalPlanInfo = $this->load($maalPlanArray['0']['maalplan_id']);
		}
		else
		{
			$maalPlanPreArray = $this->loadMaalPlanList($patientId,'archive');
			if(is_array($maalPlanPreArray) && count($maalPlanPreArray)>0)
			{
				$maalPlanInfoPre = $this->load($maalPlanPreArray['0']['maalplan_id']);
				$fromDate = $maalPlanInfoPre['maalplan_to_date'];
				$fromDateObj = new DateTime($fromDate);
				$fromDateObj->add(new DateInterval('P1D'));
			}
			else
			{
				$maalPlanInfo = array();
				$maalPlanInfo['maalplan_patientID'] = $patientId;
				$maalPlanInfo['maalplan_id'] = 0;
				
				if(is_array($patientArray) && count($patientArray)>0)
				{
					//$maalPlanInfo['maalplan_from_date'] = $patientArray['patient_date_of_joining'];
					$fromDate = $patientArray['patient_date_of_joining'];
					$fromDateObj = new DateTime($fromDate);
				}
				else
				{
					
				}
					
			}
			
			$fromYear = $fromDateObj->format('Y');
			$fromMonth = $fromDateObj->format('m');
			switch($fromMonth)
			{
				case '12':
				case '1':
				case '2':
					$toDateObj = new DateTime($fromYear.'-12-01');
					$toDateObj->add(new DateInterval('P3M'));
					$toDateObj->sub(new DateInterval('P1D'));
				break;
					
				case '3':
				case '4':
				case '5':
					$toDateObj = new DateTime($fromYear.'-03-01');
					$toDateObj->add(new DateInterval('P3M'));
					$toDateObj->sub(new DateInterval('P1D'));
				break;
					
				case '6':
				case '7':
				case '8':
					$toDateObj = new DateTime($fromYear.'-06-01');
					$toDateObj->add(new DateInterval('P3M'));
					$toDateObj->sub(new DateInterval('P1D'));
				break;
				
				case '9':
				case '10':
				case '11':
					$toDateObj = new DateTime($fromYear.'-09-01');
					$toDateObj->add(new DateInterval('P3M'));
					$toDateObj->sub(new DateInterval('P1D'));
				break;
			}
			$maalPlanInfo['maalplan_from_date'] = $fromDateObj->format('Y-m-d');
			$maalPlanInfo['maalplan_to_date'] = $toDateObj->format('Y-m-d');
			$maalPlanInfo['maalplan_status'] = 'yes';
			$maalPlanInfo['maalplan_patientID'] = $patientId;
			$maalPlanInfo['maalplan_id'] = 0;
			
		}
		
		if(is_array($patientArray) && count($patientArray)>0)
		{
			$maalPlanInfo['patient_location'] = $patientArray['patient_location'];
			$maalPlanInfo['patient_actionplan'] = $patientArray['patient_actionplan'];
			$maalPlanInfo['patient_resource'] = $patientArray['patient_resource'];
		}
		return $maalPlanInfo;
    }  

    public function loadAll($_tableField=array())
    {
        return parent::loadAll($this->_tableField);
    } 

	public function loadMaalPlanList($patientId = 0, $type='active', $filter='')
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('mp'=>'maalplan'))
						->where($adapter->quoteInto('maalplan_patientID = ?', $patientId))
						->order(array('maalplan_id DESC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('maalplan_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('maalplan_status = ?', 'yes'));
				break;
				case 'all':
				break;
				default:
					//You can get List of all Maal List
				break;
			}
			$switchFl = substr($filter,0,1);
			$maxMaalPlan = new DateTime();
			$minMaalPlan = clone $maxMaalPlan;
			switch($switchFl)
			{
				case 'M':
					$switchCnt = substr($filter,1);
					if($switchCnt>0)
					{
						$minMaalPlan = $minMaalPlan->sub(new DateInterval('P'.$switchCnt.'M'));
					}
					else
					{
						$minMaalPlan = $minMaalPlan->sub(new DateInterval('P1M'));
					}
					//$select = $select->where($adapter->quoteInto('vaktrap_from_date <= ?', $maxVak->format('Y-m-d')));
					$select = $select->where($adapter->quoteInto('maalplan_tiltak_fromDate >= ?', $minMaalPlan->format('Y-m-d')));
				break;
				case 'Y':
					$switchCnt = substr($filter,1);
					if($switchCnt>0)
					{
						$minMaalPlan = $minMaalPlan->sub(new DateInterval('P'.$switchCnt.'Y'));
					}
					else
					{
						$minMaalPlan = $minMaalPlan->sub(new DateInterval('P1Y'));
					}
					//$select = $select->where($adapter->quoteInto('vaktrap_from_date <= ?', $maxVak->format('Y-m-d')));
					$select = $select->where($adapter->quoteInto('maalplan_tiltak_fromDate >= ?', $minMaalPlan->format('Y-m-d')));
				break;
				case 'A':
				break;
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
    /*public function loadPageData($patientId, $page=1, $type='active')
    {
		$select = $this->loadList($patientId, $type);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }*/
	// Load All Vaktrapport Depending Upon Pasient ID in Paging Manner
	public function loadPageData($patientId, $page=1, $type='archive', $sizeList=0, $filter='M1')
    {
		if($filter=='A')
		{
			$select = $this->loadMaalPlanList($patientId, $type);
		}
		else
		{
			$select = $this->loadMaalPlanList($patientId, $type, $filter);
		}
		if($sizeList<1000)
		{
			$result = $this->loadPage($this->_tableField, $select, $page, $sizeList);
			return $result;
		}
		return $select;
    }
	public function update(array $dataPost, $key='maalplan_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'maalplan_from_date':
				case 'maalplan_to_date':
				case 'maalplan_tiltak_fromDate':
				case 'maalplan_tiltak_toDate':
				case 'maalplan_maalsty_fromDate':
				case 'maalplan_maalsty_toDate':
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
				case 'maalplan_from_date':
				case 'maalplan_to_date':
				case 'maalplan_tiltak_fromDate':
				case 'maalplan_tiltak_toDate':
				case 'maalplan_maalsty_fromDate':
				case 'maalplan_maalsty_toDate':
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
				case 'maalplan_userID':
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
		//print_r($data);exit();
        return parent::insert($data);
    }
}