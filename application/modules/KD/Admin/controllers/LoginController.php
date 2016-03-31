<?php
class System_LoginController extends KD_Controller_Action {
	protected $_tableField = array('dept_code','dept_name','dept_address1','dept_address2','dept_city','dept_zip','dept_state','dept_country','dept_phone1','dept_phone2','dept_phone3','dept_mail1','dept_mail2','dept_expertise','dept_capacity','dept_ownerid','dept_certificate','dept_date_created','dept_created_by','dept_date_modified',
		'dept_modified_by','dept_status');
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

    public function loginAction() {
 
    }
	public function logoutAction()
	{	 
		$session = new Zend_Session_Namespace('Acl');								
		$session->loggedIn = false;	 
		Zend_Auth::getInstance()->clearIdentity();
		 
	    $this->_helper->redirector('login'); 	 
	}
	
    public function logincheckAction() {

		$request = $this->getRequest();
		$userId = $request->getParam('username');
		$userPassword = $request->getParam('password');	
		$controllerName= $this->getRequest()->getControllerName();
		
		$valid = new Zend_Validate_NotEmpty();
		
   		if($valid->isValid($request->getParam('username')) != null && $valid->isValid($request->getParam('password')) != null ){
	
			$auth   = Zend_Auth::getInstance();
			$db = $this->_getParam('db');
	
			$db1 = Zend_Db_Table::getDefaultAdapter();
			$select = new Zend_Db_Select($db1);
			$select->from('admin_user')
				->where('username = ?', $userId)/*->where('password = ?', $userPassword)*/;
			$result = $select->query();

			$row = $result->fetch();

			$password_salt = $row['password_salt'];
			$temp = trim($password_salt.$userPassword);
			$generated_password = sha1($temp);
			
	
	
			try{
				$adapter = new Zend_Auth_Adapter_DbTable(
					$db,
					'admin_user',
					'username',
					'password',
					'sha1(CONCAT(password_salt,?))'
					);
	
				$adapter->setIdentity($request->getParam('username'));
				$adapter->setCredential($request->getParam('password'));
	
	 
				$auth   = Zend_Auth::getInstance();
				$result = $auth->authenticate($adapter);
				$identity = $auth->getIdentity();
			
				$session = new Zend_Session_Namespace('Acl');				
				
				if ($result->isValid()) {

					$acl1 = new KD_Acl();
					$acl = $acl1->getUserAcl($userId);

			

					$session->useracl = $acl;
					$session->username = $userId;
					$session->loggedIn = true;
		
	                $this->flashMessenger->setNamespace('success')->addMessage('Pålogging vellykket');
					$this->_redirect('system/login/success');
	
				}
				else{
/*					unset($newsession->useracl);
					unset($newsession->username);*/				
					echo "unsuccessfull login";
					$this->flashMessenger->setNamespace('error')->addMessage('Invalid username or password.Please try again.!');
					$this->_redirect('system/login/login');
					exit();
				
				}
				exit();
	
	
			} 
			catch (Exception $e) {
			  echo $e->getMessage()."error";
		    }
		}
		else{
		        $this->flashMessenger->setNamespace('error')->addMessage('Invalid username or password.Please try again.!');
				//$this->_helper->flashMessenger('Invalid ID or password.Please try again.!');
				$this->_redirect('system/login/login');
		}
		
	}
	
    public function createdepartmentAction() { 

		$this->view->title = $this->view->translate('Create Department');
		$this->view->className = 'PTCLEFTSYSTEM'; 
		
		if($_POST)
		{
			$departmentArray = KD::getModel('system/department')->checkDepartment($_POST['dept_code']);
			if($departmentArray)
			{
				$this->flashMessenger->setNamespace('error')->addMessage('Department Code already exist');
			}
			else
			{
				$this->createdepartmentpost($_POST);
			}
		}
	}
	
	public function createdepartmentpost($data) { 

		KD::getModel('system/department')->insert($data);
		$this->flashMessenger->setNamespace('success')->addMessage('Record was Successfully Added');
		$this->_redirect('/system/index/');
	}
	
    public function updatedepartmentAction() { 
		$departmentID = $this->getRequest()->getParam('id');
		$this->view->id = array($departmentID);
		if(isset($departmentID) && $departmentID>0)
		{
			$departmentInfoArray =  KD::getModel('system/department')->load($departmentID);
			$this->view->departmentInfo = $departmentInfoArray;
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage('Invalid Request');
			$this->_redirect('/system/index/');
		}
		if($_POST)
		{
			$departmentArray = KD::getModel('system/department')->checkDepartment($_POST['dept_code']);
			if($departmentArray)
			{
				$this->flashMessenger->setNamespace('error')->addMessage('Department Code already exist');
			}
			else
			{
				$this->updatedepartmentpost($_POST);
			}
		}
	}
    public function updatedepartmentpost($data) { 
	
			$updatedepartmentid =$data["dept_id"]; 
			if(isset($updatedepartmentid) && (int)$updatedepartmentid>0)
			{
				$where = 'dept_id = '.$updatedepartmentid;
				$result = KD::getModel('system/department')->update($data,$where);			
			
				if($result > 0){
					$this->flashMessenger->setNamespace('success')->addMessage('Record was Successfully Updated');
					$this->_redirect('/system/index/');
				}
				else{
					$this->flashMessenger->setNamespace('error')->addMessage('Unsuccessfull Update.Please try again.!');
					$this->_redirect('/system/info/updatedepartmentrequest');
				}	   
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage('Department Updatation Not allowed');
			}    			
	}

    public function registerAction() {	
		
	}
    public function registrationAction() {	

			echo $password_salt = KD::getHelper('system')->getPasswordSalt();	
			echo "  ".$firstname = $_POST['firstname'];
			echo "  ".$lastname = $_POST['lastname'];						
			echo "  ".$username = $_POST['username'];
			echo "  ".$password = $_POST['password'];
			
		if (empty($_POST["username"]) || empty($_POST["password"])) {
			if (empty($_POST["username"])) {
				echo "Missing username";
			}
			else if(empty($_POST["password"])) {
			echo "Missing password";
		    }
			exit();
		}
		else{
			if($username != '' && $password != ''){
			$password_encrypted = sha1($password_salt.$password); 
			$user = 'root';
			$pass = '';
			$conn = new PDO('mysql:host=localhost;dbname=bse_zend', $user, $pass);

			$stmt = $conn->prepare('INSERT INTO admin_user (username, password ,password_salt,firstname,lastname) VALUES (?,?,?,?,?)');
			$res = $stmt->execute(array($username, $password_encrypted, $password_salt,$firstname,$lastname));
			echo "<br>";

				
			if($res == null){

			$this->flashMessenger->setNamespace('error')->addMessage('User allready exists.Please try again.!');
			$this->_redirect('system/login/register');
			$this->_request->setPost(array(
				'someKey' => 'someValue'
			));
			
			}
			else{
			$this->flashMessenger->setNamespace('success')->addMessage('User registration was Successfully done');
			$this->_redirect('system/login/register');
			}
			}
			elseif($password == ''){
			$this->_redirect('system/login/register');
			}
			elseif($username == ''){
			$this->_redirect('system/login/register');
			}
			
		}	
	}
	
    public function successAction() {  }	
		
}