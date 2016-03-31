<?php 

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Tiltakinst extends KD_Client_Model_Tiltakinst_Collection
{
    protected $_name = 'tiltak_institute';

	protected $_tableField = array('tilins_maalID'=>false,'tilins_patientID'=>false,'tilins_userID'=>false,'tilins_desc'=>true,'tilins_explanation'=>true,'tilins_owner'=>false,'tilins_status'=>false,'tilins_result'=>false,'tilins_vaktrapportID'=>false,'tilins_previous_vaktrapportID'=>false,'tilins_futureID'=>false,'date_of_creation'=>false,'from_date'=>false,'to_date'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/tiltakinst','tilins_id');
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
	// For getting Vaktrapport Institute Tiltak Order where $type = active for Acive, Archive for archive and none for all (used at vaktraparchiveAction), $showNext = true  for showing Next vaktrapport Vaktrapport Institute Tiltak List( used At vaktraparchiveAction)
	public function loadList($patientId = 0, $vaktrapId = 0, $type='active', $showNext=false)
	{
		if($patientId > 0)
		{
			$date=date("Y-m-d H:i:s");



			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('ti'=>'tiltak_institute'),array('tilins_id', 'tilins_maalID', 'tilins_patientID', 'tilins_userID', 'tilins_desc', 'tilins_explanation', 'tilins_owner', 'tilins_result'))
						->join(array('m'=>'maal'),'ti.tilins_maalID=m.maal_id and m.maal_patientID=ti.tilins_patientID',array('maal_id','maal_desc'))
						->join(array('p'=>'patient'),'ti.tilins_patientID=p.patient_id',array('patient_code'))
						->where($adapter->quoteInto('ti.tilins_patientID = ?', $patientId))
						->where($adapter->quoteInto('ti.from_date <= ?', $date)." AND ".$adapter->quoteInto('ti.to_date >= ?', $date). " OR (ti.from_date is NULL AND ti.to_date is NULL)")
//						->Orwhere($adapter->quoteInto('ti.from_date = ?',null ))
//						->Orwhere($adapter->quoteInto('ti.to_date = ?', null))
						->order(array('m.maal_order ASC','m.maal_id ASC'))
						->setIntegrityCheck(false);
			if($vaktrapId > 0)
			{	
				if($showNext)
				{
					$select = $select->where($adapter->quoteInto('ti.tilins_previous_vaktrapportID = ?', $vaktrapId));
				}
				else
				{
					$select = $select->where($adapter->quoteInto('ti.tilins_vaktrapportID = ?', $vaktrapId));
				}
			}
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('ti.tilins_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('ti.tilins_status = ?', 'yes'))->where($adapter->quoteInto('m.maal_status = ?', 'yes'));
				break;
				default:
					// You can get List of all Institute Tiltak List
				break;
			}
			$tableField = $this->_tableField;
			$tableField['maal_desc'] = true;

			
			$stmt = $select->query();
			$result = $stmt->fetchAll();


			return $this->decryptData($result,$tableField);
		}
		return array();
	}
	// For getting Vaktrapport Institute Tiltak Order By Maal where $forActive = false for all list without any status restriction (Used at Vaktrarp Archive "vaktraparchiveAction") and $inArray = true if we are giving Vaktrapport Ids to search
	public function loadListByMaal($patientId = 0, $maalId = 0, $vaktrapId = 0, $forActive = true, $inArray = false)
	{
		
		if($patientId > 0)
		{
			$date=date("Y-m-d H:i:s");
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('ti'=>'tiltak_institute'),array('tilins_id', 'tilins_maalID', 'tilins_patientID', 'tilins_userID', 'tilins_desc', 'tilins_explanation', 'tilins_owner', 'tilins_result'))
						->joinLeft(array('m'=>'maal'),'ti.tilins_maalID=m.maal_id and m.maal_patientID=ti.tilins_patientID',array('maal_id','maal_desc'))
						->where($adapter->quoteInto('ti.tilins_patientID = ?', $patientId))
						->where($adapter->quoteInto('ti.tilins_maalID = ?', $maalId))
				        ->where($adapter->quoteInto('ti.from_date <= ?', $date)." AND ".$adapter->quoteInto('ti.to_date >= ?', $date). " OR (ti.from_date is NULL AND ti.to_date is NULL)")

				//->where($adapter->quoteInto('ti.tilins_vaktrapportID = ?', $vaktrapId))
						->order(array('m.maal_order ASC','m.maal_id ASC'))
						->setIntegrityCheck(false);
			if($inArray)
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_vaktrapportID IN (?)', $vaktrapId));
			}
			else
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_vaktrapportID = ?', $vaktrapId));
			}
			if($forActive)
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_status = ?', 'yes'));
			}
			
			$tableField = $this->_tableField;
			$tableField['maal_desc'] = true;
			
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return array();
	}
    public function loadPageData($patientId, $page=1, $type='active')
    {
		$select = $this->loadList($patientId, $type);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
	public function archiveList($updateTilInsId,$val='no')
	{
		$data = array();
		$data['tilins_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('tilins_id in (?)', $updateTilInsId);
		return parent::update($data,$where);
	}
	// Get Governemt Tiltak List from tilgov_id list comma seperated For directly insert in to vaktrap government tiltak
	public function getInsTiltListToInsert($insTilIds)
	{		
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('ti'=>'tiltak_institute'),array('tilins_maalID','tilins_patientID','tilins_userID','tilins_desc','tilins_owner','tilins_previous_vaktrapportID'=>'tilins_vaktrapportID'))
					->where($adapter->quoteInto('tilins_id in (?)', $insTilIds))
					->setIntegrityCheck(false);

		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);

	}
	
	// For getting Vaktrapport Institute Tiltak count where $forActive = false for all list without any status restriction (Used at Vaktrarp Archive "vaktraparchiveAction") and $inArray = true if we are giving Vaktrapport Ids to search
	/*public function loadListByVaktrap($patientId = 0, $vaktrapId = 0, $forActive = true, $inArray = false)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('ti'=>'tiltak_institute'),array('tilins_result', 'result_count'=>'COUNT(tilins_result)'))
						->where($adapter->quoteInto('ti.tilins_patientID = ?', $patientId))
						->group(array('tilins_result'=>'ti.tilins_result'))
						->setIntegrityCheck(false);
			if($inArray)
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_vaktrapportID IN (?)', $vaktrapId));
			}
			else
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_vaktrapportID = ?', $vaktrapId));
			}
			if($forActive)
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_status = ?', 'yes'));
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}*/
	// For getting Vaktrapport Institute Tiltak  where $forActive = false for all list without any status restriction (Used at Vaktrarp Archive "vaktraparchiveAction") and $inArray = true if we are giving Vaktrapport Ids to search
	public function loadListByResult($patientId = 0, $tilins_result='', $vaktrapId = 0, $forActive = true, $inArray = false, $isDept=false)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			
			$select = $db->select()
						->from(array('ti'=>'tiltak_institute'),array('tilins_desc'));
						
			if($isDept)
			{
				$select = $select->join(array('p'=>'patient'),'ti.tilins_patientID=p.patient_id',array('patient_deptID'))->where($adapter->quoteInto('p.patient_deptID = ?', $patientId));
			}
			else
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_patientID = ?', $patientId));
			}
			
			$select = $select->order('ti.tilins_result')->setIntegrityCheck(false);
			
			if(isset($tilins_result) && $tilins_result>=0 && $tilins_result<=2)
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_result = ?', $tilins_result));
			}
			else
			{
				$select = $select->where('ti.tilins_result IS NULL');	
			}
			if($inArray)
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_vaktrapportID IN (?)', $vaktrapId));
			}
			else
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_vaktrapportID = ?', $vaktrapId));
			}
			if($forActive)
			{
				$select = $select->where($adapter->quoteInto('ti.tilins_status = ?', 'yes'));
			}
			//echo $select.'<br>';
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	
	// Update Vaktrapport Institute Tiltak  where $key = Name of field for where condition, $val = Value for that condition, $flag = true if we are using array for IN Query Used 	
	//at (locking vaktrapport "saveAction")
	public function update(array $dataPost, $key='tilins_id',$val='',$flag=false)
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
		// By Default $flag is false and if its true it will update all given ids and this functionalitu used while creating Vaktrap first time
		if($flag)
		{
			$where = $adapter->quoteInto(' '.$key .' IN (?) ', $val);
		}
		else
		{
			$where = $adapter->quoteInto(' '.$key .' = ? ', $val);
		}
		
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
				case 'from_date':
				case 'to_date':
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
				case 'tilins_userID':
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