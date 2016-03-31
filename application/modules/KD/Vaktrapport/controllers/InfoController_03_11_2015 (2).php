<?php
use KD_Vaktrapport_Middleware_TreatmentPersonnelFacade as TreatmentPersonnel;
use KD_Vaktrapport_Middleware_LogFacade as Log;

class Vaktrapport_InfoController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }
 
    public function indexAction() {
		$vaktrapID = $this->getRequest()->getParam('id');        
		if(isset($vaktrapID) && $vaktrapID > 0)
		{
			$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->getVaktrap($vaktrapID);
			
			// If they are mannually Adding some Id in address bar which is not exist 
			// Here if vaktrapport is not active you are redirected to invalid page
			if(!empty($vaktrapArray) && $vaktrapArray[0]['vaktrap_status']=='yes')
			{
				
				$vaktrapInfo = $vaktrapArray[0];
				$patientID = $vaktrapInfo['vaktrap_patientID'];
				
				$vaktrapportArray = KD::getModel('vaktrapport/vaktrapport')->loadList($patientID,"all"); 
				
				$nextLink = "";
				$previousLink = "";
				
				$cureentIndex = "";
				
				for($i=0;$i<count($vaktrapportArray);$i++)
				{
					if($vaktrapID==$vaktrapportArray[$i]['vaktrap_id'])
					{
						$cureentIndex = $i;
						break;			
					}
				}
				
				if($cureentIndex > 0)
				{
					if($vaktrapportArray[$cureentIndex-1]['vaktrap_status']=='yes'): 
						$previousLink = $this->getUrl('vaktrapport/info/index/id/'.$vaktrapportArray[$cureentIndex-1]['vaktrap_id']);
					else:
						$previousLink = $this->getUrl('vaktrapport/info/vaktraparchive/id/'.$vaktrapportArray[$cureentIndex-1]['vaktrap_id']);
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
				
			
				
				$maalDataArray = KD::getModel('client/maal')->loadList($patientID);
				$count = 1; 
				$maalArray = array();
				$instTiltakArray = array();
				$vakGovTiltakArray = array();
				$observationArray = array();
				$observationResArray = array();
				$observationArray = KD::getModel('client/observation')->loadList($patientID);
				foreach($maalDataArray as $maal)
				{
					$maalArray[$maal['maal_id']] = array('maal_id'=>$maal['maal_id'],'maal_desc'=>$maal['maal_desc'],'counter'=>$count++);
					$instTiltakArray[$maal['maal_id']] = KD::getModel('client/tiltakinst')->loadListByMaal($patientID,$maal['maal_id'],$vaktrapID);
					$vakGovTiltakArray[$maal['maal_id']] = KD::getModel('vaktrapport/vaktraptilgov')->loadListByMaal($patientID,$maal['maal_id'],$vaktrapID);
				}
				
				$vaktrapFromDate = $vaktrapInfo['vaktrap_from_date'];
				$vaktrapToDate = $vaktrapInfo['vaktrap_to_date'];
				$begin = new DateTime($vaktrapFromDate);
				$endcheck = new DateTime($vaktrapToDate);
				$difference = $endcheck->diff($begin)->format("%a");
				if(isset($vaktrapToDate) && $vaktrapToDate!='' && (strpos($vaktrapToDate,'0000')===false) && $difference<=7)
				{
					$end = new DateTime($vaktrapToDate);
				}
				else
				{
					$end = clone $begin;
					$end->add(new DateInterval('P6D'));
					$hideObservation = true;
				}
				$end = $end->modify( '+1 day' );
				$interval = new DateInterval('P1D');
				$daterange = new DatePeriod($begin, $interval ,$end);
				
				foreach($observationArray as $observation)
				{
					$daterange = new DatePeriod($begin, $interval ,$end);
					foreach($daterange as $date){
						$observationResArray[$observation['observation_id']][$date->format('y-m-d')] = KD::getModel('vaktrapport/vaktrapobser')->loadListByDate($patientID,/*$vaktrapID,*/$observation['observation_id'],$date->format("Y-m-d"));
					}
					
					/*$tmpArray = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservation($patientID,$vaktrapID,$observation['observation_id']);
					foreach($tmpArray as $resArray)
					{
						$index = new Datetime($resArray['vaktrap_obser_date']);
						$observationResArray[$observation['observation_id']][$index->format('y-m-d')] = $resArray;	
					}*/
					
				}
				$end = $end->modify( '-1 day' );
				#$weekplanArray = KD::getModel('client/weekplan')->loadListByDate($patientID,$begin->format("Y-m-d"),$end->format("Y-m-d"));
                $this->view->weekplanCollection = KD::getModel('client/weekplan')->loadByWeek($patientID, date('W'), date('Y'));
				
				//echo '<prE>';print_r($observationResArray);exit();
				
				$maalplanInfo = KD::getModel('client/maalplan')->getActiveMaalPlan($patientID);
				$format = KD::getModel('core/format');
				//echo '<prE>';print_r($maalplanInfo);exit();
				$data = array();

				// Dates are now synced according to Målstyringsplan, so we can safely rely on stored data.
				$vaktrapInfo['vaktrap_tilspan_from_date'] = (isset($vaktrapInfo['vaktrap_tilspan_from_date'])
					&& $vaktrapInfo['vaktrap_tilspan_from_date'] !== '0000-00-00 00:00:00')
					? $vaktrapInfo['vaktrap_tilspan_from_date']
					: $maalplanInfo['maalplan_tiltak_fromDate'];
					
				$vaktrapInfo['vaktrap_tilspan_to_date'] = (isset($vaktrapInfo['vaktrap_tilspan_to_date'])
					&& $vaktrapInfo['vaktrap_tilspan_to_date'] !== '0000-00-00 00:00:00')
					? $vaktrapInfo['vaktrap_tilspan_to_date']
					: $maalplanInfo['maalplan_tiltak_toDate'];

				
                $vaktrapInfo['vaktrap_maalplan_from_date'] = (isset($vaktrapInfo['vaktrap_maalpan_from_date'])
                    && $vaktrapInfo['vaktrap_maalpan_from_date'] !== '0000-00-00 00:00:00')
                    ? $vaktrapInfo['vaktrap_maalpan_from_date']
                    : $maalplanInfo['maalplan_maalsty_fromDate'];

                $vaktrapInfo['vaktrap_maalplan_to_date'] = (isset($vaktrapInfo['vaktrap_maalpan_to_date'])
                    && $vaktrapInfo['vaktrap_maalpan_to_date'] !== '0000-00-00 00:00:00')
                    ? $vaktrapInfo['vaktrap_maalpan_to_date']
                    : $maalplanInfo['maalplan_maalsty_toDate'];

				$vaktrapInfo['nextLink'] = $nextLink;
				$vaktrapInfo['previousLink'] = $previousLink;
				$this->view->maalCollection = $maalArray;
				$this->view->instTiltakCollection = $instTiltakArray;
				$this->view->vakGovTiltakCollection = $vakGovTiltakArray;
				$this->view->observationCollection = $observationArray;
				$this->view->observationResCollection = $observationResArray;
				$this->view->vaktrapInfo = $vaktrapInfo;
				$this->view->vaktrapInfoData = $data;
                $this->view->maalPlanInfo = $maalplanInfo;
				// Logg Colletion
				$loggArray = KD::getModel('client/logg')->getLoggByVaktrap($vaktrapID,$patientID);
				$vaktrapInfo_Counter = unserialize($vaktrapInfo['vaktrap_counters']);
				$vaktrapInfo_Counter['logg'] = count($loggArray);
				$vaktrapInfo['vaktrap_counters'] = serialize($vaktrapInfo_Counter);
				$this->view->vaktrapInfo = $vaktrapInfo;
//echo "<pre>"; print_r($vaktrapInfo); exit;				
                #var_dump($loggArray);die;
				if(count($loggArray)>0) $loggArray = $loggArray[0] ;
				$loggArray['vaktrapId'] = $vaktrapID;
				$loggArray['patientId'] = $patientID;
				$this->view->loggInfo = $loggArray;
				// Force Collection
				$forceArray = KD::getModel('client/force')->getForceByVaktrap($vaktrapID,$patientID);
				if(count($forceArray)>0) $forceArray = $forceArray[0] ;
				
				$force214Array = KD::getModel('client/force214')->getForceByVaktrap($vaktrapID,$patientID);
				if(count($force214Array)>0) $force214Array = $force214Array[0] ;
				$forceArray = $forceArray + $force214Array;
				
				$force2511Array = KD::getModel('client/force2511')->getForceByVaktrap($vaktrapID,$patientID);
				if(count($force2511Array)>0) $force2511Array = $force2511Array[0] ;
				$forceArray = $forceArray + $force2511Array;

				$forceArray['vaktrapId'] = $vaktrapID;
				$forceArray['patientId'] = $patientID;
				$this->view->forceInfo = $forceArray;
				
				// Medicine Vaktrapport Collection
				$weekDays = array(0=>'mon',1=>'tue',2=>'wed',3=>'thu',4=>'fri',5=>'sat',6=>'sun');
				$medicineVaktrapArray = array();
				foreach($weekDays as $day)
				{
					$medicineVaktrapArray[$day] = KD::getModel('vaktrapport/medvaktrap')->getMedVakByDate($patientID,$vaktrapID,$vaktrapInfo['vaktrap_from_date'],$vaktrapInfo['vaktrap_to_date'],$day); 
				}
				$clientID = $patientID;
				$vaktrapID = $vaktrapID;
				$userID = $vaktrapInfo['vaktrap_userID'];
				$departmentID = $vaktrapInfo['vaktrap_deptID'];
				$this->view->medicineVaktrapCollection = $medicineVaktrapArray;
				$this->view->userID = $userID;
				$this->view->departmentID = $departmentID;
				$this->view->clientID = $clientID;
				$this->view->vaktrapID = $vaktrapID;

                $this->view->treatmentPersonnel = TreatmentPersonnel::getTreatmentPersonnel($vaktrapID, $patientID, $vaktrapInfo);

				if(isset($vaktrapInfo['vaktrap_genogram']) && $vaktrapInfo['vaktrap_genogram']!='')
				{
					$this->view->initString = $vaktrapInfo['vaktrap_genogram'];
				}
				else
				{
					$this->view->initString = '{"class":"go.GraphLinksModel","linkFromPortIdProperty": "fromPort","linkToPortIdProperty":"toPort"}';
				}
				
				$this->view->title = $this->view->translate('Security Report For').'  '.KD::getModel('client/client')->getClient($clientID,'name').'';
				$this->view->className = 'PTCLEFTVAKTRAPORT';
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
				$this->_redirect('/vaktrapport/index/');
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/vaktrapport/index/');
		}
    }

	public function vaktraparchiveAction() {
		$vaktrapID = $this->getRequest()->getParam('id');        
		if(isset($vaktrapID) && $vaktrapID > 0)
		{
			$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->getVaktrap($vaktrapID);

			// If they are mannually Adding some Id in address bar which is not exist 
			// Here if vaktrapport is not active you are redirected to invalid page
			if(!empty($vaktrapArray) && $vaktrapArray[0]['vaktrap_status']=='no')
			{
				$vaktrapInfo = $vaktrapArray[0];
				$patientID = $vaktrapInfo['vaktrap_patientID'];
				$maalDataArray = (isset($vaktrapInfo['vaktrap_maaldesc']) && $vaktrapInfo['vaktrap_maaldesc']!='')?unserialize($vaktrapInfo['vaktrap_maaldesc']):array();
				
				
				$vaktrapportArray = KD::getModel('vaktrapport/vaktrapport')->loadList($patientID,"all"); 

				$nextLink = "";
				$previousLink = "";
				
				$cureentIndex = "";
				
				for($i=0;$i<count($vaktrapportArray);$i++)
				{
					if($vaktrapID==$vaktrapportArray[$i]['vaktrap_id'])
					{
						$cureentIndex = $i;
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
				
				$count = 1; 
				$maalArray = array();
				$instTiltakArray = array();
				$vakGovTiltakArray = array();
				$instTiltakNextArray = array();
				$vakGovTiltakNextArray = array();
				$observationArray = array();
				$observationResArray = array();
				//$observationArray = KD::getModel('vaktrapport/vaktrapobser')->loadObservation($patientID,$vaktrapID,'none',true);
				$observationArray = (isset($vaktrapInfo['vaktrap_observationdesc']) && $vaktrapInfo['vaktrap_observationdesc']!='')?unserialize($vaktrapInfo['vaktrap_observationdesc']):array();
				foreach($maalDataArray as $maal)
				{
					$maalArray[$maal['maal_id']] = array('maal_id'=>$maal['maal_id'],'maal_desc'=>$maal['maal_desc'],'counter'=>$count++);
					$instTiltakArray[$maal['maal_id']] = KD::getModel('client/tiltakinst')->loadListByMaal($patientID,$maal['maal_id'],$vaktrapID,false);
					$vakGovTiltakArray[$maal['maal_id']] = KD::getModel('vaktrapport/vaktraptilgov')->loadListByMaal($patientID,$maal['maal_id'],$vaktrapID,false);
				}
				$vaktrapFromDate = $vaktrapInfo['vaktrap_from_date'];
				$vaktrapToDate = $vaktrapInfo['vaktrap_to_date'];
				$begin = new DateTime($vaktrapFromDate);
				$endcheck = new DateTime($vaktrapToDate);
				$difference = $endcheck->diff($begin)->format("%a");
				if(isset($vaktrapToDate) && $vaktrapToDate!='' && (strpos($vaktrapToDate,'0000')===false) && $difference<=7)
				{
					$end = new DateTime($vaktrapToDate);
				}
				else
				{
					$end = clone $begin;
					$end->add(new DateInterval('P6D'));
					$hideObservation = true;
				}
				$end = $end->modify( '+1 day' );
				$interval = new DateInterval('P1D');
				$daterange = new DatePeriod($begin, $interval ,$end);
				
				
				$vaktrapFromDate = $vaktrapInfo['vaktrap_from_date'];
				$vaktrapToDate = $vaktrapInfo['vaktrap_to_date'];
				$begin = new DateTime($vaktrapFromDate);
				$endcheck = new DateTime($vaktrapToDate);
				$difference = $endcheck->diff($begin)->format("%a");
				if(isset($vaktrapToDate) && $vaktrapToDate!='' && (strpos($vaktrapToDate,'0000')===false) && $difference<=7)
				{
					$end = new DateTime($vaktrapToDate);
				}
				else
				{
					$end = clone $begin;
					$end->add(new DateInterval('P6D'));
					$hideObservation = true;
				}
				$end = $end->modify( '+1 day' );
				$interval = new DateInterval('P1D');
				$daterange = new DatePeriod($begin, $interval ,$end);
				
				
				foreach($observationArray as $observation)
				{
					$daterange = new DatePeriod($begin, $interval ,$end);
					foreach($daterange as $date){
						$observationResArray[$observation['observation_id']][$date->format('y-m-d')] = KD::getModel('vaktrapport/vaktrapobser')->loadListByDate($patientID,/*$vaktrapID,*/$observation['observation_id'],$date->format("Y-m-d"));
					}				
				}
				
				$instTiltakNextArray = KD::getModel('client/tiltakinst')->loadList($patientID,$vaktrapID,'none',true);
				$vakGovTiltakNextArray = KD::getModel('vaktrapport/vaktraptilgov')->loadList($patientID,$vaktrapID,'none',true);
				
				$end = $end->modify( '-1 day' );
				$weekplanArray = KD::getModel('client/weekplan')->loadListByDate($patientID,$begin->format("Y-m-d"),$end->format("Y-m-d"));;
				$this->view->weekplanCollection = $weekplanArray;
				
				$this->view->maalCollection = $maalArray;
				$this->view->instTiltakCollection = $instTiltakArray;
				$this->view->vakGovTiltakCollection = $vakGovTiltakArray;
				$this->view->instTiltakNextCollection = $instTiltakNextArray;
				$this->view->vakGovTiltakNextCollection = $vakGovTiltakNextArray;
				$this->view->observationCollection = $observationArray;
				$this->view->observationResCollection = $observationResArray;
				$this->view->vaktrapInfo = $vaktrapInfo;
				$this->view->title = $this->view->translate('Security Report For').'  '.KD::getModel('client/client')->getClient($patientID,'name').'';

                $loggArray = KD::getModel('client/logg')->getLoggByVaktrap($vaktrapID, $patientID, null);
				$this->view->logArchive = $loggArray;
				$vaktrapInfo_Counter = unserialize($vaktrapInfo['vaktrap_counters']);
				$vaktrapInfo_Counter['logg'] = count($loggArray);
				$vaktrapInfo['vaktrap_counters'] = serialize($vaktrapInfo_Counter);
				$this->view->vaktrapInfo = $vaktrapInfo;
				
				$this->view->className = 'PTCLEFTVAKTRAPORT';
				if(isset($vaktrapInfo['vaktrap_genogram']) && $vaktrapInfo['vaktrap_genogram']!='')
				{
					$this->view->initString = $vaktrapInfo['vaktrap_genogram'];
				}
				else
				{
					$this->view->initString = '{"class":"go.GraphLinksModel","linkFromPortIdProperty": "fromPort","linkToPortIdProperty":"toPort"}';
				}
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
				$this->_redirect('/vaktrapport/index/');
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/vaktrapport/index/');
		}
    }
	public function saveAction() {
        #TODO 2015-05-30 Kardigan AS, Code review: Filter all input data
		$vaktrapID = $_POST['vaktrap_id'];
		$patientID = $_POST['vaktrap_patientID'];
        $vaktrapObsUpdFlag = array();
        $vaktrapObsInsFlag = array();
		if(isset($_POST['save_report']) || isset($_POST['new_report']) || isset($_POST['freez_report']))
		{
			// Saving Vaktrapport
			if(isset($vaktrapID) && $vaktrapID > 0)
			{
				$vaktrapData = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapID);
				if($vaktrapData['vaktrap_patientID'] == $patientID)
				{
					$vakGovTiltakRes = array(); 
					$vakGovTiltakExpl = array(); 
					$vakInsTiltakRes = array(); 
					$vakInsTiltakExpl = array(); 
					$observationNew = array();
					$observation = array();

					$format = KD::getModel('core/format');
					$vaktFrom = $_POST['vaktrap_from_date'];
					$vaktTo = $_POST['vaktrap_to_date'];
					$vaktFromPreg = preg_match("/(\d{4})-(\d{2})-(\d{2})/",$format->PrepareDateDB($vaktFrom),$matchFrom);
					$vaktToPreg = preg_match("/(\d{4})-(\d{2})-(\d{2})/",$format->PrepareDateDB($vaktTo),$matchTo);
					
					//For Updateing status we are using Id Array Starts Here
					$vakInsTiltakIDsArray = array(); //$vakInsTiltakIDsArray[] =0;
					$vaktrapInsFlag = array();
					$vakInsTiltakCarryIDsArray = array(); // This array contain list of all Ids which are carry forworded to next Vaktrapport
					$vakGovTiltakIDsArray = array(); //$vakGovTiltakIDsArray[] =0;
					$vaktrapGovFlag = array();
					$vakGovTiltakCarryIDsArray = array(); // This array contain list of all Ids which are carry forworded to next Vaktrapport

					//For Updateing status we are using Id Array Endss Here
					if(isset($_POST['vakGovTiltakRes'])){$vakGovTiltakRes = $_POST['vakGovTiltakRes'];unset($_POST['vakGovTiltakRes']);}
					if(isset($_POST['vakGovTiltakExpl'])){$vakGovTiltakExpl = $_POST['vakGovTiltakExpl'];unset($_POST['vakGovTiltakExpl']);}
					if(isset($_POST['vakInsTiltakRes'])){$vakInsTiltakRes = $_POST['vakInsTiltakRes'];unset($_POST['vakInsTiltakRes']);}
					if(isset($_POST['vakInsTiltakExpl'])){$vakInsTiltakExpl = $_POST['vakInsTiltakExpl'];unset($_POST['vakInsTiltakExpl']);}
					if(isset($_POST['observationNew'])){$observationNew = $_POST['observationNew'];unset($_POST['observationNew']);}
					if(isset($_POST['observation'])){$observation = $_POST['observation'];unset($_POST['observation']);}
	
					// Updating vaktrap_tiltak_government Table
					if(!empty($vakGovTiltakRes))
					{
						foreach($vakGovTiltakRes as $vakGovTiltakId=> $vakGovTiltakResult)
						{
							$data = array();
							$data['vaktrap_tilgov_result'] = $vakGovTiltakResult;
							if(isset($vakGovTiltakExpl[$vakGovTiltakId]) && ($vakGovTiltakResult!=2)){$data['vaktrap_tilgov_explanation'] = $vakGovTiltakExpl[$vakGovTiltakId];}else{$data['vaktrap_tilgov_explanation'] = '';}
							$vaktrapGovFlag[] = KD::getModel('vaktrapport/vaktraptilgov')->update($data,'vaktrap_tilgov_id',$vakGovTiltakId);//$vakGovTiltakId
						}
					}
					
					//echo '<pre>';print_r($vakGovTiltakArray);
					// Updating tiltak_institute Table
					if(!empty($vakInsTiltakRes))
					{
						foreach($vakInsTiltakRes as $vakInsTiltakId=> $vakInsTiltakResult)
						{
							$data = array();
							$data['tilins_result'] = $vakInsTiltakResult;
							if(isset($vakInsTiltakExpl[$vakInsTiltakId]) && ($vakInsTiltakResult!=2)){$data['tilins_explanation'] = $vakInsTiltakExpl[$vakInsTiltakId];}else{$data['tilins_explanation'] = '';}
							$vaktrapInsFlag[] = KD::getModel('client/tiltakinst')->update($data,'tilins_id',$vakInsTiltakId);//$vakInsTiltakId
						}
					}
					
					// Update Observation Comes First as Upadation work upon Database so if you put insert first it will set result to recently added Observation

					//if(!empty($observation))
					{
						$observationModel = KD::getModel('client/observation');
						$observationResModel = KD::getModel('vaktrapport/vaktrapobser');
						$observationArray = $observationModel->loadList($patientID,'active');
						$startDateObj = new DateTime($format->PrepareDateDB($vaktFrom));
						if(!(isset($vaktTo) && $matchFrom[3]>0 && $matchTo[3]>0))
						{
							$endDateObj = clone $startDateObj;
							$endDateObj->add(new DateInterval('P7D'));
						}
						else
						{
							$endDateObj= new DateTime($format->PrepareDateDB($vaktTo));
						    $endDateObj->add(new DateInterval('P1D'));
						}
						while($startDateObj->format("Y-m-d")!=$endDateObj->format("Y-m-d"))
						{
							foreach($observationArray as $observationData)
							{
								$observationID = $observationData['observation_id'];
								//$observationResArray = $observationResModel->loadListByDate($patientID,$vaktrapID,$observationID,$startDateObj->format("Y-m-d"));
								$observationResArray = $observationResModel->loadListByDate($patientID,/*$vaktrapID,*/$observationID,$startDateObj->format("Y-m-d"));
								if(!empty($observationResArray))
								{
									$data = array();
									$data['vaktrap_obser_res'] = '0';
									if(isset($observation[$observationID][$observationResArray['vaktrap_obser_id']]))
									{
										$data['vaktrap_obser_res'] = '1';
									}
									$vaktrapObsUpdFlag[] = $observationResModel->update($data,'vaktrap_obser_id',$observationResArray['vaktrap_obser_id']);
								}
								else
								{
									$data = array();
									$data['vaktrap_obser_res'] = '0';
									if(isset($observationNew[$observationID]))
									{
										$data['vaktrap_obser_res'] = isset($observationNew[$observationID][$startDateObj->format("Y-m-d")])?'1':'0';	
									}
									$data['vaktrap_observationID'] = $observationID;$data['vaktrap_patientID'] = $patientID;$data['vaktrap_vaktrapID'] = $vaktrapID;$data['vaktrap_obser_date'] = $startDateObj->format("Y-m-d");

									$vaktrapObsInsFlag[] = $observationResModel->insert($data);//$resArray['vaktrap_obser_id']
								}
								
							}
							$startDateObj->add(new DateInterval('P1D'));
						}
					}
					// Inserting Observation
					/*$vaktrapObsInsFlag = array();
					if(!empty($observationNew))
					{
						$format = KD::getModel('core/format');
						echo '<pre>';print_r($_POST['vaktrap_to_date']);exit();
						$toDate = date_create($format->PrepareDateDB($_POST['vaktrap_to_date']));
						foreach($observationNew as $observationId=> $observationData)
						{
							$observationModel = KD::getModel('client/observation')->load($observationId);
							foreach($observationData as $id=> $observationRes)
							{
								$checkDate=date_create($observationRes);
								$diff=date_diff($checkDate,$toDate);
								$sign = $diff->format("%R");
								// save only if Vaktrapport To date is more than Observation Res Date
								if($sign == '+')
								{
									$data = array();$data['vaktrap_observationID'] = $observationId;$data['vaktrap_patientID'] = $patientID;$data['vaktrap_vaktrapID'] = $vaktrapID;$data['vaktrap_obser_date'] = $observationRes;$data['vaktrap_obser_res'] = 1;
									$vaktrapObsInsFlag[] = KD::getModel('vaktrapport/vaktrapobser')->insert($data);
								}
							}
						}
					}*/
					
					// Updating vaktrap Table	
				
					$loggArray = KD::getModel('client/logg')->getLoggByVaktrap($vaktrapID,$patientID,false);
					// Force Collection
					$forceArray = KD::getModel('client/force')->getForceByVaktrap($vaktrapID,$patientID,false);
					//
					$medicineCounter = 0;
					if(isset($vaktFrom,$vaktTo) && $matchFrom[3]>0 && $matchTo[3]>0)
					{
						$medicineCounter = KD::getModel('vaktrapport/medvaktrap')->getMedCountByDate($patientID,$format->PrepareDateDB($vaktFrom),$format->PrepareDateDB($vaktTo));
					}
					// Use For Tiltak Counters
					
					// Using this to get IDS of all Vaktrap Goverment Tiltak Used in report and are active
					$vakGovTiltakArray = KD::getModel('vaktrapport/vaktraptilgov')->loadList($patientID,$vaktrapID,'active');
					foreach($vakGovTiltakArray as $Gov)
					{
						$vakGovTiltakIDsArray[] =  $Gov['vaktrap_tilgov_id'];
						if($Gov['vaktrap_tilgov_result']!=2)
						{
							$vakGovTiltakCarryIDsArray[] =  $Gov['vaktrap_tilgov_id'];
						}
					}
					
					// Using this to get IDS of all Vaktrap Institute Tiltak Used in report and are active
					$vakInsTiltakArray = KD::getModel('client/tiltakinst')->loadList($patientID,$vaktrapID,'active');
					foreach($vakInsTiltakArray as $Ins)
					{
						$vakInsTiltakIDsArray[] =  $Ins['tilins_id'];
						if($Ins['tilins_result']!=2)
						{
							$vakInsTiltakCarryIDsArray[] =  $Ins['tilins_id'];
						}
					}
					$vaktrapCounters = array();
					$vaktrapCounters['gov'] = count($vakGovTiltakIDsArray);//-1 Because of we have added 0 as id for updation
					$vaktrapCounters['ins'] = count($vakInsTiltakIDsArray);//-1 Because of we have added 0 as id for updation
					$vaktrapCounters['force'] = count($forceArray);
					$vaktrapCounters['medicine'] = $medicineCounter;
					$vaktrapCounters['avvik'] = 0;
					$vaktrapCounters['logg'] = count($loggArray);

					$data = array();
					$data = $_POST;
					$data['vaktrap_counters'] = serialize($vaktrapCounters);

					$vaktrapFlag = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$vaktrapID);

					#KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$vaktrapID);
					$errorFlag = false;
					if(!$vaktrapFlag){$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is some problem while saving Vaktrapport data'));$errorFlag =true;}
					if(in_array(0,$vaktrapGovFlag)){$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is some problem while saving Government Tiltak data'));$errorFlag =true;}
					if(in_array(0,$vaktrapInsFlag)){$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is some problem while saving Institute Tiltak data'));$errorFlag =true;}
					if(in_array(0,$vaktrapObsUpdFlag) || in_array(0,$vaktrapObsInsFlag)){$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is some problem while saving Observation data'));$errorFlag =true;}

					if(!$errorFlag)
					{
						
						//Once Vaktrapport is saved successfully you can lock or freez that vaktrapport report
						if(isset($_POST['new_report']) || isset($_POST['freez_report']))
						{
							// Before Locking we are checking the Force & logg are active for thatperiod
							$loggCount = KD::getModel('client/logg')->getLoggByVaktrap($vaktrapID,$patientID);
							$forceCount = KD::getModel('client/force')->getForceByVaktrap($vaktrapID,$patientID);
							
							if(count($loggCount)==0)
							{
							  if(count($forceCount)==0)
							  {
								// We are locking the report
								$data = array();$data['vaktrap_status'] = 'no';
								//save Maal Details in Vaktrapport Table
								$maalArray = KD::getModel('client/maal')->loadList($patientID);
								//$maalIDs = array();
								$maalDATAs = array();
								foreach($maalArray as $maal)
								{
									//$maalIDs[] = $maal['maal_id'];
									$maalDATAs[$maal['maal_id']] = array('maal_id'=>$maal['maal_id'],'maal_desc'=>$maal['maal_desc'], 'maal_from_date'=>$maal['maal_from_date'], 'maal_to_date'=>$maal['maal_to_date'], 'maal_order'=>$maal['maal_order'], 'maal_achived_status'=>$maal['maal_achived_status']);
								}
								$observationArray = KD::getModel('client/observation')->loadList($patientID);
								$observationDATAs = array();
								foreach($observationArray as $observation)
								{
									$observationDATAs[$observation['observation_id']] = array('observation_id'=>$observation['observation_id'],'observation_desc'=>$observation['observation_desc'], 'observation_type'=>$observation['observation_type'], 'observation_relationID'=>$observation['observation_relationID'], 'observation_order'=>$observation['observation_order']);
								}
								//echo '<pre>';echo serialize($maalIDs);exit();
								$data['vaktrap_observationdesc'] = serialize($observationDATAs);
								$data['vaktrap_maaldesc'] = serialize($maalDATAs);
								$data['vaktrap_lock_date'] = date("Y-m-d H:i:s");
								//save Maal Details in Vaktrapport Table 
								$vaktrapLockFlag = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$vaktrapID);
								
                               $this->_logUserActionOnReport($vaktrapID, $patientID, 'Lock');
                                if($vaktrapLockFlag)
								{
									// Once Vaktrapport is Locked we can change status of the Government & Institute Tiltak  Start
									if(!empty($vakGovTiltakIDsArray))
									{
										KD::getModel('vaktrapport/vaktraptilgov')->archiveList($vakGovTiltakIDsArray,'no');
									}
									if(!empty($vakInsTiltakIDsArray))
									{
										KD::getModel('client/tiltakinst')->archiveList($vakInsTiltakIDsArray,'no');
									}


									// Once Vaktrapport is Locked we can change status of the Government & Institute Tiltak Done
									
									if(isset($_POST['new_report']))
									{
										// If its Lock than we have to create ne vaktrapport
										$clintInfo = KD::getModel('client/client')->load($patientID);
										$data = array();
										$data['vaktrap_patientID'] = $patientID;
										$data['vaktrap_deptID'] = $clintInfo['patient_deptID'];
										
										// Setting Previous ID and From and To dates
										$data['vaktrap_from_date'] = $_POST['vaktrap_to_date'];
										$data['vaktrap_previousID'] = $vaktrapID;
										
										$maalPlanDates = KD::getModel('client/maalplan')->getMaalPlanForNew($patientID);
										foreach($maalPlanDates as $maalPlanDate)
										{
											$data['vaktrap_tilspan_from_date'] = $format->FormatDate($maalPlanDate['maalplan_tiltak_fromDate']);
											$data['vaktrap_tilspan_to_date'] = $format->FormatDate($maalPlanDate['maalplan_tiltak_toDate']);
											$data['vaktrap_maalpan_from_date'] = $format->FormatDate($maalPlanDate['maalplan_maalsty_fromDate']);
											$data['vaktrap_maalpan_to_date'] = $format->FormatDate($maalPlanDate['maalplan_maalsty_toDate']);
										}
										
										$startDateObj = new DateTime($format->PrepareDateDB($_POST['vaktrap_to_date']));
										$endDateObj = clone $startDateObj;
										$endDateObj->add(new DateInterval('P7D'));
										$data['vaktrap_to_date'] = $format->FormatDate($endDateObj->format('Y-m-d'));
										// Setting Previous ID and From and To dates
										
										$vaktrapInserId = KD::getModel('vaktrapport/vaktrapport')->insert($data);
                                        $this->_logUserActionOnReport($vaktrapInserId, $patientID, 'Create');
										if($vaktrapInserId)
										{
											// Once New vaktrapport is created we can directly insert Not completed and partially completed tiltak to Vaktrap_Government_tiltak and 
											// Insitution tiltak directly from table using function
											//print_r($vakGovTiltakCarryIDsArray);exit();
											if(!empty($vakGovTiltakCarryIDsArray))
											{
												$newGovTiltaks = KD::getModel('vaktrapport/vaktraptilgov')->getGovTiltListToInsert($vakGovTiltakCarryIDsArray);
												if(!empty($newGovTiltaks))
												{
													$newGovTiltakInsertedIds = array();
													foreach($newGovTiltaks as $newGovTiltak)
													{
														$newGovTiltakInsertedIds[] = KD::getModel('vaktrapport/vaktraptilgov')->insert($newGovTiltak);
													}
													$data = array();
													$data['vaktrap_vaktrapID'] = $vaktrapInserId;
													$data['vaktrap_previous_vaktrapID'] = $vaktrapID;
													KD::getModel('vaktrapport/vaktraptilgov')->update($data,'vaktrap_tilgov_id',$newGovTiltakInsertedIds,true);
												}
											}
											
											if(!empty($vakInsTiltakCarryIDsArray))
											{
												$newInsTiltaks = KD::getModel('client/tiltakinst')->getInsTiltListToInsert($vakInsTiltakCarryIDsArray);
												if(!empty($newInsTiltaks))
												{
													$newInsTiltakInsertedIds = array();
													foreach($newInsTiltaks as $newInsTiltak)
													{
														$newInsTiltakInsertedIds[] = KD::getModel('client/tiltakinst')->insert($newInsTiltak);
													}
													$data = array();
													$data['tilins_vaktrapportID'] = $vaktrapInserId;
													$data['tilins_previous_vaktrapportID'] = $vaktrapID;
													KD::getModel('client/tiltakinst')->update($data,'tilins_id',$newInsTiltakInsertedIds,true);
												}
											}
											
											$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Vaktrapport Saved Successfully And New Shift is created'));
											$this->_redirect('/client/mto/govtiltak/t/6/id/'.$patientID);
										}
										else
										{ 
										 $this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Vaktrapport Saved Successfully But New Shift is not created properly'));
										}
									}
									elseif(isset($_POST['freez_report']))
									{
									  $data = array();
									  //save Freeze Date Vaktrapport Table 
									  $data['vaktrap_freeze_date'] = date("Y-m-d H:i:s");
									  $vaktrapFreezFlag = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$vaktrapID);
                                        $this->_logUserActionOnReport($vaktrapID, $patientID, 'Freeze');
                                        $data = array();
                                        //save Freeze Date Vaktrapport Table
                                        $data['patient_freeze_status'] = 'yes';
                                        KD::getModel('client/client')->update($data,'patient_id',$patientID);
									  if($vaktrapFreezFlag)
									  {
										  $this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Vaktrapport Saved Successfully And Clint has been Freezed'));
									  }
									  else
									  { 
										 $this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Vaktrapport Saved Successfully But New Clint has Not Freezed'));
									  }
										
									}
									$this->_redirect('/vaktrapport/info/index/id/'.$vaktrapID);
								}
							  }
								else
							  {
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Vaktrapport Saved Successfully But Force is Active, So you cant Lock Vaktrapport Before Locking Active Force'));
								$this->_redirect('/vaktrapport/info/index/id/'.$vaktrapID);
							  }
							}
							else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Vaktrapport Saved Successfully But Logg is Active, So you cant Lock Vaktrapport Before Locking Active Logg'));
								$this->_redirect('/vaktrapport/index/index/id/'.$vaktrapID);
							}
						}
						else
						{
                            $this->_logUserActionOnReport($vaktrapID, $patientID, 'Save');
							// Show success message to User
							$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Vaktrapport Saved Successfully'));
						}
					}
					else
					{
						//Once Vaktrapport is not saved successfully, so you cant lock or freez that vaktrapport report
						if(isset($_POST['new_report']) || isset($_POST['freez_report']))
						{
							$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Vaktrapport is not saved successfully, so you cant lock or freez that vaktrapport report'));
						}
					}
					$this->_redirect('/vaktrapport/info/index/id/'.$vaktrapID);
					
				}
				else
				{
					// Patient Id of data base for that vaktrapport report does not match with POST patient ID
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Post'));
					$this->_redirect('/vaktrapport/info/index/id/'.$vaktrapID);
					exit();
				}
			}
			else
			{
				// invalid Vaktrapport ID
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
				$this->_redirect('/vaktrapport/info/index/id/'.$vaktrapID);
				exit();
			}
		}    
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/vaktrapport/index');
			exit();
		}
	}
	
	public function saveloggAction()
	{
		$patientId = $this->getRequest()->getParam('id');
        if(empty($patientId)) {
            return $this->_httpErrorRedirect('Invalid Patient Id for Logg', '/vaktrapport/index/index');
        }

        if(!Log::save($patientId, $this->getRequest())) {
            return $this->_httpErrorRedirect(Log::getResponseText(), '/vaktrapport/index/index');
        }

        return $this->_httpSuccessRedirect(
            Log::getResponseText(),
            '/vaktrapport/info/index/t/3/id/' . Log::getReportId()
        );
/**
		if(isset($patientId) && ($patientId == $_POST['logg_patientID']))
		{
			$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->getCurrentVaktrap($patientId);
			$vaktrapId = $vaktrapArray['vaktrap_id'];
			if(isset($vaktrapId) && ($vaktrapId == $_POST['logg_vaktrapID'])) 
			{
				$loggArray = KD::getModel('client/logg')->getLoggByVaktrap($vaktrapId,$patientId);
				if(count($loggArray)>0) $loggArray = $loggArray[0] ;
				if(count($loggArray)>0 && ($loggArray['logg_id'] == $_POST['logg_id']))
				{
					$flag = KD::getModel('client/logg')->update($_POST,'logg_id',$_POST['logg_id']);
					$opName = $this->view->translate('Changed');
					if($_POST['lock_logg'])
					{
						$data = array();
                        $data['logg_status']='no';
                        $data['logg_locked_by']=$_SESSION['Acl']['userID'];
                        $data['logg_locked_at']=date("Y-m-d H:i:s");

						$flag = KD::getModel('client/logg')->update($data,'logg_id',$_POST['logg_id']);
						$opName = $this->view->translate('Locked');
						
						$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapId);
						$counters = unserialize($vaktrapDetail['vaktrap_counters']);
						$counters['logg'] = $counters['logg'] + 1;
						$counters = serialize($counters);
						$data = array();
                        $data['vaktrap_counters'] = $counters;
						$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$vaktrapId);
						
						
						//SENDING MAIL FOR LOGG
						$patientDetail = KD::getModel('client/client')->load($patientId);
						$dataEmail = array();$dataEmail['type'] = 'Logg';$dataEmail['client'] = KD::getModel('client/client')->getClient($patientId);$dataEmail['department'] = KD::getModel('client/client')->getClient($patientDetail['patient_deptID']);$dataEmail['date'] = KD::getModel('core/format')->FormatDate(date("Y-m-d"));$dataEmail['user'] = KD::getModel('user/user')->getUser($_SESSION['Acl']['userID']);$dataEmail['time'] = date('H:i:s');$dataEmail['identity'] = 'staff';$dataEmail['name'] = 'Admin';
						parent::sendEmail(KD::getModel('system/system')->getEmail(),'System','email.phtml',$dataEmail);
					}
				}
				else
				{
					$data = array();$data = $_POST;
					if($_POST['lock_logg'])
					{
						$data['logg_status']='no';$data['logg_locked_by']=$_SESSION['Acl']['userID'];$data['logg_locked_at']=date("Y-m-d H:i:s");
						
						
						$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapId);
						$counters = unserialize($vaktrapDetail['vaktrap_counters']);
						$counters['logg'] = $counters['logg']+1;
						$counters = serialize($counters);
						$dataVak = array();$dataVak['vaktrap_counters'] = $counters;
						$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->update($dataVak,'vaktrap_id',$vaktrapId);
						
						
						//SENDING MAIL FOR LOGG
						$patientDetail = KD::getModel('client/client')->load($patientId);
						$dataEmail = array();$dataEmail['type'] = 'Logg';$dataEmail['client'] = KD::getModel('client/client')->getClient($patientId);$dataEmail['department'] = KD::getModel('client/client')->getClient($patientDetail['patient_deptID']);$dataEmail['date'] = KD::getModel('core/format')->FormatDate(date("Y-m-d"));$dataEmail['user'] = KD::getModel('user/user')->getUser($_SESSION['Acl']['userID']);$dataEmail['time'] = date('H:i:s');$dataEmail['identity'] = 'staff';$dataEmail['name'] = 'Admin';
						parent::sendEmail(KD::getModel('system/system')->getEmail(),'System','email.phtml',$dataEmail);
						
					}
					$flag = KD::getModel('client/logg')->insert($data);
					if($data['logg_status']=='no')
					{
						$opName = $this->view->translate('Locked');
					}
					else
					{
						$opName = $this->view->translate('Created');
					}
				}
				if($flag)
				{
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Logg %s Successfully',$opName));
					$this->_redirect('/vaktrapport/info/index/t/3/id/'.$vaktrapId);
				}
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Logg',$opName));
					$this->_redirect('/vaktrapport/index/index');
				}
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Vaktrapport Id for Logg'));
				$this->_redirect('/vaktrapport/index/index');
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Patient Id for Logg'));
			$this->_redirect('/vaktrapport/index/index');
		} **/
	}
	public function saveforceAction()
	{
		
		$patientId = $this->getRequest()->getParam('id'); 
                
		if(isset($patientId) && ($patientId == $_POST['force_patientID']))
		{
			$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->getCurrentVaktrap($patientId);
			$vaktrapId = $vaktrapArray['vaktrap_id'];
			if(isset($vaktrapId) && ($vaktrapId == $_POST['force_vaktrapID'])) 
			{
				$forceArray = KD::getModel('client/force')->getForceByVaktrap($vaktrapId,$patientId);
				if(count($forceArray)>0) $forceArray = $forceArray[0] ;
				$data = array();$data = $_POST['force'];
				// Set Status
				
				$data['force_chk21_status'] = isset($data['force_chk21_status'])?($data['force_chk21_status']):'0';
				$data['force_chk22_status'] = isset($data['force_chk22_status'])?($data['force_chk22_status']):'0';
				$data['force_chk23_status'] = isset($data['force_chk23_status'])?($data['force_chk23_status']):'0';
				$data['force_chk24_status'] = isset($data['force_chk24_status'])?($data['force_chk24_status']):'0';
				$data['force_chk25_status'] = isset($data['force_chk25_status'])?($data['force_chk25_status']):'0';
				$data['force_chk26_status'] = isset($data['force_chk26_status'])?($data['force_chk26_status']):'0';
				$data['force_chk27_status'] = isset($data['force_chk27_status'])?($data['force_chk27_status']):'0';
				$data['force_chk28_status'] = isset($data['force_chk28_status'])?($data['force_chk28_status']):'0';
				$data['force_chk29_status'] = isset($data['force_chk29_status'])?($data['force_chk29_status']):'0';
				$data['force_chk210_status'] = isset($data['force_chk210_status'])?($data['force_chk210_status']):'0';
				$data['force_chk211_status'] = isset($data['force_chk211_status'])?($data['force_chk211_status']):'0';
				
				$data214 = array();$data214 = $_POST['force214'];
				// Set all Check box For Form 2.1 - 2.4
				$data214['force_chk21_property'] = isset($data214['force_chk21_property'])?($data214['force_chk21_property']):'0';
				$data214['force_chk21_personal'] = isset($data214['force_chk21_personal'])?($data214['force_chk21_personal']):'0';
				
				$data214['force_chk22_sus_stolen'] = isset($data214['force_chk22_sus_stolen'])?($data214['force_chk22_sus_stolen']):'0';
				$data214['force_chk22_sus_danger'] = isset($data214['force_chk22_sus_danger'])?($data214['force_chk22_sus_danger']):'0';
				$data214['force_chk22_sus_alcohol'] = isset($data214['force_chk22_sus_alcohol'])?($data214['force_chk22_sus_alcohol']):'0';
				$data214['force_chk22_sus_other'] = isset($data214['force_chk22_sus_other'])?($data214['force_chk22_sus_other']):'0';
				$data214['force_chk22_sus_parapher'] = isset($data214['force_chk22_sus_parapher'])?($data214['force_chk22_sus_parapher']):'0';
				$data214['force_chk22_encp_body'] = isset($data214['force_chk22_encp_body'])?($data214['force_chk22_encp_body']):'0';
				$data214['force_chk22_encp_through'] = isset($data214['force_chk22_encp_through'])?($data214['force_chk22_encp_through']):'0';
				$data214['force_chk22_encp_undress'] = isset($data214['force_chk22_encp_undress'])?($data214['force_chk22_encp_undress']):'0';
				$data214['force_chk22_encp_oral'] = isset($data214['force_chk22_encp_oral'])?($data214['force_chk22_encp_oral']):'0';
				
				$data214['force_chk23_sus_stolen'] = isset($data214['force_chk23_sus_stolen'])?($data214['force_chk23_sus_stolen']):'0';
				$data214['force_chk23_sus_danger'] = isset($data214['force_chk23_sus_danger'])?($data214['force_chk23_sus_danger']):'0';
				$data214['force_chk23_sus_alcohol'] = isset($data214['force_chk23_sus_alcohol'])?($data214['force_chk23_sus_alcohol']):'0';
				$data214['force_chk23_sus_other'] = isset($data214['force_chk23_sus_other'])?($data214['force_chk23_sus_other']):'0';
				$data214['force_chk23_sus_parapher'] = isset($data214['force_chk23_sus_parapher'])?($data214['force_chk23_sus_parapher']):'0';
				
				$data214['force_chk24_ingestion'] = isset($data214['force_chk24_ingestion'])?($data214['force_chk24_ingestion']):'0';
				$data214['force_chk24_stay'] = isset($data214['force_chk24_stay'])?($data214['force_chk24_stay']):'0';
				$data214['force_chk24_seiz_stolen'] = isset($data214['force_chk24_seiz_stolen'])?($data214['force_chk24_seiz_stolen']):'0';
				$data214['force_chk24_seiz_danger'] = isset($data214['force_chk24_seiz_danger'])?($data214['force_chk24_seiz_danger']):'0';
				$data214['force_chk24_seiz_intox'] = isset($data214['force_chk24_seiz_intox'])?($data214['force_chk24_seiz_intox']):'0';
				$data214['force_chk24_seiz_other'] = isset($data214['force_chk24_seiz_other'])?($data214['force_chk24_seiz_other']):'0';
				$data214['force_chk24_seiz_parapher'] = isset($data214['force_chk24_seiz_parapher'])?($data214['force_chk24_seiz_parapher']):'0';
				$data214['force_chk24_occur_body'] = isset($data214['force_chk24_occur_body'])?($data214['force_chk24_occur_body']):'0';
				$data214['force_chk24_occur_ransak'] = isset($data214['force_chk24_occur_ransak'])?($data214['force_chk24_occur_ransak']):'0';
				$data214['force_chk24_occur_mail'] = isset($data214['force_chk24_occur_mail'])?($data214['force_chk24_occur_mail']):'0';
				$data214['force_chk24_occur_other'] = isset($data214['force_chk24_occur_other'])?($data214['force_chk24_occur_other']):'0';
				$data214['force_chk24_done_police'] = isset($data214['force_chk24_done_police'])?($data214['force_chk24_done_police']):'0';
				$data214['force_chk24_done_loot'] = isset($data214['force_chk24_done_loot'])?($data214['force_chk24_done_loot']):'0';
				$data214['force_chk24_done_return'] = isset($data214['force_chk24_done_return'])?($data214['force_chk24_done_return']):'0';
				$data214['force_chk24_done_crus'] = isset($data214['force_chk24_done_crus'])?($data214['force_chk24_done_crus']):'0';
				$data214['force_chk24_done_storage'] = isset($data214['force_chk24_done_storage'])?($data214['force_chk24_done_storage']):'0';
				
				
				$data2511 = array();$data2511 = $_POST['force2511'];
				// Set all Check box For Form 2.5 - 2.11
				$data2511['force_chk25_drug'] = isset($data2511['force_chk25_drug'])?($data2511['force_chk25_drug']):'0';
				$data2511['force_chk25_danger'] = isset($data2511['force_chk25_danger'])?($data2511['force_chk25_danger']):'0';
				$data2511['force_chk25_residen'] = isset($data2511['force_chk25_residen'])?($data2511['force_chk25_residen']):'0';
				$data2511['force_chk25_sender'] = isset($data2511['force_chk25_sender'])?($data2511['force_chk25_sender']):'0';
				
				$data2511['force_chk28_within'] = isset($data2511['force_chk28_within'])?($data2511['force_chk28_within']):'0';
				$data2511['force_chk28_outside'] = isset($data2511['force_chk28_outside'])?($data2511['force_chk28_outside']):'0';
				
				$data2511['force_chk211_decision'] = isset($data2511['force_chk211_decision'])?($data2511['force_chk211_decision']):'0';
				$data2511['force_chk211_consent'] = isset($data2511['force_chk211_consent'])?($data2511['force_chk211_consent']):'0';
				//$data214['force_chk21_property'] = isset($data214['force_chk21_property'])?($data214['force_chk21_property']):'0';
				//$data214['force_chk21_personal'] = isset($data214['force_chk21_personal'])?($data214['force_chk21_personal']):'0';
				//$data214['force_chk21_property'] = isset($data214['force_chk21_property'])?($data214['force_chk21_property']):'0';
					
					
				if(count($forceArray)>0 && ($forceArray['force_id'] == $_POST['force_id']))
				{					
					$flag = KD::getModel('client/force')->update($data,'force_id',$_POST['force_id']);

					$force214Array = KD::getModel('client/force214')->getForceByVaktrap($vaktrapId,$patientId);
					// If Force Not created for 2.1 - 2.4 Create New otherwise Update
					if(count($force214Array)>0)
					{
						$flagForce214 = KD::getModel('client/force214')->update($data214,'force214_forceID',$_POST['force_id']);
					}
					else
					{
						$data214['force214_patientID'] = $_POST['force_patientID'];$data214['force214_vaktrapID'] = $_POST['force_vaktrapID'];$data214['force214_forceID'] = $_POST['force_id'];
						
						$flagForce214 = KD::getModel('client/force214')->insert($data214);
						if(!$flagForce214)
						{
							$opName = $this->view->translate('Created');
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Force 2.1-2.4',$opName));
						}
					}
					


					$force2511Array = KD::getModel('client/force2511')->getForceByVaktrap($vaktrapId,$patientId);
					// If Force Not created for 2.5 - 2.11 Create New otherwise Update
					if(count($force2511Array)>0)
					{
						$flagForce2511 = KD::getModel('client/force2511')->update($data2511,'force2511_forceID',$_POST['force_id']);
					}
					else
					{
						$data2511['force2511_patientID'] = $_POST['force_patientID'];$data2511['force2511_vaktrapID'] = $_POST['force_vaktrapID'];$data2511['force2511_forceID'] = $_POST['force_id'];
							
						$flagForce2511 = KD::getModel('client/force2511')->insert($data2511);
						if(!$flagForce2511)
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Force 2.5-2.11',$opName));
							$this->_redirect('/vaktrapport/index/index');
						}
					}
					/*if(!($flagForce214 && $flagForce2511))
					{
						$this->_redirect('/vaktrapport/info/index/t/2/id/'.$vaktrapId);
					}*/
					


					$opName = $this->view->translate('Changed');
					
					if($_POST['continue_force'] ||  $_POST['continue_force_action']==1)
					{
						$data['force_continue']='yes';
						$data['force_status']='no';
						$flag = KD::getModel('client/force')->update($data,'force_id',$_POST['force_id']);
					}
						

					if($_POST['lock_force'])
					{
						$data['force_status']='no';$data['force_continue']='no';$data['force_lock_by']=$_SESSION['Acl']['userID'];$data['force_lock_at']=date("Y-m-d H:i:s");
						$flag = KD::getModel('client/force')->update($data,'force_id',$_POST['force_id']);
						
						$data214 = array();$data214['force214_lock_by']=$_SESSION['Acl']['userID'];$data214['force214_lock_date']=date("Y-m-d H:i:s");
						$flagForce214 = KD::getModel('client/force214')->update($data214,'force214_forceID',$_POST['force_id']);
						
						$data2511 = array();$data2511['force2511_lock_by']=$_SESSION['Acl']['userID'];$data2511['force2511_lock_date']=date("Y-m-d H:i:s");
						$flagForce2511 = KD::getModel('client/force2511')->update($data2511,'force2511_forceID',$_POST['force_id']);
						
						$opName = $this->view->translate('Locked');
						
						$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapId);
						$counters = unserialize($vaktrapDetail['vaktrap_counters']);
						$counters['force'] = $counters['force']+1;
						$counters = serialize($counters);
						$data = array();$data['vaktrap_counters'] = $counters;
						$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$vaktrapId);
						
						
						//SENDING MAIL FOR FORCE
						$patientDetail = KD::getModel('client/client')->load($patientId);
						$dataEmail = array();$dataEmail['type'] = 'Force';$dataEmail['client'] = KD::getModel('client/client')->getClient($patientId);$dataEmail['department'] = KD::getModel('client/client')->getClient($patientDetail['patient_deptID']);$dataEmail['date'] = KD::getModel('core/format')->FormatDate(date("Y-m-d"));$dataEmail['user'] = KD::getModel('user/user')->getUser($_SESSION['Acl']['userID']);$dataEmail['time'] = date('H:i:s');$dataEmail['identity'] = 'staff';$dataEmail['name'] = 'Admin';
						parent::sendEmail(KD::getModel('system/system')->getEmail(),'System','email.phtml',$dataEmail);
						


						if(!($flag && $flagForce214 && $flagForce2511))
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Force',$opName));
							$this->_redirect('/vaktrapport/index/index');
						}
					}
				}
				else
				{
					// Insert Force
					$data['force_patientID'] = $_POST['force_patientID'];$data['force_vaktrapID'] = $_POST['force_vaktrapID'];
					
					if($_POST['lock_force']){$data['force_status']='no';$data['force_continue']='no';$data['force_lock_by']=$_SESSION['Acl']['userID'];$data['force_lock_date']=date("Y-m-d H:i:s");}
					
					$flag = KD::getModel('client/force')->insert($data);
					if($data['force_status']=='no')
					{
						$opName = $this->view->translate('Locked');
					}
					else
					{
						$opName = $this->view->translate('Created');
					}
					
					if($flag)
					{
						$data214['force214_patientID'] = $_POST['force_patientID'];$data214['force214_vaktrapID'] = $_POST['force_vaktrapID'];$data214['force214_forceID'] = $flag;
						
						$flagForce214 = KD::getModel('client/force214')->insert($data214);
						if(!$flagForce214)
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Force 2.1-2.4',$opName));
							$this->_redirect('/vaktrapport/index/index');
						}
						
						$data2511['force2511_patientID'] = $_POST['force_patientID'];$data2511['force2511_vaktrapID'] = $_POST['force_vaktrapID'];$data2511['force2511_forceID'] = $flag;
						
						$flagForce2511 = KD::getModel('client/force2511')->insert($data2511);
						if(!$flagForce2511)
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Force 2.5-2.11',$opName));
							$this->_redirect('/vaktrapport/index/index');
						}
						
						if($_POST['continue_force'] || $_POST['continue_force_action']==1)
						{
							$data['force_continue']='yes';
							$data['force_status']='no';
							$flag = KD::getModel('client/force')->update($data,'force_id',$flag);
						}
						
						if($_POST['lock_force'])
						{
							$data['force_status']='no';$data['force_continue']='no';$data['force_lock_by']=$_SESSION['Acl']['userID'];$data['force_lock_date']=date("Y-m-d H:i:s");
							$flag = KD::getModel('client/force')->update($data,'force_id',$flag);
							
							$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapId);
							$counters = unserialize($vaktrapDetail['vaktrap_counters']);
							$counters['force'] = $counters['force']+1;
							$counters = serialize($counters);
							$data = array();$data['vaktrap_counters'] = $counters;
							$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$vaktrapId);
							
							
							//SENDING MAIL FOR FORCE
							$patientId = $data['force_patientID'];
							$patientDetail = KD::getModel('client/client')->load($patientId);
							$dataEmail = array();$dataEmail['type'] = 'Force';$dataEmail['client'] = KD::getModel('client/client')->getClient($patientId);$dataEmail['department'] = KD::getModel('client/client')->getClient($patientDetail['patient_deptID']);$dataEmail['date'] = KD::getModel('core/format')->FormatDate(date("Y-m-d"));$dataEmail['user'] = KD::getModel('user/user')->getUser($_SESSION['Acl']['userID']);$dataEmail['time'] = date('H:i:s');$dataEmail['identity'] = 'staff';$dataEmail['name'] = 'Admin';
							parent::sendEmail(KD::getModel('system/system')->getEmail(),'System','email.phtml',$dataEmail);
						}
						
					}
					else
					{
						$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Force',$opName));
						$this->_redirect('/vaktrapport/index/index');
					}
				}
				if($flag)
				{
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Force %s Successfully',$opName));
					$this->_redirect('/vaktrapport/info/index/t/2/id/'.$vaktrapId);
				}
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Force',$opName));
					$this->_redirect('/vaktrapport/index/index');
				}
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Vaktrapport Id for Logg'));
				$this->_redirect('/vaktrapport/index/index');
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Patient Id for Logg'));
			$this->_redirect('/vaktrapport/index/index');
		}
	}
	
	public function savemedicineAction()
	{
		$patientId = $this->getRequest()->getParam('id'); 
		$format = KD::getModel('core/format');
		if(isset($patientId) && ($patientId == $_POST['medvak_patientID']))
		{
			$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->getCurrentVaktrap($patientId);
			$vaktrapId = $vaktrapArray['vaktrap_id'];
			if(isset($vaktrapId) && ($vaktrapId == $_POST['medvak_vaktrapID'])) 
			{
				$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapId);
				$dataPost = array();$dataPost['medvak_patientID'] = $patientId;$dataPost['medvak_vaktrapID'] = $vaktrapId;
				//echo '<prE>';print_r($_POST);exit();
				// Medicine Details are all medicines come from Medicine Detail
				$flag = false;
				if(isset($_POST['med_det']) && count($_POST['med_det'])>0)
				{
					$medicineDetails = $_POST['med_det'];
					foreach($medicineDetails as $medDetId=>$medicineDetail)
					{
						if(isset($medDetId) && $medDetId>0)
						{
							if(isset($medicineDetail['took'],$medicineDetail['desc']) && $medicineDetail['took']>=0 && $medicineDetail['took']!='' && $medicineDetail['time']!='')
							{
								$medineDetailTable = KD::getModel('client/medicinedetail')->load($medDetId);
								$data = $dataPost;
								$data['medvak_detId'] = $medDetId; 
								$data['medvak_date'] = $format->FormatDate($medineDetailTable['med_det_date']);
								$data['medvak_day'] = $medineDetailTable['med_det_day'];
								$data['medvak_took'] = $medicineDetail['took'];
								$data['medvak_time'] = $medicineDetail['time'];
								$data['medvak_desc'] = $medicineDetail['desc'];
								$data['medvak_year'] = (int)$medineDetailTable['med_det_date'];
								
								$flagInsert = KD::getModel('vaktrapport/medvaktrap')->insert($data);
								if($flagInsert)
								{
									$flag = true;
								}
								else
								{	
									$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Error while saveMedicine Plan for Vaktrapport named %s on Date ',$medineDetailTable['med_det_name'], $format->FormatDate($medineDetailTable['med_det_date'])));
								}
							}
							
						}
					}
				}				
				
				// Medicine Vaktrap Details are all medicines come from Medicine Vaktrap Detail 
				
				if(isset($_POST['medvak']) && count($_POST['medvak'])>0)
				{
					$medicineVaktrapDetails = $_POST['medvak'];
					foreach($medicineVaktrapDetails as $medVakId=>$medicineVaktrapDetail)
					{
						if(isset($medVakId) && $medVakId>0)
						{
							if(isset($medicineVaktrapDetail['took'],$medicineVaktrapDetail['time']) && $medicineVaktrapDetail['took']>=0 && $medicineVaktrapDetail['time']!='')
							{
								$medineDetailTable = KD::getModel('client/medicinedetail')->load($medDetId);
								$data = array();
								$data['medvak_took'] = $medicineVaktrapDetail['took'];
								$data['medvak_time'] = $medicineVaktrapDetail['time'];
								$data['medvak_desc'] = $medicineVaktrapDetail['desc'];
								$flagUpdate = KD::getModel('vaktrapport/medvaktrap')->update($data,'medvak_id',$medVakId);
								if($flagUpdate)
								{
									$flag = true;
								}
							}
							
						}
					}
				}
				$vaktFrom = $vaktrapArray['vaktrap_from_date'];
				$vaktTo = $vaktrapArray['vaktrap_to_date'];
				$vaktFromPreg = preg_match("/(\d{4})-(\d{2})-(\d{2})/",$vaktFrom,$matchFrom);
				$vaktToPreg = preg_match("/(\d{4})-(\d{2})-(\d{2})/",$vaktTo,$matchTo);
				
				if(isset($vaktFrom,$vaktTo) && $vaktFrom!='' && $vaktTo!='' && $vaktFromPreg==true && $vaktToPreg==true && count($matchFrom)>0 && $matchFrom[3]>0 && count($matchTo)>0 && $matchTo[3]>0)
				{
				
					$startDate = $vaktrapArray['vaktrap_from_date']; 
					$startDateObj = new DateTime($startDate); 
					$endDate = $vaktrapArray['vaktrap_to_date']; 
					$endDateObj = new DateTime($endDate);
					$endDateObj->add(new DateInterval('P1D'));
					while($startDateObj->format("Y-m-d")!=$endDateObj->format("Y-m-d"))
					{
						$startDateVal = $format->FormatDate($startDate);
						$week = $startDateObj->format("W");
						$weekDay = $startDateObj->format("l");
						$day = $startDateObj->format("D");
						$day = strtolower($day);
						$startDateObj = new DateTime($startDate);
						if(isset($_POST['extra_name'][$day]) && count($_POST['extra_name'][$day])>0)
						{
							$medicineArray = KD::getModel('client/medicine')->checkMedicineByDate($patientId,$startDate);
							
							$medicineArray = (count($medicineArray)>0)?$medicineArray[0]:$medicineArray;
							//echo '<pre>';print_r($medicineArray);
							if(count($medicineArray)>0)
							{
								$medicineExtras = $_POST['extra_name'][$day];
								foreach($medicineExtras as $id=>$medicineExtra)
								{
									if(isset($medicineExtra) && $medicineExtra!='')
									{
										$name = $medicineExtra;
										$nos = (isset($_POST['extra_nos'][$day][$id]) && $_POST['extra_nos'][$day][$id]>0)?$_POST['extra_nos'][$day][$id]:0;
										$took = (isset($_POST['extra_took'][$day][$id]) && $_POST['extra_took'][$day][$id]>0)?$_POST['extra_took'][$day][$id]:0;
										$time = (isset($_POST['extra_time'][$day][$id]) && $_POST['extra_time'][$day][$id]!='')?$_POST['extra_time'][$day][$id]:'';
										$desc = (isset($_POST['extra_desc'][$day][$id]) && $_POST['extra_desc'][$day][$id]!='')?$_POST['extra_desc'][$day][$id]:'';
										if(isset($nos,$time) && $time!='' && $nos>0)
										{
											$dataExtra = array();$dataExtra['med_det_patientID'] = $patientId;$dataExtra['med_det_medicineID'] = $medicineArray['medicine_id'];$dataExtra['med_det_date'] = $startDateVal;$dataExtra['med_det_day'] = $day;$dataExtra['med_det_name'] = $name;$dataExtra['med_det_nos'] = $nos;$dataExtra['med_det_time'] = $time;$dataExtra['med_det_isExtra'] = 'yes';
											$flagInsertDetail = KD::getModel('client/medicinedetail')->insert($dataExtra);
											if($flagInsertDetail)
											{	
											  if(isset($took) && $took>0 && $took<=$nos)
											  {
												$dataVak = array();$dataVak['medvak_detId'] = $flagInsertDetail; $dataVak['medvak_patientID'] = $patientId;  $dataVak['medvak_vaktrapID'] = $vaktrapId; $dataVak['medvak_year'] = (int)$startDateVal;$dataVak['medvak_date'] = $startDateVal;$dataVak['medvak_day'] = $day;$dataVak['medvak_took'] = $took;$dataVak['medvak_time'] = $time;$dataVak['medvak_desc'] = $desc;
												$flagInsertVak = KD::getModel('vaktrapport/medvaktrap')->insert($dataVak);
												if(!$flagInsertVak)
												{
												  // Insert Vaktrap Error
												  $this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem While saving Medicine Description for "%s"',$name));
												}
											  }
											  else
											  {
											    // Took not set or more than nos
												$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Took is more than Nos of Medicine For "%s"',$name));
											  }
											}
											else
											{
												//error while Inserting Medicine detail 
												$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem While saving Medicine "%s"',$name));
											}
										}
										else
										{
											// Time Or Nos are not Set
											$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Have not Peroperly Set Time Or Nos For Medicine "%s"',$name));
										}
									}
								}
							}
						}
						$startDateObj->add(new DateInterval('P1D'));
						$startDate = $startDateObj->format("Y-m-d");
					}
				}
				//exit();
				/*$weekDays = array(0=>'mon',1=>'tue',2=>'wed',3=>'thu',4=>'fri',5=>'sat',6=>'sun');
				$medicineextraArray = array();
				foreach($weekDays as $day)
				{
					echo '<pre>';print_r($_POST);
					if(isset($_POST['extra'][$day]) && count($_POST['extra'][$day])>0)
					{
						echo 'Insert New Medicines created By operator ';
						exit();
					}
				}*/
				$vaktFrom = $vaktrapArray['vaktrap_from_date'];
				$vaktTo = $vaktrapArray['vaktrap_to_date'];
				$vaktFromPreg = preg_match("/(\d{4})-(\d{2})-(\d{2})/",$vaktFrom,$matchFrom);
				$vaktToPreg = preg_match("/(\d{4})-(\d{2})-(\d{2})/",$vaktTo,$matchTo);
				
				$medicineCounter = 0;
				if(isset($vaktFrom,$vaktTo) && $matchFrom[3]>0 && $matchTo[3]>0)
				{
					$medicineCounter = KD::getModel('vaktrapport/medvaktrap')->getMedCountByDate($patientId,$vaktFrom,$vaktTo);
					
					$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapId);
					$counters = unserialize($vaktrapDetail['vaktrap_counters']);
					$counters['medicine'] = $medicineCounter;
					$counters = serialize($counters);
					$data = array();$data['vaktrap_counters'] = $counters;
					$vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$vaktrapId);
					
				}
				
				if($flag)
				{
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Medicine Plan for Vaktrapport has been saved'));
					$this->_redirect('/vaktrapport/info/index/t/4/id/'.$vaktrapId);
				}
					$this->_redirect('/vaktrapport/info/index/t/4/id/'.$vaktrapId);

			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Vaktrapport Id for Medicine Plan'));
				$this->_redirect('/client/report/index/t2/id/'.$patientId);
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Patient Id for Medicine Plan'));
			$this->_redirect('/vaktrapport/index/index');
		}
	}
	
	public function savegenogramAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$vaktrapId =$_POST['vaktrapId']; 
		$genogram_str =$_POST['genogram_str']; 
		if(isset($vaktrapId) && $vaktrapId>0)
		{
			$data = array(); $data['vaktrap_genogram'] = $genogram_str;
			$result = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$vaktrapId);			
			echo ('Genogram Saved Successfully');   
		}
		else
		{
			echo ('Genogram Updatation Not allowed');
		}
		exit();
	}

    protected function _logUserActionOnReport($reportId, $patientId, $action) {
        $locked = ($action === 'Locked');
        $result = KD::getModel('client/logg')->insert(array(
			'logg_type' => '',
            'logg_patientID' => $patientId,
            'logg_userID' => $_SESSION['Acl']['userID'],
            'logg_vaktrapID' => $reportId,
            'logg_desc' => $action,
            'logg_locked_by' => $locked ? $_SESSION['Acl']['userID'] : 0,
            'logg_locked_at' => $locked ? date('Y-m-d H:i:S') : '',
            'logg_status' => 'no'
        ));
    }
}