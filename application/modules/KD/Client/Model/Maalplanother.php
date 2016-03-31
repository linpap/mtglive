<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Maalplanother extends KD_Client_Model_Maalplanother_Collection
{
    protected $_name = 'maalplan_other';

	protected $_tableField = array('maalplan_other_id'=>false,'maalplan_other_maalplanID'=>false,'maalplan_other_patientID'=>false,'maalplan_other_type'=>false,'maalplan_other_certainDate'=>false,'maalplan_other_certainDesc'=>true,'maalplan_other_meetingDate'=>false,'maalplan_other_meetingDesc'=>true,'maalplan_other_activityDesc1'=>true,'maalplan_other_activityDesc2'=>true,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/maalplanother','maalplan_maal_id');
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

	public function loadList($patientId = 0,$maalplanID = 0, $maalplanOtherType='')
	{
		if($patientId > 0 && $maalplanID>0 && $maalplanOtherType!='')
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('mpo'=>'maalplan_other'))
						->where($adapter->quoteInto('maalplan_other_patientID = ?', $patientId))
						->where($adapter->quoteInto('maalplan_other_maalplanID = ?', $maalplanID))
						->where($adapter->quoteInto('maalplan_other_type = ?', $maalplanOtherType))
						->order(array('maalplan_other_id ASC'))
						->setIntegrityCheck(false);
						
			$tableField = $this->_tableField;
			$tableField['maal_desc'] = true;
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return array();
	}
	public function loadAchiveList($patientId = 0,$maalplanID = 0)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('m'=>'maal'),array('maal_order','maal_desc','maal_from_date','maal_to_date'))
						->join(array('mpm'=>'maalplan_maal'),'m.maal_id=mpm.maalplan_maalID')
						->where($adapter->quoteInto('maal_patientID = ?', $patientId))
						->where($adapter->quoteInto('maalplan_maalplanID <> ?', $maalplanID))
						->order(array('maal_order ASC','maalplan_maal_id DESC'))
						->setIntegrityCheck(false);
						
			$tableField = $this->_tableField;
			$tableField['maal_desc'] = true;
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$tableField);
		}
		return array();
	}
    public function loadPageData($patientId, $page=1)
    {
		$select = $this->loadList($patientId);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
	public function update(array $dataPost, $key='maalplan_maal_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'maalplan_other_certainDate':
				case 'maalplan_other_meetingDate':
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
		//print_r($dataPost);exit();
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'maalplan_other_certainDate':
				case 'maalplan_other_meetingDate':
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
		//print_r($data);exit();
        return parent::insert($data);
    }
}