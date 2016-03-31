<?php
class Client_AdditionalController extends KD_Controller_Action {

    public function init() {
        // Initialize action controller here 
        parent::init();
    }
    /*public function indexAction() {
		$this->_helper->layout->setLayout('layout_ajax');$clientID = $this->getRequest()->getParam('id');//exit();
		if(isset($clientID) && $clientID>0 )
		{
			$this->view->id = $clientID;
			$pageM = $this->getRequest()->getParam('pm');
			$pageMA = $this->getRequest()->getParam('pma');
			$pageMD = $this->getRequest()->getParam('pmc');
			if($pageM<=0){$pageM = 1;}
			if($pageMA<=0){$pageMA = 1;}
			if($pageMD<=0){$pageMD = 1;}
			
			$maalArray = KD::getModel('client/maal')->loadPageData($clientID, $pageM, 'active');
			$this->view->maalCollection = $maalArray;	

			$maalArchiveArray = KD::getModel('client/maal')->loadPageData($clientID, $pageMA, 'archive');
			$this->view->maalArchiveCollection = $maalArchiveArray;
			
			$maalAchivedArray = KD::getModel('client/maal')->loadPageData($clientID, $pageMD, 'achived');
			$this->view->maalAchivedCollection = $maalAchivedArray;
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }*/
	public function uploadfileAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $_POST['patient_id'];//exit();
		if(isset($clientID) && $clientID>0 )
		{
			$this->view->clientID = $clientID;	
			if($_SERVER['REQUEST_METHOD'] == "POST" && isset($clientID) && $clientID>0)
			{
				$upload_array = array('measurement','decisioin_welfare', 'decisioin_ppt', 'decisioin_school', 'decisioin_other','refreat_welfare','refreat_school','refreat_psychiatry','refreat_other','history_welfare','history_school','history_psychiatry','history_other','investigation_neuro','investigation_hospital','investigation_bup','investigation_ppt','investigation_other','others_letter','others_complain','others_nav','others_job','others_economy','others_other');
				if(in_array($_POST['button_type'],$upload_array))
				{
					$fileContent = '';
				    if (is_file($_FILES['file']["tmp_name"])) {
						$fp  = fopen($_FILES['file']["tmp_name"], 'r');
						$fileContent = fread($fp, $_FILES['file']["size"]);
						fclose($fp);
					}
					else {
						$arrayJson = array('msg'=>'Invlaid File','status'=>false);
						echo json_encode($arrayJson);
						exit();
					}
					
					$userDeptId = KD::getModel('user/user')->getUser($_SESSION['Acl']['userID'],'user_deptid');
					//createDokument
				  if($_FILES['file']['size']>0 && $_FILES['file']['type']!='') 
				  {
						$userDeptId = KD::getModel('user/user')->getUser($_SESSION['Acl']['userID'],'user_deptid');
						//$documentFlag = $KD::getModel('client/document')->createDocument($file,$userDeptId,$patientId,$buttonType);
						$documentFlag = KD::getModel('client/document')->createDokument($_FILES['file'],$userDeptId,$_POST['patient_id'],$_POST['button_type']);
						if($documentFlag)
						{
							$arrayJson = array('msg'=>'File Uploaded Successfully','status'=>true);
							echo json_encode($arrayJson);
							exit();
						}
						else
						{
							$arrayJson = array('msg'=>'There is Some problem while uploading','status'=>false);
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
				else
				{
					$arrayJson = array('msg'=>'Invalid Action','status'=>false);
					echo json_encode($arrayJson);
					exit();
				}
				
			}
			else
			{
				$arrayJson = array('msg'=>'Invalid Client','status'=>false);
				echo json_encode($arrayJson);
				exit();
			}		
			
		}
		if($clientID<=0)
		{
			$arrayJson = array('msg'=>'Invalid Request','status'=>false);
			echo json_encode($arrayJson);
			exit();
		}
    }
	public function placementAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		$clientModel = KD::getModel('client/client');
		$this->view->clientID = $clientID;
		if(isset($clientID) && $clientID>0 )
		{
			$clientInfoArray =  $clientModel->load($clientID);
			if(!empty($clientInfoArray))
			{
				$this->view->clientInfo = $clientInfoArray;
			}
			else
			{
				$clientInfoArray = array('patient_location'=>'');
				$this->view->clientInfo = $clientInfoArray;
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function placementpostAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$this->view->clientID = $clientID;
		$clientModel = KD::getModel('client/client');
		$clientArray = $clientModel->load($clientID);
		if(isset($clientID) && $clientID>0 && $_POST['patient_id'] == $clientID && !empty($clientArray))
		{
			$clientDetailArray =  $clientModel->update($_POST['placement'],'patient_id',$clientID);
			$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Placement Saved Successfully',$this->view->translate('Placement')));
			$this->_redirect('/client/info/index/s/p/id/'.$clientID);
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function decisionAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$clientDocument = KD::getModel('client/document');
		if(isset($clientID) && $clientID>0 )
		{
			
			if(!empty($clientDocument))
			{
				$this->view->documentWelfare = $clientDocument->loadList($clientID,'decisioin_welfare');
				$this->view->documentPpt = $clientDocument->loadList($clientID,'decisioin_ppt');
				$this->view->documentSchool = $clientDocument->loadList($clientID,'decisioin_school');
				$this->view->documentOther = $clientDocument->loadList($clientID,'decisioin_other');
			}
			else
			{
				
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function detailAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$this->view->clientID = $clientID;
		$clientInfoModel = KD::getModel('client/clientdetail');
		if(isset($clientID) && $clientID>0 )
		{
			$clientDetailArray =  $clientInfoModel->load($clientID,'patient_detail_patientID');
			// If they are mannually Adding some Id in address bar which is not exist
			if(!empty($clientDetailArray))
			{
				$this->view->clientDetail = $clientDetailArray;
			}
			else
			{
				$clientInfoArray = array('patient_detail_patientID'=>0,'patient_detail_location'=>'','patient_detail_desease'=>'','patient_detail_allergy'=>'','patient_detail_motorskill'=>'','patient_detail_aids'=>'','patient_detail_diet'=>'','patient_detail_hegiene'=>'','patient_detail_diagnoses'=>'','patient_detail_cognitive'=>'','patient_detail_mental'=>'','patient_detail_rushi'=>'','patient_detail_social'=>'','patient_detail_school'=>'','patient_detail_ppt'=>'','patient_detail_religion'=>'','patient_detail_language'=>'','patient_detail_interest'=>'','patient_detail_support'=>'','patient_detail_nav'=>'','patient_detail_economy'=>'','patient_detail_bup_dps'=>'','patient_detail_others'=>'');
				$this->view->clientDetail = $clientInfoArray;
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function detailpostAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$this->view->clientID = $clientID;
		$clientInfoModel = KD::getModel('client/clientdetail');
		$clientArray = KD::getModel('client/client')->load($clientID);
		if(isset($clientID) && $clientID>0  && $_POST['patient_id'] == $clientID && !empty($clientArray))
		{
			$clientExist = KD::getModel('client/clientdetail')->checkClientDetail($clientID);
			if($clientExist)
			{
				$clientDetailArray =  $clientInfoModel->update($_POST['detail'],'patient_detail_patientID',$clientID);
			}
			else
			{
				$data = $_POST['detail'];$data['patient_detail_patientID'] = $_POST['patient_id'];
				$clientDetailArray =  $clientInfoModel->insert($data);
			}
			$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('%s Saved Successfully',$this->view->translate('Client Information')));
			$this->_redirect('/client/info/index/s/d/id/'.$clientID);
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	
	public function addressesAction() {
		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$this->view->clientID = $clientID;
		$clientInfoModel = KD::getModel('client/clientdetail');
		$clientDetailArray = $clientInfoModel->load($clientID,'patient_detail_patientID');
		if(isset($clientID) && $clientID>0 )
		{
			if(!empty($clientDetailArray))
			{
				$this->view->clientDetail = $clientDetailArray;
			}
			else
			{
				$clientDetailArray = array('patient_detail_patientID'=>0,'patient_detail_address1'=>'','patient_detail_address2'=>'','patient_detail_address3'=>'','patient_detail_address4'=>'','patient_detail_address5'=>'','patient_detail_address6'=>'','patient_detail_address7'=>'','patient_detail_address8'=>'','patient_detail_address9'=>'','patient_detail_address10'=>'');
				$this->view->clientDetail = $clientDetailArray;
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function addressespostAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$this->view->clientID = $clientID;
		$clientDetailModel = KD::getModel('client/clientdetail');
		$clientArray = KD::getModel('client/client')->load($clientID);
		if(isset($clientID) && $clientID>0  && $_POST['patient_id'] == $clientID && !empty($clientArray))
		{
			$clientExist = KD::getModel('client/clientdetail')->checkClientDetail($clientID);
			if($clientExist)
			{
				$clientDetailArray =  $clientDetailModel->update($_POST['address'],'patient_detail_patientID',$clientID);
			}
			else
			{
				$data = $_POST['address'];$data['patient_detail_patientID'] = $_POST['patient_id'];
				$clientDetailArray =  $clientDetailModel->insert($data);
			}
			$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Client Adresslist Saved Successfully',$this->view->translate('Client Adresslist')));
			$this->_redirect('/client/info/index/s/d/id/'.$clientID);
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
	}
	
	public function networkAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		$clientModel = KD::getModel('client/client');
		$this->view->clientID = $clientID;
		if(isset($clientID) && $clientID>0 )
		{
			$clientInfoArray =  $clientModel->load($clientID);
			if(!empty($clientInfoArray))
			{
				$this->view->clientInfo = $clientInfoArray;
			}
			else
			{
				$clientInfoArray = array('patient_location'=>'');
				$this->view->clientInfo = $clientInfoArray;
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function networkpostAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$this->view->clientID = $clientID;
		$clientModel = KD::getModel('client/client');
		$clientArray = $clientModel->load($clientID);
		if(isset($clientID) && $clientID>0 && $_POST['patient_id'] == $clientID && !empty($clientArray))
		{
			$clientDetailArray =  $clientModel->update($_POST['network'],'patient_id',$clientID);
			$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Network Saved Successfully',$this->view->translate('Network')));
			$this->_redirect('/client/info/index/s/p/id/'.$clientID);
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function measurementAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$clientDocument = KD::getModel('client/document');
		if(isset($clientID) && $clientID>0 )
		{
			
			if(!empty($clientDocument))
			{
				$this->view->documentMeasurement = $clientDocument->loadList($clientID,'measurement');
			}
			else
			{
				
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function refreatAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$clientDocument = KD::getModel('client/document');
		if(isset($clientID) && $clientID>0 )
		{
			
			if(!empty($clientDocument))
			{
				$this->view->documentWelfare = $clientDocument->loadList($clientID,'refreat_welfare');
				$this->view->documentSchool = $clientDocument->loadList($clientID,'refreat_school');
				$this->view->documentPsy = $clientDocument->loadList($clientID,'refreat_psychiatry');
				$this->view->documentOther = $clientDocument->loadList($clientID,'refreat_other');
			}
			else
			{
				
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function historyAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$clientDocument = KD::getModel('client/document');
		if(isset($clientID) && $clientID>0 )
		{
			
			if(!empty($clientDocument))
			{
				$this->view->documentWelfare = $clientDocument->loadList($clientID,'history_welfare');
				$this->view->documentSchool = $clientDocument->loadList($clientID,'history_school');
				$this->view->documentPsy = $clientDocument->loadList($clientID,'history_psychiatry');
				$this->view->documentOther = $clientDocument->loadList($clientID,'history_other');
			}
			else
			{
				
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function investigationAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$clientDocument = KD::getModel('client/document');
		if(isset($clientID) && $clientID>0 )
		{
			
			if(!empty($clientDocument))
			{
				$this->view->documentNeuro = $clientDocument->loadList($clientID,'investigation_neuro');
				$this->view->documentHospital = $clientDocument->loadList($clientID,'investigation_hospital');
				$this->view->documentBup = $clientDocument->loadList($clientID,'investigation_bup');
				$this->view->documentPpt = $clientDocument->loadList($clientID,'investigation_ppt');
				$this->view->documentOther = $clientDocument->loadList($clientID,'investigation_other');
			}
			else
			{
				
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function avvikAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$this->view->clientID = $clientID;
		$clientDeviation = KD::getModel('client/deviation');

		if(isset($clientID) && $clientID>0 )
		{
			
			if(!empty($clientDeviation))
			{
				$this->view->deviationM = $clientDeviation->loadList($clientID,'archive','M');
				$this->view->deviationP = $clientDeviation->loadList($clientID,'archive','P');
				$this->view->deviationO = $clientDeviation->loadList($clientID,'archive','O');
			}
			else
			{
				
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function loggAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();

		$this->view->clientID = $clientID;
		$clientLogg = KD::getModel('client/logg');
		if(isset($clientID) && $clientID>0 )
		{

			if(!empty($clientLogg))
			{
				$this->view->loggM = $clientLogg->loadList($clientID,'archive','M');
				$this->view->loggP = $clientLogg->loadList($clientID,'archive','P');
				$this->view->loggO = $clientLogg->loadList($clientID,'archive','O');
			}
			else
			{

			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
	}
	
	public function showloggAction() {
		$this->_helper->layout->setLayout('layout_ajax');
		$loggID = $this->getRequest()->getParam('id');//exit();
		
		$this->view->loggID = $loggID;
		$clientLoggModel = KD::getModel('client/logg');
		if(isset($loggID) && $loggID>0 )
		{
			$clientLogg = $clientLoggModel->load($loggID);
			if(!empty($clientLogg))
			{
				$this->view->logg = $clientLogg;
				$this->view->show = true;
			}
			else
			{
				$this->view->show = false;
			}
		}
		else
		{
			$this->view->show = false;
		}
    }

	public function showdeviationAction() {
		$this->_helper->layout->setLayout('layout_ajax');
		$deviationID = $this->getRequest()->getParam('id');//exit();

		$this->view->loggID = $deviationID;
		$clientDeviationModel = KD::getModel('client/deviation');
		if(isset($deviationID) && $deviationID>0 )
		{
			$clientDeviation = $clientDeviationModel->load($deviationID);
			if(!empty($clientDeviation))
			{
				$this->view->deviation = $clientDeviation;
				$this->view->show = true;
			}
			else
			{
				$this->view->show = false;
			}
		}
		else
		{
			$this->view->show = false;
		}
	}

	
	public function forceAction() {
		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$this->view->clientID = $clientID;
		$clientForce = KD::getModel('client/force');
		if(isset($clientID) && $clientID>0 )
		{
			
			if(!empty($clientForce))
			{
				$this->view->force = $clientForce->loadList($clientID,'archive');
			}
			else
			{
				
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	
	public function othersAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');//exit();
		
		$clientDocument = KD::getModel('client/document');
		if(isset($clientID) && $clientID>0 )
		{
			
			if(!empty($clientDocument))
			{
				$this->view->documentLetter = $clientDocument->loadList($clientID,'others_letter');
				$this->view->documentComplain = $clientDocument->loadList($clientID,'others_complain');
				$this->view->documentNav = $clientDocument->loadList($clientID,'others_nav');
				$this->view->documentJob = $clientDocument->loadList($clientID,'others_job');
				$this->view->documentEconomy = $clientDocument->loadList($clientID,'others_economy');
				$this->view->documentOther = $clientDocument->loadList($clientID,'others_other');
			}
			else
			{
				
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
				
}