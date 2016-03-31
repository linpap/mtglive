<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
abstract class KD_Core_Model_Abstract extends Zend_Db_Table_Abstract {
    
    protected $_resourceName;
    protected $_resourceCollectionName;
    protected $_idFieldName;
    
    protected function _init($resourceModel,$idFieldName=null) {
        if($idFieldName==null)
        {
            KD::throwException('Empty identifier field name For Class => '.$resourceModel);
        }
        $this->_idFieldName = $idFieldName;
        $this->_setResourceModel($resourceModel);
    }
    
    public function getIdFieldName()
    {
        if (empty($this->_idFieldName)) {
            KD::throwException('Empty identifier field name');
        }
        return $this->_idFieldName;
    }
    
    protected function _setResourceModel($resourceName, $resourceCollectionName=null)
    {
        $this->_resourceName = $resourceName;
        if (is_null($resourceCollectionName)) {
            $resourceCollectionName = $resourceName.'_collection';
        }
        $this->_resourceCollectionName = $resourceCollectionName;
    }
    
    protected function _getResource()
    {
        if (empty($this->_resourceName)) {
            KD::throwException('Resource is not set.');
        }

        return KD::getResourceSingleton($this->_resourceName);
    }
    
    protected function load($id, $field=null)
    {
//        echo '<pre>';
//        print_r($this->_getResource()->fetchRow('user_id = ' . $id));
//        $this->_getResource()->insert(array('user_name'=>'abhijt','user_fname'=>'abhijitkumar',
//            'user_lname'=>'patel'));
//        $this->_getResource()->update(array('user_name'=>'abhijt1','user_fname'=>'abhijitkumar1',
//            'user_lname'=>'patel1'),'user_id = ' . $id);
//        exit();
//        $model = new User_Model_DbTable_User();
//        $model->getAlbum($id);
//        echo '<pre>';
//        print_r($model);exit();
//        echo '<pre>';
//        print_r($this->_getResource());
//        $this->getIdFieldName())
        $search = ($field!=null)?$field:$this->getIdFieldName();
		if($this->_getResource()->fetchRow($search."='".$id."'")!=NULL)
		{
        	return $this->_getResource()->fetchRow($search."='".$id."'")->toArray();
		}
		return NULL;
    }
    protected function loadAll()
    {
        return $this->_getResource()->fetchAll()->toArray();
    }

}
?>
