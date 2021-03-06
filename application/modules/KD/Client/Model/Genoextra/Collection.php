<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Genoextra_Collection extends KD_Core_Model_Abstract
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

	public function insert(array $data)
    {
		$db = $this->_getResource();
        return $db->insert($data);
    }
	
    protected function checkClient($code=0, $patient_id = 0, $action='insert')
    {
		if($action=='insert')
		{
			$result =$this->load($code,'patient_birk_no');
			if($result!=NULL)
			{	
				return $result;
			}
		}
		else
		{
			$adapter = $this->_getResource()->getAdapter();
			$select = $adapter->select()->from('patient')->where($adapter->quoteInto('patient_birk_no = ?', $code))->where($adapter->quoteInto('patient_id <> ?', $patient_id));
			$stmt = $select->query();
			$result = $stmt->fetchAll();
			if($result!=NULL)
			{	
				return $result;
			}
		}
        return NULL;
    }	
	public function update(array $data, $where='')
    {	
		$db = $this->_getResource();
        return $db->update($data, $where);
	}	
}