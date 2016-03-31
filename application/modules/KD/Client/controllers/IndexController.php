<?php
class Client_IndexController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }
    public function indexAction() {
	 	$pageC = $this->getRequest()->getParam('pageC');
		$pageCA = $this->getRequest()->getParam('pageCA');
		if($pageC<=0){$pageC = 1;}
		if($pageCA<=0){$pageCA = 1;}
		
		$this->view->title = $this->view->translate('%s List',$this->view->translate('Client'));
		$this->view->className = 'PTCLEFT';
		
		$clientArray = KD::getModel('client/client')->loadPageData($pageC,'active');
		$this->view->clientCollection = $clientArray;	
		
		$clientArchiveArray = KD::getModel('client/client')->loadPageData($pageCA,'archive');
		$this->view->clientArchiveCollection = $clientArchiveArray;	       
    }
	public function archiveAction()
	{
		$ids = $this->_archive($_POST,'no');
		if($ids)
		{
			$result = KD::getModel('client/client')->archiveList($ids,'no');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Archived Successfully'));
				$this->_redirect('/client/index/index/t/2');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Archived Error'));
		$this->_redirect('/client/index/index/t/1');
	}
	public function deleteAction()
	{
		$ids = $this->_archive($_POST,'no');
		if($ids)
		{
			$result = KD::getModel('client/client')->deleteList($ids,'yes');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Deleted Successfully'));
				$this->_redirect('/client/index/index/t/1');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Deletion Error'));
		$this->_redirect('/client/index/index/t/1');
	}
	
	public function restoreAction()
	{
		$ids = $this->_archive($_POST,'yes');
		if($ids)
		{
			$result = KD::getModel('client/client')->archiveList($ids,'yes');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Restored Successfully'));
				$this->_redirect('/client/index/index/t/1');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Restore Error'));
		$this->_redirect('/client/index/index/t/2');
	}
	
	public function getlistAction() {
		$this->_helper->layout->setLayout('layout_ajax');
	    $key = $this->getRequest()->getParam('key');
		//$default = $this->getRequest()->getParam('default');
		$clientArray = KD::getModel('client/client')->loadClientByDept($key);
		//$this->view->defaultOption = $default;
		$this->view->clientCollection = $clientArray;
    }
	
	public function getlistnonvaktrapAction() {
		$this->_helper->layout->setLayout('layout_ajax');
	    $key = $this->getRequest()->getParam('key');
		//$default = $this->getRequest()->getParam('default');
		//Load only Those Client who is not having vaktrapport report active yet
		$clientArray = KD::getModel('client/client')->loadClientByDeptHasNoVakt($key);
		//$this->view->defaultOption = $default;
		$this->view->clientCollection = $clientArray;
    }
	

}