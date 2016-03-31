<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Maalplantiltak extends KD_Client_Model_Maalplantiltak_Collection
{
    protected $_name = 'maalplantiltak';

	protected $_tableField = array('maalplan_maalplanID'=>false,'maalplan_maalID'=>false,'maalplan_tiltakID'=>false,'maalplan_tiltakName'=>true,'maalplan_tiltakDesc'=>true,'maalplan_tiltakFromDate'=>false,'maalplan_tiltakToDate'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/maalplantiltak','maalplan_tiltak_id');
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

	public function loadTiltakList($patientId = 0, $maal_id=0, $maalplanID = 0,$type = 'active')
	{
		if($patientId > 0 && $maal_id>0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			if($type=='active')
			{
				$select = $db->select()
						->from(array('tg'=>'tiltak_government'),array('tilgov_id','tilgov_from_date','tilgov_to_date','tilgov_desc','tilgov_owner'))
						->joinLeft(array('mpt'=>'maalplan_tiltak'),'mpt.maalplan_tiltakID = tg.tilgov_id AND '.$adapter->quoteInto('mpt.maalplan_maalplanID = ?', $maalplanID))
						->where($adapter->quoteInto('tg.tilgov_maalID = ?', $maal_id))
						->where($adapter->quoteInto('tg.tilgov_patientID = ?', $patientId))
						->where($adapter->quoteInto('tg.tilgov_status = ?', 'yes'))
						->order(array('maalplan_tiltak_id DESC'))
						->setIntegrityCheck(false);
			}
			elseif($type=='archive')
			{
				$select = $db->select()
						->from(array('tg'=>'tiltak_government'),array('tilgov_id','tilgov_from_date','tilgov_to_date','tilgov_desc','tilgov_owner'))
						->joinInner(array('mpt'=>'maalplan_tiltak'),'mpt.maalplan_tiltakID = tg.tilgov_id AND '.$adapter->quoteInto('mpt.maalplan_maalplanID = ?', $maalplanID))
						->where($adapter->quoteInto('tg.tilgov_maalID = ?', $maal_id))
						->where($adapter->quoteInto('tg.tilgov_patientID = ?', $patientId))
						->order(array('maalplan_tiltak_id DESC'))
						->setIntegrityCheck(false);
			}
			else
			{
				$select = $db->select()
						->from(array('tg'=>'tiltak_government'),array('tilgov_id','tilgov_from_date','tilgov_to_date','tilgov_desc','tilgov_owner'))
						->joinLeft(array('mpt'=>'maalplan_tiltak'),'mpt.maalplan_tiltakID = tg.tilgov_id AND '.$adapter->quoteInto('mpt.maalplan_maalplanID = ?', $maalplanID))
						->where($adapter->quoteInto('tg.tilgov_maalID = ?', $maal_id))
						->where($adapter->quoteInto('tg.tilgov_patientID = ?', $patientId))
						->order(array('maalplan_tiltak_id DESC'))
						->setIntegrityCheck(false);
			}
				
			$tableField = $this->_tableField;
			$tableField['tilgov_desc'] = true;
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
	public function update(array $dataPost, $key='maalplan_tiltak_id',$val='')
    {
	  if($val!='')
	  {
		$format = KD::getModel('core/format');
		$ende = KD::getModel('core/endecrypt');//print_r($dataPost);
		foreach($this->_tableField as $column=>$encrypt)
		{
			switch($column)
			{
				case 'maalplan_tiltakFromDate':
				case 'maalplan_tiltakToDate':
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
				case 'maalplan_tiltakFromDate':
				case 'maalplan_tiltakToDate':
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