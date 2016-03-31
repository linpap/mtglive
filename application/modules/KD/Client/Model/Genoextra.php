<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Genoextra extends KD_Client_Model_Genoextra_Collection
{
    protected $_name = 'genoextra';

	protected $_tableField = array('geno_extra_patientID'=>false,'geno_extra_deptID'=>false,'geno_extra_type'=>false,'geno_extra_desc1'=>true,'geno_extra_desc2'=>true,'geno_extra_desc3'=>true,'geno_extra_desc4'=>true,'geno_extra_desc5'=>true,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/genoextra','geno_extra_id');
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

	public function loadList($patientId = 0,$type='active')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('g'=>'geno_extra'))
					->where($adapter->quoteInto('geno_extra_patientID = ?', $patientId))
					->setIntegrityCheck(false);
				
		switch($type)
		{
			case 'type1': 
				$select = $select->where($adapter->quoteInto('geno_extra_type = ?', $type));
			break;
			case 'type2': 
				$select = $select->where($adapter->quoteInto('geno_extra_type = ?', $type));
			break;
		}
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
	public function update(array $dataPost, $key='patient_id',$val='')
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