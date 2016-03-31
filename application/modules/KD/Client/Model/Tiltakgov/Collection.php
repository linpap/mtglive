<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Client_Model_Tiltakgov_Collection extends KD_Core_Model_Abstract
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
	
	public function update(array $data, $where='')
    {	
		$db = $this->_getResource();
        return $db->update($data, $where);
	}	
}