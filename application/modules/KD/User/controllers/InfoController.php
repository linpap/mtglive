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
				$clientDocument = KD::getModel('client/document');
				if(isset($userID) && $userID>0 )
				{
					
					if(!empty($clientDocument))
					{
						$this->view->userCollection['documents'] = $clientDocument->loadList($userID,'user_document');
					}
				}
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
				//$userArray = KD::getModel('user/user')->checkUser($_POST['user_code'],$userID,'update');
				if(count($userArray)>0)
				{
					$this->view->message['error'][]= $this->view->translate('User already exist');
					$userInfoArray =  KD::getModel('user/user')->load($userID);
					$this->view->userCollection = $userInfoArray;
				}
				else
				{
					$data = array();
					$data = $_POST;
					if($_POST['user_role']=='L')
					{
						$data['user_deptid'] = implode(',',$_POST['user_deptidMul']);
					}
					else
					{
						$data['user_deptid'] = $_POST['user_deptid'];
					}
					$this->updateuserpost($data);
				}
			}
		}
		if($userID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/user/index/');
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
				$upload_array = array('user_image','user_document');
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
								if($_POST['button_type']=='user_image')
								{
									$data = array();
									$data['user_image'] = $documentFlag;
									KD::getModel('user/user')->update($data,'user_id',$userID);
								}
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
					$arrayJson = array('msg'=>'Invalid Action','status'=>false);
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
	
	public function updateuserpost($data) { 

		$updateuserid =$data["user_id"]; 
		if(isset($updateuserid) && (int)$updateuserid>0)
		{
			//$where = 'user_id = '.$updateuserid;
			$result = KD::getModel('user/user')->update($data,'user_id',$updateuserid);			
		
			if($result > 0){
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Record was Successfully Updated'));
				$this->_redirect('/user/info/index/id/'.$updateuserid);
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
	
	
	public function getdocumentAction() 
	{
		$imageID = $this->getRequest()->getParam('id');
		$this->_helper->layout->setLayout('layout_ajax');
		if(isset($imageID) && $imageID>0 )
    	{
			try     
			{
				//echo $imageID;exit();
				$imageArray = KD::getModel('client/document')->getDocument($imageID);
				/*** check we have a single image and type ***/
				/*** set the headers and display the image ***/
				//echo $imageArray['document_filename'];exit();
				//header("Content-type: ".$imageArray['document_mimetype']);
				$this->view->mimetype =  $imageArray['document_mimetype']; 
				$this->view->filecontent =  $imageArray['document_filecontent']; 
				/*** output the image ***/
				//echo base64_decode($imageArray['document_filecontent']);
				
				//exit();
			}
			catch(PDOException $e)
			{
				echo false;
				exit();
			}
			catch(Exception $e)
			{
				echo false;
				exit();
			}
		}
		else
		{
			echo false;
			exit();
		}
	}		
}
