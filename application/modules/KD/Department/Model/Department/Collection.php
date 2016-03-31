<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Department_Model_Department_Collection extends KD_Core_Model_Abstract
{
    protected $_resourceName;
    protected $_resourceCollectionName;
    protected $_idFieldName;
	protected $db;
	
    
    protected function _init($resourceModel,$idFieldName=null) {
        if($idFieldName==null)
        {
            KD::throwException('Empty identifier field name For Class => '.$resourceModel);
        }
        parent::_init($resourceModel, $idFieldName);
    }
    
    public function getIdFieldName()
    {
        if (empty($this->_idFieldName)) {
            KD::throwException('Empty identifier field name');
        }
        return $this->_idFieldName;
    }
    
    protected function _getResource()
    {
        if (empty($this->_resourceName)) {
            KD::throwException('Resource is not set.');
        }

        return KD::getResourceSingleton($this->_resourceName);
    }
    
    protected function checkDepartment($code=0, $dept_id = 0, $action='insert')
    {
        //$search = ($field!=null)?$field:$this->getIdFieldName();
        //return $this->_getResource()->fetchRow($search."='".$id."'")->toArray();
        //print_r($this);exit();
        //$db = $this->_getResource();
        //$result = $db->fetchRow('dept_code = '. $code);
		//$where = $this->_getResource()->getAdapter()->quoteInto('dept_code = ?', $code);
		//$result = $this->_getResource()->fetchRow(array($where));
		if($action=='insert')
		{
			$result =$this->load($code,'dept_code');
			if($result!=NULL)
			{	
				return $result;
			}
		}
		else
		{
			//$db = $this->_getResource()->fetchRow($search."='".$id."'")->toArray();
			//echo 'sadf';exit();
			//$where = $db->select()->where('dept_code = ?', $code)->where('dept_id <> ?', $dept_id);
			//print_r($where);exit();
			//$db->select($where);
			//echo $where = $this->_getResource()->getAdapter()->quoteInto('dept_code = ?', $code)->quoteInto('dept_code <> ?', $code);
			//$select = $this->_getResource()->getAdapter()->select()->from('department')->where('dept_code = ?', $filter->filter($code))->where('dept_id <> ?', $filter->filter($dept_id));
			$adapter = $this->_getResource()->getAdapter();
			$select = $adapter->select()->from('department')->where($adapter->quoteInto('dept_code = ?', $code))->where($adapter->quoteInto('dept_id <> ?', $dept_id));
			//$db->quoteInto('user_id = ?', $form['id'])
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			if($result!=NULL)
			{	
				return $result;
			}
		}
        //->from(array('d' => 'department'), array('uf'=>'user_fname'))
        //->joinLeft(array('us' => 'users'), 'u.user_id = us.id')
        //->where('us.id = ?', $id)
        //->orwhere('us.id = ?', '3')
        //->setIntegrityCheck(false); // ADD This Line

        //$query = $db->fetchAll($consulta)->toArray();
        return NULL;
    }
	public function insert(array $data)
    {
		$db = $this->_getResource();
        return $db->insert($data);
    }

    /*public function loadAll()
    {
        $db = $this->_getResource();
        $select = $db->select()
        ->from(array('d' => 'department'),array('id'=>'dept_id','code'=>'dept_code','name'=>'dept_name','city'=>'dept_city','state'=>'dept_state'))
        ->setIntegrityCheck(false); 
		
        $query = $db->fetchAll($select)->toArray();
		//print_r($query);exit();
        return $query;
    }
	
    protected function loadId($id)
    {
        $db = $this->_getResource();
        $select = $db->select()
        ->from(array('d' => 'department'))
        ->where('d.dept_id = ?', $id)
        ->setIntegrityCheck(false);
		//$result = self::$db->fetchRow($select);
        $query = $db->fetchRow($select)->toArray();
		//print_r($query);//exit();
        return $query;
    }*/
	public function update(array $data, $where='')
    {	
		$db = $this->_getResource();
        return $db->update($data, $where);
	}				
}