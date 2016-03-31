<?php
class Client_ScorecardController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }
	
    public function indexAction() {
       $clientID = $this->getRequest()->getParam('id');//exit();
		if(isset($clientID) && $clientID>0 )
		{
			$this->view->id = $clientID;
			$this->view->className = 'PTCLEFTVAKTRAPORT';
			$this->view->title = $this->view->translate('Scorecard For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
			// Collection Maal Plan Info
			$maalplanInfo = KD::getModel('client/maalplan')->getActiveMaalPlan($clientID);

			$this->view->maalplanInfo = $maalplanInfo;
			
			//Collect Force Counter
			$vaktrap_counters = array('gov'=>0,'ins'=>0,'force'=>0,'medicine'=>0,'logg'=>0,'avvik'=>0);
			if(isset($maalplanInfo['maalplan_to_date']) && $maalplanInfo['maalplan_to_date']!='')
			{
				$enddateObj  = new DateTime($maalplanInfo['maalplan_to_date']);
				$year  = $enddateObj->format('Y');
				$period  = ceil($enddateObj->format('m')/3);
				$allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapMonthQuartal($clientID,'kvartal',$period,$year);	

				if(count($allEnkelVaktrapresult)>0 && is_array($allEnkelVaktrapresult))
				{
					foreach($allEnkelVaktrapresult as $vaktrap)
					{
						$vaktrap_countersTmp  = unserialize($vaktrap['vaktrap_counters']);
						if(!empty($vaktrap_countersTmp))
						{
							foreach($vaktrap_countersTmp as $key=>$value)
							{
								if(isset($vaktrap_countersTmp[$key]) && $vaktrap_countersTmp[$key]>=0)
								{
									$vaktrap_counters[$key] = $value + $vaktrap_counters[$key];
								}
							}
						}
					}
				}
			}
			else
			{
				//echo 'asdf';exit();
			}

			if(isset($vaktrap_counters) && is_array($vaktrap_counters))
			{
				$this->view->maalplanCounters = $vaktrap_counters;
			}
			else
			{
				$this->view->maalplanCounters = array();
			}
			
			//echo '<pre>';print_r($maalplanInfo);exit();
			// Collection Maal Which are Active for that patient
			if(isset($maalplanInfo['maalplan_id']) && $maalplanInfo['maalplan_id']>0)
			{
				$maalplanMaalInfo = KD::getModel('client/maalplanmaal')->loadMaalList($clientID,$maalplanInfo['maalplan_id']);
			}
			else
			{
				$maalplanMaalInfo = KD::getModel('client/maalplanmaal')->loadMaalList($clientID);
			}
			$this->view->maalplanMaalInfo = $maalplanMaalInfo;
			//echo '<pre>';print_r($maalplanInfo);exit();
			
			// Collection Maal which are achived for that patient but not in the current report
			$maalplanMaalAchiveInfo = KD::getModel('client/maalplanmaal')->loadAchiveList($clientID);
			$this->view->maalplanMaalAchiveInfo = $maalplanMaalAchiveInfo;
			
			// Collection of tiltal order by maal order
			$maalplanTiltakInfo = array();
			foreach($maalplanMaalInfo as $maalplanMaal)
			{
				$maalplanTiltakInfo[$maalplanMaal['maal_id']] = array();
				$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maalplan_maal_id'] = $maalplanMaal['maalplan_maal_id'];
				$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maal_desc'] = $maalplanMaal['maal_desc'];
				$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maalplan_statusdesc'] = $maalplanMaal['maalplan_statusdesc'];
				$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maalplan_evaluering'] = $maalplanMaal['maalplan_evaluering'];
				
				if(isset($maalplanInfo['maalplan_id']) && $maalplanInfo['maalplan_id']>0)
				{
					$maalplanTiltakInfo[$maalplanMaal['maal_id']]['data'] = KD::getModel('client/maalplantiltak')->loadTiltakList($clientID,$maalplanMaal['maal_id'],$maalplanInfo['maalplan_id']);
				}
				else
				{
					$maalplanTiltakInfo[$maalplanMaal['maal_id']]['data'] = KD::getModel('client/maalplantiltak')->loadTiltakList($clientID,$maalplanMaal['maal_id']);
				}
			}

			$this->view->maalplanTiltakInfo = $maalplanTiltakInfo;
			$maalplanId = 0;
			
			// Collection Maal which are achived for that patient but not in the current report
			if(isset($maalplanInfo) && count($maalplanInfo)>0)
			{
				$maalplanId = $maalplanInfo['maalplan_id'];
			}
			$maalPlanCertain = KD::getModel('client/maalplanother')->loadList($clientID,$maalplanId,'certain');
			$this->view->maalPlanCertain = $maalPlanCertain;
			
			// Collection Maal which are achived for that patient but not in the current report
			$maalPlanActivity = KD::getModel('client/maalplanother')->loadList($clientID,$maalplanId,'activity');
			$this->view->maalPlanActivity = $maalPlanActivity;
			
			// Collection Maal which are achived for that patient but not in the current report
			$maalPlanMeeting = KD::getModel('client/maalplanother')->loadList($clientID,$maalplanId,'meeting');
			$this->view->maalPlanMeeting = $maalPlanMeeting;
			
			$clientLogg = KD::getModel('client/logg');
			$this->view->loggM = $clientLogg->loadListByDate($clientID,'archive','M',$maalplanInfo['maalplan_from_date'],$maalplanInfo['maalplan_to_date']);
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	
	public function archiveAction() {
       $clientID = $this->getRequest()->getParam('id');//exit();
		if(isset($clientID) && $clientID>0 )
		{
			$this->view->id = $clientID;
			$this->view->className = 'PTCLEFTVAKTRAPORT';
			$this->view->title = $this->view->translate('Scorecard For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
			
			$maalPlanID = $this->getRequest()->getParam('mid');//exit();
			if(isset($maalPlanID) && $maalPlanID>0)
			{
				// Collection Maal Plan Info
				$maalplanInfo = KD::getModel('client/maalplan')->load($maalPlanID);
				$this->view->maalplanInfo = $maalplanInfo;
				if($maalplanInfo['maalplan_patientID']==$clientID)
				{
					//Collect Force Counter
					$vaktrap_counters = array('gov'=>0,'ins'=>0,'force'=>0,'medicine'=>0,'logg'=>0,'avvik'=>0);
					if(isset($maalplanInfo['maalplan_to_date']) && $maalplanInfo['maalplan_to_date']!='')
					{
						$enddateObj  = new DateTime($maalplanInfo['maalplan_to_date']);
						$year  = $enddateObj->format('Y');
						$period  = ceil($enddateObj->format('m')/3);
						$allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapMonthQuartal($clientID,'kvartal',$period,$year);	
						
						foreach($allEnkelVaktrapresult as $vaktrap)
						{
							$vaktrap_countersTmp  = unserialize($vaktrap['vaktrap_counters']);
							if(!empty($vaktrap_countersTmp))
							{
								foreach($vaktrap_countersTmp as $key=>$value)
								{
									if(isset($vaktrap_countersTmp[$key]) && $vaktrap_countersTmp[$key]>=0)
									{
										$vaktrap_counters[$key] = $value + $vaktrap_counters[$key];
									}
								}
							}
						}
					}
					if(isset($vaktrap_counters) && is_array($vaktrap_counters))
					{
						$this->view->maalplanCounters = $vaktrap_counters;
					}
					else
					{
						$this->view->maalplanCounters = array();
					}
					
					// Collection Maal Which are Active for that patient
					$maalplanMaalInfo = KD::getModel('client/maalplanmaal')->loadMaalList($clientID,$maalPlanID,'archive');
					$this->view->maalplanMaalInfo = $maalplanMaalInfo;
					//echo '<pre>';print_r($maalplanInfo);exit();
					
					// Collection Maal which are achived for that patient but not in the current report
					$maalplanMaalAchiveInfo = KD::getModel('client/maalplanmaal')->loadAchiveList($clientID);
					$this->view->maalplanMaalAchiveInfo = $maalplanMaalAchiveInfo;
					


					
					// Collection of tiltal order by maal order
					$maalplanTiltakInfo = array();




					foreach($maalplanMaalInfo as $maalplanMaal)
					{
						$maalplanTiltakInfo[$maalplanMaal['maal_id']] = array();
						$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maalplan_maal_id'] = $maalplanMaal['maalplan_maal_id'];
						$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maal_desc'] = $maalplanMaal['maal_desc'];
						$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maalplan_statusdesc'] = $maalplanMaal['maalplan_statusdesc'];
						$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maalplan_evaluering'] = $maalplanMaal['maalplan_evaluering'];

						$maalplanTiltakInfo[$maalplanMaal['maal_id']]['data'] = KD::getModel('client/maalplantiltak')->loadTiltakList($clientID, $maalplanMaal['maal_id'],$maalplanMaal['maalplan_maalplanID'],'archive');
						
					}

					

					$this->view->maalplanTiltakInfo = $maalplanTiltakInfo;


					$maalplanId = 0;
					
					// Collection Maal which are achived for that patient but not in the current report
					if(isset($maalplanInfo) && count($maalplanInfo)>0)
					{
						$maalplanId = $maalplanInfo['maalplan_id'];
					}
					$maalPlanCertain = KD::getModel('client/maalplanother')->loadList($clientID,$maalplanId,'certain');
					$this->view->maalPlanCertain = $maalPlanCertain;
					
					// Collection Maal which are achived for that patient but not in the current report
					$maalPlanActivity = KD::getModel('client/maalplanother')->loadList($clientID,$maalplanId,'activity');
					$this->view->maalPlanActivity = $maalPlanActivity;
					
					// Collection Maal which are achived for that patient but not in the current report
					$maalPlanMeeting = KD::getModel('client/maalplanother')->loadList($clientID,$maalplanId,'meeting');
					$this->view->maalPlanMeeting = $maalPlanMeeting;
				}
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('You are Trying to request Invalid Client MaalPlan'));
					$this->_redirect('/client/report/index/id/'.$clientID);	
				}
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Maalplan'));
				$this->_redirect('/client/report/index/id/'.$clientID);				
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Client'));
			$this->_redirect('/client/index/');
		}
    }
	
	public function saveAction()
	{

        $format = KD::getModel('core/format');
		$clientID = $this->getRequest()->getParam('pdid');//exit();
		$clientIDPost = $_POST['maalplan_patientID'];
		if(isset($_POST['lock_maalplan'])){

					if(isset($clientID) && $clientID>0 && $clientIDPost==$clientID)
					{
						$maalPlanID = $this->getRequest()->getParam('id');
						$maalPlanIDPost = $_POST['maalplan_id'];
						if($maalPlanID==$maalPlanIDPost)
						{
							$deptId = KD::getModel('client/client')->getClient($clientID,'patient_deptID');
							// Maal Plan ID >0 Update
							$data = $_POST['maalplan'];
							$data['maalplan_location'] = $_POST['patient']['patient_location'];
							$data['maalplan_actionplan'] = $_POST['patient']['patient_actionplan'];
							$data['maalplan_resource'] = $_POST['patient']['patient_resource'];
							//echo $maalPlanID;
							//exit;
							if($maalPlanID>0)
							{
								//exit;
								if(isset($_POST['lock_maalplan'])){
									$data['maalplan_status'] = 'no';
									$data['maalplan_lock_date'] = date("Y-m-d H:i:s");
									$data['maalplan_lock_by'] = $_SESSION['Acl']['userID'];
									
								}
								KD::getModel('client/maalplan')->update($data,'maalplan_id',$maalPlanID);
							}
							// Maal Plan ID == 0 Insert
							else
							{
								//exit;
								$data['maalplan_status'] = 'no';
								$data['maalplan_patientID'] = $clientID;$data['maalplan_deptID'] = $deptId; 
								$maalPlanID = KD::getModel('client/maalplan')->insert($data);
								
								//KD::getModel('client/maal')->update($data,'maal_id',$maalId);
								
								//echo $maalPlanID;exit;
							}
							// $maalPlanID > 0 means maalplan Updated or inserted
							if($maalPlanID>0)
							{
								// Update current vaktrapport - Issue #10
								$vakt_model = KD::getModel('vaktrapport/vaktrapport');
								$report_id = $vakt_model->getCurrentVaktrap($clientID);
								$vakt_model->update(array(
									'vaktrap_tilspan_from_date' => $data['maalplan_tiltak_fromDate'],
									'vaktrap_tilspan_to_date' => $data['maalplan_tiltak_toDate'],
									'vaktrap_maalpan_from_date' => $data['maalplan_maalsty_fromDate'],
									'vaktrap_maalpan_to_date' => $data['maalplan_maalsty_toDate']
								), 'vaktrap_id', $report_id);
			
								// Updating Patient Information like Plasseting and resources
								KD::getModel('client/client')->update($_POST['patient'],'patient_id',$clientID);
								// Insert or Update MaalPlan Maals
								$maalIds = array();
								$tiltakIds = array();
								if(isset($_POST['maal']) && count($_POST['maal']) > 0)
								{
									foreach($_POST['maal'] as $maalId=>$maal)
									{
										// Load Maal Data and save in MaalPlanMaal table
										$maalData = KD::getModel('client/maal')->load($maalId);
										$data = array();
										$data = $maal; 
										$data['maalplan_maalplanID'] = $maalPlanID; 
										$data['maalplan_maalID'] = $maalId; 
										$data['maalplan_patientID'] = $clientID; 
										$data['maalplan_deptID'] = $deptId; 
										$data['maalplan_maalFromDate'] = $format->FormatDate($maalData['maal_from_date']); 
										$data['maalplan_maalToDate'] = $format->FormatDate($maalData['maal_to_date']);
										
										KD::getModel('client/maalplanmaal')->insert($data);
										$maalIds[] = $maalId;
										$_SESSION['bm_'.$maalId] = $_POST['maal'][$maalId]['maalplan_evaluering'];
										
										//$maal = KD::getModel('client/maal')->load($maalId);
										$datalock = array();
										$datalock['maal_lockset'] = 'yes';
										if(isset($_POST['maal'][$maalId]['maalplan_maalResult'])){
											if($_POST['maal'][$maalId]['maalplan_maalResult'] == '2'){
												KD::getModel('client/maal')->update($datalock,'maal_id',$maalId);
											}
										}
									}
									//print_r($_POST['maal'] );exit;
								}
								
								elseif(isset($_POST['maalplanmaal']) && count($_POST['maalplanmaal']) > 0)
								{
									foreach($_POST['maalplanmaal'] as $maalplan_maal_id => $maalplanmaal)
									{
									
										KD::getModel('client/maalplanmaal')->update($maalplanmaal,'maalplan_maal_id',$maalplan_maal_id);
										$maalIds[] = $maalplanmaal['maal_id'];
										//$c_bm = '"maal['.$maalplanmaal['maal_id'].'][maalplan_evaluering]"';
										$_SESSION['bm_'.$maalplanmaal['maal_id']] = $maalplanmaal['maalplan_evaluering'];
										//print_r($maalplan_maal_id);
										//$maal_id_bm= $maalplanmaal['maal_id'];
										$datalock['maal_lockset'] = 'yes';
										//print_r($maalplanmaal).'<br>';
										if(isset($maalplanmaal['maalplan_maalResult'])){
											if($maalplanmaal['maalplan_maalResult'] == '2'){											
												KD::getModel('client/maal')->update($datalock,'maal_id',$maalplanmaal['maal_id']);
											}
										}
									}
									//exit;
								}
								
								// Insert or Update MaalPlan Tiltaks
								if(isset($_POST['tiltak']) && count($_POST['tiltak']) > 0)
								{
									foreach($_POST['tiltak'] as $tiltakId=>$tiltak)
									{
										// Load Maal Data and save in MaalPlanMaal table
										$tiltakData = KD::getModel('client/tiltakgov')->load($tiltakId);
										$data = array();
										$data = $tiltak;
										$data['maalplan_maalplanID'] = $maalPlanID;
										$data['maalplan_maalID'] = $tiltakData['tilgov_maalID'];
										$data['maalplan_tiltakName'] = $tiltakData['tilgov_desc'];
										//$data['maalplan_tiltakDesc'] = $tiltak['maalplan_tiltakDesc'];
										$data['maalplan_tiltakID'] = $tiltakId;
										$data['maalplan_tiltakFromDate'] = $format->FormatDate($tiltakData['tilgov_from_date']);
										$data['maalplan_tiltakToDate'] = $format->FormatDate($tiltakData['tilgov_to_date']);


										KD::getModel('client/maalplantiltak')->insert($data);
										$tiltakIds[] = $tiltakId;
									}
								}
								elseif(isset($_POST['maalplantiltak']) && count($_POST['maalplantiltak']) > 0)
								{
									foreach($_POST['maalplantiltak'] as $maalplan_tiltak_id => $maalplantiltak)
									{
										KD::getModel('client/maalplantiltak')->update($maalplantiltak,'maalplan_tiltak_id',$maalplan_tiltak_id);
										$tiltakIds[] = $maalplantiltak['tiltak_id'];
									}
								}
								
								// Insert or Update MaalPlan Other certains
								if(isset($_POST['certain']['maalplan_other_certainDate']) && count($_POST['certain']['maalplan_other_certainDate']) > 0)
								{
									foreach($_POST['certain']['maalplan_other_certainDate'] as $key => $maalplan_other_certainDate)
									{
										if(isset($maalplan_other_certainDate) && $maalplan_other_certainDate!='')
										{
											$data = array();
											$data['maalplan_other_certainDate'] = $maalplan_other_certainDate;
											$data['maalplan_other_certainDesc'] = $_POST['certain']['maalplan_other_certainDesc'][$key];
											$data['maalplan_other_type'] = 'certain';
											$data['maalplan_other_maalplanID'] = $maalPlanID;$data['maalplan_other_patientID'] = $clientID;
											KD::getModel('client/maalplanother')->insert($data);
										}
									}
								}
								elseif(isset($_POST['maalplancertain']) && count($_POST['maalplancertain']) > 0)
								{
									foreach($_POST['maalplancertain'] as $maalplan_other_id => $maalplancertain)
									{
										KD::getModel('client/maalplanother')->update($maalplancertain,'maalplan_other_id',$maalplan_other_id);
									}
								}
								
								// Insert or Update MaalPlan Other Metting
								if(isset($_POST['meeting']['maalplan_other_meetingDate']) && count($_POST['meeting']['maalplan_other_meetingDate']) > 0)
								{
									foreach($_POST['meeting']['maalplan_other_meetingDate'] as $key => $maalplan_other_meetingDate)
									{
										if(isset($maalplan_other_meetingDate) && $maalplan_other_meetingDate!='')
										{
											$data = array();
											$data['maalplan_other_meetingDate'] = $maalplan_other_meetingDate;
											$data['maalplan_other_meetingDesc'] = $_POST['meeting']['maalplan_other_meetingDesc'][$key];
											$data['maalplan_other_type'] = 'meeting';
											$data['maalplan_other_maalplanID'] = $maalPlanID;
											$data['maalplan_other_patientID'] = $clientID;
											KD::getModel('client/maalplanother')->insert($data);
										}
									}
								}
								elseif(isset($_POST['maalplanmeeting']) && count($_POST['maalplanmeeting']) > 0)
								{
									foreach($_POST['maalplanmeeting'] as $maalplan_other_id => $maalplanmeeting)
									{
										KD::getModel('client/maalplanother')->update($maalplanmeeting,'maalplan_other_id',$maalplan_other_id);
									}
								}
								
								// Insert or Update MaalPlan Other Activity
								if(isset($_POST['activity']['maalplan_other_activityDesc1']) && count($_POST['activity']['maalplan_other_activityDesc1']) > 0)
								{
									foreach($_POST['activity']['maalplan_other_activityDesc1'] as $key => $maalplan_other_activityDesc1)
									{
										if(isset($maalplan_other_activityDesc1) && $maalplan_other_activityDesc1!='')
										{
											$data = array();
											$data['maalplan_other_activityDesc1'] = $maalplan_other_activityDesc1;
											$data['maalplan_other_activityDesc2'] = $_POST['activity']['maalplan_other_activityDesc2'][$key];
											$data['maalplan_other_type'] = 'activity';
											$data['maalplan_other_maalplanID'] = $maalPlanID;$data['maalplan_other_patientID'] = $clientID;
											KD::getModel('client/maalplanother')->insert($data);
										}
									}
								}
								elseif(isset($_POST['maalplanactivity']) && count($_POST['maalplanactivity']) > 0)
								{
									foreach($_POST['maalplanactivity'] as $maalplan_other_id => $maalplanactivity)
									{
										KD::getModel('client/maalplanother')->update($maalplanactivity,'maalplan_other_id',$maalplan_other_id);
									}
								}
								
								$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('MaalPlan Report saved Successfully'));
								$this->_redirect('/client/scorecard/index/t/3/id/'.$clientID);
							}
							else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem while saving MaalPlan Report'));
								$this->_redirect('/client/info/index/id/'.$clientID);
							}
						
						}
					}
	}else {
		if(isset($clientID) && $clientID>0 && $clientIDPost==$clientID)
		{
			$maalPlanID = $this->getRequest()->getParam('id');
			$maalPlanIDPost = $_POST['maalplan_id'];
			if($maalPlanID==$maalPlanIDPost)
			{
				$deptId = KD::getModel('client/client')->getClient($clientID,'patient_deptID');
				// Maal Plan ID >0 Update
				$data = $_POST['maalplan'];
				$data['maalplan_location'] = $_POST['patient']['patient_location'];
				$data['maalplan_actionplan'] = $_POST['patient']['patient_actionplan'];
				$data['maalplan_resource'] = $_POST['patient']['patient_resource'];
				if($maalPlanID>0)
				{

                    if(isset($_POST['lock_maalplan']))
					{
						$data['maalplan_status'] = 'no';
						$data['maalplan_lock_date'] = date("Y-m-d H:i:s");
						$data['maalplan_lock_by'] = $_SESSION['Acl']['userID'];
					}
					KD::getModel('client/maalplan')->update($data,'maalplan_id',$maalPlanID);
				}
				// Maal Plan ID == 0 Insert
				else
				{
					$data['maalplan_patientID'] = $clientID;$data['maalplan_deptID'] = $deptId; 
					$maalPlanID = KD::getModel('client/maalplan')->insert($data);
				}
				// $maalPlanID > 0 means maalplan Updated or inserted
				if($maalPlanID>0)
				{
					// Update current vaktrapport - Issue #10
					$vakt_model = KD::getModel('vaktrapport/vaktrapport');
					$report_id = $vakt_model->getCurrentVaktrap($clientID);
					$vakt_model->update(array(
						'vaktrap_tilspan_from_date' => $data['maalplan_tiltak_fromDate'],
						'vaktrap_tilspan_to_date' => $data['maalplan_tiltak_toDate'],
						'vaktrap_maalpan_from_date' => $data['maalplan_maalsty_fromDate'],
						'vaktrap_maalpan_to_date' => $data['maalplan_maalsty_toDate']
					), 'vaktrap_id', $report_id);

					// Updating Patient Information like Plasseting and resources
					KD::getModel('client/client')->update($_POST['patient'],'patient_id',$clientID);
					// Insert or Update MaalPlan Maals
					$maalIds = array();
					$tiltakIds = array();
					if(isset($_POST['maal']) && count($_POST['maal']) > 0)
					{
						foreach($_POST['maal'] as $maalId=>$maal)
						{
							// Load Maal Data and save in MaalPlanMaal table
							$maalData = KD::getModel('client/maal')->load($maalId);
							$data = array();$data = $maal; $data['maalplan_maalplanID'] = $maalPlanID; $data['maalplan_maalID'] = $maalId; $data['maalplan_patientID'] = $clientID; $data['maalplan_deptID'] = $deptId; $data['maalplan_maalFromDate'] = $format->FormatDate($maalData['maal_from_date']); $data['maalplan_maalToDate'] = $format->FormatDate($maalData['maal_to_date']);
							KD::getModel('client/maalplanmaal')->insert($data);
							$maalIds[] = $maalId;
							$_SESSION['bm_'.$maalId] = '';
						}
					}
					elseif(isset($_POST['maalplanmaal']) && count($_POST['maalplanmaal']) > 0)
					{
						foreach($_POST['maalplanmaal'] as $maalplan_maal_id => $maalplanmaal)
						{
							KD::getModel('client/maalplanmaal')->update($maalplanmaal,'maalplan_maal_id',$maalplan_maal_id);
							$maalIds[] = $maalplanmaal['maal_id'];
						}
					}
					
					// Insert or Update MaalPlan Tiltaks
					if(isset($_POST['tiltak']) && count($_POST['tiltak']) > 0)
					{
						foreach($_POST['tiltak'] as $tiltakId=>$tiltak)
						{
							// Load Maal Data and save in MaalPlanMaal table
							$tiltakData = KD::getModel('client/tiltakgov')->load($tiltakId);
							$data = array();$data = $tiltak;$data['maalplan_maalplanID'] = $maalPlanID;$data['maalplan_maalID'] = $tiltakData['tilgov_maalID'];$data['maalplan_tiltakName'] = $tiltakData['tilgov_desc'];$data['maalplan_tiltakID'] = $tiltakId;$data['maalplan_tiltakFromDate'] = $format->FormatDate($tiltakData['tilgov_from_date']);$data['maalplan_tiltakToDate'] = $format->FormatDate($tiltakData['tilgov_to_date']);
							
							KD::getModel('client/maalplantiltak')->insert($data);
							$tiltakIds[] = $tiltakId;
						}
					}
					elseif(isset($_POST['maalplantiltak']) && count($_POST['maalplantiltak']) > 0)
					{
						foreach($_POST['maalplantiltak'] as $maalplan_tiltak_id => $maalplantiltak)
						{
							KD::getModel('client/maalplantiltak')->update($maalplantiltak,'maalplan_tiltak_id',$maalplan_tiltak_id);
							$tiltakIds[] = $maalplantiltak['tiltak_id'];
								$_SESSION['bm_'.$maalplanmaal['maal_id']] = '';
						}
					}
					
					// Insert or Update MaalPlan Other certains
					if(isset($_POST['certain']['maalplan_other_certainDate']) && count($_POST['certain']['maalplan_other_certainDate']) > 0)
					{
						foreach($_POST['certain']['maalplan_other_certainDate'] as $key => $maalplan_other_certainDate)
						{
							if(isset($maalplan_other_certainDate) && $maalplan_other_certainDate!='')
							{
								$data = array();$data['maalplan_other_certainDate'] = $maalplan_other_certainDate;$data['maalplan_other_certainDesc'] = $_POST['certain']['maalplan_other_certainDesc'][$key];$data['maalplan_other_type'] = 'certain';$data['maalplan_other_maalplanID'] = $maalPlanID;$data['maalplan_other_patientID'] = $clientID;
								KD::getModel('client/maalplanother')->insert($data);
							}
						}
					}
					elseif(isset($_POST['maalplancertain']) && count($_POST['maalplancertain']) > 0)
					{
						foreach($_POST['maalplancertain'] as $maalplan_other_id => $maalplancertain)
						{
							KD::getModel('client/maalplanother')->update($maalplancertain,'maalplan_other_id',$maalplan_other_id);
						}
					}
					
					// Insert or Update MaalPlan Other Metting
					if(isset($_POST['meeting']['maalplan_other_meetingDate']) && count($_POST['meeting']['maalplan_other_meetingDate']) > 0)
					{
						foreach($_POST['meeting']['maalplan_other_meetingDate'] as $key => $maalplan_other_meetingDate)
						{
							if(isset($maalplan_other_meetingDate) && $maalplan_other_meetingDate!='')
							{
								$data = array();$data['maalplan_other_meetingDate'] = $maalplan_other_meetingDate;$data['maalplan_other_meetingDesc'] = $_POST['meeting']['maalplan_other_meetingDesc'][$key];$data['maalplan_other_type'] = 'meeting';$data['maalplan_other_maalplanID'] = $maalPlanID;$data['maalplan_other_patientID'] = $clientID;
								KD::getModel('client/maalplanother')->insert($data);
							}
						}
					}
					elseif(isset($_POST['maalplanmeeting']) && count($_POST['maalplanmeeting']) > 0)
					{
						foreach($_POST['maalplanmeeting'] as $maalplan_other_id => $maalplanmeeting)
						{
							KD::getModel('client/maalplanother')->update($maalplanmeeting,'maalplan_other_id',$maalplan_other_id);
						}
					}
					
					// Insert or Update MaalPlan Other Activity
					if(isset($_POST['activity']['maalplan_other_activityDesc1']) && count($_POST['activity']['maalplan_other_activityDesc1']) > 0)
					{
						foreach($_POST['activity']['maalplan_other_activityDesc1'] as $key => $maalplan_other_activityDesc1)
						{
							if(isset($maalplan_other_activityDesc1) && $maalplan_other_activityDesc1!='')
							{
								$data = array();$data['maalplan_other_activityDesc1'] = $maalplan_other_activityDesc1;$data['maalplan_other_activityDesc2'] = $_POST['activity']['maalplan_other_activityDesc2'][$key];$data['maalplan_other_type'] = 'activity';$data['maalplan_other_maalplanID'] = $maalPlanID;$data['maalplan_other_patientID'] = $clientID;
								KD::getModel('client/maalplanother')->insert($data);
							}
						}
					}
					elseif(isset($_POST['maalplanactivity']) && count($_POST['maalplanactivity']) > 0)
					{
						foreach($_POST['maalplanactivity'] as $maalplan_other_id => $maalplanactivity)
						{
							KD::getModel('client/maalplanother')->update($maalplanactivity,'maalplan_other_id',$maalplan_other_id);
						}
					}
					
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('MaalPlan Report saved Successfully'));
					$this->_redirect('/client/scorecard/index/t/3/id/'.$clientID);
				}
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem while saving MaalPlan Report'));
					$this->_redirect('/client/info/index/id/'.$clientID);
				}
				/*// Collection Maal Plan Info
				$maalplanInfo = KD::getModel('client/maalplan')->getActiveMaalPlan($clientID);
				$this->view->maalplanInfo = $maalplanInfo;
				
				// Collection Maal Which are Active for that patient
				$maalplanMaalInfo = KD::getModel('client/maalplanmaal')->loadList($clientID);
				$this->view->maalplanMaalInfo = $maalplanMaalInfo;
				
				// Collection Maal which are achived for that patient but not in the current report
				$maalplanMaalAchiveInfo = KD::getModel('client/maalplanmaal')->loadAchiveList($clientID);
				$this->view->maalplanMaalAchiveInfo = $maalplanMaalAchiveInfo;
				
				// Collection of tiltal order by maal order
				$maalplanTiltakInfo = array();
				foreach($maalplanMaalInfo as $maalplanMaal)
				{
					$maalplanTiltakInfo[$maalplanMaal['maal_id']] = array();
					$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maalplan_maal_id'] = $maalplanMaal['maalplan_maal_id'];
					$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maal_desc'] = $maalplanMaal['maal_desc'];
					$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maalplan_statusdesc'] = $maalplanMaal['maalplan_statusdesc'];
					$maalplanTiltakInfo[$maalplanMaal['maal_id']]['maalplan_evaluering'] = $maalplanMaal['maalplan_evaluering'];
					$maalplanTiltakInfo[$maalplanMaal['maal_id']]['data'] = KD::getModel('client/maalplantiltak')->loadList($clientID,$maalplanMaal['maal_id']);
				}
				$this->view->maalplanTiltakInfo = $maalplanTiltakInfo;*/
			}
		}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
	}
}

