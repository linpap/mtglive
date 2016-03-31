<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Observation extends KD_Client_Model_Observation_Collection
{
    protected $_name = 'observation';

	protected $_tableField = array('observation_patientID'=>false,'observation_userID'=>false,'observation_type'=>false,'observation_relationID'=>false,'observation_desc'=>true,'observation_fulldesc'=>true,'observation_status'=>false,'observation_order'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/observation','observation_id');
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
						->from(array('o'=>'observation'),array('observation_id', 'observation_patientID', 'observation_userID', 'observation_type', 'observation_relationID', 'observation_desc', 'observation_fulldesc','observation_order'))
						->join(array('p'=>'patient'),'o.observation_patientID=p.patient_id',array('patient_code'))
						->joinLeft(array('m'=>'maal'),'o.observation_relationID=m.maal_id AND o.observation_type=\'M\'',array('relation1'=>'maal_desc'))
						->joinLeft(array('g'=>'tiltak_government'),'o.observation_relationID=g.tilgov_id AND o.observation_type=\'T\'',array('relation2'=>'tilgov_desc'))
						->where($adapter->quoteInto('o.observation_patientID = ?', $patientId))
						->order(array('observation_order ASC','observation_id ASC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('observation_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('observation_status = ?', 'yes'));
				break;
				default:
					// You can get List of all Observation List
				break;
			}
			$tableField = $this->_tableField;
			$tableField['relation1'] = true;
			$tableField['relation2'] = true;
			
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
	public function archiveList($updatepatientid,$val='no')
	{
		$data = array();
		$data['observation_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('observation_id in (?)', $updatepatientid);
		return parent::update($data,$where);
	}
	public function update(array $dataPost, $key='observation_id',$val='')
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
				case 'date_of_creation':
					$data[$column] = date("Y-m-d H:i:s");
				break;
				case 'observation_userID':
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