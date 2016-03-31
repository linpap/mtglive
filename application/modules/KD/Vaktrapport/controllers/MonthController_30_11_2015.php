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
		$vaktrapID = $this->getRequest()->getParam('vid');
		if(isset($vaktrapID) && $vaktrapID > 0)
		{
			$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapID);
			$this->_showReport($vaktrapID,'GET',$vaktrapArray['vaktrap_type']);
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request in Show'));
			$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
		}
		
	}
	public function createAction() {
		$clientID = $this->getRequest()->getParam('id');
		$reportType = $this->getRequest()->getParam('type');
		if($reportType=='maaned' || $reportType=='kvartal')
		{
			$this->_showReport($clientID,'POST',$reportType);
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Report Type'));
			$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
		}
	}
	public function showarchiveAction() {
		$vaktrapID = $this->getRequest()->getParam('vid');
		if(isset($vaktrapID) && $vaktrapID > 0)
		{
			$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapID);
			$this->_showReport($vaktrapID,'GET',$vaktrapArray['vaktrap_type']);
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request in Show Archive'));
			$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
		}
	}
	
	public function searchAction() {
		$clientID = $this->getRequest()->getParam('id');
		$clientPostID = isset($_POST['clientId'])?$_POST['clientId']:0;
		
		if($clientPostID==$clientID)
		{
			if(isset($_POST['year'],$_POST['period']) && $_POST['year']>0 && $_POST['period']>0)
			{	
				$this->_showReport($clientID,'POST',$_POST['reportType'],$_POST['year'],$_POST['period']);
			}
			else 
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request in Search'));
				$this->_redirect('/client/report/create/id/'.$clientID.'/type/'.$_POST['reportType']);
			}
		}
		else
		{
			// May be they have changed Post and Get ID
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request in Search same Id' ));
			$this->_redirect('/client/report/index/tt/23/id/'.$clientID);
		}
	}
	
	
	///
	public function showkAction() {
		$vaktrapID = $this->getRequest()->getParam('vid');
		if(isset($vaktrapID) && $vaktrapID > 0)
		{
			$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapID);
			$this->_showReport($vaktrapID,'GET',$vaktrapArray['vaktrap_type']);
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request in Show'));
			$this->_redirect('/client/report/index/tt/24/id/'.$clientID);
		}
		
	}
	public function createkAction() {
		$clientID = $this->getRequest()->getParam('id');
		$reportType = $this->getRequest()->getParam('type');
		if($reportType=='maaned' || $reportType=='kvartal')
		{
			$this->_showReport($clientID,'POST',$reportType);
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Report Type'));
			$this->_redirect('/client/report/index/tt/24/id/'.$clientID);
		}
	}
	public function showarchivekAction() {
		$vaktrapID = $this->getRequest()->getParam('vid');
		if(isset($vaktrapID) && $vaktrapID > 0)
		{
			$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->load($vaktrapID);
			$this->_showReport($vaktrapID,'GET',$vaktrapArray['vaktrap_type']);
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request in Show Archive'));
			$this->_redirect('/client/report/index/tt/24/id/'.$clientID);
		}
	}
	
	public function searchkAction() {
		$clientID = $this->getRequest()->getParam('id');
		$clientPostID = isset($_POST['clientId'])?$_POST['clientId']:0;
		
		if($clientPostID==$clientID)
		{
			if(isset($_POST['year'],$_POST['period']) && $_POST['year']>0 && $_POST['period']>0)
			{	
				$this->_showReport($clientID,'POST',$_POST['reportType'],$_POST['year'],$_POST['period']);
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request in Search'));
				$this->_redirect('/client/report/index/id/'.$clientID);
			}
		}
		else
		{
			// May be they have changed Post and Get ID
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request in Search same Id' ));
			$this->_redirect('/client/report/index/tt/24/id/'.$clientID);
		}
	}
	///
	private function _showReport($clientID,$method='POST',$reportType='maaned',$year=0,$period=0)
	{	
		//echo $clientID ;exit();
		//$showVaktrap = false; used for show emtpy report for first time when we create
		$showVaktrap = false;
		$vaktrapInfoArray = array();
		
		if($reportType == 'maaned'){$redirectTab = '23';$reportName = $this->view->translate('Monthly');}else{$redirectTab = '24';$reportName = $this->view->translate('Quarterly');}      
		//if method = GET Means its Vaktrapport id is created and we need to do for that vaktrapport ID
		//if method = POST Means its Vaktrapport id is created or may be not created and we need to do for that Client ID
		if($method=='GET')
		{
			if(isset($clientID) && $clientID > 0)
			{
				$showVaktrap = true;
				$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->load($clientID);
				$clientID = $vaktrapArray['vaktrap_patientID'];
				$year = $vaktrapArray['vaktrap_year'];
				$period = $vaktrapArray['vaktrap_period'];
			}
			else
			{
				$clientID = 0;
			}
		}
		else
		{
			if(isset($year,$period) && $year > 0 && $period > 0)
			{
				$showVaktrap = true;
				$vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->checkVaktrapMonthQuartal($clientID,$reportType,$period,$year);
				if(count($vaktrapArray)>0)
				{
					$vaktrapArray = $vaktrapArray[0];
				}
			}
		}
		
		if((isset($clientID) && $clientID > 0))
		{
			$this->view->title = $this->view->translate('%s Security Report For',$reportName).'  "'.KD::getModel('client/client')->getClient($clientID,'name').'"';
			$this->view->className = 'PTCLEFTVAKTRAPORT';
			
			$clientArray = KD::getModel('client/client')->load($clientID);
			if(!empty($clientArray) && $clientArray['patient_status']=='yes')
			{
				//For search, saved and Locked reports
				if($showVaktrap)
				{
					if($reportType=='maaned')
					{
						$startDateObj = new DateTime($year.'-'.$period.'-01'); 
					}
					elseif($reportType=='kvartal')
					{
						if($period==1)
						{
							$startDateObj = new DateTime(($year-1).'-12-01'); 
						}
						else
						{
							$startDateObj = new DateTime($year.'-'.(($period-1)*3).'-01'); 
						}
					}
					else
					{
						$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Report Type'));
						$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
					}
					$endcheck = new DateTime();
					$difference = $startDateObj->diff($endcheck)->format("%R");
					if($difference==='+' || (!empty($vaktrapArray) && $vaktrapArray['vaktrap_status']=='no'))
					{
						$format = KD::getModel('core/format');
						$endDateObj = clone $startDateObj;
						
						if($reportType=='maaned')
						{
							$endDateObj->add(new DateInterval('P1M'));
						}
						elseif($reportType=='kvartal')
						{
							$endDateObj->add(new DateInterval('P3M'));
						}
						else
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Report Type'));
							$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
						}
						$startDate = $startDateObj->format('Y-m-d');
						$endDateObj->sub(new DateInterval('P1D'));
						$endDate = $endDateObj->format('Y-m-d');
						
						// for Locked report we have to get All Vaktrapport collection By vaktrapportIds
						if(!empty($vaktrapArray))
						{
							$vaktrapInfoArray = $vaktrapArray; 
							$vaktrapInfoArray['user_name'] = KD::getModel('user/user')->getUser($vaktrapArray['vaktrap_userID']);
							$vaktrapInfoArray['vaktrap_counters'] = unserialize($vaktrapArray['vaktrap_counters']);
							$vaktrapInfoArray['vaktrap_vaktrapIds'] = unserialize($vaktrapArray['vaktrap_vaktrapIds']);
							$vaktrapInfoArray['vaktrap_maaldesc'] = unserialize($vaktrapArray['vaktrap_maaldesc']);
							$vaktrapInfoArray['vaktrap_observationdesc'] = unserialize($vaktrapArray['vaktrap_observationdesc']);
						}
						else
						{
							$vaktrapInfoArray['vaktrap_patientID'] = $clientID;
							$vaktrapInfoArray['vaktrap_year'] = $year;
							$vaktrapInfoArray['vaktrap_period'] = $period;
							$vaktrapInfoArray['vaktrap_from_date'] = $startDate;
							$vaktrapInfoArray['vaktrap_to_date'] = $endDate;
							
							$vaktrapInfoArray['vaktrap_vaktrapIds'] = array();
							$vaktrapInfoArray['vaktrap_fremgang'] = '';
							$vaktrapInfoArray['vaktrap_hendelser'] = '';
							$vaktrapInfoArray['vaktrap_aarsak'] = '';
							$vaktrapInfoArray['vaktrap_merknader'] = '';
							$vaktrapInfoArray['vaktrap_oppsumering'] = '';

							$vaktrapInfoArray['vaktrap_deptID'] = $clientArray['patient_deptID'];
							$vaktrapInfoArray['vaktrap_userID'] = $clientArray['patient_primary_userID'];
							$vaktrapInfoArray['vaktrap_status'] = 'yes';
							
							$vaktrapInfoArray['user_name'] = KD::getModel('user/user')->getUser($clientArray['patient_primary_userID']);
						}
						// Gether All Details for the period
						$vaktrap_observationdesc = array();
						$vaktrap_maaldesc = array();
						$vaktrap_counters = array('gov'=>0,'ins'=>0,'force'=>0,'medicine'=>0,'logg'=>0,'avvik'=>0);
						$vaktrapIds = array();
						$vaktrap_fremgang = '';
						$vaktrap_hendelser = '';
						$vaktrap_aarsak = '';
						$vaktrap_merknader = '';
						
						$vaktrap_deptID = 0;
						// Status is no means locked so we dont need to collect maal, observation, counter
						if(!empty($vaktrapArray) && $vaktrapArray['vaktrap_status']=='no')
						{
							
							$vaktrapInfoArray['patient_name'] = KD::getModel('client/client')->getClient($clientID);
							$vaktrapInfoArray['patient_legal'] = KD::getModel('client/client')->getClient($clientID,'patient_legal');
							if(is_array($vaktrapInfoArray['vaktrap_vaktrapIds']) && !empty($vaktrapInfoArray['vaktrap_vaktrapIds']))
							{
								$vaktrapIds = $vaktrapInfoArray['vaktrap_vaktrapIds'];
								$allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapList($vaktrapIds);
								foreach($allEnkelVaktrapresult as $vaktrap)
								{
									$vaktrap_deptID = $vaktrap['vaktrap_deptID'];
									$duration = $format->FormatDate($vaktrap['vaktrap_from_date']).' - '.$format->FormatDate($vaktrap['vaktrap_to_date']).'<br>';
									$vaktrapInfoArray['vaktrap_fremgang'] .= $duration.$vaktrap['vaktrap_fremgang'].'<br><br>';
									$vaktrapInfoArray['vaktrap_hendelser'] .= $duration.$vaktrap['vaktrap_hendelser'].'<br><br>';
									$vaktrapInfoArray['vaktrap_aarsak'] .= $duration.$vaktrap['vaktrap_aarsak'].'<br><br>';
									$vaktrapInfoArray['vaktrap_merknader'] .= $duration.$vaktrap['vaktrap_merknader'].'<br><br>';
								}
								$vaktrap_maaldesc = $vaktrapInfoArray['vaktrap_maaldesc'];
							}
							/*else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem while Generating %s Vaktrapport Report, May be vaktrapport ids are not set',$reportName));
								$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
							}*/
						}
						// Status is no means locked so we dont need to collect maal, observation, counter
						elseif((!empty($vaktrapArray) && $vaktrapArray['vaktrap_status']=='yes') || empty($vaktrapArray))
						{
							$allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapMonthQuartal($clientID,$reportType,$period,$year);	
							$vaktrapIds = array();
							$vaktrap_observationdesc = array();
							$vaktrap_maaldesc = array();
							$observationdesc = array();
							$maaldesc = array();
							
							foreach($allEnkelVaktrapresult as $vaktrap)
							{
								$duration = $format->FormatDate($vaktrap['vaktrap_from_date']).' - '.$format->FormatDate($vaktrap['vaktrap_to_date']).'<br>';
								//echo '<pre>';print_r(unserialize($vaktrap['vaktrap_observationdesc']));print_r($vaktrapInfoArray['vaktrap_observationdesc']);exit();
								$observationdesc = unserialize($vaktrap['vaktrap_observationdesc']);
								if(isset($observationdesc) && is_array($observationdesc))
								{
									$vaktrap_observationdesc = $vaktrap_observationdesc+$observationdesc;
									
								}
								$maaldesc = unserialize($vaktrap['vaktrap_maaldesc']);
								if(isset($maaldesc) && is_array($maaldesc))
								{
									$vaktrap_maaldesc = $vaktrap_maaldesc+$maaldesc;
								}
								
								$vaktrap_countersTmp  = unserialize($vaktrap['vaktrap_counters']);
								if(!empty($vaktrap_countersTmp))
								{
									foreach($vaktrap_countersTmp as $key=>$value)
									{
										$vaktrap_counters[$key] = $value + $vaktrap_counters[$key];
									}
								}
								$vaktrapIds[] = $vaktrap['vaktrap_id'];
								$vaktrapInfoArray['vaktrap_fremgang'] .= $duration.$vaktrap['vaktrap_fremgang'].'<br><br>';
								$vaktrapInfoArray['vaktrap_hendelser'] .= $duration.$vaktrap['vaktrap_hendelser'].'<br><br>';
								$vaktrapInfoArray['vaktrap_aarsak'] .= $duration.$vaktrap['vaktrap_aarsak'].'<br><br>';
								$vaktrapInfoArray['vaktrap_merknader'] .= $duration.$vaktrap['vaktrap_merknader'].'<br><br>';
							}
							$vaktrapInfoArray['vaktrap_observationdesc'] = $vaktrap_observationdesc;
							$vaktrapInfoArray['vaktrap_maaldesc'] = $vaktrap_maaldesc;
							
							$vaktrapInfoArray['vaktrap_counters'] = $vaktrap_counters;
							$vaktrapInfoArray['vaktrap_vaktrapIds'] = $vaktrapIds;
							$vaktrapInfoArray['patient_name'] = KD::getModel('client/client')->getClient($clientID);
							$vaktrapInfoArray['patient_legal'] = KD::getModel('client/client')->getClient($clientID,'patient_legal');
						}
						else
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem while Generating %s Vaktrapport Report',$reportName));
							$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
						}
						//echo '<pre>';print_r($vaktrapInfoArray);exit();
						$this->view->vaktrapInfo = $vaktrapInfoArray;
						$this->view->clientInfo = $clientArray;
						
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
						if(is_array($vaktrapIds) && count($vaktrapIds)>0)
						{
							$arrayResult = array('NOTCOMPLETE'=>'0','PARTIALCOMPLETE'=>'1','COMPLETE'=>'2','NOTEVALUATED'=>'3');
							$arrayColor = array(0 => '#E40303',1 => '#FDC600',2 => '#7DC10F',3 => '#4C70F5');
							
							//$tiltakInstResult = KD::getModel('client/tiltakinst')->loadListByVaktrap($clientID,$vaktrapIds,false,true);
							$this->view->instTiltakCountCollection = array();
							foreach($arrayResult as $key =>$tiltakInst)
							{
							  $dataRes = KD::getModel('client/tiltakinst')->loadListByResult($clientID,$tiltakInst,$vaktrapIds,false,true);
							  if(isset($tiltakInst) && $tiltakInst>=0 && $tiltakInst<=2)
							  {
								$this->view->instTiltakCountCollection[$key] = array('count'=>count($dataRes),'result'=>$tiltakInst,'color'=>$arrayColor[$tiltakInst],'data'=>$dataRes);
							  }
							  else
							  {
								$this->view->instTiltakCountCollection[$key] = array('count'=>count($dataRes),'result'=>'3','color'=>$arrayColor[3],'data'=>$dataRes);
							  }
							}
							//exit();
							//$tiltakGovResult = KD::getModel('vaktrapport/vaktraptilgov')->loadListByVaktrap($clientID,$vaktrapIds,false,true);
							$this->view->govTiltakCountCollection = array();
							foreach($arrayResult as $key =>$tiltakGov)
							{
							  $dataRes = KD::getModel('vaktrapport/vaktraptilgov')->loadListByResult($clientID,$tiltakGov,$vaktrapIds,false,true);
							  if(isset($tiltakGov) && $tiltakGov>=0 && $tiltakGov<=2)
							  {
								$this->view->govTiltakCountCollection[$key] = array('count'=>count($dataRes),'result'=>$tiltakGov,'color'=>$arrayColor[$tiltakGov],'data'=>$dataRes);
							  }
							  else
							  {
								$this->view->govTiltakCountCollection[$key] = array('count'=>count($dataRes),'result'=>'3','color'=>$arrayColor[3],'data'=>$dataRes);
							  }
							}
						}
						//echo '<pre>';print_r($this->view->govTiltakCountCollection);
						//echo '<pre>';print_r($this->view->instTiltakCountCollection);exit();
						$this->view->vaktrapInfo = $vaktrapInfoArray;
						$this->view->maalCollection = $maalArray;
						$this->view->instTiltakCollection = $instTiltakArray;
						$this->view->vakGovTiltakCollection = $vakGovTiltakArray;
					}
					else
					{
						$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period is not started Yet, So you can\'t create %s Vaktrapport Report',$reportName));
						$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
					}
					
				}
				// This will load firt time when we click on create monthly vaktrapport and also if period and year is not ser
				else
				{
					$this->view->clientInfo = $clientArray;
					$this->view->vaktrapInfo = array();
				}
			}
			else
			{
				// Error for invalid Client ID or Client is not active
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Client Is not active'));
				$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
			}
		}
		else
		{
			// Client Id not set
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request Client'));
			$this->_redirect('/client/index');
		}
	}

	public function saveAction() {
		$clientID = $this->getRequest()->getParam('id');
		$reportType = $this->getRequest()->getParam('type');  
		if($reportType == 'maaned'){$redirectTab = '23';$reportName = $this->view->translate('Monthly');}else{$redirectTab = '24';$reportName = $this->view->translate('Quarterly');}      
		if(isset($clientID) && $clientID > 0)
		{
			$clientPostID = isset($_POST['vaktrap_patientID'])?$_POST['vaktrap_patientID']:0;
			$reportPostType = isset($_POST['reportPostType'])?$_POST['reportPostType']:0;
			if($_SERVER['REQUEST_METHOD'] === 'POST' && $clientPostID==$clientID && $reportPostType ==  $reportType)
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

					$result = KD::getModel('vaktrapport/vaktrapport')->checkVaktrapMonthQuartal($clientID,$reportType,$period,$year);

					$count = count($result);
					if($count>0)$result = $result[0];
					
					//if $count = 0 Not exist,  ,if $count<0 invalid patient id
					if($count==0 || ($count>0 && $result['vaktrap_status']=='yes'))
					{
						
						//$startDateObj = new DateTime($year.'-'.$period.'-01'); 
						if($reportType=='maaned')
						{
							$startDateObj = new DateTime($year.'-'.$period.'-01'); 
						}
						elseif($reportType=='kvartal')
						{
							if($period==1)
							{
								$startDateObj = new DateTime(($year-1).'-12-01'); 
							}
							else
							{
								$startDateObj = new DateTime($year.'-'.(($period-1)*3).'-01'); 
							}
						}
						else
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Report Type'));
							$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
						}
						
						$endcheck = new DateTime();
						$difference = $startDateObj->diff($endcheck)->format("%R");
						if($difference==='+')
						{
							$format = KD::getModel('core/format');
							$endDateObj = clone $startDateObj;
							//$endDateObj->add(new DateInterval('P1M'));
							if($reportType=='maaned')
							{
								$endDateObj->add(new DateInterval('P1M'));
							}
							elseif($reportType=='kvartal')
							{
								$endDateObj->add(new DateInterval('P3M'));
							}
							else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Report Type'));
								$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
							}
							$startDate = $startDateObj->format('Y-m-d');
							$endDateObj->sub(new DateInterval('P1D'));
							$endDate = $endDateObj->format('Y-m-d');
							
							$format = KD::getModel('core/format');
							$allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapMonthQuartal($clientID,$reportType,$period,$year);
							$vaktrapInfoArray = array();
							$vaktrapInfoArray['vaktrap_patientID'] = $clientID;
							$vaktrapInfoArray['vaktrap_year'] = $year;
							$vaktrapInfoArray['vaktrap_period'] = $period;
							$vaktrapInfoArray['vaktrap_from_date'] = $format->FormatDate($startDate);
							$vaktrapInfoArray['vaktrap_to_date'] = $format->FormatDate($endDate);
							
							if(isset($_POST['vaktrap_tilspan_from_date']) && $_POST['vaktrap_tilspan_from_date']!='')
							{
								$vaktrapInfoArray['vaktrap_tilspan_from_date'] = $_POST['vaktrap_tilspan_from_date'];
							}
							if(isset($_POST['vaktrap_tilspan_to_date']) && $_POST['vaktrap_tilspan_to_date']!='')
							{
								$vaktrapInfoArray['vaktrap_tilspan_to_date'] = $_POST['vaktrap_tilspan_to_date'];
							}
							if(isset($_POST['vaktrap_maalpan_from_date']) && $_POST['vaktrap_maalpan_from_date']!='')
							{
								$vaktrapInfoArray['vaktrap_maalpan_from_date'] = $_POST['vaktrap_maalpan_from_date'];
							}
							if(isset($_POST['vaktrap_maalpan_to_date']) && $_POST['vaktrap_maalpan_to_date']!='')
							{
								$vaktrapInfoArray['vaktrap_maalpan_to_date'] = $_POST['vaktrap_maalpan_to_date'];
							}

							// Gether All Details for the period
							$vaktrap_observationdesc = array();
							$vaktrap_maaldesc = array();
							$vaktrap_counters = array('gov'=>0,'ins'=>0,'force'=>0,'medicine'=>0,'logg'=>0,'avvik'=>0);
							$vaktrapIds = array();
							$vaktrap_deptID = 0;
							
							$observationdesc = array();
							$maaldesc = array();
							$vaktrap_allowed_lock = true;

							foreach($allEnkelVaktrapresult as $vaktrap)
							{
								$vaktrap_deptID = $vaktrap['vaktrap_deptID'];
								if($vaktrap['vaktrap_status']=='yes')
								{
									$vaktrap_allowed_lock = false;
								}
								
								//$vaktrap_observationdesc = unserialize($vaktrap['vaktrap_observationdesc']) + $vaktrap_observationdesc;
								$observationdesc = unserialize($vaktrap['vaktrap_observationdesc']);
								if(isset($observationdesc) && is_array($observationdesc))
								{
									$vaktrap_observationdesc = $vaktrap_observationdesc+$observationdesc;
									
								}
								//$vaktrap_maaldesc = unserialize($vaktrap['vaktrap_maaldesc']) + $vaktrap_maaldesc;
								$maaldesc = unserialize($vaktrap['vaktrap_maaldesc']);
								if(isset($maaldesc) && is_array($maaldesc))
								{
									$vaktrap_maaldesc = $vaktrap_maaldesc+$maaldesc;
								}
								
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
							
							$vaktrapInfoArray['vaktrap_deptID'] = $clientArray['patient_deptID'];
							$vaktrapInfoArray['vaktrap_observationdesc'] = serialize($vaktrap_observationdesc);
							$vaktrapInfoArray['vaktrap_maaldesc'] = serialize($vaktrap_maaldesc);
							$vaktrapInfoArray['vaktrap_counters'] = serialize($vaktrap_counters);
							$vaktrapInfoArray['vaktrap_vaktrapIds'] = serialize($vaktrapIds);
							$vaktrapInfoArray['vaktrap_type'] = $reportType;
							$vaktrapInfoArray['vaktrap_oppsumering'] = isset($_POST['vaktrap_oppsumering'])?$_POST['vaktrap_oppsumering']:'';
							$flagError = false;
							// Update Monthly Vaktrapport Page
							$action = 'saved';
							if($count>0)
							{
								if(isset($_POST['lock_report']))
								{
									
										//$endDate
										$checkDateObj = new DateTime($endDate); 
										$currentDateObj = new DateTime();
										$sign = $checkDateObj->diff($currentDateObj)->format("%R");
										if($sign=='-')
										{
											$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('As the Current Date is not Reach End Date, so you can\'t Lock the %s Report',$reportName));
										}
										else
										{	
											if($vaktrap_allowed_lock)
											{
												$vaktrapInfoArray['vaktrap_status'] = 'no';
												$action = 'locked';
											}
											else
											{
												$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('As Any of the Vaktrapport is in use, so you can\'t Lock the %s Report',$reportName));
											}
										}
								}
								$id = KD::getModel('vaktrapport/vaktrapport')->update($vaktrapInfoArray,'vaktrap_id',$result['vaktrap_id']);
							}
							// Insert Monthly Vaktrapport Page
							else
							{
								$id = KD::getModel('vaktrapport/vaktrapport')->insert($vaktrapInfoArray);
							}
							
							$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('%s Vaktrapport is %s Successfully.',$reportName,$action));
							$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
						}
						else
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period is not started Yet, So you can\'t create %s report',$reportName));
							$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
						}
						
					}
					else
					{
						$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('You are trying to save Locked %s Report',$reportName));
						$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
					}

					$this->view->title = $this->view->translate('Security Report For');//.' '.$fomat$vaktrapInfo[''];
					$this->view->className = 'PTCLEFTVAKTRAPORT';
				}
				else
				{
					// Error for invalid Client ID or Client is not active
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Client Is not active'));
					$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
				}
			}
			else
			{
				// May be they have changed Post and Get ID
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request save Client Id not same'));
				$this->_redirect('/client/report/index/id/'.$clientID.'/tt/'.$redirectTab);
			}
		}
		else
		{
			// Client Id not set
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request Client invalid'));
			$this->_redirect('/client/index');
		}
		
    }
}