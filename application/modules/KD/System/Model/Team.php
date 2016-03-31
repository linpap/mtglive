<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_System_Model_Team extends KD_System_Model_Team_Collection
{
    protected $_name = 'team';
	protected $_pk = 'team_id';
	protected $_tableField = array('team_deptID'=>false,'team_userID'=>false,'team_team'=>false,'date_of_creation'=>false,'created_by'=>false,'date_of_modification'=>false,'modified_by'=>false);

    public function __construct()
    {
        $this->_init('system/team','team_id');
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
    public function loadList($teamId = 0, $deptId = 0, $type='active')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$select = $db->select()
					->from(array('t'=>'team'),array('team_deptID', 'team_userID', 'team_team'))
					->joinLeft(array('u'=>'user'),'t.team_userID=u.user_id ',array('user_id', 'user_code', 'user_fname', 'user_mname', 'user_lname', 'user_deptid', 'user_phone', 'user_mobile', 'user_email', 'user_date_joining'))
					->where($adapter->quoteInto('t.team_team = ?', $teamId))
					->where($adapter->quoteInto('t.team_deptID = ?', $deptId))
					->setIntegrityCheck(false);
		switch($type)
		{
			case 'archive': 
				$select = $select->where($adapter->quoteInto('user_status = ?', 'no'));
			break;
			default:
				$select = $select->where($adapter->quoteInto('user_status = ?', 'yes'));
			break;
		}
		
		$tableField = $this->_tableField;
		$tableField['user_fname'] = true;
		$tableField['user_mname'] = true;
		$tableField['user_lname'] = true;
		$tableField['user_phone'] = true;
		$tableField['user_mobile'] = true;
		$tableField['user_email'] = true;
			
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$tableField);
	}
	
	public function loadListUserIds($deptId = 0)
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$team_array = array(1,2,3);
		$select = $db->select()
					->from(array('t'=>'team'),array('team_userID'))
					->where($adapter->quoteInto('t.team_deptID = ?', $deptId))
					->where($adapter->quoteInto('t.team_team IN (?)', $team_array))
					->setIntegrityCheck(false);
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$this->_tableField);
	}
	
	public function loadAvailUserByDept($id='0')
	{
		$db = $this->_getResource();
		$adapter = $this->_getResource()->getAdapter();
		$teamList = $this->loadListUserIds($id);
		$team_array = array_merge(array(0),$teamList);
		$select = $db->select()
					->from(array('u'=>'user'),array('user_id', 'user_code', 'user_fname', 'user_mname', 'user_lname', 'user_deptid', 'user_phone', 'user_mobile', 'user_email', 'user_date_joining'))
					->where($adapter->quoteInto('u.user_deptid = ?', $id))
					->where($adapter->quoteInto('u.user_status = ?', 'yes'))
					->where($adapter->quoteInto('u.user_id NOT IN (?)', $team_array))
					->where($adapter->quoteInto('u.user_delete_status = ?', 'no'))
					->setIntegrityCheck(false);
					
		$tableField = $this->_tableField;
		$tableField['user_fname'] = true;
		$tableField['user_mname'] = true;
		$tableField['user_lname'] = true;
		$tableField['user_phone'] = true;
		$tableField['user_mobile'] = true;
		$tableField['user_email'] = true;
		
		$stmt = $select->query();
		$result = $stmt->fetchAll();
		return $this->decryptData($result,$tableField);
	}
	
    public function getUserDetail($id)
    {
        if((int)$id>0)
        {
            return parent::getUserDetail($id);
        }
        else
        {
            KD::throwException('Empty identifier field name');
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
	public function update(array $dataPost, $key='user_id',$val='')
    {
	  if($val!='')
	  {
		$ende = KD::getModel('Core/Endecrypt');
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

}