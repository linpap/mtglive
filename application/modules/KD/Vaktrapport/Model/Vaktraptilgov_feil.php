<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Vaktrapport_Model_Vaktraptilgov extends KD_Vaktrapport_Model_Vaktraptilgov_Collection
{ 
    protected $_name = 'vaktrap_tiltak_government';

	protected $_tableField = array('vaktrap_tilgov_maalID'=>false,'vaktrap_tilgovID'=>false,'vaktrap_tilgov_patientID'=>false,'vaktrap_tilgov_userID'=>false,'vaktrap_vaktrapID'=>false,'vaktrap_tilgov_desc'=>true,'vaktrap_tilgov_owner'=>false,'vaktrap_tilgov_result'=>false,'vaktrap_tilgov_status'=>false,'vaktrap_tilgov_from_date'=>false,'vaktrap_tilgov_to_date'=>false,'vaktrap_tilgov_explanation'=>true,'vaktrap_previous_vaktrapID'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('vaktrapport/vaktraptilgov','vaktrap_tilgov_id');
    }
 
    public function load($id, $field=null, $_tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }

        return parent::load($id,$field,$this->_tableField);
    }   
	
	public function getTiltak($id=0, $attribute='vaktrap_tilgov_desc')
	{
		if(isset($id) && $id > 0)
		{
			$tiltak = $this->load($id);
			if(isset($tiltak[$attribute]))
			{
				return $tiltak[$attribute];
			}
		}
		else
		{
			return 'Invalid Tiltak';
		} 
	} 
	
	public function checkVakGovTiltak($tilgovID=0,$vaktrapId=0)
	{
		if($tilgovID > 0 && $vaktrapId>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('vtg'=>'vaktrap_tiltak_government'),array('vaktrap_tilgov_id'))
						->where($adapter->quoteInto('vtg.vaktrap_tilgovID = ?', $tilgovID))
						->where($adapter->quoteInto('vtg.vaktrap_vaktrapID = ?', $vaktrapId))
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
	// For getting Vaktrapport Government Tiltak Order where $type = active for Acive, Archive for archive and none for all (used at vaktraparchiveAction), $showNext = true  for showing Next vaktrapport Vaktrapport Government Tiltak List( used At vaktraparchiveAction)
	public function loadList($patientId = 0, $vaktrapId = 0, $type='active', $showNext=false)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('vtg'=>'vaktrap_tiltak_government'))
						->join(array('tg'=>'tiltak_government'),'tg.tilgov_id=vtg.vaktrap_tilgovID',array('tilgov_from_date','tilgov_to_date'))
						->join(array('m'=>'maal'),'vtg.vaktrap_tilgov_maalID=m.maal_id',array('maal_id','maal_desc'))
						->join(array('p'=>'patient'),'vtg.vaktrap_tilgov_patientID=p.patient_id',array('patient_code'))
						->where($adapter->quoteInto('vtg.vaktrap_tilgov_patientID = ?', $patientId))
						->order(array('m.maal_order ASC','m.maal_id ASC'))
						->setIntegrityCheck(false);
			if($vaktrapId > 0)
			{	
				if($showNext)
				{
					$select = $select->where($adapter->quoteInto('vtg.vaktrap_previous_vaktrapID = ?', $vaktrapId));
				}
				else
				{
					$select = $select->where($adapter->quoteInto('vtg.vaktrap_vaktrapID = ?', $vaktrapId));
				}
			}
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('vtg.vaktrap_tilgov_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('vtg.vaktrap_tilgov_status = ?', 'yes'));
				break;
				default:
					//$select = $select->where($adapter->quoteInto('vtg.vaktrap_tilgov_status = ?', 'yes'));
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
    public function loadPageData($patientId, $page=1, $type='active')
    {
		$select = $this->loadList($patientId, $type);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
	// For getting Vaktrapport Government Tiltak Order By Maal where $forActive = false for all list without any status restriction (Used at Vaktrarp Archive "vaktraparchiveAction")  and $inArray = true if we are giving Vaktrapport Ids to search
	public function loadListByMaal($patientId = 0, $maalId = 0, $vaktrapId = 0, $forActive = false, $inArray = false)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('vtg'=>'vaktrap_tiltak_government'))
						->joinLeft(array('m'=>'maal'),'vtg.vaktrap_tilgov_maalID=m.maal_id and m.maal_patientID=vtg.vaktrap_tilgov_patientID',array('maal_id','maal_desc'))
						->where($adapter->quoteInto('vtg.vaktrap_tilgov_patientID = ?', $patientId))
						->where($adapter->quoteInto('vtg.vaktrap_tilgov_maalID = ?', $maalId))
						//->where($adapter->quoteInto('vtg.vaktrap_vaktrapID = ?', $vaktrapId))
						->order(array('m.maal_order ASC','m.maal_id ASC'))
						->setIntegrityCheck(false);
			if($inArray)
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_vaktrapID IN (?)', $vaktrapId));
			}
			else
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_vaktrapID = ?', $vaktrapId));
			}

			if($forActive)
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_tilgov_status = ?', 'yes'));
			}

			$tableField = $this->_tableField;
			$tableField['maal_desc'] = true;


			
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return array();
	}
	public function archiveList($updateVakTilGovId,$val='no')
	{
		$data = array();
		$data['vaktrap_tilgov_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('vaktrap_tilgov_id in (?)', $updateVakTilGovId);
		return parent::update($data,$where);
	}
	// Get Governemt Tiltak List from tilgov_id list comma seperated For directly insert in to vaktrap government tiltak
	public function getGovTiltListToInsert($govTilIds)
	{		
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('vtg'=>'vaktrap_tiltak_government'),array('vaktrap_tilgov_maalID','vaktrap_tilgovID','vaktrap_tilgov_patientID','vaktrap_tilgov_userID','vaktrap_tilgov_desc','vaktrap_tilgov_owner','vaktrap_tilgov_from_date','vaktrap_tilgov_to_date','vaktrap_previous_vaktrapID'=>'vaktrap_vaktrapID'))
					->where($adapter->quoteInto('vaktrap_tilgov_id in (?)', $govTilIds))
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
						->from(array('vtg'=>'vaktrap_tiltak_government'),array('vaktrap_tilgov_result', 'result_count'=>'COUNT(vaktrap_tilgov_result)'))
						->where($adapter->quoteInto('vtg.vaktrap_tilgov_patientID = ?', $patientId))
						->group(array('vtg.vaktrap_tilgov_result'))
						->setIntegrityCheck(false);
			if($inArray)
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_vaktrapID IN (?)', $vaktrapId));
			}
			else
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_vaktrapID = ?', $vaktrapId));
			}
			if($forActive)
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_tilgov_status = ?', 'yes'));
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}*/
	// For getting Vaktrapport Institute Tiltak  where $forActive = false for all list without any status restriction (Used at Vaktrarp Archive "vaktraparchiveAction") and $inArray = true if we are giving Vaktrapport Ids to search
	public function loadListByResult($patientId = 0, $tilgov_result='', $vaktrapId = 0, $forActive = true, $inArray = false, $isDept=false)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			
			$select = $db->select()
						->from(array('vtg'=>'vaktrap_tiltak_government'),array('vaktrap_tilgov_desc'));
			
			// if $isDept = true Get Data for Deparment so will ignore patientId
			if($isDept)
			{
				$select = $select->join(array('p'=>'patient'),'vtg.vaktrap_tilgov_patientID=p.patient_id',array('patient_deptID'))->where($adapter->quoteInto('p.patient_deptID = ?', $patientId));
						
			}
			else
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_tilgov_patientID = ?', $patientId));
			}
			
			$select = $select->order('vtg.vaktrap_tilgov_result')->setIntegrityCheck(false);
			
			if(isset($tilgov_result) && $tilgov_result>=0 && $tilgov_result<=2)
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_tilgov_result = ?', $tilgov_result));
			}
			else
			{
				$select = $select->where('vtg.vaktrap_tilgov_result IS NULL');	
			}
			if($inArray)
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_vaktrapID IN (?)', $vaktrapId));
			}
			else
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_vaktrapID = ?', $vaktrapId));
			}
			if($forActive)
			{
				$select = $select->where($adapter->quoteInto('vtg.vaktrap_tilgov_status = ?', 'yes'));
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	
	// Update Vaktrapport Government Tiltak  where $key = Name of field for where condition, $val = Value for that condition, $flag = true if we are using array for IN Query Used 	
	//at (locking vaktrapport "saveAction") and  $forActive = true for Only active Vaktrapport Government Tiltak only (Used at Government Tiltak Edit)
	public function update(array $dataPost, $key='vaktrap_tilgov_id', $val='', $flag=false, $forActive = false)
    {

	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'vaktrap_tilgov_from_date':
				case 'vaktrap_tilgov_to_date':
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
		// By Default $flag is false and if its true it will update all give ids and this functionalitu used while creating Vaktrap first time
		$where = array();
		if($flag)
		{
			$where[] = $adapter->quoteInto(' '.$key .' IN (?) ', $val);
		}
		else
		{
			$where[] = $adapter->quoteInto(' '.$key .' = ? ', $val);
		}
		if($forActive)
		{
			$where[] = $adapter->quoteInto(' vaktrap_tilgov_status = ? ', 'yes');
		}
		//print_r($where);exit();
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
				case 'vaktrap_tilgov_from_date':
				case 'vaktrap_tilgov_to_date':
					$pdoj = '';
					if(isset($dataPost[$column]))
					{
						// We are not Preparing Date Here as normal because we are just giving data from Government Tiltak Table directly
						//$pdoj = $dataPost[$column];
						//$data[$column] = $format->PrepareDateDB($pdoj);
						$data[$column] = $dataPost[$column];;
					}
				break;
				case 'date_of_creation':
					$data[$column] = date("Y-m-d H:i:s");
				break;
				case 'vaktrap_tilgov_userID':
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
		$where = $adapter->quoteInto('vaktrap_tilgov_id = ?', $id);
		return parent::delete($where);
	}
}