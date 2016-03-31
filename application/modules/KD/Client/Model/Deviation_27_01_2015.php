<?php
/**
 * Created by PhpStorm.
 * User: sarwar
 * Date: 11/24/15
 * Time: 11:49 AM
 */

class KD_Client_Model_Deviation extends KD_Client_Model_Deviation_Collection{

    protected $_name = 'deviation';

    protected $_tableField = array(
        'deviation_patientID'=>false,
        'deviation_userID'=>false,
        'deviation_vaktrapID'=>false,
        'deviation_type'=>false,
        'deviation_deptID'=>false,
        'deviation_desc'=>true,
        'correct_deviation'=>true,
        'deviation_proposed_measures'=>true,
        'deviation_decided_measures'=>true,
        'deviation_status'=>false,
        'deviation_locked_at'=>false,
        'deviation_locked_by'=>false,
        'date_of_creation'=>false,
        'date_of_modification'=>false,
        'created_by'=>false,
        'modified_by'=>false);
    public function __construct()
    {
        $this->_init('client/deviation','deviation_id');
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
    public function loadListByDate($patientId = 0, $type='active', $Deviation_type='',$fromDate='',$toDate='')
    {
        if($patientId > 0 && $fromDate!='' && $toDate!='')
        {
            $db = $this->_getResource();
            $adapter = $this->_getResource()->getAdapter();
            $select = $db->select()
                ->from(array('d'=>'deviation'))
                ->where($adapter->quoteInto('deviation_patientID = ?', $patientId))
                ->where($adapter->quoteInto('deviation_locked_at >= ?', $fromDate))
                ->where($adapter->quoteInto('deviation_locked_at <= ?', $toDate))
                ->order(array('deviation_id ASC'))
                ->setIntegrityCheck(false);
            switch($type)
            {
                case 'archive':
                    $select = $select->where($adapter->quoteInto('deviation_status = ?', 'no'));
                    break;
                case 'active':
                    $select = $select->where($adapter->quoteInto('deviation_status = ?', 'yes'));
                    break;
                case 'all':
                    break;
                default:
                    //You can get List of all Maal List
                    break;
            }
            switch($Deviation_type)
            {
                case 'M':
                    $select = $select->where($adapter->quoteInto('deviation_type = ?', 'M'));
                    break;
                case 'P':
                    $select = $select->where($adapter->quoteInto('deviation_type = ?', 'P'));
                    break;
                case 'O':
                    $select = $select->where($adapter->quoteInto('deviation_type = ?', 'O'));
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
    public function loadList($patientId = 0, $type='active', $Deviation_type='')
    {
        if($patientId > 0)
        {
            $db = $this->_getResource();
            $adapter = $this->_getResource()->getAdapter();
            $select = $db->select()
                ->from(array('d'=>'deviation'))
                ->where($adapter->quoteInto('deviation_patientID = ?', $patientId))
                ->order(array('deviation_id ASC'))
                ->setIntegrityCheck(false);
            switch($type)
            {
                case 'archive':
                    $select = $select->where($adapter->quoteInto('deviation_status = ?', 'no'));
                    break;
                case 'active':
                    $select = $select->where($adapter->quoteInto('deviation_status = ?', 'yes'));
                    break;
                case 'all':
                    break;
                default:
                    //You can get List of all Maal List
                    break;
            }
            switch($Deviation_type)
            {
                case 'M':
                    $select = $select->where($adapter->quoteInto('deviation_type = ?', 'M'));
                    break;
                case 'P':
                    $select = $select->where($adapter->quoteInto('deviation_type = ?', 'P'));
                    break;
                case 'O':
                    $select = $select->where($adapter->quoteInto('deviation_type = ?', 'O'));
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
    // Get Deviation By Vaktrapport Id
    public function getDeviationByVaktrap($vaktrapId=0,$patientId=0, $status=true, $type = 'E')
    {


        if($patientId <= 0 && $vaktrapId <= 0) {

            return array();
        }


        $select = $this->_getResource()->select()
            ->from(array('d'=>'deviation'))
            ->where('deviation_patientID = ?', $patientId)
            ->where('deviation_vaktrapID = ?', $vaktrapId)
            ->join(
                array('u' => 'user'),
                'd.deviation_userID = u.user_id',
                array(
                    'user_fname',
                    'user_mname',
                    'user_lname'
                )
            )
            ->order(array('d.deviation_id ASC'))
            ->setIntegrityCheck(false);



        if (! is_null($type)) {
            $select->where('deviation_type != ?', $type);
        }

        if(!is_null($status)) {
            $status = $status ? 'yes' : 'no';
            $select->where('deviation_status = ?', $status);
        }





        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $this->decryptData($result, array_merge($this->_tableField, array(
            'user_fname' => false,
            'user_mname' => false,
            'user_lname' => false
        )));
    }
    public function getDeviationByVaktrape($vaktrapId=0,$patientId=0, $status=true, $type = 'E')
    {

        if($patientId <= 0 && $vaktrapId <= 0) {

            return array();
        }


        $select = $this->_getResource()->select()
            ->from(array('d'=>'deviation'))
            ->where('deviation_patientID = ?', $patientId)
            ->where('Deviation_vaktrapID = ?', $vaktrapId)
            ->join(
                array('u' => 'user'),
                'd.deviation_userID = u.user_id',
                array(
                    'user_fname',
                    'user_mname',
                    'user_lname'
                )
            )
            ->order(array('l.deviation_id ASC'))
            ->setIntegrityCheck(false);


        if (! is_null($type)) {
            $select->where('deviation_type = ?', $type);
        }

        if(!is_null($status)) {
            $status = $status ? 'yes' : 'no';
            $select->where('deviation_status = ?', $status);
        }



        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $this->decryptData($result, array_merge($this->_tableField, array(
            'user_fname' => false,
            'user_mname' => false,
            'user_lname' => false
        )));
    }

    public function update(array $dataPost, $key='deviation_id',$val='')
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
                case 'deviation_userID':
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