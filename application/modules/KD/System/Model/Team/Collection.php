<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_System_Model_Team_Collection extends KD_Core_Model_Abstract
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
    
/*    protected function getUserDetail($id)
    {
        //$search = ($field!=null)?$field:$this->getIdFieldName();
        //return $this->_getResource()->fetchRow($search."='".$id."'")->toArray();
        //print_r($this);exit();
        $db = $this->_getResource();
        $consulta = $db->select()

        ->from(array('u' => 'user'), array('uf'=>'user_fname'))
        ->joinLeft(array('us' => 'users'), 'u.user_id = us.id')
        //->where('us.id = ?', $id)
        //->orwhere('us.id = ?', '3')
        ->setIntegrityCheck(false); // ADD This Line

        $query = $db->fetchAll($consulta)->toArray();
        return $query;
    }*/
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