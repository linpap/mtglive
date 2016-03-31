<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Vaktrapport_Model_Medvaktrap extends KD_Vaktrapport_Model_Medvaktrap_Collection
{
    protected $_name = 'medicine_vaktrap';

	protected $_tableField = array('medvak_detId'=>false,'medvak_patientID'=>false,'medvak_vaktrapID'=>false,'medvak_year'=>false,'medvak_date'=>false,'medvak_day'=>false,'medvak_took'=>false,'medvak_time'=>false,'medvak_desc'=>true,'medvak_userID'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('vaktrapport/medvaktrap','medvak_id');
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

	public function loadList($patientId = 0, $vaktrapId = 0)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('mdv'=>'medicine_vaktrap'))
						->where($adapter->quoteInto('medvak_patientID = ?', $patientId))
						->where($adapter->quoteInto('medvak_vaktrapID = ?', $vaktrapId))
						->order(array('medvak_date ASC'))
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
	public function getMedVakByDate($patientId=0,$vaktrapId=0,$fromDate='',$toDate='',$day='')
	{
		
		if($patientId > 0 && $vaktrapId>0 && $day!='' && preg_match("/(\d{4})-(\d{2})-(\d{2})/",'2015-15-04')==true && preg_match("/(\d{4})-(\d{2})-(\d{2})/",'2015-15-04')==true)
		{
			$adapter = $this->_getResource()->getAdapter();
			$onTable = '';
			$onTable .= ' md.med_det_id = mdv.medvak_detId';
			$onTable .= ' AND  md.med_det_day = mdv.medvak_day';
			$onTable .= ' AND  md.med_det_date = mdv.medvak_date';
			$onTable .= ' AND  md.med_det_patientID = mdv.medvak_patientID ';
			$onTable .= ' AND  '.$adapter->quoteInto(' mdv.medvak_vaktrapID = ? ', $vaktrapId);
			$db = $this->_getResource();
			$select = $db->select()
						->from(array('md'=>'medicine_detail'))
						->joinLeft(array('mdv'=>'medicine_vaktrap'),$onTable,array('medvak_id','medvak_detId','medvak_patientID','medvak_vaktrapID','medvak_year','medvak_date','medvak_day','medvak_took','medvak_time','medvak_desc','medvak_userID','date_of_creation_mdv'=>'date_of_creation','date_of_modification_mdv'=>'date_of_modification','created_by_mdv'=>'created_by','modified_by_mdv'=>'modified_by'))
						->where($adapter->quoteInto('md.med_det_patientID = ?', $patientId))
						->where($adapter->quoteInto('md.med_det_date >= ?', $fromDate))
						->where($adapter->quoteInto('md.med_det_date <= ?', $toDate))
						->where($adapter->quoteInto('md.med_det_day = ?', $day))
						->order(array('md.med_det_id ASC'))
						->setIntegrityCheck(false);
			$tableField = $this->_tableField;			
			$tableField['med_det_name'] = true;
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return false;
	}
	
	// Get Logg By Medicine Id Only For Copy Purpose
	public function getMedCountByDate($patientId=0,$fromDate='',$toDate='')
	{
		
		if($patientId > 0 && $fromDate!='' && $toDate!='')
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('mdd'=>'medicine_detail'),array('med_det_id'))
						->joinLeft(array('mdv'=>'medicine_vaktrap'),'mdd.med_det_id=mdv.medvak_detId',array('medvak_id'))
						->where($adapter->quoteInto('mdd.med_det_patientID = ?', $patientId))
						->where($adapter->quoteInto('mdd.med_det_date >= ?', $fromDate))
						->where($adapter->quoteInto('mdd.med_det_date <= ?', $toDate))
						->order(array('med_det_date ASC'))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			
			
			$select1 = $db->select()
						->from(array('mdd'=>'medicine_detail'),array('med_det_id'))
						->joinLeft(array('mdv'=>'medicine_vaktrap'),'mdd.med_det_id=mdv.medvak_detId',array('medvak_id'))
						->where($adapter->quoteInto('mdd.med_det_patientID = ?', $patientId))
						->where($adapter->quoteInto('mdd.med_det_date >= ?', $fromDate))
						->where($adapter->quoteInto('mdd.med_det_date <= ?', $toDate))
						->where('mdd.med_det_nos = mdv.medvak_took')
						//med_det_date>='2015-03-27' AND med_det_date<='2015-04-02' AND md.med_det_nos=mv.medvak_took 
						->order(array('med_det_date ASC'))
						->setIntegrityCheck(false);
			$stmt1 = $select1->query();
			$result1 = $stmt1->fetchAll();
			return count($result)-count($result1);
		}
		return false;
	}
	
	public function update(array $dataPost, $key='medvak_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'medvak_date':
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
				case 'medvak_date':
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
				case 'medvak_userID':
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