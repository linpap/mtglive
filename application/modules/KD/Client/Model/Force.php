<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Force extends KD_Client_Model_Force_Collection
{
    protected $_name = 'force';
	protected $_tableField = array('force_id'=>false,'force_patientID'=>false,'force_userID'=>false,'force_vaktrapID'=>false,'force_continue'=>false,'force_status'=>false,'force_chk1_status'=>false,'force_chk1_status'=>false,'force_chk21_status'=>false,'force_chk22_status'=>false,'force_chk23_status'=>false,'force_chk24_status'=>false,'force_chk25_status'=>false,'force_chk26_status'=>false,'force_chk27_status'=>false,'force_chk28_status'=>false,'force_chk29_status'=>false,'force_chk210_status'=>false,'force_chk211_status'=>false,'force_chk3_status'=>false,'force_chk4_status'=>false,'force_chk5_status'=>false,'force_sel1_location'=>true,'force_txta1_resposible'=>true,'force_txta1_address'=>true,'force_txta1_specification'=>true,'force_txt1_institue'=>true,'force_txt1_reg'=>true,'force_txta1_director'=>true,'force_txta1_perform'=>true,'force_txta1_other'=>true,'force_txt1_time_use'=>false,'force_txta1_time_end'=>true,'force_rad1_undergone'=>false,'force_txta1_undergone_res'=>true,'force_rad1_inmate'=>false,'force_txta1_inmate_res'=>true,'force_rad1_want'=>false,'force_rad1_want_res'=>false,'force_rad3_agree'=>false,'force_txta3_desc'=>true,'force_rad3_complain'=>false,'force_txt3_complain'=>true,'force_txta4_desc'=>true,'force_txta5_reflection'=>true,'force_txta5_measure'=>true,'force_rad5_institute'=>false,'force_txta5_institute_res'=>true,'force_txta5_process'=>true,'force_txta5_evaluate'=>true,'force_rad5_submit'=>false,'force_txta5_submit_yes'=>true,'force_txta5_submit_no'=>true,'force_txta5_desc'=>true,'force_lock_date'=>false,'force_lock_by'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);

    public function __construct()
    {
        $this->_init('client/force','force_id');
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

	public function loadList($patientId = 0, $type='active')
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('f'=>'force'))
						->where($adapter->quoteInto('force_patientID = ?', $patientId))
						->order(array('force_id DESC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('force_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('force_status = ?', 'yes'));
				break;
				case 'all': 
				break;
				default:
					//You can get List of all Maal List
				break;
			}
			
			$stmt = $select->query();
			$result = $stmt->fetchAll();
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
	// Get Logg By Vaktrapport Id
	public function getForceByVaktrap($vaktrapId=0,$patientId=0, $status=true)
	{
		if($patientId > 0 && $vaktrapId>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('f'=>'force'))
						->where($adapter->quoteInto('force_patientID = ?', $patientId))
						->where($adapter->quoteInto('force_vaktrapID = ?', $vaktrapId))
						->order(array('force_id DESC'))
						->setIntegrityCheck(false);
			if($status)
			{
				$select = $select->where($adapter->quoteInto('force_status = ?', 'yes'));
			}
			else
			{
				$select = $select->where($adapter->quoteInto('force_status = ?', 'no'));
			}
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	
	// Get Logg By Force Id
	public function getForceByForceId($forceId=0)
	{
		if($forceId>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('f'=>'force'))
						->where($adapter->quoteInto('force_id = ?', $forceId))
						->order(array('force_id DESC'))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	
	public function update(array $dataPost, $key='force_id',$val='')
    {


	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');

		foreach($this->_tableField as $column=>$encrypt)
		{	

			switch($column)
			{
				case 'force_txt1_date_use':
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
				case 'force_txt1_date_use':
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
				case 'force_userID':
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