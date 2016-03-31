<?php

class Client_InfoController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() 
	{
		$format = KD::getModel('core/format');
		$this->view->className = 'PTCLEFTVAKTRAPORT';
		$clientID = $this->getRequest()->getParam('id');//exit();
		$selected = $this->getRequest()->getParam('s');//exit();
		$clientModel = KD::getModel('client/client');
		if(isset($clientID) && $clientID>0 )
		{
	
			$this->view->id = $clientID;
			$this->view->selected = $selected;
			$clientInfoArray =  $clientModel->load($clientID);
			// If they are mannually Adding some Id in address bar which is not exist
			if(!empty($clientInfoArray))
			{
				$this->view->clientInfo = $clientInfoArray;
				$this->view->title = $this->view->translate('Client Info').'  '.$clientModel->getClient($clientID,'name').'';
			}
			else
			{
				$clientID = 0;
			}
			
		}
		
		if($_POST)
		{
			$clientID = $this->getRequest()->getParam('patient_id');
			if(isset($clientID) && $clientID>0 )
			{				
				$this->view->id = $clientID;
				$this->view->title = $clientModel->getClient($clientID,'name');
				$clientArray = KD::getModel('client/client')->checkClient($_POST['patient_birk_no'],$clientID,'update');
				if(count($clientArray)>0)
				{
					$this->view->message['error'][]= $this->view->translate('Client Birk No. already exist');
					$clientInfoArray =  KD::getModel('client/client')->load($clientID);
					$this->view->clientInfo = $clientInfoArray;
				}
				else
				{
					$this->updateclientpost($_POST);
				}
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
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
	
	public function uploadfileAction() {
	
		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $_POST['patient_id'];
		if(isset($clientID) && $clientID>0 )
		{
			$this->view->clientID = $clientID;	
			if($_SERVER['REQUEST_METHOD'] == "POST" && isset($clientID) && $clientID>0)
			{
				$upload_array = array('patient_image');
				if(in_array($_POST['button_type'],$upload_array))
				{
					$fileContent = '';
				    if (is_file($_FILES['file']["tmp_name"])) {
						if($_FILES['file']['size']>0 && $_FILES['file']['type']!='') 
						{
							$userDeptId = KD::getModel('client/client')->getClient($clientID,'patient_deptID');
							//$documentFlag = $KD::getModel('client/document')->createDocument($file,$userDeptId,$patientId,$buttonType);
							$documentFlag = KD::getModel('client/document')->createDokument($_FILES['file'],$userDeptId,$clientID,$_POST['button_type'],true);
							//print_r($documentFlag);exit();
							if(isset($documentFlag) && $documentFlag>0)
							{
								$data = array();
								$data['patient_image'] = $documentFlag;
								KD::getModel('client/client')->update($data,'patient_id',$clientID);
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
	
	public function updateclientpost($data) { 
	
		$updateclientid =$data["patient_id"]; 
		if(isset($updateclientid) && (int)$updateclientid>0)
		{
			//$where = 'patient_id = '.$updateclientid;
			$result = KD::getModel('client/client')->update($data,'patient_id',$updateclientid);			
		
			if($result > 0){
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Record was Successfully Updated'));
				$this->_redirect('/client/index/');
			}
			else{
				$this->view->message['error'][]= $this->view->translate('Updation error.Please try again.!');
			}	   
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Client Updatation Not allowed'));
		}    			
	}		
}
