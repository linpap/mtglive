<?php
 
class User_IndexController extends KD_Controller_Action {

    public function init() {
        //echo 'asdg';exit();
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
	    $pageU = $this->getRequest()->getParam('pageU');
		$pageUA = $this->getRequest()->getParam('pageUA');
		if($pageU<=0){$pageU = 1;}
		if($pageUA<=0){$pageUA = 1;}
		$this->view->title = $this->view->translate('%s List',$this->view->translate('User'));
		
		$userArray = KD::getModel('user/user')->loadPageData($pageU,'active');
		$this->view->userCollection = $userArray;
		
		$userArchiveArray = KD::getModel('user/user')->loadPageData($pageUA,'archive');
		$this->view->userArchiveCollection = $userArchiveArray; 		 
    }
	
	public function searchAction() {
	    $search = $this->getRequest()->getParam('search');
		$session = new Zend_Session_Namespace('Acl');
		
		if(isset($search) && $search!='')
		{
			$ende = KD::getModel('Core/Endecrypt');
			$searchText = $search;
			$search = $ende->getEnc($search);
			$userArray = KD::getModel('user/user')->load($searchText,'user_code');
			$clientArray = KD::getModel('client/client')->load($search,'patient_birk_no');
			if(is_array($userArray) && count($userArray)>0)
			{
				$this->_redirect('/user/info/index/id/'.$userArray['user_id']);
			}
			elseif(is_array($clientArray) && count($clientArray)>0)
			{
				$this->_redirect('/client/info/index/id/'.$clientArray['patient_id']);
			}
			else
			{
			
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('You searched for "%s", but we found no client or treat with this ID',$searchText));
				$this->_redirect($_SERVER['HTTP_REFERER']);
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('You search Text is Empty'));
			$this->_redirect($_SERVER['HTTP_REFERER']);
		} 
    }
	
	public function archiveAction()
	{
		$ids = $this->_archive($_POST,'no');
		if($ids)
		{
			$result = KD::getModel('user/user')->archiveList($ids,'no');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Archived Successfully'));
				$this->_redirect('/user/index/index/t/2');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Archived Error'));
		$this->_redirect('/user/index/index/t/1');
	}
	
	public function deleteAction()
	{
		$ids = $this->_archive($_POST,'no');
		if($ids)
		{
			$result = KD::getModel('user/user')->deleteList($ids,'yes');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Deleted Successfully'));
				$this->_redirect('/user/index/index/t/1');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Deletion Error'));
		$this->_redirect('/user/index/index/t/1');
	}
	
	public function restoreAction()
	{
		$ids = $this->_archive($_POST,'yes');
		if($ids)
		{
			$result = KD::getModel('user/user')->archiveList($ids,'yes');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Restored Successfully'));
				$this->_redirect('/user/index/index/t/1');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Restore Error'));
		$this->_redirect('/user/index/index/t/2');
	}
	
	public function getlistAction() {
		$this->_helper->layout->setLayout('layout_ajax');
	    $key = $this->getRequest()->getParam('key');
		$default = $this->getRequest()->getParam('default');
		$userArray = KD::getModel('user/user')->loadUserByDept($key);
		$this->view->defaultOption = $default;
		$this->view->userCollection = $userArray;
		//print_r($userArray);exit();
    }
	public function getuserAction() {
		$this->_helper->layout->setLayout('layout_ajax');
	    $key = $this->getRequest()->getParam('key');
		$userArray = KD::getModel('user/user')->load($key);
		$this->view->userCollection = $userArray;
		//print_r($userArray);exit();
    }
}

