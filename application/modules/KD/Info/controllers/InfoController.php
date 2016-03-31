<?php

class Info_InfoController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() 
	{
		$this->view->title = $this->view->translate('Info');
		$this->view->className = 'PTCLEFTVAKTRAPORT';
		
		$infoID = $this->getRequest()->getParam('id');
		$infoType = $this->getRequest()->getParam('type');
		if(isset($infoType) && $infoType!='' )
		{
			$this->view->id = array($infoID);
			$infoArray =  KD::getModel('info/info')->load($infoID);
			// If they are mannually Adding some Id in address bar which is not exist
			if(!empty($infoArray))
			{
				$this->view->infoCollection = $infoArray;
			}
			else
			{	
				$infoID = 0;
				$infoArray = array();
				$infoArray['info_id'] = 0;
				$infoArray['info_title'] = '';
				$infoArray['info_desc'] = '';
				$infoArray['info_status'] = 'yes';
				$infoArray['info_deptIDs'] = '';
				$infoArray['info_type'] = $infoType;
				$this->view->infoCollection = $infoArray;
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem while adding or editing Information'));
			$this->_redirect('/info/index/');
		}
	}
	
	public function addAction()
	{	
		$infoID = $this->getRequest()->getParam('id');
		$infoType = $this->getRequest()->getParam('type');
		if(isset($infoID) &&  $infoID == $_POST['info_id'] && isset($infoType) && $infoType!='' && $infoType==$_POST['info_type'])
		{
			$infoArray = array();
			$infoArray['info_title'] = $_POST['info_title'];
			$infoArray['info_desc'] = $_POST['info_desc'];
			$infoArray['info_status'] = 'yes';
			if(is_array($_POST['info_deptIDs']) && count($_POST['info_deptIDs'])>0)
			{
				$infoArray['info_deptIDs'] = implode(',',$_POST['info_deptIDs']);
			}
			else
			{
				$infoArray['info_deptIDs'] = '';
			}
			$infoArray['info_type'] = $_POST['info_type'];
			//echo '<pre>';print_r($infoArray);exit();
			if($infoID>0)
			{
				// Update
				KD::getModel('info/info')->update($infoArray,'info_id',$infoID);
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Record was Successfully Updated'));
				$this->_redirect('/info/index/');
			}
			else
			{	
				
				// add
				KD::getModel('info/info')->insert($infoArray);
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Record was Successfully Added'));
				$this->_redirect('/info/index/');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem while adding or editing Information'));
		$this->_redirect('/info/index/');
	}
	
	public function updateuserpost($data) { 

		$updateinfoid = $data["info_id"]; 
		if(isset($updateinfoid) && (int)$updateinfoid>0)
		{
			//$where = 'user_id = '.$updateuserid;
			$result = KD::getModel('info/info')->update($data,'info_id',$updateinfoid);			
		
			if($result > 0){
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Record was Successfully Updated'));
				$this->_redirect('/user/index/');
			}
			else{
				$this->view->message['error'][]= $this->view->translate('Updation error.Please try again.!');
			}	   
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('User Updatation Not allowed'));
		}    			
	}		
}
