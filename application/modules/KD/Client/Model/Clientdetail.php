<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Clientdetail extends KD_Client_Model_Clientdetail_Collection
{
    protected $_name = 'patient_detail';

	protected $_tableField = array('patient_detail_patientID'=>false,'patient_detail_location'=>true,'patient_detail_desease'=>true,'patient_detail_allergy'=>true,'patient_detail_motorskill'=>true,'patient_detail_aids'=>true,'patient_detail_diet'=>true,'patient_detail_hegiene'=>true,'patient_detail_diagnoses'=>true,'patient_detail_cognitive'=>true,'patient_detail_mental'=>true,'patient_detail_rushi'=>true,'patient_detail_social'=>true,'patient_detail_school'=>true,'patient_detail_ppt'=>true,'patient_detail_religion'=>true,'patient_detail_language'=>true,'patient_detail_interest'=>true,'patient_detail_support'=>true,'patient_detail_nav'=>true,'patient_detail_economy'=>true,'patient_detail_bup_dps'=>true,'patient_detail_others'=>true,'patient_detail_address1'=>true,'patient_detail_address2'=>true,'patient_detail_address3'=>true,'patient_detail_address4'=>true,'patient_detail_address5'=>true,'patient_detail_address6'=>true,'patient_detail_address7'=>true,'patient_detail_address8'=>true,'patient_detail_address9'=>true,'patient_detail_address10'=>true,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/clientdetail','patient_detail_id');
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

	public function loadList($type='active')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('pd'=>'patient_detail'))
					->join(array('p'=>'patient'),'p.patient_id=pd.patient_detail_patientID')
					->setIntegrityCheck(false);
		switch($type)
		{
			case 'archive': 
				$select = $select->where($adapter->quoteInto('p.patient_status = ?', 'no'));
			break;
			case 'active': 
				$select = $select->where($adapter->quoteInto('p.patient_status = ?', 'yes'));
			break;
			default:
				//You can get List of all Client List
			break;
		}
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}

    public function loadPageData($page=1, $type='active')
    {
		$select = $this->loadList($type);
		$result = $this->loadPage($this->_tableField, $select, $page);
		return $result;
    }
    public function checkClientDetail($patient_id=0)
    {
		
        if($patient_id>0)
        {
            return parent::checkClientDetail($patient_id);
        }
        else
        {
           return false;
        }
    }	
	

	public function update(array $dataPost, $key='patient_detail_id',$val='')
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