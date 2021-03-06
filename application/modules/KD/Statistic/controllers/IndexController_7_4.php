<?php
class Statistic_IndexController extends KD_Controller_Action {

    public function init() {
        //echo 'asdg';exit();
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
		$clientIDGet = $this->getRequest()->getParam('id');
		//print_r($_POST);exit();
		$deptId = 0;
		if(isset($_POST['deptId']) && $_POST['deptId']>0)
		{
			$deptId = $_POST['deptId'];
			if(isset($_POST['period']) && $_POST['period']!='') 
			{
				$period = $_POST['period'];
			}
			else
			{
				$period = 'lifetime';
			}
		}
		else
		{
			$deptId = 0;
			$period = 'lifetime';
		}
		$departmentArray = KD::getModel('department/department')->loadList();
		$this->view->departmentCollection = $departmentArray; 	
		
		$this->view->deptId = $deptId;
		$this->view->title = $this->view->translate('Statistic');
		$this->view->className = 'PTCLEFTSTATISTIKK';
		/*if(isset($_POST['clientId']) && $_POST['clientId']>0)
		{
			$clientID = $_POST['clientId'];
			if(isset($_POST['period']) && $_POST['period']!='') 
			{
				$period = $_POST['period'];
			}
			else
			{
				$period = 'lifetime';
			}
		}
		elseif(isset($clientIDGet) && $clientIDGet>0)
		{
			$clientID = $clientIDGet;//exit();
			$period = 'lifetime';
		}
		else
		{
			$clientID = 0;
			$period = 'lifetime';
		}*/
		
		if(isset($deptId) && $deptId>0 )
		{
			$this->view->title = $this->view->translate('Statistic');
			$this->view->className = 'PTCLEFTSTATISTIKK';
		
			$deptInfo = KD::getModel('department/department')->load($deptId);
			$format = KD::getModel('core/format');
			$deptName = $deptInfo['dept_name'];
			$this->view->title = $this->view->translate('Statistikk For').' "'.$deptName.'"';
			if(isset($_POST['year']) && $_POST['year']>0) 
			{
				$year = $_POST['year'];
			}
			else
			{
				$year = date('Y');
			}
			
			if(isset($period, $year) && $period!='' && in_array($period,array('curyear','quartal1','quartal2','quartal3','quartal4','lifetime')) && isset($year) && $year>0)
			{
			
				//$showVaktrap = false; used for show emtpy report for first time when we create
				$group = 'date';
				if(isset($_POST['zoomFilter'],$_POST['zoomValue']) && $_POST['zoomFilter']!='' && $_POST['zoomValue']>0)
				{
					echo $group = $_POST['zoomFilter'];
					$period = $_POST['zoomFilter'];
					$zoom = $_POST['zoomValue'];
					if($group=='week')
					{
						$group='date';
						$startDateObj = new DateTime();
						$endDateObj = clone $startDateObj; 
						$startDateObj->setISODate($year,($zoom+1),0);
						$endDateObj->setISODate($year,($zoom+1),6);
						$startDate = $startDateObj->format('Y-m-d');
						$endDate = $endDateObj->format('Y-m-d');
					}
					elseif($group=='month')
					{
						$group='week';
						$startDate = date('Y-m-d', mktime(0, 0, 0, $zoom, 1,$year));
						$startDateObj = new DateTime($startDate);
						$endDateObj = clone $startDateObj; 
						$startDate = $startDateObj->format('Y-m-d');
						$endDateObj->add(new DateInterval('P1M'));
						$endDateObj->sub(new DateInterval('P1D'));
						$endDate = $endDateObj->format('Y-m-d');
					}
					
				}
				else
				{
					switch($period)
					{
						case 'curyear':
							$startDate = ($year-1).'-12-01'; 
							$endDate = $year.'-11-30'; 
							$group = 'month';
						break;
						case 'quartal1':
						case 'quartal2':
						case 'quartal3':
						case 'quartal4':
							if($period=='quartal1')
							{
								$startDate = ($year-1).'-12-01'; 
							}
							else
							{
								$quartal = substr($period,7);
								$quartal = ($quartal-1)*3;
								if($quartal<10) $quartal = '0'.$quartal;
								$startDate = $year.'-'.$quartal.'-01';
							}
							$endDateObj = new DateTime($startDate);
							$endDateObj->add(new DateInterval('P3M'));
							$endDateObj->sub(new DateInterval('P1D'));
							$endDate = $endDateObj->format('Y-m-d');
							$group = 'week';
						break;
		
						case 'lifetime':
							$startDate = '2012-12-01'; 
							$endDate = date('Y-m-d'); 
							$group = 'month';
						break;
					}
				}
				
				if(isset($startDate,$endDate))
				{
					$this->view->startDate = $startDate;
					$this->view->endDate = $endDate;
					$this->view->year = $year;
					$this->view->period = $period;
					//$this->view->clientInfo = $clientInfo;
					// Collecting All vaktrapport Ids
					$allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapFromDate($deptId,$startDate,$endDate,true);	
					$vaktrapIds = array();
					foreach($allEnkelVaktrapresult as $vaktrap)
					{
						$vaktrapIds[] = $vaktrap['vaktrap_id'];
					}
					if(is_array($vaktrapIds) && count($vaktrapIds)>0)
					{
					
						$arrayResult = array('NOTCOMPLETE'=>'0','PARTIALCOMPLETE'=>'1','COMPLETE'=>'2','NOTEVALUATED'=>'3');
						$arrayColor = array(0 => '#E40303',1 => '#FDC600',2 => '#7DC10F',3 => '#4C70F5');
						
						
						//$tiltakInstResult = array('10','0','1','2');//KD::getModel('client/tiltakinst')->loadListByVaktrap($clientID,$vaktrapIds,false,true);
						$this->view->instTiltakCountCollection = array();
						foreach($arrayResult as $key =>$tiltakInst)
						{
						  $dataRes = KD::getModel('client/tiltakinst')->loadListByResult($deptId,$tiltakInst,$vaktrapIds,false,true,true);
						  if(isset($tiltakInst) && $tiltakInst>=0 && $tiltakInst<=2)
						  {
							$this->view->instTiltakCountCollection[$key] = array('count'=>count($dataRes),'result'=>$tiltakInst,'color'=>$arrayColor[$tiltakInst],'data'=>$dataRes);
						  }
						  else
						  {
							$this->view->instTiltakCountCollection[$key] = array('count'=>count($dataRes),'result'=>'3','color'=>$arrayColor[3],'data'=>$dataRes);
						  }
						}
						//echo '<prE>';print_r($this->view->instTiltakCountCollection);exit();
						//$tiltakGovResult = KD::getModel('vaktrapport/vaktraptilgov')->loadListByVaktrap($clientID,$vaktrapIds,false,true);
						$this->view->govTiltakCountCollection = array();
						foreach($arrayResult as $key =>$tiltakGov)
						{
						  $dataRes = KD::getModel('vaktrapport/vaktraptilgov')->loadListByResult($deptId,$tiltakGov,$vaktrapIds,false,true,true);
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
					//Collection ALL Observation With result
					$this->view->observationCollection = array();
					$observations = KD::getModel('vaktrapport/vaktrapobser')->loadListByVaktrap($deptId,$startDate,$endDate,'none',true);
					
					foreach($observations as $key =>$observation)
					{
					  $dataRes = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationForReport($deptId,$startDate,$endDate,$observation['observation_id'],$group,false,true);
					  $checkArray = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationForReport($deptId,$startDate,$endDate,$observation['observation_id'],$group,true,true);
					  
					  $this->view->observationCollection[$observation['observation_id']] = array('count'=>count($checkArray),'checked'=>true,'yTitle'=>'Observation','title'=>$observation['observation_desc'],'xTitle'=>$group,'id'=>$observation['observation_id'],'linedata'=>$dataRes);
					  
					  if(!empty($checkArray))
					  {
						$observations[$key]['checked']=true;
						 $this->view->observationCollection[$observation['observation_id']]['checked'] = true;
					  }
					  else
					  {
						$observations[$key]['checked']=false;
						 $this->view->observationCollection[$observation['observation_id']]['checked'] = false;
					  }
					  
					}
					$observationAlls = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationAllForReport($deptId,$startDate,$endDate,$group,'',true);
					$tmpPeriod = '';
					$observationAllData = array();
					foreach($observationAlls as $observationAll)
					{
						if($observationAll['period']!=$tmpPeriod)
						{ 
							$tmpPeriod = $observationAll['period'];
							$observationAllData[$tmpPeriod] = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationAllForReport($deptId,$startDate,$endDate,$group,$tmpPeriod,true);
						}
					}
					$this->view->observationAllData = $observationAllData;
					$this->view->observations = $observations;
				}
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period & Year for statistik not set'));
					$this->_redirect('/client/info/index/id/'.$clientID);
				}
			}
			else
			{
				
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period & Year for statistik not set'));
				$this->_redirect('/client/info/index/id/'.$clientID);
			}			
		}
		
    }
	public function clientAction() {
		$clientIDGet = $this->getRequest()->getParam('id');
		//print_r($_POST);exit();
		$deptId = 0;
		$defaultTab = 2;
		$this->defaultTab = $defaultTab;
		$this->defaultTabs = $defaultTab;
		if(isset($_POST['deptId']) && $_POST['deptId']>0)
		{
			$deptId = $_POST['deptId'];
		}
		$departmentArray = KD::getModel('department/department')->loadList();
		$this->view->departmentCollection = $departmentArray; 	
		
		$this->view->deptId = $deptId;
		$this->view->title = $this->view->translate('Statistic');
		$this->view->className = 'PTCLEFTSTATISTIKK';
		if(isset($_POST['clientId']) && $_POST['clientId']>0)
		{
			$clientID = $_POST['clientId'];
			if(isset($_POST['period']) && $_POST['period']!='') 
			{
				$period = $_POST['period'];
			}
			else
			{
				$period = 'lifetime';
			}
		}
		elseif(isset($clientIDGet) && $clientIDGet>0)
		{
			$clientID = $clientIDGet;//exit();
			$period = 'lifetime';
		}
		else
		{
			$clientID = 0;
			$period = 'lifetime';
		}
		
		if(isset($clientID) && $clientID>0 )
		{
			$this->view->id = $clientID;
			$this->view->title = $this->view->translate('Statistic');
			$this->view->className = 'PTCLEFTSTATISTIKK';
		
			$clientInfo = KD::getModel('client/client')->load($clientID);
			$format = KD::getModel('core/format');
			$clientName = $format->FormatName($clientInfo['patient_fname'],$clientInfo['patient_mname'],$clientInfo['patient_lname']);
			$this->view->title = $this->view->translate('Statistikk For').' '.$clientName.'';
			if(isset($_POST['year']) && $_POST['year']>0) 
			{
				$year = $_POST['year'];
			}
			else
			{
				$year = date('Y');
			}
			
			if(isset($period, $year) && $period!='' && in_array($period,array('curyear','quartal1','quartal2','quartal3','quartal4','lifetime')) && isset($year) && $year>0)
			{
			
				//$showVaktrap = false; used for show emtpy report for first time when we create
				$group = 'date';
				if(isset($_POST['zoomFilter'],$_POST['zoomValue']) && $_POST['zoomFilter']!='' && $_POST['zoomValue']>0)
				{
					echo $group = $_POST['zoomFilter'];
					$period = $_POST['zoomFilter'];
					$zoom = $_POST['zoomValue'];
					if($group=='week')
					{
						$group='date';
						$startDateObj = new DateTime();
						$endDateObj = clone $startDateObj; 
						$startDateObj->setISODate($year,($zoom+1),0);
						$endDateObj->setISODate($year,($zoom+1),6);
						$startDate = $startDateObj->format('Y-m-d');
						$endDate = $endDateObj->format('Y-m-d');
					}
					elseif($group=='month')
					{
						$group='week';
						$startDate = date('Y-m-d', mktime(0, 0, 0, $zoom, 1,$year));
						$startDateObj = new DateTime($startDate);
						$endDateObj = clone $startDateObj; 
						$startDate = $startDateObj->format('Y-m-d');
						$endDateObj->add(new DateInterval('P1M'));
						$endDateObj->sub(new DateInterval('P1D'));
						$endDate = $endDateObj->format('Y-m-d');
					}
					
				}
				else
				{
					switch($period)
					{
						case 'curyear':
							$startDate = ($year-1).'-12-01'; 
							$endDate = $year.'-11-30'; 
							$group = 'month';
						break;
						case 'quartal1':
						case 'quartal2':
						case 'quartal3':
						case 'quartal4':
							if($period=='quartal1')
							{
								$startDate = ($year-1).'-12-01'; 
							}
							else
							{
								$quartal = substr($period,7);
								$quartal = ($quartal-1)*3;
								if($quartal<10) $quartal = '0'.$quartal;
								$startDate = $year.'-'.$quartal.'-01';
							}
							$endDateObj = new DateTime($startDate);
							$endDateObj->add(new DateInterval('P3M'));
							$endDateObj->sub(new DateInterval('P1D'));
							$endDate = $endDateObj->format('Y-m-d');
							$group = 'week';
						break;
		
						case 'lifetime':
							$startDate = $clientInfo['patient_date_of_joining']; 
							$endDate = date('Y-m-d'); 
							$group = 'month';
						break;
					}
				}
				
				if(isset($startDate,$endDate))
				{
					$this->view->startDate = $startDate;
					$this->view->endDate = $endDate;
					$this->view->year = $year;
					$this->view->period = $period;
					$this->view->clientInfo = $clientInfo;
					// Collecting All vaktrapport Ids
					$allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapFromDate($clientID,$startDate,$endDate);	
					$vaktrapIds = array();
					foreach($allEnkelVaktrapresult as $vaktrap)
					{
						$vaktrapIds[] = $vaktrap['vaktrap_id'];
					}
					if(is_array($vaktrapIds) && count($vaktrapIds)>0)
					{
					
						$arrayResult = array('NOTCOMPLETE'=>'0','PARTIALCOMPLETE'=>'1','COMPLETE'=>'2','NOTEVALUATED'=>'3');
						$arrayColor = array(0 => '#E40303',1 => '#FDC600',2 => '#7DC10F',3 => '#4C70F5');
						
						
						//$tiltakInstResult = array('10','0','1','2');//KD::getModel('client/tiltakinst')->loadListByVaktrap($clientID,$vaktrapIds,false,true);
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
						//echo '<prE>';print_r($this->view->instTiltakCountCollection);exit();
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
					//Collection ALl Observation With result
					$this->view->observationCollection = array();
					$observations = KD::getModel('vaktrapport/vaktrapobser')->loadListByVaktrap($clientID,$startDate,$endDate,'none');
					
					foreach($observations as $key =>$observation)
					{
					  $dataRes = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationForReport($clientID,$startDate,$endDate,$observation['observation_id'],$group);
					  $checkArray = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationForReport($clientID,$startDate,$endDate,$observation['observation_id'],$group,true);
					  
					  $this->view->observationCollection[$observation['observation_id']] = array('count'=>count($checkArray),'checked'=>true,'yTitle'=>'Observation','title'=>$observation['observation_desc'],'xTitle'=>$group,'id'=>$observation['observation_id'],'linedata'=>$dataRes);
					  
					  if(!empty($checkArray))
					  {
						$observations[$key]['checked']=true;
						 $this->view->observationCollection[$observation['observation_id']]['checked'] = true;
					  }
					  else
					  {
						$observations[$key]['checked']=false;
						 $this->view->observationCollection[$observation['observation_id']]['checked'] = false;
					  }
					  
					}
					$observationAlls = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationAllForReport($clientID,$startDate,$endDate,$group);
					$tmpPeriod = '';
					$observationAllData = array();
					foreach($observationAlls as $observationAll)
					{
						if($observationAll['period']!=$tmpPeriod)
						{ 
							$tmpPeriod = $observationAll['period'];
							$observationAllData[$tmpPeriod] = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationAllForReport($clientID,$startDate,$endDate,$group,$tmpPeriod);
						}
					}
					$this->view->observationAllData = $observationAllData;
					$this->view->observations = $observations;
				}
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period & Year for statistik not set'));
					$this->_redirect('/client/info/index/id/'.$clientID);
				}
			}
			else
			{
				
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period & Year for statistik not set'));
				$this->_redirect('/client/info/index/id/'.$clientID);
			}			
		}
		
    }

    public function index1Action() {

        // action body
    }

}

