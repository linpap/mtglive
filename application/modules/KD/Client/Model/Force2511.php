<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Force2511 extends KD_Client_Model_Force2511_Collection
{
    protected $_name = 'force2511';

	protected $_tableField = array('force2511_id'=>false,'force2511_patientID'=>false,'force2511_userID'=>false,'force2511_vaktrapID'=>false,'force2511_forceID'=>false,'force_txta25_method'=>true,'force_chk25_drug'=>false,'force_chk25_danger'=>false,'force_txta25_describe'=>true,'force_txta25_suspicion'=>true,'force_sel25_decision'=>false,'force_sel25_county'=>false,'force_sel25_open'=>false,'force_sel25_open_who'=>false,'force_txt25_open_name'=>true,'force_chk25_residen'=>false,'force_chk25_sender'=>false,'force_txta26_urine'=>true,'force_sel26_tobog'=>false,'force_sel26_policy'=>false,'force_sel26_present'=>false,'force_sel26_discovery'=>false,'force_txta26_discovery_res'=>true,'force_sel26_sample_submit'=>false,'force_txta27_reversal'=>true,'force_txta27_institute'=>true,'force_sel27_welfare'=>false,'force_chk28_within'=>false,'force_chk28_outside'=>false,'force_txta28_limit'=>true,'force_txta28_location'=>true,'force_txta28_howlong'=>true,'force_txta28_howlong'=>true,'force_txta29_location'=>true,'force_txta29_limit'=>true,'force_txta29_howlong'=>true,'force_txta210_factor'=>true,'force_txta210_howlong'=>true,'force_sel210_revoke'=>false,'force_txta210_revoke_res'=>true,'force_txta210_why'=>true,'force_chk211_decision'=>false,'force_chk211_consent'=>false,'force_txta211_urine'=>true,'force_sel211_urine_res'=>false,'force_sel211_policy'=>false,'force_txta211_policy_res'=>true,'force_sel211_present'=>false,'force_sel211_discovery'=>false,'force_txta211_discovery_res'=>true,'force_sel211_analysis'=>false,'force_sel211_sample'=>false,'force_sel211_control'=>false, 'force2511_lock_date'=>false,'force2511_lock_by'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
	
    public function __construct()
    {
        $this->_init('client/force2511','force2511_id');
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
						->join(array('f214'=>'force2511'),'f214.force2511_forceID=f.force_id AND f214.force2511_patientID = force_patientID')
						->where($adapter->quoteInto('f214.force2511_patientID = ?', $patientId))
						->order(array('f.force_id DESC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('f.force_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('f.force_status = ?', 'yes'));
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
	public function getForceByVaktrap($vaktrapId=0,$patientId=0)
	{
		if($patientId > 0 && $vaktrapId>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('f'=>'force'))
						->join(array('f2511'=>'force2511'),'f2511.force2511_forceID=f.force_id')
						->where($adapter->quoteInto('f.force_patientID = ?', $patientId))
						->where($adapter->quoteInto('f.force_vaktrapID = ?', $vaktrapId))
						->where($adapter->quoteInto('f.force_status = ?', 'yes'))
						->order(array('f.force_id ASC'))
						->setIntegrityCheck(false);
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
						->from(array('f'=>'force2511'))
						->where($adapter->quoteInto('force2511_forceID = ?', $forceId))
						->order(array('force2511_forceID DESC'))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	public function update(array $dataPost, $key='force2511_id',$val='')
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
				case 'force2511_userID':
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