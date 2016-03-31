<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Medicine extends KD_Client_Model_Medicine_Collection
{
    protected $_name = 'medicine';

	protected $_tableField = array('medicine_patientID'=>false,'medicine_userID'=>false,'medicine_week'=>false,'medicine_start_date'=>false,'medicine_end_date'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/medicine','medicine_id');
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

	public function loadList($patientId = 0)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('md'=>'medicine'))
						->where($adapter->quoteInto('medicine_patientID = ?', $patientId))
						->order(array('medicine_start_date DESC'))
						->setIntegrityCheck(false);
						
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
    public function loadPageData($patientId, $page=1)
    {
		$select = $this->loadList($patientId);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
	
	// Get Logg By Vaktrapport Id
	public function getMedicineByFromDate($patientId=0,$fromDate='')
	{
		
		if($patientId > 0 && $fromDate!='' && (strpos($fromDate,'0000')===false))
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('md'=>'medicine'))
						->where($adapter->quoteInto('medicine_patientID = ?', $patientId))
						->where($adapter->quoteInto('medicine_start_date like ?', $fromDate.'%'))
						->order(array('medicine_start_date DESC'))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return false;
	}
	
	// Get Logg By Vaktrapport Id
	public function checkMedicineByDate($patientId=0,$checkDate='')
	{
		
		if($patientId > 0 && $checkDate!='' && (strpos($checkDate,'0000')===false))
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('md'=>'medicine'))
						->where($adapter->quoteInto('medicine_patientID = ?', $patientId))
						->where($adapter->quoteInto('medicine_end_date >= ?', $checkDate))
						->where($adapter->quoteInto('medicine_start_date <= ?', $checkDate))
						->order(array('medicine_start_date DESC'))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return false;
	}
	
	public function update(array $dataPost, $key='medicine_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'medicine_start_date':
				case 'medicine_end_date':
					$pdoj = '';
					if(isset($dataPost[$column]))
					{
						$pdoj = $dataPost[$column];
						$data[$column] = $format->PrepareDateDB($pdoj);
					}
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
	
	public function insert(array $dataPost)
    {
		$ende = KD::getModel('core/endecrypt');
		$format = KD::getModel('core/format');
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'medicine_start_date':
				case 'medicine_end_date':
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
				case 'medicine_userID':
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