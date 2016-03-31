<?php

class Department_InfoController extends KD_Controller_Action {

    public function init() {
        //echo 'asdg';exit();
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() 
	{
		$this->view->title = $this->view->translate('Department');
		$this->view->className = 'PTCLEFTVAKTRAPORT';
		
		$departmentID = $this->getRequest()->getParam('id');
		if(isset($departmentID) && $departmentID>0 )
		{
			$this->view->id = array($departmentID);
			$departmentInfoArray =  KD::getModel('department/department')->load($departmentID);
			// If they are mannually Adding some Id in address bar which is not exist
			if(!empty($departmentInfoArray))
			{
				$this->view->departmentInfo = $departmentInfoArray;
			}
			else
			{
			
			}
		}
		
		if($_POST)
		{
			$sessionDeptId = $this->getRequest()->getParam('sessionDeptId');
			if(!isset($sessionDeptId))
			{
				$departmentID = $this->getRequest()->getParam('dept_id');
				//$departmentArray = KD::getModel('department/department')->checkDepartment($_POST['dept_code'],$departmentID,'update');
				if(count($departmentArray)>0)
				{
					$this->view->message['error'][]= $this->view->translate('Department Code already exist');
					$departmentInfoArray =  KD::getModel('department/department')->load($departmentID);
					$this->view->departmentInfo = $departmentInfoArray;
				}
				else
				{
					$this->updatedepartmentpost($_POST);
				}
			}
		}
		if($departmentID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/department/index/');
		}
		
	
	}
		
	public function uploadfileAction() {
	
		$this->_helper->layout->setLayout('layout_ajax');
		$userID = $_POST['patient_id'];
		if(isset($userID) && $userID>0 )
		{
			$this->view->userID = $userID;	
			if($_SERVER['REQUEST_METHOD'] == "POST" && isset($userID) && $userID>0)
			{
				$upload_array = array('dept_image');
				if(in_array($_POST['button_type'],$upload_array))
				{
					$fileContent = '';
				    if (is_file($_FILES['file']["tmp_name"])) {
						if($_FILES['file']['size']>0 && $_FILES['file']['type']!='') 
						{
							$userDeptId = KD::getModel('user/user')->getUser($_SESSION['Acl']['userID'],'user_deptid');
							//$documentFlag = $KD::getModel('client/document')->createDocument($file,$userDeptId,$patientId,$buttonType);
							$documentFlag = KD::getModel('client/document')->createDokument($_FILES['file'],$userDeptId,$userID,$_POST['button_type'],true);
							//print_r($documentFlag);exit();
							if(isset($documentFlag) && $documentFlag>0)
							{
								$data = array();
								$data['dept_image'] = $documentFlag;
								KD::getModel('department/department')->update($data,'dept_id',$userID);
								$arrayJson = array('msg'=>'File Uploaded Successfully','status'=>true);
								echo json_encode($arrayJson);
								exit();
							}
							else
							{
								$arrayJson = array('msg'=>$documentFlag,'status'=>false);
								echo json_encode($arrayJson);
								exit();
							}
						}
						else
						{
							$arrayJson = array('msg'=>'There is Some problem while uploading','status'=>false);
							echo json_encode($arrayJson);
							exit();
						}
					}
					else {
						$arrayJson = array('msg'=>'Invlaid File','status'=>false);
						echo json_encode($arrayJson);
						exit();
					}
				}
				else
				{
					$arrayJson = array('msg'=>'Invalid Action'.$_POST['button_type'],'status'=>false);
					echo json_encode($arrayJson);
					exit();
				}
				
			}
			else
			{
				$arrayJson = array('msg'=>'Invalid User','status'=>false);
				echo json_encode($arrayJson);
				exit();
			}		
			
		}
		if($userID<=0)
		{
			$arrayJson = array('msg'=>'Invalid Request','status'=>false);
			echo json_encode($arrayJson);
			exit();
		}
    }
	
	public function updatedepartmentpost($data) { 
	
		$updatedepartmentid =$data["dept_id"]; 
		if(isset($updatedepartmentid) && (int)$updatedepartmentid>0)
		{
			//$where = 'dept_id = '.$updatedepartmentid;
			$result = KD::getModel('department/department')->update($data,'dept_id',$updatedepartmentid);			
		
			if($result > 0){
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Record was Successfully Updated'));
				$this->_redirect('/department/info/index/id/'.$updatedepartmentid);
			}
			else{
				$this->view->message['error'][]= $this->view->translate('Updation error.Please try again.!');
				//$this->_redirect('/system/info/updatedepartmentrequest');
			}	   
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Department Updatation Not allowed'));
		}    			
	}	
        
    

    public function loginAction() {
        //echo 'tsedZTG';exit();
        // action body
    }

    public function index1Action() {

        // action body
    }

}

