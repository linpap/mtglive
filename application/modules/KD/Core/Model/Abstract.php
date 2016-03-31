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
    
    protected function load($id, $field=null, $tableField=array())
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
		$adapter = $this->_getResource()->getAdapter();
		$select = $this->_getResource()->fetchRow($adapter->quoteInto(' '.$search.' = ?', $id));
		if($this->_getResource()->fetchRow($adapter->quoteInto(' '.$search.' = ?', $id))!=NULL)
		{
			$data = $this->_getResource()->fetchRow($adapter->quoteInto(' '.$search.' = ?', $id))->toArray();
			$dataRes = array();
			$dataRes[] = $data;
			$dataReal = $this->decryptData($dataRes, $tableField);	
			if(count($dataReal)>0)$dataReal= $dataReal[0];
			return $dataReal;
		}
		return NULL;
    }
    protected function loadAll($tableField=array())
    {
        $data = $this->_getResource()->fetchAll()->toArray();
		$dataRes = $this->decryptData($data, $tableField);
		return $dataRes;
		
    }
	protected function decryptData($data, $tableField=array())
	{
		$dataRes = array();
		$dataEnc = array();
		$dataRes = $data;
		$ende = KD::getModel('Core/Endecrypt');
		foreach($tableField as $column=>$encrypt)
		{
			if($encrypt)
			{
				$dataEnc[] = $column;
			}
		}
		
		for($i=0;$i<count($data);$i++)
		{
		  for($j=0;$j<count($dataEnc);$j++)
		  {
			if(isset($data[$i][$dataEnc[$j]]) && $data[$i][$dataEnc[$j]]!='')
			{
				#echo $dataEnc[$j] . '<br>';
                $dataRes[$i][$dataEnc[$j]] = $ende->getDec($data[$i][$dataEnc[$j]]);
                #echo $data[$i][$dataEnc[$j]] . ' => ' . $ende->getDec($data[$i][$dataEnc[$j]]) . '<br>';
			}
			/*else
			{
				$dataRes[$i][$dataEnc[$j]] = '';
			}*/
		  }
		}
		return $dataRes;
	}
	protected function loadPage($tableField=array(), $select=array(), $page=1, $pageSize = 0)
    {
		$format = KD::getModel('core/format');
		if($pageSize>0)
		{
			$page_size = $pageSize;
		}
		else
		{
			$page_size = $format->PageSize();
		}
		$paginator = Zend_Paginator::factory($select);
		$paginator->setCurrentPageNumber($page);
		$paginator->setItemCountPerPage($page_size);
		
		return $paginator;
    }

}
?>
