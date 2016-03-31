<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_System_Model_System extends KD_System_Model_System_Collection
{
    protected $_name = 'system';
	protected $_pk = 'system_id';
	protected $_tableField = array('system_key'=>false,'system_value'=>false,'date_of_creation'=>false,'date_of_modification'=>false,'modified_by'=>false);

    public function __construct()
    {
        $this->_init('system/system','system_id');
    }
    
    public function load($id, $field=null, $tableField=array())
    {
        // If id is not set that show 404 page not found page
        if (is_null($id)) {
            //return $this->noRoutePage();
        }
//        parent::load($id,$field);
//        //$model = new User_Model_DbTable_User();
//        //$model->getAlbum($id);
//        //$row = $this->fetchRow('id = ' . $id);
//        echo '<pre>';
//        print_r($model);exit();
        return parent::load($id,$field, $this->_tableField);
    }
    public function loadAll($_tableField=array())
    {
        return parent::loadAll($this->_tableField);
    }
    public function loadList()
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('s'=>'system'),array('system_id', 'system_key', 'system_value', 'date_of_creation', 'date_of_modification','modified_by'))
					->setIntegrityCheck(false);

		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
    public function getIdentity()
    {
        $db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('s'=>'system'),array('system_id', 'system_key', 'system_value', 'date_of_creation', 'date_of_modification','modified_by'))
					->where($adapter->quoteInto(' system_key = ? ', 'identity'))
					->setIntegrityCheck(false);

		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $result[0]['system_value'];
    }
	public function getEmail()
    {
        $db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('s'=>'system'),array('system_id', 'system_key', 'system_value', 'date_of_creation', 'date_of_modification','modified_by'))
					->where($adapter->quoteInto(' system_key = ? ', 'email'))
					->setIntegrityCheck(false);

		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $result[0]['system_value'];
    }
	public function update(array $dataPost, $key='system_id',$val='')
    {

	  	$record = 0;
		$config = $this->loadAll();
		$adapter = $this->_getResource()->getAdapter();
		foreach($config as $column=>$val)
		{
			$data = array();
			if(isset($dataPost[$val['system_key']]) &&  $dataPost[$val['system_key']]!=''):
				switch($val['system_key'])
				{
					default:
						$data['system_value'] = $dataPost[$val['system_key']];
						$data['date_of_modification'] = date("Y-m-d H:i:s");
						$data['modified_by'] = $_SESSION['Acl']['userID'];
						$where = $adapter->quoteInto(' system_key = ? ', $val['system_key']);
						$record += parent::update($data,$where);
					break;
					
				}
			endif;
		}
		return $record;
    }

}