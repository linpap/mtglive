<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Force214 extends KD_Client_Model_Force214_Collection
{
    protected $_name = 'force214';

	protected $_tableField = array('force214_id'=>false,'force214_patientID'=>false,'force214_userID'=>false,'force214_vaktrapID'=>false,'force214_forceID'=>false,'force_chk21_personal'=>false,'force_chk21_property'=>false,'force_txta21_happened'=>true,'force_txta21_prevent'=>true,'force_txta21_inadequate'=>true,'force_txta21_coercive'=>true,'force_txta21_degree'=>true,'force_txt21_howlong'=>true,'force_sel21_averted'=>false,'force_txta21_averted_res'=>true,'force_sel21_resident'=>false,'force_sel21_decision'=>false,'force_txt21_decision_name'=>true,'force_sel21_adjoin'=>false,'force_sel21_insulation'=>false,'force_txta21_timeaspect'=>true,'force_sel21_police'=>false,'force_txta21_police_res'=>true,'force_txta22_search'=>true,'force_sel22_search_who'=>false,'force_txt22_search_name'=>true,'force_sel22_happen'=>false,'force_txta22_present'=>true,'force_sel22_samesex'=>false,'force_chk22_sus_stolen'=>false,'force_chk22_sus_danger'=>false,'force_chk22_sus_alcohol'=>false,'force_chk22_sus_other'=>false,'force_chk22_sus_parapher'=>false,'force_txta22_closer'=>true,'force_txta22_individual'=>true,'force_chk22_encp_body'=>false,'force_chk22_encp_through'=>false,'force_chk22_encp_undress'=>false,'force_chk22_encp_oral'=>false,'force_txta22_complete'=>true,'force_txta23_method'=>true,'force_sel23_search'=>false,'force_txt23_search_name'=>true,'force_sel23_happen'=>false,'force_chk23_sus_stolen'=>false,'force_chk23_sus_danger'=>false,'force_chk23_sus_alcohol'=>false,'force_chk23_sus_other'=>false,'force_chk23_sus_parapher'=>false,'force_txta23_closer'=>true,'force_txta23_individual'=>true,'force_txta23_how_search'=>true,'force_sel23_resident'=>false,'force_txta23_resident_res'=>true,'force_chk24_ingestion'=>false,'force_chk24_stay'=>false,'force_chk24_seiz_stolen'=>false,'force_chk24_seiz_danger'=>false,'force_chk24_seiz_intox'=>false,'force_chk24_seiz_other'=>false,'force_chk24_seiz_parapher'=>false,'force_txta24_closer'=>true,'force_chk24_occur_body'=>false,'force_chk24_occur_ransak'=>false,'force_chk24_occur_mail'=>false,'force_chk24_occur_other'=>false,'force_txta24_other_res'=>true,'force_chk24_done_police'=>false,'force_chk24_done_loot'=>false,'force_chk24_done_return'=>false,'force_chk24_done_crus'=>false,'force_chk24_done_storage'=>false, 'force214_lock_date'=>false,'force214_lock_by'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
	
    public function __construct()
    {
        $this->_init('client/force214','force214_id');
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
						->join(array('f214'=>'force214'),'f214.force214_forceID=f.force_id AND f214.force214_patientID = force_patientID')
						->where($adapter->quoteInto('f214.force214_patientID = ?', $patientId))
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
						->join(array('f214'=>'force214'),'f214.force214_forceID=f.force_id')
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
						->from(array('f'=>'force214'))
						->where($adapter->quoteInto('force214_forceID = ?', $forceId))
						->order(array('force214_forceID DESC'))
						->setIntegrityCheck(false);
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	public function update(array $dataPost, $key='force214_id',$val='')
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
				case 'force214_userID':
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