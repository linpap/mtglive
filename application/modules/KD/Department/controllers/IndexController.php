<?php

class Department_IndexController extends KD_Controller_Action {
 
    public function init() {
        //echo 'asdg';exit();
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
		$pageD = $this->getRequest()->getParam('pageD');
		if($pageD<=0)
		{
			$pageD = 1;
		}
		$this->view->title = $this->view->translate('%s List',$this->view->translate('Department'));
		$this->view->className = 'PTCLEFTVAKTRAPORT';
		$departmentPagination = KD::getModel('department/department')->loadPageData($pageD);
		$this->view->departmentCollection = $departmentPagination;        
    }

	public function archiveAction()
	{
		$ids = $this->_archive($_POST,'no');
		if($ids)
		{
			$result = KD::getModel('department/department')->archiveList($ids,'no');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Archived Successfully'));
				$this->_redirect('/department/index/');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Archived Error'));
		$this->_redirect('/department/index/');
	}
	
	public function restoreAction()
	{
		$ids = $this->_archive($_POST,'yes');
		if($ids)
		{
			$result = KD::getModel('department/department')->archiveList($ids,'yes');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Restored Successfully'));
				$this->_redirect('/department/index/');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Restore Error'));
		$this->_redirect('/department/index/');
	}
}