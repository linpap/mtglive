<?php
class System_InfoController extends KD_Controller_Action {
	protected $_tableField = array('dept_code','dept_name','dept_address1','dept_address2','dept_city','dept_zip','dept_state','dept_country','dept_phone1','dept_phone2','dept_phone3','dept_mail1','dept_mail2','dept_expertise','dept_capacity','dept_ownerid','dept_certificate','dept_date_created','dept_created_by','dept_date_modified',
		'dept_modified_by','dept_status');
    public function init() {
        //echo 'asdg';exit();
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
		
		       
    }

    public function loginAction() {
 
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
		//$this->getResponse()->setRedirect($this->getUrl("system/info/updatedepartment"));
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
}

