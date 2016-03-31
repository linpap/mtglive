<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Logg extends KD_Client_Model_Logg_Collection
{
    protected $_name = 'logg';

	protected $_tableField = array('logg_patientID'=>false,'logg_userID'=>false,'logg_desc'=>true,'logg_type'=>false,'logg_vaktrapID'=>false,'logg_status'=>false,'logg_locked_at'=>false,'logg_locked_by'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'created_by'=>false,'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/logg','logg_id');
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
	
	public function loadListByDate($patientId = 0, $type='active', $logg_type='',$fromDate='',$toDate='')
	{
		if($patientId > 0 && $fromDate!='' && $toDate!='')
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('l'=>'logg'))
						->where($adapter->quoteInto('logg_patientID = ?', $patientId))
						->where($adapter->quoteInto('logg_locked_at >= ?', $fromDate))
						->where($adapter->quoteInto('logg_locked_at <= ?', $toDate))
						->order(array('logg_id ASC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('logg_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('logg_status = ?', 'yes'));
				break;
				case 'all': 
				break;
				default:
					//You can get List of all Maal List
				break;
			}
			switch($logg_type)
			{
				case 'M': 
					$select = $select->where($adapter->quoteInto('logg_type = ?', 'M'));
				break;
				case 'P': 
					$select = $select->where($adapter->quoteInto('logg_type = ?', 'P'));
				break;
				case 'O': 
					$select = $select->where($adapter->quoteInto('logg_type = ?', 'O'));
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
	public function loadList($patientId = 0, $type='active', $logg_type='')
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('l'=>'logg'))
						->where($adapter->quoteInto('logg_patientID = ?', $patientId))
						->order(array('logg_id ASC'))
						->setIntegrityCheck(false);
			switch($type)
			{
				case 'archive': 
					$select = $select->where($adapter->quoteInto('logg_status = ?', 'no'));
				break;
				case 'active': 
					$select = $select->where($adapter->quoteInto('logg_status = ?', 'yes'));
				break;
				case 'all': 
				break;
				default:
					//You can get List of all Maal List
				break;
			}
			switch($logg_type)
			{
				case 'M': 
					$select = $select->where($adapter->quoteInto('logg_type = ?', 'M'));
				break;
				case 'P': 
					$select = $select->where($adapter->quoteInto('logg_type = ?', 'P'));
				break;
				case 'O': 
					$select = $select->where($adapter->quoteInto('logg_type = ?', 'O'));
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
	public function getLoggByVaktrap($vaktrapId=0,$patientId=0, $status=true, $type = 'E')
	{

		if($patientId <= 0 && $vaktrapId <= 0) {

            return array();
        }


        $select = $this->_getResource()->select()
            ->from(array('l'=>'logg'))
            ->where('logg_patientID = ?', $patientId)
            ->where('logg_vaktrapID = ?', $vaktrapId)
            ->join(
                array('u' => 'user'),
                'l.logg_userID = u.user_id',
                array(
                    'user_fname',
                    'user_mname',
                    'user_lname'
                )
            )
            ->order(array('l.logg_id ASC'))
            ->setIntegrityCheck(false);
            

		if (! is_null($type)) {
			$select->where('logg_type != ?', $type);
		}

        if(!is_null($status)) {
            $status = $status ? 'yes' : 'no';
            $select->where('logg_status = ?', $status);
        }



		$stmt = $select->query();
		$result = $stmt->fetchAll();

        return $this->decryptData($result, array_merge($this->_tableField, array(
            'user_fname' => false,
            'user_mname' => false,
            'user_lname' => false
        )));
	}
	public function getLoggByVaktrape($vaktrapId=0,$patientId=0, $status=true, $type = 'E')
	{

		if($patientId <= 0 && $vaktrapId <= 0) {

			return array();
		}


		$select = $this->_getResource()->select()
			->from(array('l'=>'logg'))
			->where('logg_patientID = ?', $patientId)
			->where('logg_vaktrapID = ?', $vaktrapId)
			->join(
				array('u' => 'user'),
				'l.logg_userID = u.user_id',
				array(
					'user_fname',
					'user_mname',
					'user_lname'
				)
			)
			->order(array('l.logg_id ASC'))
			->setIntegrityCheck(false);


		if (! is_null($type)) {
			$select->where('logg_type = ?', $type);
		}

		if(!is_null($status)) {
			$status = $status ? 'yes' : 'no';
			$select->where('logg_status = ?', $status);
		}



		$stmt = $select->query();
		$result = $stmt->fetchAll();

		return $this->decryptData($result, array_merge($this->_tableField, array(
			'user_fname' => false,
			'user_mname' => false,
			'user_lname' => false
		)));
	}
	
	public function update(array $dataPost, $key='logg_id',$val='')
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
				case 'logg_userID':
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