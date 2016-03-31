<?php

class User_InfoController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() 
	{
		$this->view->title = $this->view->translate('User');
		$this->view->className = 'PTCLEFTVAKTRAPORT';
		
		$userID = $this->getRequest()->getParam('id');
		
		if(isset($userID) && $userID>0 )
		{
			$this->view->id = array($userID);
			$userInfoArray =  KD::getModel('user/user')->load($userID);
			// If they are mannually Adding some Id in address bar which is not exist
			if(!empty($userInfoArray))
			{
				$this->view->userCollection = $userInfoArray;
			}
			else
			{	
				$userID = 0;
			}
		}
		
		if($_POST)
		{
			$userID = $this->getRequest()->getParam('user_id');
			if(isset($userID) && $userID > 0)
			{
				$userArray = KD::getModel('user/user')->checkUser($_POST['user_code'],$userID,'update');
				if(count($userArray)>0)
				{
					$this->view->message['error'][]= $this->view->translate('User already exist');
					$userInfoArray =  KD::getModel('user/user')->load($userID);
					$this->view->userCollection = $userInfoArray;
				}
				else
				{
					$this->updateuserpost($_POST);
				}
			}
		}
		if($userID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/user/index/');
		}
		
	
	}
		
	public function updateuserpost($data) { 

		$updateuserid =$data["user_id"]; 
		if(isset($updateuserid) && (int)$updateuserid>0)
		{
			//$where = 'user_id = '.$updateuserid;
			$result = KD::getModel('user/user')->update($data,'user_id',$updateuserid);			
		
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
