<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Maalplanmaal extends KD_Client_Model_Maalplanmaal_Collection
{
    protected $_name = 'maalplan_maal';

	protected $_tableField = array('maalplan_maalplanID'=>false,'maalplan_maalID'=>false,'maalplan_patientID'=>false,'maalplan_deptID'=>false,'maalplan_maaldesc'=>true,'maalplan_maalFromDate'=>false,'maalplan_maalToDate'=>false,'maalplan_statusdesc'=>true,'maalplan_evaluering'=>true,'maalplan_maalResult'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/maalplanmaal','maalplan_maal_id');
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

	public function loadMaalList($patientId = 0,$maalplanID = 0,$type = 'active')
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			if($type=='active')
			{
				$select = $db->select()
						->from(array('m'=>'maal'),array('maal_id','maal_order','maal_desc','maal_from_date','maal_to_date','maal_lockset'))
						->joinLeft(array('mpm'=>'maalplan_maal'),'m.maal_id=mpm.maalplan_maalID AND '.$adapter->quoteInto('maalplan_maalplanID = ?', $maalplanID))
						->where($adapter->quoteInto('maal_patientID = ?', $patientId))
						->where($adapter->quoteInto('maal_status = ?', 'yes'))
						//->where($adapter->quoteInto('maal_lockset = ?', 'no'))
						/*->order(array('maal_order ASC','maalplan_maal_id DESC'))*/
						->order(array('maal_order ASC'))
						->setIntegrityCheck(false);
			}
			elseif($type=='archive')
			{
				$select = $db->select()
						->from(array('m'=>'maal'),array('maal_id','maal_order','maal_desc','maal_from_date','maal_to_date'))
						->joinInner(array('mpm'=>'maalplan_maal'),'m.maal_id=mpm.maalplan_maalID AND '.$adapter->quoteInto('maalplan_maalplanID = ?', $maalplanID))
						->where($adapter->quoteInto('maal_patientID = ?', $patientId))
						/*->order(array('maal_order ASC','maalplan_maal_id DESC'))*/
						->order(array('maal_order ASC'))
						->setIntegrityCheck(false);
			}
			else
			{
				$select = $db->select()
						->from(array('m'=>'maal'),array('maal_id','maal_order','maal_desc','maal_from_date','maal_to_date'))
						->joinLeft(array('mpm'=>'maalplan_maal'),'m.maal_id=mpm.maalplan_maalID AND '.$adapter->quoteInto('maalplan_maalplanID = ?', $maalplanID))
						->where($adapter->quoteInto('maal_patientID = ?', $patientId))
						/*->order(array('maal_order ASC','maalplan_maal_id DESC'))*/
						->order(array('maal_order ASC'))
						->setIntegrityCheck(false);
			}
				
			$tableField = $this->_tableField;
			$tableField['maal_desc'] = true;
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return array();
	}
	public function loadAchiveList($patientId = 0,$maalplanID = 0)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('m'=>'maal'),array('maal_order','maal_desc','maal_from_date','maal_to_date'))
						->join(array('mpm'=>'maalplan_maal'),'m.maal_id=mpm.maalplan_maalID')
						->where($adapter->quoteInto('maal_patientID = ?', $patientId))
						->where($adapter->quoteInto('maal_achived_status = ?', 'yes'))
						->where($adapter->quoteInto('maalplan_maalplanID <> ?', $maalplanID))
						/*->order(array('maal_order ASC','maalplan_maal_id DESC'))*/
						->order(array('maal_order ASC'))
						->setIntegrityCheck(false);
						
			$tableField = $this->_tableField;
			$tableField['maal_desc'] = true;
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return array();
	}
    public function loadPageData($patientId, $page=1)
    {
		$select = $this->loadList($patientId);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
	public function loadListByResult($patientId,$maalRes,$startDate,$endDate, $isDept=false)
    {
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
						->from(array('m'=>'maal'),array('maal_id','maal_order','maal_desc','maal_from_date','maal_to_date'))
						->joinInner(array('mpm'=>'maalplan_maal'),'m.maal_id=mpm.maalplan_maalID  AND m.maal_patientID=mpm.maalplan_patientID',array('maalplan_maal_id','maalplan_maaldesc'))
						->joinInner(array('mp'=>'maalplan'),'mpm.maalplan_maalplanID=mp.maalplan_id AND mp.maalplan_patientID=mpm.maalplan_patientID',array('maalplan_id'))
						->where($adapter->quoteInto('mp.maalplan_from_date >= ?', $startDate))
						->where($adapter->quoteInto('mp.maalplan_from_date <= ?', $endDate))
						/*->order(array('maal_order ASC','maalplan_maal_id DESC'))*/
						->order(array('maal_order ASC'))
						->setIntegrityCheck(false);
		if($isDept)
		{
			$select = $select->where($adapter->quoteInto('mp.maalplan_deptID = ?', $patientId));
		}
		else
		{
			$select = $select->where($adapter->quoteInto('mp.maalplan_patientID = ?', $patientId));
		}
		
		if(isset($maalRes) && $maalRes>=0 && $maalRes<=2)
		{
			$select = $select->where($adapter->quoteInto('mpm.maalplan_maalResult = ?', $maalRes));
		}
		else
		{
			$select = $select->where('mpm.maalplan_maalResult IS NULL');	
		}

		$tableField = $this->_tableField;
		$tableField['maal_desc'] = true;
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$tableField);
    }
	
	public function update(array $dataPost, $key='maalplan_maal_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'maalplan_maalFromDate':
				case 'maalplan_maalToDate':
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
				case 'maalplan_maalFromDate':
				case 'maalplan_maalToDate':
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