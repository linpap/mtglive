<?php
class IndexController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
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
        $ns = new Zend_Session_Namespace('Acl');
		$loggedinsession = $ns->loggedIn; 
		if($loggedinsession)
		{
			$this->_redirect('default');
		}
		else
		{
			$this->view->title = $this->view->translate('Login');
			$this->view->className = 'PTCLEFTSYSTEM';
		}
    }
	
	public function logoutAction()
	{	 
		$session = new Zend_Session_Namespace('Acl');								
		$session->loggedIn = false;	 
		unset($session->useracl);
		unset($session->username);
		Zend_Auth::getInstance()->clearIdentity();
		 
	    $this->_redirect('admin'); 	 
	}
	
	protected function logincheckAction()
    {
        $request = $this->getRequest();
		$valid = new Zend_Validate_NotEmpty();
		if($valid->isValid($request->getParam('username')) != null && $valid->isValid($request->getParam('password')) != null )
		{
			$values = array();
			$values['username'] = $request->getParam('username');
			$values['password'] = $request->getParam('password');	
			
			if($this->_process($values))
			{
				$session = new Zend_Session_Namespace('Acl');
				$acl1 = new KD_Acl();
				$acl = $acl1->getUserAcl($values['username']);
				$session->useracl = $acl;
				$session->username = $values['username'];
				$session->loggedIn = true;
				$this->flashMessenger->setNamespace('success')->addMessage('Login Successfull ');
				$this->_redirect('default');
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage('Invalid username or password.Please try again.!');
				$this->_redirect('admin');
				exit();
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage('Empty username or password.Please try again.!');
			$this->_redirect('admin');
			exit();
		}
    }
	
	protected function _process($values)
    {
        // Get our authentication adapter and check credentials
		$request = $this->getRequest();
        $adapter = $this->_getAuthAdapter();
		$userId = $values['username'];
		$userPassword = $values['password'];	
        $adapter->setIdentity($userId);
        $adapter->setCredential($userPassword);

        $auth = Zend_Auth::getInstance();
        $result = $auth->authenticate($adapter);
        if ($result->isValid()) {
            return true;
        }
        return false;
    }

    protected function _getAuthAdapter()
    {
		try{
			$dbAdapter = Zend_Db_Table::getDefaultAdapter();
			$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
	
			$authAdapter->setTableName('admin_user')
						->setIdentityColumn('username')
						->setCredentialColumn('password')
						->setCredentialTreatment('SHA1(CONCAT(password_salt,?))');
	
			return $authAdapter;
		}
		catch (Exception $e) {
				 $this->flashMessenger->setNamespace('success')->addMessage($e->getMessage());
				 $this->_redirect('admin');
				 exit();
		    }
    }
}