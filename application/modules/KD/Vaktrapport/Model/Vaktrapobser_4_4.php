<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Vaktrapport_Model_Vaktrapobser extends KD_Vaktrapport_Model_Vaktrapobser_Collection
{
    protected $_name = 'vaktrap_observation';

	protected $_tableField = array('vaktrap_observationID'=>false,'vaktrap_patientID'=>false,'vaktrap_userID'=>false,'vaktrap_vaktrapID'=>false,'vaktrap_obser_date'=>false,'vaktrap_obser_res'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('vaktrapport/vaktrapobser','vaktrap_obser_id');
    }
 
    public function load($id, $field=null, $_tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }

        return parent::load($id,$field,$this->_tableField);
    }   
	
	public function getObservation($id=0, $attribute='vaktrap_obser_date')
	{
		if(isset($id) && $id > 0)
		{
			$observation = $this->load($id);
			if(isset($observation[$attribute]))
			{
				return $observation[$attribute];
			}
		}
		else
		{
			return 'Invalid Observation';
		} 
	} 
	// Check for Data exist for observation ID and Vaktrapport ID
	public function checkVakObservation($vakObserID=0,$vaktrapId=0)
	{
		if($vakObserID > 0 && $vaktrapId>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('vo'=>'vaktrap_observation'),array('vaktrap_observationID'))
						->where($adapter->quoteInto('vo.vaktrap_observationID = ?', $vakObserID))
						->where($adapter->quoteInto('vo.vaktrap_vaktrapID = ?', $vaktrapId))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			return $stmt->fetch();
		}
		else
		{
			return false;
		} 
	} 

    public function loadAll($_tableField=array())
    {
        return parent::loadAll($this->_tableField);
    } 

	public function loadList($patientId = 0, $vaktrapId = 0, $type='active')
	{
		if($patientId > 0 && $obserId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('vo'=>'vaktrap_observation'))
						->join(array('o'=>'observation'),'o.observation_id=vo.vaktrap_observationID',array('observation_type','observation_relationID'))
						->where($adapter->quoteInto('vo.vaktrap_patientID = ?', $patientId))
						->where($adapter->quoteInto('vo.vaktrap_vaktrapID = ?', $vaktrapId))
						->where($adapter->quoteInto('vo.vaktrap_observationID = ?', $obserId))
						->order(array('o.observation_id DESC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('o.observation_status = ?', 'no'));
				break;
				case 'active':
					$select = $select->where($adapter->quoteInto('o.observation_status = ?', 'yes'));
				break;
				case 'none':
					
				break;
				default:
					// You can get List of all Vaktrapport Observation List
				break;
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->tableField);
		}
		return array();
	}
	//
	public function loadListByVaktrap($patientId = 0, $startDate = '', $endDate = '',  $type='active')
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('vo'=>'vaktrap_observation',array('vo.vaktrap_obser_date')))
						->join(array('o'=>'observation'),'o.observation_id=vo.vaktrap_observationID',array('o.observation_desc','o.observation_id'))
						->where($adapter->quoteInto('vo.vaktrap_patientID = ?', $patientId))
						->where($adapter->quoteInto('vo.vaktrap_obser_date >= ?', $startDate))
						->where($adapter->quoteInto('vo.vaktrap_obser_date <= ?', $endDate))
						->order(array('o.observation_id ASC'))
						->group(array('o.observation_id'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('o.observation_status = ?', 'no'));
				break;
				case 'active':
					$select = $select->where($adapter->quoteInto('o.observation_status = ?', 'yes'));
				break;
				case 'none':
					
				break;
				default:
					// You can get List of all Vaktrapport Observation List
				break;
			}
			
			$tableField = $this->_tableField;
			$tableField['observation_desc']=true;
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return array();
	}
	// Get List of all Vaktrapport Observation result according to Patient, Vaktrapport ID and Observation ID 
	public function loadListByObservation($patientId = 0, $vaktrapId = 0, $obserId=0, $type='')
	{
		if($patientId > 0 && $obserId > 0 && $vaktrapId> 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('vo'=>'vaktrap_observation'))
						->join(array('o'=>'observation'),'o.observation_id=vo.vaktrap_observationID',array('observation_type','observation_relationID'))
						->where($adapter->quoteInto('vo.vaktrap_patientID = ?', $patientId))
						->where($adapter->quoteInto('vo.vaktrap_observationID = ?', $obserId))
						->where($adapter->quoteInto('vo.vaktrap_vaktrapID = ?', $vaktrapId))
						->order(array('o.observation_id ASC'))
						->setIntegrityCheck(false);
			// Here active Means Current Observation and archive means old and nome mean for reporting (For All Vaktrapport User blank)
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('o.observation_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('o.observation_status = ?', 'yes'));
				break;
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	// Get List of all Vaktrapport Observation result according to Patient, Vaktrapport ID and Observation ID 
	public function loadListByObservationForReport($patientId = 0, $startDate = '', $endDate = '', $obserId=0, $group='date', $checked=false)
	{
		if($patientId > 0 && $obserId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select();
			switch($group)
			{
				case 'date':
					$select = $select->from(array('vo'=>'vaktrap_observation'),array('period'=>'DATE_FORMAT(vaktrap_obser_date,\'%d/%m\')','result'=>'vaktrap_obser_res'));
				break;
				case 'week':
					$select = $select->from(array('vo'=>'vaktrap_observation'),array('period'=>'WEEK(vaktrap_obser_date)','result'=>'SUM(vaktrap_obser_res)'));
				break;
				case 'month':
					$select = $select->from(array('vo'=>'vaktrap_observation'),array('period'=>'MONTH(vaktrap_obser_date)','result'=>'SUM(vaktrap_obser_res)'));
				break;
			}
			
			$select = $select->where($adapter->quoteInto('vo.vaktrap_patientID = ?', $patientId))
							->where($adapter->quoteInto('vo.vaktrap_observationID = ?', $obserId))
							->where($adapter->quoteInto('vo.vaktrap_obser_date >= ?', $startDate))
							->where($adapter->quoteInto('vo.vaktrap_obser_date <= ?', $endDate));
							
			if($group=='date')
			{
				$select = $select->setIntegrityCheck(false)->order(array('vaktrap_obser_date ASC'));
			}
			else
			{
				$select = $select->group(array('period'))->order(array('period ASC'))->setIntegrityCheck(false);
			}
			if($checked)
			{
				$select = $select->having($adapter->quoteInto('result > ?', '0'));
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	
	// Get List of all Vaktrapport Observation result according to Patient, Vaktrapport ID and Observation ID 
	public function loadListByObservationAllForReport($patientId = 0, $startDate = '', $endDate = '', $group='date', $having='')
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select();
			switch($group)
			{
				case 'date':
					$select = $select->from(array('vo'=>'vaktrap_observation'),array('period'=>'DATE_FORMAT(vaktrap_obser_date,\'%d/%m\')','result'=>'vaktrap_obser_res'));
				break;
				case 'week':
					$select = $select->from(array('vo'=>'vaktrap_observation'),array('period'=>'WEEK(vaktrap_obser_date)','result'=>'SUM(vaktrap_obser_res)'));
				break;
				case 'month':
					$select = $select->from(array('vo'=>'vaktrap_observation'),array('period'=>'MONTH(vaktrap_obser_date)','result'=>'SUM(vaktrap_obser_res)'));
				break;
			}
			$select = $select->join(array('o'=>'observation'),'o.observation_id=vo.vaktrap_observationID',array('observation_desc'));
			$select = $select->where($adapter->quoteInto('vo.vaktrap_patientID = ?', $patientId))
							->where($adapter->quoteInto('vo.vaktrap_obser_date >= ?', $startDate))
							->where($adapter->quoteInto('vo.vaktrap_obser_date <= ?', $endDate));
			
			$select = $select->group(array('period','vaktrap_observationID'));				
			if($group=='date')
			{
				$select = $select->order(array('vaktrap_obser_date ASC'))->setIntegrityCheck(false);
			}
			else
			{
				$select = $select->order(array('period ASC'))->setIntegrityCheck(false);
			}
			if($having!='')
			{
				$select = $select->having($adapter->quoteInto('period = ?', $having));
			}
			$tableField = $this->_tableField;
			$tableField['observation_desc'] = true;
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return array();
	}
	// Get List of all Vaktrapport Observation result according to Patient, Vaktrapport ID and Observation ID  & Date
	public function loadListByDate($patientId = 0, $vaktrapId = 0, $obserId=0, $date)
	{
		if($patientId > 0 && $obserId > 0 && $vaktrapId> 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('vo'=>'vaktrap_observation'),array('vaktrap_obser_date','vaktrap_obser_res','vaktrap_obser_id'))
						->where($adapter->quoteInto('vo.vaktrap_patientID = ?', $patientId))
						->where($adapter->quoteInto('vo.vaktrap_observationID = ?', $obserId))
						->where($adapter->quoteInto('vo.vaktrap_vaktrapID = ?', $vaktrapId))
						->where($adapter->quoteInto('vo.vaktrap_obser_date = ?', $date))
						->order(array('vo.vaktrap_obser_date ASC'))
						->setIntegrityCheck(false);

			$stmt = $select->query();
			$result = $stmt->fetchAll();
			$result = $result[0];
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
    public function loadPageData($patientId, $page=1, $type='active')
    {
		$select = $this->loadList($patientId, $type);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
	// Load Observation list Inner Joint with observation and vaktrapport observation  whhere $isLeftJoin = true for Left join (Used in Save Vaktrapport)
	public function loadObservation($patientId = 0, $vaktrapId = 0, $type='active', $isLeftJoin = false)
	{
		if($patientId > 0 && $vaktrapId> 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			if($isLeftJoin)
			{
				// Used when archive Report 
				$select = $db->select()
							->from(array('o'=>'observation'))
							->joinLeft(array('vo'=>'vaktrap_observation'),'o.observation_patientID=vo.vaktrap_patientID')
							->where($adapter->quoteInto('vo.vaktrap_patientID = ?', $patientId))
							->where($adapter->quoteInto('vo.vaktrap_vaktrapID = ?', $vaktrapId))
							->group(array("o.observation_id"))
							->order(array('o.observation_id ASC'))
							->setIntegrityCheck(false);
			}
			else
			{
				// Used when Vaktrapport Report is Active
				$select = $db->select()
							->from(array('vo'=>'vaktrap_observation'))
							->join(array('o'=>'observation'),'o.observation_id=vo.vaktrap_observationID',array('observation_id','observation_type','observation_relationID','observation_desc'))
							->where($adapter->quoteInto('vo.vaktrap_patientID = ?', $patientId))
							->where($adapter->quoteInto('vo.vaktrap_vaktrapID = ?', $vaktrapId))
							->group(array("o.observation_id"))
							->order(array('o.observation_id ASC'))
							->setIntegrityCheck(false);
			}
			// Here active Means Current Observation and archive means old and not ser mean for reporting (For archive Vaktrapport User blank)
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('o.observation_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('o.observation_status = ?', 'yes'));
				break;
				default: 
					//
				break;
			}
			$tableField= $this->_tableField;
			$tableField['observation_desc']=true;
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	public function update(array $dataPost, $key='vaktrap_obser_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
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
	
	public function delete($id)
    {
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('vaktrap_obser_id = ?', $id);
		return parent::delete($where);
	}
}