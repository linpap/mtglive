<?php
class Client_MtoController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }
    public function indexAction() {

		$isAjax = $this->getRequest()->getParam('isAjax');
		if($isAjax)
		{
			$this->_helper->layout->setLayout('layout_ajax');
		}
		$clientID = $this->getRequest()->getParam('id');//exit();
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
    }

    public function createAction() {
        if($this->getRequest()->isPost()) {
            $this->_saveGovTiltak();
        }
        $this->_helper->layout->setLayout('layout_ajax');
        $this->view->clientID = $this->getRequest()->getParam('id');
        $this->view->redirectUrl = APPLICATION_URL . '/client/scorecard/index/t/3/id/' . $this->view->clientID;

        $maalArray = KD::getModel('client/maal')->loadList($this->view->clientID);
        $this->view->maalCollection = $maalArray;

    }

	public function maalAction() {

		$nextLink = "";
		$previousLink = "";
		$cureentIndex = "";

		$isAjax = $this->getRequest()->getParam('isAjax');
		if($isAjax)
		{
			$this->_helper->layout->setLayout('layout_ajax');
		}
		
		$clientID = $this->getRequest()->getParam('id');//exit();
		$vaktrapportArray = KD::getModel('vaktrapport/vaktrapport')->loadList($clientID,"all");



		for($i=0;$i<count($vaktrapportArray);$i++)
		{
			if($vaktrapportArray[$i]['vaktrap_id'])
			{

				$cureentIndex = $i +1;
				break;
			}
		}



		if($cureentIndex > 0)
		{

			if($vaktrapportArray[$cureentIndex-1]['vaktrap_status']=='yes'):
				$previousLink = 'vaktrapport/info/index/id/'.$vaktrapportArray[$cureentIndex-1]['vaktrap_id'];
			else:
				$previousLink = 'vaktrapport/info/vaktraparchive/id/'.$vaktrapportArray[$cureentIndex-1]['vaktrap_id'];
			endif;


		}

		if($cureentIndex < count($vaktrapportArray)-1)
		{
			if($vaktrapportArray[$cureentIndex+1]['vaktrap_status']=='yes'):
				$nextLink = 'vaktrapport/info/index/id/'.$vaktrapportArray[$cureentIndex+1]['vaktrap_id'];
			else:
				$nextLink = 'vaktrapport/info/vaktraparchive/id/'.$vaktrapportArray[$cureentIndex+1]['vaktrap_id'];
			endif;
		}

		$vaktrapInfo['nextLink'] = $nextLink;
		$vaktrapInfo['previousLink'] = $previousLink;

		$this->view->vaktrapInfo = $vaktrapInfo;



		$this->view->className = 'PTCLEFTVAKTRAPORT';
		$this->view->title = $this->view->translate('MTO For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
		$this->view->isAjax = $isAjax;
		if(isset($clientID) && $clientID>0 )
		{
			$this->view->id = $clientID;

			/*$pageM = $this->getRequest()->getParam('pm');
			$pageMA = $this->getRequest()->getParam('pma');
			$pageMD = $this->getRequest()->getParam('pmc');
			if($pageM<=0){$pageM = 1;}
			if($pageMA<=0){$pageMA = 1;}
			if($pageMD<=0){$pageMD = 1;}*/
			
			$maalArray = KD::getModel('client/maal')->loadList($clientID, 'active');
			$this->view->maalCollection = $maalArray;	

			$maalArchiveArray = KD::getModel('client/maal')->loadList($clientID, 'archive');
			$this->view->maalArchiveCollection = $maalArchiveArray;
			
			$maalAchivedArray = KD::getModel('client/maal')->loadList($clientID, 'achived');
			$this->view->maalAchivedCollection = $maalAchivedArray;
			
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	
	public function updatemaalorderAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$maalIds = $_POST['maal'];
		$maalModel = KD::getModel('client/maal');
		foreach($maalIds as $key=>$id)
		{
			$data = array();$data['maal_order'] = ($key+1);
			$maalModel->update($data,'maal_id',$id);
		}
		$maalObj = $maalModel->load($id);
		if(isset($maalObj['maal_patientID']) && $maalObj['maal_patientID']>0)
		{
			$maalArray = KD::getModel('client/maal')->loadList($maalObj['maal_patientID'], 'active');
			$this->view->maalCollection = $maalArray;
		}
		else
		{
			echo 'Invalid Operation Please Try Again!';
			//$this->_redirect('/client/mto/maal/id/'.$maalObj['maal_patientID']);
			exit();
		}
    }
	
	public function addmaalAction() {
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		if(isset($patientId) && $patientId>0 )
		{
			
			$this->view->id = $patientId;
			$format = KD::getModel('core/format');
			foreach($_POST['maalDescs'] as $key => $maal)
			{
				if(isset($maal) && $maal!='')
				{
					$data = array('maal_desc'=>$maal, 'maal_order'=>$_POST['maalOrders'][$key], 'maal_from_date'=>$_POST['fromDates'][$key],'maal_to_date'=>$_POST['toDates'][$key],'maal_patientID'=>$patientId);
					//echo '<pre>';print_r($data);exit();
					//$_POST['maalDescs'][$key]
					KD::getModel('client/maal')->insert($data);
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Maal was Successfully Added',$this->view->translate('Maal')));
					//$this->getResponse()->setRedirect($this->getUrl("system/info/updatedepartment"));
				}
			}
			$this->_redirect('/client/mto/maal/id/'.$patientId);
		}
		if($patientId<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function editmaalAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$maalId = $this->getRequest()->getParam('id');//exit();
		if(isset($maalId) && $maalId>0)
		{
			$maal = KD::getModel('client/maal')->load($maalId);
			if($maal)
			{
				$this->view->maalCollection = $maal;
				$this->view->maalID = $maalId;
			}
		}
	}
	public function editmaalpostAction()
	{
		
		$maalId = $this->getRequest()->getParam('id');//exit();
		$maalPatientId = $this->getRequest()->getParam('pid');//exit();
		if(isset($maalId) && $maalId>0)
		{
			$maal = KD::getModel('client/maal')->load($maalId);
			if($maalPatientId==$maal['maal_patientID'])
			{
				$data = array('maal_desc'=>$_POST['maal_desc'],'maal_order'=>$_POST['maal_order'],'maal_from_date'=>$_POST['maal_from_date'],'maal_to_date'=>$_POST['maal_to_date']);
				KD::getModel('client/maal')->update($data,'maal_id',$maalId);
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Maal was Successfully Updated',$this->view->translate('Maal')));
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Error While Updating Maal ',$this->view->translate('Maal')));
			}
			$this->_redirect('/client/mto/maal/id/'.$maalPatientId);
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
		$this->_redirect('/client/mto/maal/id/'.$maalPatientId);
	}
	
	public function archivemaalAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'no');
		if($ids && isset($patientId) && $patientId>0)
		{
			$result = KD::getModel('client/maal')->archiveList($ids,'no');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Archived Successfully'));
				$this->_redirect('/client/mto/maal/tt/63/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Archived Error'));
		$this->_redirect('/client/mto/maal/tt/61/id/'.$patientId);
	}
	
	public function restoremaalAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'yes');
		if($ids && isset($patientId) && $patientId>0)
		{
			$result = KD::getModel('client/maal')->archiveList($ids,'yes');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Restored Successfully'));
				$this->_redirect('/client/mto/maal/tt/61/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Restore Error'));
		$this->_redirect('/client/mto/maal/tt/63/id/'.$patientId);
	}
	
	public function govtiltakAction() {

		$clientID = $this->getRequest()->getParam('id');//exit();<br />
		if(isset($clientID) && $clientID>0 )
		{
			$isAjax = $this->getRequest()->getParam('isAjax');
			if($isAjax)
			{
				$this->_helper->layout->setLayout('layout_ajax');
			}
			$this->view->id = $clientID;
			$this->view->isAjax = $isAjax;
			
			$this->view->className = 'PTCLEFTVAKTRAPORT';
			$this->view->title = $this->view->translate('MTO For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
		
			$pageG = $this->getRequest()->getParam('pg');
			$pageGA = $this->getRequest()->getParam('pga');
			if($pageG<=0){$pageG = 1;}
			if($pageGA<=0){$pageGA = 1;}

			$maalArray = KD::getModel('client/maal')->loadList($clientID);
			//echo "<pre>";
			//print_r($maalArray);

			$this->view->maalCollection = $maalArray;

			$date=date("Y-m-d H:i:s");

			//$tiltakArray = KD::getModel('client/tiltakfut')->loaddatefilterList($clientID,$date);
			//$this->view->tiltakCollection = $tiltakArray;

			//print_r($tiltakArray);
			//die;

			
			$currentVaktrap = KD::getModel('vaktrapport/vaktrapport')->getCurrentVaktrap($clientID);
			$currentVaktrap = (isset($currentVaktrap['vaktrap_id']))?$currentVaktrap['vaktrap_id']:'';

			$tiltakArray = KD::getModel('client/tiltakgov')->loadListVakShown($clientID,$currentVaktrap);
			$this->view->tiltakCollection = $tiltakArray;
			
			$tiltakArchiveArray = KD::getModel('client/tiltakgov')->loadList($clientID,'archive');
			$this->view->tiltakArchiveCollection = $tiltakArchiveArray;
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function addgovtiltakAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		if(isset($patientId) && $patientId>0 )
		{
			
			$this->view->id = $patientId;
			$format = KD::getModel('core/format');
			foreach($_POST['tilgovDescs'] as $key => $tiltak)
			{
				if(isset($tiltak) && $tiltak!='')
				{
					$data = array('tilgov_desc'=>$tiltak, 'tilgov_owner'=>$_POST['tilgovowner'][$key], 'tilgov_maalID'=>$_POST['maalIds'][$key], 'tilgov_from_date'=>$_POST['fromDates'][$key], 'tilgov_to_date'=>$_POST['toDates'][$key], 'tilgov_patientID'=>$patientId);
					//echo '<pre>';print_r($data);exit();
					//$_POST['maalDescs'][$key]
					KD::getModel('client/tiltakgov')->insert($data);
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Government Tiltak was Successfully Added',$this->view->translate('Government Tiltak')));
					//$this->getResponse()->setRedirect($this->getUrl("system/info/updatedepartment"));
				}
			}
			$this->_redirect('/client/mto/govtiltak/id/'.$patientId);
		}
		if($patientId<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage();
			$this->_redirect('/client/index/');
		}
	}
	public function editgovtiltakAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$tiltakId = $this->getRequest()->getParam('id');//exit();
		if(isset($tiltakId) && $tiltakId>0)
		{
			$tiltak = KD::getModel('client/tiltakgov')->load($tiltakId);
			if($tiltak)
			{
				$this->view->tiltakCollection = $tiltak;
				$this->view->tiltakID = $tiltakId;
			}
		}
	}
	public function editgovtiltakpostAction()
	{
		
		$tiltakId = $this->getRequest()->getParam('id');//exit();
		$tiltakPatientId = $this->getRequest()->getParam('pid');//exit();
		if(isset($tiltakId) && $tiltakId>0)
		{
			$tiltak = KD::getModel('client/tiltakgov')->load($tiltakId);
			if($tiltakPatientId==$tiltak['tilgov_patientID'])
			{
				$data = array('tilgov_desc'=>$_POST['tilgov_desc'],'tilgov_owner'=>$_POST['tilgov_owner'],'tilgov_from_date'=>$_POST['tilgov_from_date'],'tilgov_to_date'=>$_POST['tilgov_to_date']);
				KD::getModel('client/tiltakgov')->update($data,'tilgov_id',$tiltakId);
				$data = array();$data['vaktrap_tilgov_desc'] = $_POST['tilgov_desc'];
				KD::getModel('vaktrapport/vaktraptilgov')->update($data,'vaktrap_tilgovID',$tiltakId,false,true);
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Government Measure was Successfully Updated',$this->view->translate('Government Measure')));
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Update Problem'));
			}
			$this->_redirect('/client/mto/govtiltak/id/'.$tiltakPatientId);
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
		$this->_redirect('/client/mto/govtiltak/id/'.$tiltakPatientId);
	}
	public function archivegovtiltakAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'no');
		if($ids && isset($patientId) && $patientId>0)
		{
			$result = KD::getModel('client/tiltakgov')->archiveList($ids,'no');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Archived Successfully'));
				$this->_redirect('/client/mto/govtiltak/tt/62/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Archived Error'));
		$this->_redirect('/client/mto/govtiltak/tt/61/id/'.$patientId);
	}
	
	public function restoregovtiltakAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'yes');
		if($ids && isset($patientId) && $patientId>0)
		{
			$result = KD::getModel('client/tiltakgov')->archiveList($ids,'yes');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Restored Successfully'));
				$this->_redirect('/client/mto/govtiltak/tt/61/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Restore Error'));
		$this->_redirect('/client/mto/govtiltak/tt/62/id/'.$patientId);
	}
	
	public function showninvaktrapAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'yes');
		if($ids && isset($patientId) && $patientId>0)
		{
			$vaktrapportModel = KD::getModel('vaktrapport/vaktrapport');
			$govTilList = KD::getModel('client/tiltakgov')->getGovTiltList($ids);
			$currentVaktrap = $vaktrapportModel->getCurrentVaktrap($patientId);
			$currentVaktrap = (isset($currentVaktrap['vaktrap_id']))?$currentVaktrap['vaktrap_id']:'';
			$previousVaktrap = $vaktrapportModel->getPreviousVaktrap($patientId);
			$previousVaktrap = (isset($previousVaktrap['vaktrap_id']))?$previousVaktrap['vaktrap_id']:'';
			if($currentVaktrap)
			{
				$errorFlag = false;
				$errorStr = '';
				foreach($govTilList as $govTiltak)
				{
					$data = array();
					$data['vaktrap_tilgovID'] = $govTiltak['tilgov_id'];
					$data['vaktrap_tilgov_maalID'] = $govTiltak['tilgov_maalID'];
					$data['vaktrap_tilgov_patientID'] = $govTiltak['tilgov_patientID'];
					$data['vaktrap_vaktrapID'] = $currentVaktrap;
					//$data['vaktrap_tilgov_userID'] = $govTiltak['tilgov_userID'];
					$data['vaktrap_tilgov_desc'] = $govTiltak['tilgov_desc'];
					$data['vaktrap_tilgov_owner'] = $govTiltak['tilgov_owner'];
					$data['vaktrap_tilgov_status'] = $govTiltak['tilgov_status'];
					$data['vaktrap_tilgov_from_date'] = $govTiltak['tilgov_from_date'];
					$data['vaktrap_tilgov_to_date'] = $govTiltak['tilgov_to_date'];
					$data['vaktrap_previous_vaktrapID'] = $previousVaktrap;
					//echo '<pre>';print_r($data);exit();
					$vaktraptilgovModel = KD::getModel('vaktrapport/vaktraptilgov');
					$vaktraptilgovRes = $vaktraptilgovModel->checkVakGovTiltak($govTiltak['tilgov_id'], $currentVaktrap);

                    if($vaktraptilgovRes && count($vaktraptilgovRes)>0)
					{
						$insertFlag = false;
					}
					else
					{
						$insertFlag = $vaktraptilgovModel->insert($data);
					}
					if(!$insertFlag)
					{
						$errorFlag = true;
						$errorStr .= '<li><i>"' . $govTiltak['tilgov_desc'] . '"</i> ligger allerede i aktiv vaktrapport</li>';
					}
				}
				/*if(!$previousVaktrap)
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is no Previous Vaktrapport'));
				}*/
				if($errorFlag)
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Error while showing some Government Tiltak For Vaktrapport listed Below:'));
					$this->flashMessenger->setNamespace('error')->addMessage('<ul>' . $errorStr . '</ul');
					$this->_redirect('/client/mto/govtiltak/tt/61/id/'.$patientId);
				}
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('No Vaktrapport is Active For this Customer'));
				$this->_redirect('/client/mto/govtiltak/tt/61/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Government Tiltak is now shown in Current Vaktrapport'));
		$this->_redirect('/client/mto/govtiltak/tt/61/id/'.$patientId);
	}
	
	public function removefromvaktrapAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'yes');
		
		if($ids && isset($patientId) && $patientId>0)
		{
			$vaktrapportModel = KD::getModel('vaktrapport/vaktrapport');
			$govTilList = KD::getModel('client/tiltakgov')->getGovTiltList($ids);
			$currentVaktrap = $vaktrapportModel->getCurrentVaktrap($patientId);
			$currentVaktrap = (isset($currentVaktrap['vaktrap_id']))?$currentVaktrap['vaktrap_id']:'';
			//echo '<pre>';print_r($govTilList);exit();
			if($currentVaktrap)
			{
				$errorFlag = false;
				$errorStr = '';
				foreach($govTilList as $govTiltak)
				{
					$vaktraptilgovModel = KD::getModel('vaktrapport/vaktraptilgov');
					$vaktraptilgovRes = $vaktraptilgovModel->checkVakGovTiltak($govTiltak['tilgov_id'], $currentVaktrap);
					if($vaktraptilgovRes && count($vaktraptilgovRes)>0)
					{
						$insertFlag = true;
						//echo $vaktraptilgovRes['vaktrap_tilgov_id'].'<br>';
						$vaktraptilgovModel->delete($vaktraptilgovRes['vaktrap_tilgov_id']);
					}
					else
					{
						$insertFlag = false;		
					}
					if(!$insertFlag)
					{
						$errorFlag = true;
						$errorStr .= ' , '.$govTiltak['tilgov_desc'];
					}
				}
				//echo $errorFlag;
				//exit();
				/*if(!$previousVaktrap)
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is no Previous Vaktrapport'));
				}*/
				
				if($errorFlag)
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Error while Removing Government Tiltak For Vaktrapport listed Below:'));
					$this->flashMessenger->setNamespace('error')->addMessage(substr($errorStr,3));
					$this->_redirect('/client/mto/govtiltak/tt/61/id/'.$patientId);
				}
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('No Vaktrapport is Active For this Customer'));
				$this->_redirect('/client/mto/govtiltak/tt/61/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Government Tiltak is now Remove from Current Vaktrapport'));
		$this->_redirect('/client/mto/govtiltak/tt/61/id/'.$patientId);
	}
	
	public function instiltakAction() {
		
		$clientID = $this->getRequest()->getParam('id');//exit();<br />
		
		if(isset($clientID) && $clientID>0 )
		{
			$isAjax = $this->getRequest()->getParam('isAjax');
			if($isAjax)
			{
				$this->_helper->layout->setLayout('layout_ajax');
			}
			$this->view->id = $clientID;
			$this->view->isAjax = $isAjax;
			
			$this->view->className = 'PTCLEFTVAKTRAPORT';
			$this->view->title = $this->view->translate('MTO For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
		
			$pageT = $this->getRequest()->getParam('pi');
			$pageTA = $this->getRequest()->getParam('pia');
			if($pageT<=0){$pageT = 1;}
			if($pageTA<=0){$pageTA = 1;}
			
			$maalArray = KD::getModel('client/maal')->loadList($clientID);
			$this->view->maalCollection = $maalArray;	
			
			$tiltakArray = KD::getModel('client/tiltakinst')->loadList($clientID);
			$this->view->tiltakCollection = $tiltakArray;	
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function addinstiltakAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		if(isset($patientId) && $patientId>0 )
		{
			$vaktrapportModel = KD::getModel('vaktrapport/vaktrapport');
			$currentVaktrap = $vaktrapportModel->getCurrentVaktrap($patientId);
			$currentVaktrap = (isset($currentVaktrap['vaktrap_id']))?$currentVaktrap['vaktrap_id']:'';
			$previousVaktrap = $vaktrapportModel->getPreviousVaktrap($patientId);
			$previousVaktrap = (isset($previousVaktrap['vaktrap_id']))?$previousVaktrap['vaktrap_id']:'';
			
			
			$this->view->id = $patientId;
			$format = KD::getModel('core/format');
			foreach($_POST['tilinsDescs'] as $key => $tiltak)
			{
				if(isset($tiltak) && $tiltak!='')
				{
					$data = array('tilins_desc'=>$tiltak, 'tilins_owner'=>$_POST['tilinsowner'][$key], 'tilins_maalID'=>$_POST['maalIds'][$key],'tilins_patientID'=>$patientId,'tilins_vaktrapportID'=>$currentVaktrap,'tilins_previous_vaktrapportID'=>$previousVaktrap);
					//echo '<pre>';print_r($data);exit();
					//$_POST['maalDescs'][$key]
					KD::getModel('client/tiltakinst')->insert($data);
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Institute Tiltak was Successfully Added',$this->view->translate('Institute Tiltak')));
					//$this->getResponse()->setRedirect($this->getUrl("system/info/updatedepartment"));
				}
			}
			
			$this->_redirect('/client/mto/instiltak/id/'.$patientId);
		}
		if($patientId<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage();
			$this->_redirect('/client/index/');
		}
	}
	
	public function editinstiltakAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$tiltakId = $this->getRequest()->getParam('id');//exit();
		if(isset($tiltakId) && $tiltakId>0)
		{
			$tiltak = KD::getModel('client/tiltakinst')->load($tiltakId);
			if($tiltak)
			{
				$this->view->tiltakCollection = $tiltak;
				$this->view->tiltakID = $tiltakId;
			}
		}
	}
	public function editinstiltakpostAction()
	{
		
		$tiltakId = $this->getRequest()->getParam('id');//exit();
		$tiltakPatientId = $this->getRequest()->getParam('pid');//exit();
		if(isset($tiltakId) && $tiltakId>0)
		{
			$tiltak = KD::getModel('client/tiltakinst')->load($tiltakId);
			if($tiltakPatientId==$tiltak['tilins_patientID'])
			{
				$data = array('tilins_desc'=>$_POST['tilins_desc'],'tilins_owner'=>$_POST['tilins_owner']);
				KD::getModel('client/tiltakinst')->update($data,'tilins_id',$tiltakId);
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Institute Measure was Successfully Updated',$this->view->translate('Institute Measure')));
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Update Problem'));
			}
			$this->_redirect('/client/mto/instiltak/id/'.$tiltakPatientId);
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
		$this->_redirect('/client/mto/instiltak/id/'.$tiltakPatientId);
	}
	public function archiveinstiltakAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'no');
		if($ids && isset($patientId) && $patientId>0)
		{
			$result = KD::getModel('client/tiltakinst')->archiveList($ids,'no');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Archived Successfully'));
				$this->_redirect('/client/mto/instiltak/tt/61/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Archived Error'));
		$this->_redirect('/client/mto/instiltak/tt/61/id/'.$patientId);
	}
	public function featiltakAction() {

		
		$clientID = $this->getRequest()->getParam('id');//exit();<br />
		if(isset($clientID) && $clientID>0 )
		{
			$isAjax = $this->getRequest()->getParam('isAjax');
			if($isAjax)
			{
				$this->_helper->layout->setLayout('layout_ajax');
			}
			$this->view->id = $clientID;
			$this->view->isAjax = $isAjax;
			
			$this->view->className = 'PTCLEFTVAKTRAPORT';
			$this->view->title = $this->view->translate('MTO For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
		
			$pageF = $this->getRequest()->getParam('pf');
			$pageFA = $this->getRequest()->getParam('pfa');
			if($pageF<=0){$pageT = 1;}
			if($pageFA<=0){$pageTA = 1;}
			
			$maalArray = KD::getModel('client/maal')->loadList($clientID);
			$this->view->maalCollection = $maalArray;	
			
			$tiltakArray = KD::getModel('client/tiltakfut')->loadList($clientID);


			$this->view->tiltakCollection = $tiltakArray;
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	
	public function addfeatiltakAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		if(isset($patientId) && $patientId>0 )
		{
			
			$this->view->id = $patientId;
			$format = KD::getModel('core/format');
			foreach($_POST['tilfutDescs'] as $key => $tiltak)
			{
				if(isset($tiltak) && $tiltak!='')
				{
					$data = array('tilfut_desc'=>$tiltak, 'tilfut_owner'=>$_POST['tilfutowner'][$key], 'tilfut_maalID'=>$_POST['maalIds'][$key], 'tilfut_from_date'=>$_POST['fromDates'][$key], 'tilfut_to_date'=>$_POST['toDates'][$key], 'tilfut_patientID'=>$patientId);
					//echo '<pre>';print_r($data);exit();
					//$_POST['maalDescs'][$key]
					KD::getModel('client/tiltakfut')->insert($data);

					$dataArr = array('tilgov_desc'=>$tiltak, 'tilgov_owner'=>$_POST['tilfutowner'][$key], 'tilgov_maalID'=>$_POST['maalIds'][$key], 'tilgov_from_date'=>$_POST['fromDates'][$key], 'tilgov_to_date'=>$_POST['toDates'][$key], 'tilgov_patientID'=>$patientId);
					//echo '<pre>';print_r($data);exit();
					//$_POST['maalDescs'][$key]
					KD::getModel('client/tiltakgov')->insert($dataArr);
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Future Tiltak was Successfully Added',$this->view->translate('Future Tiltak')));
					//$this->getResponse()->setRedirect($this->getUrl("system/info/updatedepartment"));
				}
			}
			$this->_redirect('/client/mto/featiltak/id/'.$patientId);
		}
		if($patientId<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage();
			$this->_redirect('/client/index/');
		}
	}
	public function editfeatiltakAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$tiltakId = $this->getRequest()->getParam('id');//exit();
		if(isset($tiltakId) && $tiltakId>0)
		{
			$tiltak = KD::getModel('client/tiltakfut')->load($tiltakId);
			if($tiltak)
			{
				$this->view->tiltakCollection = $tiltak;
				$this->view->tiltakID = $tiltakId;
			}
		}
	}
	public function editfeatiltakpostAction()
	{
		
		$tiltakId = $this->getRequest()->getParam('id');//exit();
		$tiltakPatientId = $this->getRequest()->getParam('pid');//exit();
		if(isset($tiltakId) && $tiltakId>0)
		{
			$tiltak = KD::getModel('client/tiltakfut')->load($tiltakId);
			if($tiltakPatientId==$tiltak['tilfut_patientID'])
			{
				$data = array('tilfut_desc'=>$_POST['tilfut_desc'],'tilfut_owner'=>$_POST['tilfut_owner'],'tilfut_from_date'=>$_POST['tilfut_from_date'],'tilfut_to_date'=>$_POST['tilfut_to_date']);
				KD::getModel('client/tiltakfut')->update($data,'tilfut_id',$tiltakId);
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Future Measure was Successfully Updated',$this->view->translate('Future Measure')));
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Update Problem'));
			}
			$this->_redirect('/client/mto/featiltak/id/'.$tiltakPatientId);
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
		$this->_redirect('/client/mto/featiltak/id/'.$tiltakPatientId);
	}
	public function archivefeatiltakAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'no');
		if($ids && isset($patientId) && $patientId>0)
		{
			$result = KD::getModel('client/tiltakfut')->archiveList($ids,'no');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Archived Successfully'));
				$this->_redirect('/client/mto/featiltak/tt/61/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Archived Error'));
		$this->_redirect('/client/mto/featiltak/tt/61/id/'.$patientId);
	}
	public function observationAction() {
		
		$clientID = $this->getRequest()->getParam('id');//exit();<br />
		if(isset($clientID) && $clientID>0 )
		{
			$isAjax = $this->getRequest()->getParam('isAjax');
			if($isAjax)
			{
				$this->_helper->layout->setLayout('layout_ajax');
			}
			$this->view->id = $clientID;
			$this->view->isAjax = $isAjax;
			
			$this->view->className = 'PTCLEFTVAKTRAPORT';
			$this->view->title = $this->view->translate('MTO For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
			
			$pageO = $this->getRequest()->getParam('po');
			$pageOA = $this->getRequest()->getParam('poa');
			if($pageO<=0){$pageO = 1;}
			if($pageOA<=0){$pageOA = 1;}
			
			$observationArray = KD::getModel('client/observation')->loadPageData($clientID, $pageO,'active');
			$this->view->observationCollection = $observationArray;	

			$observationArchiveArray = KD::getModel('client/observation')->loadPageData($clientID, $pageOA,'archive');
			$this->view->observationArchiveCollection = $observationArchiveArray;
			
			$maalArray = KD::getModel('client/maal')->loadList($clientID);
			$this->view->maalCollection = $maalArray;	
			
			$tiltakArray = KD::getModel('client/tiltakgov')->loadList($clientID);
			$this->view->tiltakCollection = $tiltakArray;
			
			$this->view->page = $pageO;

		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	
	public function updateobserorderAction() {

		$this->_helper->layout->setLayout('layout_ajax');
		$observationIds = $_POST['observation'];
		$observationModel = KD::getModel('client/observation');
		foreach($observationIds as $key=>$id)
		{
			$data = array();$data['observation_order'] = ($key+1);
			$observationModel->update($data,'observation_id',$id);
		}
		$observationObj = $observationModel->load($id);
		if(isset($observationObj['observation_patientID']) && $observationObj['observation_patientID']>0)
		{
			$observationArray = KD::getModel('client/observation')->loadList($observationObj['observation_patientID'], 'active');
			$this->view->observationCollection = $observationArray;
		}
		else
		{
			echo 'Invalid Operation Please Try Again!';
			//$this->_redirect('/client/mto/maal/id/'.$maalObj['maal_patientID']);
			exit();
		}
    }
	
	public function addobservationAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		if(isset($patientId) && $patientId>0 )
		{
			
			$this->view->id = $patientId;
			$format = KD::getModel('core/format');
			foreach($_POST['observationDescs'] as $key => $observation)
			{
				if(isset($observation) && $observation!='')
				{
					$data = array('observation_desc'=>$observation, 'observation_type'=>$_POST['types'][$key], 'observation_relationID'=>$_POST['relations'][$key], 'observation_patientID'=>$patientId);
					//echo '<pre>';print_r($data);
					//$_POST['maalDescs'][$key]
					KD::getModel('client/observation')->insert($data);
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Observation was Successfully Added',$this->view->translate('Observation')));
					//$this->getResponse()->setRedirect($this->getUrl("system/info/updatedepartment"));
				}
			}
			$this->_redirect('/client/mto/observation/id/'.$patientId);
		}
		if($patientId<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage();
			$this->_redirect('/client/index/');
		}
	}
	public function editobservationAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$observationId = $this->getRequest()->getParam('id');//exit();
		$pageO = $this->getRequest()->getParam('po');
		$pageO = ($pageO>0)?$pageO:1;
		if(isset($observationId) && $observationId>0)
		{
			$observation = KD::getModel('client/observation')->load($observationId);
			if($observation)
			{
				$this->view->observationCollection = $observation;
				$this->view->observationID = $observationId;
				$type = $observation['observation_type'];
				$collection = array();
				$clientID = $observation['observation_patientID'];
				$defaultRelationID = $observation['observation_relationID'];
				if(isset($clientID) && $clientID>0)
				{
				  switch($type)
				  {
					case 'M':
						$collection =  KD::getModel('client/maal')->loadList($clientID);
					break;
					case 'T':
						$collection =  KD::getModel('client/tiltakgov')->loadList($clientID);
					break;
					case 'I':
					default:
						
					break;
				  }
				}
				$this->view->collection = $collection;
				$this->view->type = $type;
				$this->view->page = $pageO;
				$this->view->defualtRelID = $defaultRelationID;
			}
		}
	}
	public function editobservationpostAction()
	{
		
		$observationId = $this->getRequest()->getParam('id');//exit();
		$pageO = $this->getRequest()->getParam('po');
		$pageO = ($pageO>0)?$pageO:1;
		$observationPatientId = $this->getRequest()->getParam('pid');
		if(isset($observationId) && $observationId>0)
		{
			$observation = KD::getModel('client/observation')->load($observationId);
			if($observationPatientId==$observation['observation_patientID'])
			{
				$data = array('observation_desc'=>$_POST['observation_desc']);//,'tilfut_owner'=>$_POST['tilfut_owner'],'tilfut_from_date'=>$_POST['tilfut_from_date'],'tilfut_to_date'=>$_POST['tilfut_to_date']);
				KD::getModel('client/observation')->update($data,'observation_id',$observationId);
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Observation was Successfully Updated',$this->view->translate('Observation')));
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Update Problem'));
			}
			$this->_redirect('/client/mto/observation/po/'.$pageO.'/id/'.$observationPatientId);
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request sdf'));
		$this->_redirect('/client/mto/observation/po/'.$pageO.'/id/'.$observationPatientId);
	}
	public function archiveobservationAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'no');
		if($ids && isset($patientId) && $patientId>0)
		{
			$result = KD::getModel('client/observation')->archiveList($ids,'no');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Archived Successfully'));
				$this->_redirect('/client/mto/observation/tt/62/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Archived Error'));
		$this->_redirect('/client/mto/observation/tt/61/id/'.$patientId);
	}
	public function restoreobservationAction()
	{
		$patientId = $this->getRequest()->getParam('patient_id');//exit();
		$ids = $this->_archive($_POST,'yes');
		if($ids && isset($patientId) && $patientId>0)
		{
			$result = KD::getModel('client/observation')->archiveList($ids,'yes');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Restored Successfully'));
				$this->_redirect('/client/mto/observation/tt/61/id/'.$patientId);
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Restore Error'));
		$this->_redirect('/client/mto/observation/tt/62/id/'.$patientId);
	}
	
	public function gettypeAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$clientID = $this->getRequest()->getParam('id');
		$isEdit = $this->getRequest()->getParam('edit');
		$type = 'I';
		$collection = array();
		if(isset($clientID) && $clientID>0 )
		{
			$key = $this->getRequest()->getParam('key');
			if(isset($key) && $key!='')
			{
			  switch($key)
			  {
				case 'M':
					$type = 'M';
					$collection =  KD::getModel('client/maal')->loadList($clientID);
				break;
				case 'T':
					$type = 'T';
					$collection =  KD::getModel('client/tiltakgov')->loadList($clientID);
				break;
				case 'I':
				default:
					
				break;
			  }
			}	
		}
		$this->view->collection = $collection;
		$this->view->type = $type;
		$this->view->isEdit = $isEdit;
		
	}

    public function createShortTermGoalAction() {
        if($this->getRequest()->isPost()) {
            $this->_saveShortTermGoal();
        }
        $this->_helper->layout->setLayout('layout_ajax');
        $this->view->clientID = $this->getRequest()->getParam('id');
        $this->view->vaktrapid = $this->getRequest()->getParam('vaktrapid');
        $this->view->redirectUrl = APPLICATION_URL . '/vaktrapport/info/index/id/' . $this->getRequest()->getParam('vaktrapid');

        $maalArray = KD::getModel('client/maal')->loadList($this->view->clientID);
        $this->view->maalCollection = $maalArray;
    }


    protected function _saveGovTiltak() {
        $data = array(
            'tilgov_desc'=> $this->getRequest()->getParam('description'),
            'tilgov_owner' => $this->getRequest()->getParam('tilgovowner'),
            'tilgov_maalID' => $this->getRequest()->getParam('goalId'),
            'tilgov_from_date' => $this->getRequest()->getParam('from-date'),
            'tilgov_to_date' => $this->getRequest()->getParam('to-date'),
            'tilgov_patientID'=> $this->getRequest()->getParam('patient_id')
        );

        $r = KD::getModel('client/tiltakgov')->insert($data);
        $this->_httpSuccessRedirect(
            $this->view->translate('Government Tiltak') .' was Successfully Added',
            $this->getRequest()->getParam('redirectUrl')
        );
    }

    protected function _saveShortTermGoal() {
        $vaktrapportModel = KD::getModel('vaktrapport/vaktrapport');
        $previousVaktrap = $vaktrapportModel->getPreviousVaktrap($this->getRequest()->getParam('patient_id'));
        $previousVaktrap = isset($previousVaktrap['vaktrap_id']) ? $previousVaktrap['vaktrap_id'] : '';

        $data = array(
                'tilins_desc'=> $this->getRequest()->getParam('description'),
                'tilins_owner' => $this->getRequest()->getParam('tilgovowner'),
                'tilins_maalID' => $this->getRequest()->getParam('goalId'),
                'tilins_patientID' => $this->getRequest()->getParam('patient_id'),
                'tilins_vaktrapportID' => $this->getRequest()->getParam('vaktrapid'),
                'tilins_previous_vaktrapportID'=> $previousVaktrap
            );
        KD::getModel('client/tiltakinst')->insert($data);

        return $this->_httpSuccessRedirect(
            $this->view->translate('Institute Tiltak') . ' was Successfully Added',
            $this->getRequest()->getParam('redirectUrl')
        );
    }
				
}