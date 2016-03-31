<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Tiltakfut extends KD_Client_Model_Tiltakfut_Collection
{
    protected $_name = 'tiltak_future'; 

	protected $_tableField = array('tilfut_maalID'=>false,'tilfut_patientID'=>false,'tilfut_userID'=>false,'tilfut_desc'=>true,'tilfut_owner'=>false,'tilfut_status'=>false,'tilfut_from_date'=>false,'tilfut_to_date'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/tiltakfut','tilfut_id');
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
						->from(array('tf'=>'tiltak_future'),array('tilfut_id', 'tilfut_maalID', 'tilfut_patientID', 'tilfut_userID', 'tilfut_desc', 'tilfut_owner','tilfut_from_date','tilfut_to_date'))
						->join(array('m'=>'maal'),'tf.tilfut_maalID=m.maal_id and m.maal_patientID=tf.tilfut_patientID',array('maal_id','maal_desc'))
						->join(array('p'=>'patient'),'tf.tilfut_patientID=p.patient_id',array('patient_code'))
						->where($adapter->quoteInto('tf.tilfut_patientID = ?', $patientId))
						->order(array('m.maal_order ASC','m.maal_id ASC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('tf.tilfut_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('m.maal_status = ?', 'yes'));
				break;
				default:
					// You can get List of all Future Tiltak List
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
	public function archiveList($updatepatientid,$val='no')
	{
		$data = array();
		$data['tilfut_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('tilfut_id in (?)', $updatepatientid);
		return parent::update($data,$where);
	}
	public function update(array $dataPost, $key='tilfut_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'tilfut_from_date':
				case 'tilfut_to_date':
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
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'tilfut_from_date':
				case 'tilfut_to_date':
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
				case 'tilfut_userID':
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