<?php
class ForgotController extends KD_Controller_Action {
    public function init() {
 	 
        //parent::init();
		$this->flashMessenger = $this->_helper->getHelper('FlashMessenger');
		
		$this->_helper->layout->getView()->message = array('success','error');
		if ($this->flashMessenger->setNamespace('success')->hasMessages()):
			$this->_helper->layout->getView()->message['success'] = array();
			foreach ($this->flashMessenger->getMessages() as $msgSuccess):
				$this->_helper->layout->getView()->message['success'][] = $msgSuccess;
			endforeach;
		endif;
		// Success Message Collection
		if ($this->flashMessenger->setNamespace('error')->hasMessages() || $this->flashMessenger->getCurrentMessages()):
			$this->_helper->layout->getView()->message['error'] = array();
			foreach ($this->flashMessenger->getMessages() as $msgError):
				$this->_helper->layout->getView()->message['error'][] = $msgError;
			endforeach;
			foreach ($this->flashMessenger->getCurrentMessages() as $msgError):
				$this->_helper->layout->getView()->message['error'][] = $msgError;
			endforeach;
		endif;		
    }

    public function indexAction() {
		  
    }
	
    public function postAction() {

		$request = $this->getRequest();
		$userId = $request->getParam('username');
		$valid = new Zend_Validate_NotEmpty();
		
   		if($valid->isValid($request->getParam('username')) != null){
	
			try{
				$userArray = KD::getModel('user/user')->load($userId,'user_login');
				
				if (is_array($userArray) && count($userArray)>0) {
					$checkFlag = KD::getModel('user/user')->changepassword($userArray['user_id']);	
					if(is_array($checkFlag) && count($checkFlag)>0)
					{
						$data = array();$data['password'] = $checkFlag['user_password'];$data['name'] = KD::getModel('user/user')->getUser($userArray['user_id']);
						parent::sendEmail($userId,'Forgot Password','ForgetPassword.phtml',$data);
						$this->flashMessenger->setNamespace('success')->addMessage('Pålogging vellykket');
					}
					else
					{
						$this->flashMessenger->setNamespace('success')->addMessage('Problem While Changing Password! Try Again');
					}
					$this->_redirect('admin');
	
				}
				else{
					$this->flashMessenger->setNamespace('error')->addMessage('Invalid username.Please try again.!');
					$this->_redirect('admin');
				
				}
			} 
			catch (Exception $e) {
			  echo $e->getMessage()."error";exit();
		    }
		}
		else{
		        $this->flashMessenger->setNamespace('error')->addMessage('Invalid username.Please try again.!');
				$this->_redirect('admin');
		}
		
	}
	
    public function successAction() {  }	
		
}