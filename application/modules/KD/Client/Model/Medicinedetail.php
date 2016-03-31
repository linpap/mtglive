<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Medicinedetail extends KD_Client_Model_Medicinedetail_Collection
{
    protected $_name = 'medicine_detail';

	protected $_tableField = array('med_det_patientID'=>false,'med_det_medicineID'=>false,'med_det_date'=>false,'med_det_day'=>false,'med_det_name'=>true,'med_det_nos'=>false,'med_det_time'=>false,'med_det_isExtra'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/medicinedetail','med_det_id');
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
						->from(array('mdd'=>'medicine_detail'))
						->where($adapter->quoteInto('med_det_patientID = ?', $patientId))
						->order(array('med_det_date ASC'))
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
	
	// Get Logg By Medicine Id & Day
	public function getMedDetByDayMedID($patientId=0,$medicine_id=0,$day='',$excludeExtra=true)
	{
		
		if($patientId > 0 && $medicine_id>0 && $day!='')
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('mdd'=>'medicine_detail'))
						->where($adapter->quoteInto('med_det_patientID = ?', $patientId))
						->where($adapter->quoteInto('med_det_medicineID = ?', $medicine_id))
						->where($adapter->quoteInto('med_det_day = ?', $day))
						->order(array('med_det_date ASC'))
						->setIntegrityCheck(false);
			if($excludeExtra)
			{
				$select = $select->where($adapter->quoteInto('med_det_isExtra = ?', 'no'));
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return false;
	}
	
	// Get Logg By Medicine Id Only For Copy Purpose
	/*public function getMedDetByMedID($patientId=0,$medicine_id=0)
	{
		
		if($patientId > 0 && $medicine_id>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('mdd'=>'medicine_detail'))
						->where($adapter->quoteInto('med_det_patientID = ?', $patientId))
						->where($adapter->quoteInto('med_det_medicineID = ?', $medicine_id))
						->order(array('med_det_date ASC'))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return false;
	}*/
	
	public function update(array $dataPost, $key='med_det_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'med_det_date':
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
				case 'med_det_date':
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