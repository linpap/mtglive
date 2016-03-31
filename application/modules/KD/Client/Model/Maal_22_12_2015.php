<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Maal extends KD_Client_Model_Maal_Collection
{
    protected $_name = 'maal';

	protected $_tableField = array('maal_patientID'=>false,'maal_userID'=>false,'maal_desc'=>true,'maal_from_date'=>false,'maal_to_date'=>false,'maal_status'=>false,'maal_order'=>false,'maal_achived_status'=>false,'maal_achived_MPN'=>false,'maal_achived_date'=>false,'maal_achived_userID'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false,'maal_lockset'=>false);
    public function __construct()
    {
        $this->_init('client/maal','maal_id');
    }
 
    public function load($id, $field=null, $_tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }

        return parent::load($id,$field,$this->_tableField);
    }  
	
	public function getMaal($id=0, $attribute='maal_desc')
	{
		if(isset($id) && $id > 0)
		{
			$maal = $this->load($id);
			if(isset($maal[$attribute]))
			{
				return $maal[$attribute];
			}
		}
		else
		{
			return 'Invalid Maal';
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
			$date=date("Y-m-d H:i:s");

			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('m'=>'maal'),array('maal_id', 'maal_desc', 'maal_from_date', 'maal_to_date', 'maal_order','maal_achived_status'))
						->join(array('p'=>'patient'),'m.maal_patientID=p.patient_id',array('patient_code'))
						->where($adapter->quoteInto('maal_patientID = ?', $patientId))
						->order(array('maal_order ASC','maal_id ASC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('maal_status = ?', 'no'))->where($adapter->quoteInto('maal_achived_status = ?', 'no'));
				break;
				case 'achived': 
					//$select = $select->where($adapter->quoteInto('maal_achived_status = ?', 'yes'));
					$select = $select->where($adapter->quoteInto('maal_lockset = ?', 'yes'));
				break;
				case 'active': 
					/*$select = $select->where($adapter->quoteInto('maal_status = ?', 'yes'))->where($adapter->quoteInto('maal_achived_status = ?', 'no'));*/
					//$select = $select->where($adapter->quoteInto('maal_status = ?', 'yes'))->where($adapter->quoteInto('maal_achived_status = ?', 'no'));
					$select = $select->where($adapter->quoteInto('m.maal_from_date <= ?', $date)." AND ".$adapter->quoteInto('m.maal_to_date >= ?', $date). " OR (m.maal_from_date is NULL AND m.maal_to_date is NULL)");


//					$select = $select->where($adapter->quoteInto('maal_status = ?', 'yes'))->where($adapter->quoteInto('maal_achived_status = ?', 'no'))
//										->where($adapter->quoteInto('maal_lockset = ?', 'no'));
				break;
				case 'notachived':
					$select = $select->where($adapter->quoteInto('maal_achived_status = ?', 'no'));
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
	public function archiveList($updatepatientid,$val='no')
	{
		$data = array();
		$data['maal_status'] = $val;
		$adapter = $this->_getResource()->getAdapter();
		$where = $adapter->quoteInto('maal_id in (?)', $updatepatientid);
		return parent::update($data,$where);
	}
	public function update(array $dataPost, $key='maal_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'maal_from_date':
				case 'maal_to_date':
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
				case 'maal_from_date':
				case 'maal_to_date':
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
				case 'maal_userID':
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