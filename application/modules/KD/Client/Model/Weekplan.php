<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Weekplan extends KD_Client_Model_Weekplan_Collection
{
    protected $_name = 'weekplan';

	protected $_tableField = array(
        'weekplan_patientID'=>false,
        'weekplan_userID'=>false,
        'weekplan_title' => true,
        'weekplan_desc'=>true,
        'weekplan_date'=>false,
        'date_of_creation'=>false,
        'date_of_modification'=>false,
        'created_by'=>false,
        'modified_by'=>false,
        'deleted_at' => false,
    );

    public function __construct()
    {
        $this->_init('client/weekplan','weekplan_id');
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

    public function loadByWeek($clientId, $week, $year) {
        $date = new \DateTime('now', new \DateTimeZone('Europe/Oslo'));
        $date->setISODate($year, $week);
        $start = $date->format('Y-m-d 00:00:00');
        $date->modify('+6 days');
        $end = $date->format('Y-m-d 23:59:59');

        $db = $this->_getResource();

        $select = $db->select()
            ->from(
                array('w'=>'weekplan'),
                array('weekplan_id', 'weekplan_title', 'weekplan_desc', 'weekplan_date')
            )
            ->join(
                array('p'=>'patient'),
                'w.weekplan_patientID=p.patient_id',
                array('patient_code')
            )
            ->where('weekplan_patientID = ?', $clientId)
            ->where('weekplan_date BETWEEN "' . $start . '" AND "' . $end . '"')
            ->where('deleted_at IS NULL')
            ->order(array('weekplan_date ASC','weekplan_id DESC'))
            ->setIntegrityCheck(false);

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $this->decryptData($result,$this->_tableField);
    }

    public function getByDate($date, $department = null) {
        $db = $this->_getResource();
        $date .= '%';
        $select = $db->select()
            ->from(
                array('w'=>'weekplan'),
                array('weekplan_id', 'weekplan_title', 'weekplan_desc', 'weekplan_date')
            )
            ->join(
                array('p'=>'patient'),
                'w.weekplan_patientID=p.patient_id',
                array('patient_code', 'patient_fname', 'patient_mname', 'patient_lname')
            )
            ->where('weekplan_date LIKE ?', $date)
            ->order(array('weekplan_date ASC','weekplan_id DESC'))
            ->setIntegrityCheck(false);

        if(!is_null($department)) {
            $select->where('p.patient_deptID = ?', $department);
        }

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $this->decryptData($result, $this->_tableField + array('patient_fname', 'patient_mname', 'patient_lname'));
    }

	public function loadList($patientId = 0)
	{
		if($patientId > 0)
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('w'=>'weekplan'),array('weekplan_id', 'weekplan_title', 'weekplan_desc', 'weekplan_date'))
						->join(array('p'=>'patient'),'w.weekplan_patientID=p.patient_id',array('patient_code'))
						->where($adapter->quoteInto('weekplan_patientID = ?', $patientId))
						->order(array('weekplan_date ASC','weekplan_id DESC'))
						->setIntegrityCheck(false);

			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
	
	public function loadListByDate($patientId = 0, $fromdate='', $todate='')
	{
		if($patientId > 0 && $fromdate!=''&& $todate!='')
		{
			$db = $this->_getResource();
			$adapter = $this->_getResource()->getAdapter();
			$select = $db->select()
						->from(array('w'=>'weekplan'),array('weekplan_id', 'weekplan_desc', 'weekplan_date'))
						->join(array('p'=>'patient'),'w.weekplan_patientID=p.patient_id',array('patient_code'))
						->where($adapter->quoteInto('weekplan_patientID = ?', $patientId))
						->where($adapter->quoteInto('weekplan_date >= ?', $fromdate))
						->where($adapter->quoteInto('weekplan_date <= ?', $todate))
						->order(array('weekplan_date ASC','weekplan_id ASC'))
						->setIntegrityCheck(false);

			$stmt = $select->query();
			$result = $stmt->fetchAll();
			return $this->decryptData($result,$this->_tableField);
		}
		return array();
	}
    public function loadPageData($patientId, $page=1, $size = 7)
    {
		$select = $this->loadList($patientId);

		$result = $this->loadPage($this->_tableField, $select, $page, $size);
		return $result;
    }
	public function update(array $dataPost, $key='weekplan_id',$val='')
    {
        if($val ==='') {
          return 0;
        }

        $format = KD::getModel('core/format');
        $ende = KD::getModel('core/endecrypt');//print_r($dataPost);

        foreach($this->_tableField as $column=>$encrypt) {
            switch($column) {
                case 'weekplan_date':
                    if(isset($dataPost[$column])) {
                        $data[$column] = $dataPost[$column];
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
    return parent::update($data,$where);
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
				case 'weekplan_date':
					$pdoj = '';
					if(isset($dataPost[$column]))
					{
						$data[$column] = $dataPost[$column];#$format->PrepareDateDB($pdoj);
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

    public function softDelete($id) {
        return $this->update(array('deleted_at' => date('Y-m-d H:i:s')), 'weekplan_id', $id);
    }

    public function getDatesByDepartments(array $departments = null) {
        $sql = 'SELECT DISTINCT DATE(weekplan_date) as date FROM weekplan w
                JOIN patient p ON p.patient_id = w.weekplan_patientID';

        if(!is_null($departments)) {
            foreach($departments as $key => $department) {
                if(is_null($department)) {
                    unset($departments[$key]);
                } else {
                    $departments[$key] = intval($department);
                }
            }

            $sql .= empty($departments) ? '' : ' WHERE p.patient_deptID IN (' . implode(',', $departments) . ')';
        }

        try {
            $adapter = $this->_getResource()->getAdapter();

            $stmt = $adapter->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_COLUMN);
        } catch(\Zend_Db_Exception $e) {
            //TODO: log error
            return array();
        }
    }
}