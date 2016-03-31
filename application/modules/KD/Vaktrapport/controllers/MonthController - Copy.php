<?php

class Vaktrapport_MonthController extends KD_Controller_Action {

    public function init() {
        //echo 'asdg';exit();
        /* Initialize action controller here */
        parent::init();
    } 

    public function indexAction() {
		$this->view->title = $this->view->translate('Monthly Security Report For');
		$this->view->className = 'PTCLEFTVAKTRAPORT';
		
		
		
		$pageVM = $this->getRequest()->getParam('pageVM');
		if($pageVM<=0){$pageVM = 1;}
		
		$vaktrapportArray = KD::getModel('vaktrapport/vaktrapport')->loadAllVaktrapPageData($pageVM,'active');;
		$this->view->vaktrapportCollection = $vaktrapportArray;	
		
    }
	
	public function showAction() {
		$this->view->title = $this->view->translate('Monthly Security Report For');
		$this->view->className = 'PTCLEFTVAKTRAPORT';
		
		$clientID = $this->getRequest()->getParam('id');
		$vaktrapID = $this->getRequest()->getParam('vid');        
		if((isset($clientID) && $clientID > 0) || (isset($vaktrapID) && $vaktrapID > 0))
		{
			$clientPostID = isset($_POST['clientId'])?$_POST['clientId']:0;
			if(($_SERVER['REQUEST_METHOD'] === 'POST' && $clientPostID==$clientID) || $_SERVER['REQUEST_METHOD'] === 'GET' || $vaktrapID > 0)
			{
				if($vaktrapID>0)
				{
					$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapID);
					$clientID = $vaktrapArray['vaktrap_patientID'];
				}
				$clientArray = KD::getModel('client/client')->load($clientID);
				// If they are mannually Adding some Id in address bar which is not exist 
				// Here if vaktrapport is not active you are redirected to invalid page
				if(!empty($clientArray) && $clientArray['patient_status']=='yes')
				{
					$vaktrapID = $this->getRequest()->getParam('vid');
					$clientInfo = $clientArray;
					$this->view->clientInfo = $clientArray;
					if($_SERVER['REQUEST_METHOD'] === 'GET' && !(isset($vaktrapID) && $vaktrapID>0))
					{
						$this->view->vaktrapInfo = array();
					}
					else
					{
						$vaktrapInfoArray = array();
						if($vaktrapID>0)
						{
							$year = $vaktrapArray['vaktrap_year'];
							$period = $vaktrapArray['vaktrap_period'];
							$result = $vaktrapArray;
							$count = 1;
							
							$vaktrapInfoArray = $vaktrapArray;
							/*$vaktrapInfoArray['vaktrap_oppsumering'] = $vaktrapArray['vaktrap_oppsumering'];
							$vaktrapInfoArray['vaktrap_tilspan_from_date'] = $vaktrapArray['vaktrap_tilspan_from_date'];
							$vaktrapInfoArray['vaktrap_tilspan_to_date'] = $vaktrapArray['vaktrap_tilspan_to_date'];
							$vaktrapInfoArray['vaktrap_maalpan_from_date'] = $vaktrapArray['vaktrap_maalpan_from_date'];
							$vaktrapInfoArray['vaktrap_maalpan_to_date'] = $vaktrapArray['vaktrap_maalpan_to_date'];
							$vaktrapInfoArray['vaktrap_status'] = $vaktrapArray['vaktrap_status'];*/
						}
						else
						{
							$year = $_POST['year'];
							$period = $_POST['period'];
							$result = KD::getModel('vaktrapport/vaktrapport')->checkVaktrapMonthQuartal($clientID,'maaned',$period,$year);
							$count = count($result);
							if($count>0)$result = $result[0];
						}
						//if $count = 0 Not exist,  ,if $count<0 invalid patient id
						if($count==0 || ($count>0))
						{
							
							$startDateObj = new DateTime($year.'-'.$period.'-01'); 
							$endcheck = new DateTime();
							$difference = $startDateObj->diff($endcheck)->format("%R");
							if($difference==='+')
							{
								$format = KD::getModel('core/format');
								$endDateObj = clone $startDateObj;
								$endDateObj->add(new DateInterval('P1M'));
								$startDate = $startDateObj->format('Y-m-d');
								$endDateObj->sub(new DateInterval('P1D'));
								$endDate = $endDateObj->format('Y-m-d');
								
								$allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapMonthQuartal($clientID,'maaned',$period,$year);
								
								$vaktrapInfoArray['vaktrap_patientID'] = $clientID;
								$vaktrapInfoArray['vaktrap_year'] = $year;
								$vaktrapInfoArray['vaktrap_period'] = $period;
								$vaktrapInfoArray['vaktrap_from_date'] = $startDate;
								$vaktrapInfoArray['vaktrap_to_date'] = $endDate;
								
								// Gether All Details for the period
								$vaktrap_observationdesc = array();
								$vaktrap_observationdescfinal = array();
								$vaktrap_maaldesc = array();
								$vaktrap_counters = array('gov'=>0,'ins'=>0,'force'=>0,'medicine'=>0,'logg'=>0,'avvik'=>0);
								$vaktrapIds = array();
								$vaktrap_fremgang = '';
								$vaktrap_hendelser = '';
								$vaktrap_aarsak = '';
								$vaktrap_merknader = '';
								
								$vaktrap_deptID = 0;

								foreach($allEnkelVaktrapresult as $vaktrap)
								{
									$vaktrap_deptID = $vaktrap['vaktrap_deptID'];
									$duration = $format->FormatDate($vaktrap['vaktrap_from_date']).' - '.$format->FormatDate($vaktrap['vaktrap_to_date']).'<br>';
									$vaktrap_observationdesc = unserialize($vaktrap['vaktrap_observationdesc']) + $vaktrap_observationdesc;
									$vaktrap_maaldesc = unserialize($vaktrap['vaktrap_maaldesc']) + $vaktrap_maaldesc;
									$vaktrap_countersTmp  = unserialize($vaktrap['vaktrap_counters']);
									if(!empty($vaktrap_countersTmp))
									{
										foreach($vaktrap_countersTmp as $key=>$value)
										{
											$vaktrap_counters[$key] = $value + $vaktrap_counters[$key];
										}
									}
									$vaktrapIds[] = $vaktrap['vaktrap_id'];
									$vaktrap_fremgang .= $duration.$vaktrap['vaktrap_fremgang'].'<br><br>';
									$vaktrap_hendelser .= $duration.$vaktrap['vaktrap_hendelser'].'<br><br>';
									$vaktrap_aarsak .= $duration.$vaktrap['vaktrap_aarsak'].'<br><br>';
									$vaktrap_merknader .= $duration.$vaktrap['vaktrap_merknader'].'<br><br>';
									
									$patient_fname = $vaktrap['patient_fname'];
									$patient_mname = $vaktrap['patient_mname'];
									$patient_lname = $vaktrap['patient_lname'];
									$user_fname = $vaktrap['user_fname'];
									$user_mname = $vaktrap['user_mname'];
									$user_lname = $vaktrap['user_lname'];
									$patient_legal = $vaktrap['patient_legal'];
									$vaktrap_userID = $vaktrap['vaktrap_userID'];
 								}
								$vaktrapInfoArray['vaktrap_deptID'] = $vaktrap_deptID;
								$vaktrapInfoArray['vaktrap_observationdesc'] = $vaktrap_observationdesc;
								$vaktrapInfoArray['vaktrap_maaldesc'] = $vaktrap_maaldesc;
								$vaktrapInfoArray['vaktrap_counters'] = $vaktrap_counters;
								$vaktrapInfoArray['vaktrap_vaktrapIds'] = $vaktrapIds;
								$vaktrapInfoArray['vaktrap_fremgang'] = $vaktrap_fremgang;
								$vaktrapInfoArray['vaktrap_hendelser'] = $vaktrap_hendelser;
								$vaktrapInfoArray['vaktrap_aarsak'] = $vaktrap_aarsak;
								$vaktrapInfoArray['vaktrap_merknader'] = $vaktrap_merknader;
								
								$vaktrapInfoArray['patient_fname'] = $patient_fname;
								$vaktrapInfoArray['patient_mname'] = $patient_mname;
								$vaktrapInfoArray['patient_lname'] = $patient_lname;
								$vaktrapInfoArray['user_fname'] = $user_fname;
								$vaktrapInfoArray['user_mname'] = $user_mname;
								$vaktrapInfoArray['user_lname'] = $user_lname;
								$vaktrapInfoArray['patient_legal'] = $patient_legal;
								$vaktrapInfoArray['vaktrap_userID'] = $vaktrap_userID;
								
								//echo '<pre>';print_r($vaktrap_observationdesc);exit();
								$this->view->vaktrapInfo = $vaktrapInfoArray;
								
								$count = 1; 
								$maalArray = array();
								$instTiltakArray = array();
								$vakGovTiltakArray = array();
								/*$observationArray = array();
								$observationResArray = array();
								$observationArray = KD::getModel('client/observation')->loadList($patientID);*/
								foreach($vaktrap_maaldesc as $maal)
								{
									$maalArray[$maal['maal_id']] = array('maal_id'=>$maal['maal_id'],'maal_desc'=>$maal['maal_desc'],'counter'=>$count++);
									$instTiltakArray[$maal['maal_id']] = KD::getModel('client/tiltakinst')->loadListByMaal($clientID,$maal['maal_id'],$vaktrapIds,false,true);
									$vakGovTiltakArray[$maal['maal_id']] = KD::getModel('vaktrapport/vaktraptilgov')->loadListByMaal($clientID,$maal['maal_id'],$vaktrapIds,false,true);
								}

								$this->view->vaktrapInfo = $vaktrapInfoArray;
								$this->view->maalCollection = $maalArray;
								$this->view->instTiltakCollection = $instTiltakArray;
								$this->view->vakGovTiltakCollection = $vakGovTiltakArray;
							}
							else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period is not started Yet, So you can\'t create report'));
								$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
							}
							
						}
						// if $count>0 monthly report exist so show data of that vaktrpaport
						elseif($count<0)
						{
							// Error for invalid Client ID or Client is not active
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Client Is not Valid'));
							$this->_redirect('/client/report/index/tt/23');
						}
						
						//$clientArray;
						/*$maalDataArray = KD::getModel('client/maal')->loadList($patientID);
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
						foreach($observationArray as $observation)
						{
							$tmpArray = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservation($patientID,$vaktrapID,$observation['observation_id']);
							foreach($tmpArray as $resArray)
							{
								$index = new Datetime($resArray['vaktrap_obser_date']);
								$observationResArray[$observation['observation_id']][$index->format('y-m-d')] = $resArray;	
							}
							
						}
						
						$this->view->maalCollection = $maalArray;
						$this->view->instTiltakCollection = $instTiltakArray;
						$this->view->vakGovTiltakCollection = $vakGovTiltakArray;
						$this->view->observationCollection = $observationArray;
						$this->view->observationResCollection = $observationResArray;
						$this->view->vaktrapInfo = $vaktrapInfo;*/
						$this->view->title = $this->view->translate('Security Report For');//.' '.$fomat$vaktrapInfo[''];
						$this->view->className = 'PTCLEFTVAKTRAPORT';
					}
				}
				else
				{
					// Error for invalid Client ID or Client is not active
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Client Is not active'));
					$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
				}
			}
			else
			{
				// May be they have changed Post and Get ID
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
				$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
			}
		}
		else
		{
			// Client Id not set
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index');
		}
		
    }
	public function saveAction() {
		$clientID = $this->getRequest()->getParam('id');        
		if(isset($clientID) && $clientID > 0)
		{
			$clientPostID = isset($_POST['vaktrap_patientID'])?$_POST['vaktrap_patientID']:0;
			if($_SERVER['REQUEST_METHOD'] === 'POST' && $clientPostID==$clientID)
			{
				$clientArray = KD::getModel('client/client')->load($clientID);
				// If they are mannually Adding some Id in address bar which is not exist 
				// Here if vaktrapport is not active you are redirected to invalid page
				if(!empty($clientArray) && $clientArray['patient_status']=='yes')
				{
					$clientInfo = $clientArray;
					$this->view->clientInfo = $clientArray;
					
					$year = $_POST['year'];
					$period = $_POST['period'];
					$result = KD::getModel('vaktrapport/vaktrapport')->checkVaktrapMonthQuartal($clientID,'maaned',$period,$year);
					$count = count($result);
					$result = $result[0];
					
					//if $count = 0 Not exist,  ,if $count<0 invalid patient id
					if($count==0 || ($count>0 && $result['vaktrap_status']=='yes'))
					{
						
						$startDateObj = new DateTime($year.'-'.$period.'-01'); 
						$endcheck = new DateTime();
						$difference = $startDateObj->diff($endcheck)->format("%R");
						if($difference==='+')
						{
							$format = KD::getModel('core/format');
							$endDateObj = clone $startDateObj;
							$endDateObj->add(new DateInterval('P1M'));
							$startDate = $startDateObj->format('Y-m-d');
							$endDateObj->sub(new DateInterval('P1D'));
							$endDate = $endDateObj->format('Y-m-d');
							
							$format = KD::getModel('core/format');
							$allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapMonthQuartal($clientID,'maaned',$period,$year);
							$vaktrapInfoArray = array();
							$vaktrapInfoArray['vaktrap_patientID'] = $clientID;
							$vaktrapInfoArray['vaktrap_year'] = $year;
							$vaktrapInfoArray['vaktrap_period'] = $period;
							$vaktrapInfoArray['vaktrap_from_date'] = $format->FormatDate($startDate);
							$vaktrapInfoArray['vaktrap_to_date'] = $format->FormatDate($endDate);
							if(isset($_POST['vaktrap_tilspan_from_date']) && $_POST['vaktrap_tilspan_from_date']!='')
							{
								$vaktrapInfoArray['vaktrap_tilspan_from_date'] = $format->FormatDate($_POST['vaktrap_tilspan_from_date']);
							}
							if(isset($_POST['vaktrap_tilspan_to_date']) && $_POST['vaktrap_tilspan_to_date']!='')
							{
								$vaktrapInfoArray['vaktrap_tilspan_to_date'] = $format->FormatDate($_POST['vaktrap_tilspan_to_date']);
							}
							if(isset($_POST['vaktrap_maalpan_from_date']) && $_POST['vaktrap_maalpan_from_date']!='')
							{
								$vaktrapInfoArray['vaktrap_maalpan_from_date'] = $format->FormatDate($_POST['vaktrap_maalpan_from_date']);
							}
							if(isset($_POST['vaktrap_maalpan_to_date']) && $_POST['vaktrap_maalpan_to_date']!='')
							{
								$vaktrapInfoArray['vaktrap_maalpan_to_date'] = $format->FormatDate($_POST['vaktrap_maalpan_to_date']);
							}

							// Gether All Details for the period
							$vaktrap_observationdesc = array();
							$vaktrap_maaldesc = array();
							$vaktrap_counters = array('gov'=>0,'ins'=>0,'force'=>0,'medicine'=>0,'logg'=>0,'avvik'=>0);
							$vaktrapIds = array();
							$vaktrap_deptID = 0;

							foreach($allEnkelVaktrapresult as $vaktrap)
							{
								$vaktrap_deptID = $vaktrap['vaktrap_deptID'];
								$vaktrap_observationdesc = unserialize($vaktrap['vaktrap_observationdesc']) + $vaktrap_observationdesc;
								$vaktrap_maaldesc = unserialize($vaktrap['vaktrap_maaldesc']) + $vaktrap_maaldesc;
								$vaktrap_countersTmp  = unserialize($vaktrap['vaktrap_counters']);
								if(!empty($vaktrap_countersTmp))
								{
									foreach($vaktrap_countersTmp as $key=>$value)
									{
										$vaktrap_counters[$key] = $value + $vaktrap_counters[$key];
									}
								}
								$vaktrapIds[] = $vaktrap['vaktrap_id'];
							}
							$vaktrapInfoArray['vaktrap_deptID'] = $vaktrap_deptID;
							$vaktrapInfoArray['vaktrap_observationdesc'] = serialize($vaktrap_observationdesc);
							$vaktrapInfoArray['vaktrap_maaldesc'] = serialize($vaktrap_maaldesc);
							$vaktrapInfoArray['vaktrap_counters'] = serialize($vaktrap_counters);
							$vaktrapInfoArray['vaktrap_vaktrapIds'] = serialize($vaktrapIds);
							$vaktrapInfoArray['vaktrap_type'] = 'maaned';
							$vaktrapInfoArray['vaktrap_oppsumering'] = isset($_POST['vaktrap_oppsumering'])?$_POST['vaktrap_oppsumering']:'';

							// Update Monthly Vaktrapport Page
							if($count>0)
							{
								if($_POST['lock_report'])
								{
									$vaktrapInfoArray['vaktrap_status'] = 'no';
								}
								$id = KD::getModel('vaktrapport/vaktrapport')->update($vaktrapInfoArray,'vaktrap_id',$result['vaktrap_id']);
							}
							// Insert Monthly Vaktrapport Page
							else
							{
								$id = KD::getModel('vaktrapport/vaktrapport')->insert($vaktrapInfoArray);
							}
							
							$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Monthly Vaktrapport is saved Successfully'));
							$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
						}
						else
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period is not started Yet, So you can\'t create report'));
							$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
						}
						
					}

					$this->view->title = $this->view->translate('Security Report For');//.' '.$fomat$vaktrapInfo[''];
					$this->view->className = 'PTCLEFTVAKTRAPORT';
				}
				else
				{
					// Error for invalid Client ID or Client is not active
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Client Is not active'));
					$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
				}
			}
			else
			{
				// May be they have changed Post and Get ID
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
				$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
			}
		}
		else
		{
			// Client Id not set
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index');
		}
		
    }
}