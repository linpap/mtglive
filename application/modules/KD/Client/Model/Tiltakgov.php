<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Tiltakgov extends KD_Client_Model_Tiltakgov_Collection
{
    protected $_name = 'tiltak_government';

	protected $_tableField = array('tilgov_maalID'=>false,'tilgov_patientID'=>false,'tilgov_userID'=>false,'tilgov_desc'=>true,'tilgov_owner'=>false,'tilgov_status'=>false,'tilgov_from_date'=>false,'tilgov_to_date'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/tiltakgov','tilgov_id');
    }
 
    public function load($id, $field=null, $_tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }

        return parent::load($id,$field,$this->_tableField);
    }   
	
	public function getTiltak($id=0, $attribute='tilgov_desc')
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

    public function loadAll($_tableField=array())
    {
        return parent::loadAll($this->_tableField);
    } 

	public function loadList($patientId = 0, $type='active')
	{



		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('tg'=>'tiltak_government'),array('tilgov_id', 'tilgov_maalID', 'tilgov_patientID', 'tilgov_userID', 'tilgov_desc', 'tilgov_owner','tilgov_from_date','tilgov_to_date'))
						//->joinLeft(array('tg'=>'tiltak_government'),'tg.tilgov_id=vtg.vaktrap_tilgovID',array('tilgov_from_date','tilgov_to_date'))
						->join(array('m'=>'maal'),'tg.tilgov_maalID=m.maal_id and m.maal_patientID=tg.tilgov_patientID',array('maal_id','maal_desc'))
						->join(array('p'=>'patient'),'tg.tilgov_patientID=p.patient_id',array('patient_code'))
						->where($adapter->quoteInto('tg.tilgov_patientID = ?', $patientId))
						->order(array('m.maal_order ASC','m.maal_id ASC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'active':
					$select = $select->where($adapter->quoteInto('tg.tilgov_status = ?', 'yes'));
				break;
				case 'archive':
					$select = $select->where($adapter->quoteInto('tg.tilgov_status = ?', 'no'));
				break;
				default:
					//$select = $select->where($adapter->quoteInto('tg.tilgov_status = ?', 'yes'));
				break;
			}
			$tableField = $this->_tableField;
			$tableField['maal_desc'] = true;
//			echo $select;
//			die;

			$stmt = $select->query();
			$result = $stmt->fetchAll();

			return $this->decryptData($result,$tableField);
		}
		return array();
	}
	
	public function loadListVakShown($patientId = 0, $vaktrapId = 0, $type='active')
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('tg'=>'tiltak_government'),array('tilgov_id', 'tilgov_maalID', 'tilgov_patientID', 'tilgov_userID', 'tilgov_desc', 'tilgov_owner','tilgov_from_date','tilgov_to_date'))
						->join(array('m'=>'maal'),'tg.tilgov_maalID=m.maal_id and m.maal_patientID=tg.tilgov_patientID',array('maal_id','maal_desc'))
						->join(array('p'=>'patient'),'tg.tilgov_patientID=p.patient_id',array('patient_code'))
						->joinLeft(array('vtg'=>'vaktrap_tiltak_government'),'vtg.vaktrap_vaktrapID=\''.$vaktrapId.'\' AND vtg.vaktrap_tilgov_status=\'yes\' AND vtg.vaktrap_tilgovID=tg.tilgov_id',array('vaktrap_tilgov_id','vaktrap_tilgov_status'))
						->where($adapter->quoteInto('tg.tilgov_patientID = ?', $patientId))
						->order(array('m.maal_order ASC','m.maal_id ASC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('tg.tilgov_status = ?', 'no'));
				break;
				case 'active':
					//If You want government tiltak active Than maal must be active
					$select = $select->where($adapter->quoteInto('tg.tilgov_status = ?', 'yes'))->where($adapter->quoteInto('m.maal_status= ?', 'yes'));
				break;
				default:
					// You can get List of all Goverment Tiltak List
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
	public function archiveList($govTilIds,$val='no')
	{
		$data = array();
		$data['tilgov_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('tilgov_id in (?)', $govTilIds);
		return parent::update($data,$where);
	}
	// Get Governemt Tiltak List from tilgov_id list comma seperated 
	public function getGovTiltList($govTilIds)
	{		
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('tg'=>'tiltak_government'))
					->where($adapter->quoteInto('tilgov_id in (?)', $govTilIds))
					->setIntegrityCheck(false);

		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);

	}
	
	// Update Vaktrapport Government Tiltak  where $key = Name of field for where condition, $val = Value for that condition, $flag = true if we are using array for IN Query Used 	
	//at (locking vaktrapport "saveAction")
	public function update(array $dataPost, $key='tilgov_id',$val='',$flag=false)
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'tilgov_from_date':
				case 'tilgov_to_date':
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
				case 'tilgov_from_date':
				case 'tilgov_to_date':
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
				case 'tilgov_userID':
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