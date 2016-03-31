<?php
class Client_MedicineController extends KD_Controller_Action {

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
			$this->view->title = $this->view->translate('Medicine Plan For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
			$medicinesArray = KD::getModel('client/medicine')->loadPageData($clientID);
			$this->view->medicineCollection = $medicinesArray;
			$this->view->clientID = $clientID;
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function createAction() {
		$clientID = $this->getRequest()->getParam('id');//exit();
		$medicineID = $this->getRequest()->getParam('mdid');//exit();
		if(isset($clientID) && $clientID>0 )
		{
			$this->view->id = $clientID;
			$this->view->className = 'PTCLEFTVAKTRAPORT';
			$this->view->title = $this->view->translate('Medicine Plan For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
			if(isset($medicineID) && $medicineID>0)
			{
				$medicineArray = KD::getModel('client/medicine')->load($medicineID);
				$weekDays = array(0=>'mon',1=>'tue',2=>'wed',3=>'thu',4=>'fri',5=>'sat',6=>'sun');
				$medicineDetailArray = array();
				foreach($weekDays as $day)
				{
					$medicineDetailArray[$day] = KD::getModel('client/medicinedetail')->getMedDetByDayMedID($clientID,$medicineID,$day); 
				}
				
			}
			else
			{
				$medicineArray = array();
				$medicineDetailArray = array();
			}
			//echo '<pre>';print_r($medicineDetailArray);exit();
			$this->view->medicinCollection = $medicineArray;
			$this->view->medicineDetailCollection = $medicineDetailArray;
			$this->view->clientID = $clientID;

		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	
	public function createpostAction()
	{
		$patientId = $this->getRequest()->getParam('id'); 
	 	//echo '<pre>';print_r($_POST);exit();
		//echo '<pre>';
		//print_r($_POST);
		//exit();
		if(isset($patientId) && ($patientId == $_POST['medicine_patientID']))
		{
			if(isset($_POST['medicine_from_date']) && $_POST['medicine_from_date']!='' && (strpos($_POST['medicine_from_date'],'0000')===false))
			{
				$format = KD::getModel('core/format');
				$medicineArray = KD::getModel('client/medicine')->getMedicineByFromDate($patientId,$format->PrepareDateDB($_POST['medicine_from_date']));
				if(count($medicineArray)>0) {$medicineCount = count($medicineArray);$medicineArray = $medicineArray[0];}
				//echo '<pre>';print_r($medicineArray);exit();
				// If array count greater than zero means is not empty and medicine plan is set for that start date so edit otherwise Inser
				$startDate = $_POST['medicine_from_date'];
				$startDateForCheck = $format->PrepareDateDB($startDate);
				$endDateObj = new DateTime($startDateForCheck);
				$endDateObj->add(new DateInterval('P6D'));
				$endDate = $endDateObj->format("Y-m-d");
				$endDate = $format->FormatDate($endDate);
				$week = $endDateObj->format("W");
				// If data exist for medicineID and  post ID and exist medicine ID are same Update
				if($medicineCount>0 && ($medicineArray['medicine_id'] == $_POST['medicineID']))
				{
				  if(isset($_POST['medicineID']) && $_POST['medicineID']>0)
				  {
					//$data = array(); $data['medicine_start_date'] = $startDate; $data['medicine_end_date'] = $endDate; $data['medicine_patientID'] = $patientId; $data['medicine_week'] =  $week;
					//$flag = KD::getModel('client/medicine')->insert($data);
					$dataDetail = array();$dataDetail['med_det_patientID'] = $patientId;$dataDetail['med_det_medicineID'] = $medicineArray['medicine_id'];$dataDetail['med_det_isExtra'] = 'no';
					$weekDays = array(0=>'mon',1=>'tue',2=>'wed',3=>'thu',4=>'fri',5=>'sat',6=>'sun');
					$startDateForDate = $format->PrepareDateDB($startDate);
					
					//echo '<prE>';print_r($_POST);exit();
					foreach($weekDays as $counter=>$day)
					{
						//Updating the existing Medicne Details
						//echo $medicineArray['medicine_id']; echo $patientId; echo $day;
						$medicineDetailExistArray = KD::getModel('client/medicinedetail')->getMedDetByDayMedID($patientId,$medicineArray['medicine_id'],$day); 
						
						foreach($medicineDetailExistArray as $medicineExist)
						{
							$data = array();
							$data['med_det_name'] = $_POST['medicine_'.$medicineExist['med_det_id']];
							$data['med_det_nos'] = $_POST['medicine_no_'.$medicineExist['med_det_id']];
							$data['med_det_time'] = $_POST['medicine_time_'.$medicineExist['med_det_id']];
							if(isset($data['med_det_name'],$data['med_det_nos'],$data['med_det_time']) && $data['med_det_name']!='' && $data['med_det_nos']!='' && $data['med_det_time']!='')
							{
								$flag = KD::getModel('client/medicinedetail')->update($data,'med_det_id',$medicineExist['med_det_id']);
							}
							else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Error while Updating Medicine Name '.$data['med_det_name'].' with Qty '.$data['med_det_nos'].' at '.$data['med_det_time']));
							}
						}
						
						//Inserting Medicne Details which are not existed in Medicine Chart
						if(isset($_POST[$day]) && count($_POST[$day])>0)
						  {
						  	
							$medicineDetailArray = $_POST[$day];
							//echo '<prE>';print_r($medicineDetailArray);
							if(isset($medicineDetailArray) && count($medicineDetailArray)>0)
							{	
								$medicineDateObj = new DateTime($startDateForDate);
								$medicineDateObj->add(new DateInterval('P'.$counter.'D'));
								$medicineDate = $medicineDateObj->format("Y-m-d");
								$medicineDate = $format->FormatDate($medicineDate);
								
								$dataDetail['med_det_day'] = $day;//$medicineDateObj->format('l');
								$dataDetail['med_det_date'] = $medicineDate;
								
								foreach($medicineDetailArray['med_det_medicine'] as $id=>$medicineName)
								{
									$dataDetail['med_det_name'] = $medicineName;
									$dataDetail['med_det_nos'] = $medicineDetailArray['med_det_nos'][$id];
									$dataDetail['med_det_time'] = $medicineDetailArray['med_det_time'][$id];
									
									if(isset($dataDetail['med_det_name'],$dataDetail['med_det_nos'],$dataDetail['med_det_time']) && $dataDetail['med_det_name']!='' && $dataDetail['med_det_nos']!='' && $dataDetail['med_det_time']!='')
									{
										$flag = KD::getModel('client/medicinedetail')->insert($dataDetail);
									}
									elseif($dataDetail['med_det_name']=='' && $dataDetail['med_det_nos']=='1' && $dataDetail['med_det_time']=='')
									{
										
									}
									else
									{
										$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Some Fields are Empty while Inserting Medicine Name "%s" with Qty "%s" at "%s". So This Medicine are not addded the Medicine Plan',$dataDetail['med_det_name'],$dataDetail['med_det_nos'],$dataDetail['med_det_time']));
									}
								}
							}
						}
					}
					//exit();
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Medicine Plan Saved successfully'));
					$this->_redirect('/client/medicine/index/t/7/id/'.$patientId);
				  
				  }
				  else
				  {
				  	$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Medicine Post Data'));
					$this->_redirect('/client/index');	
				  }
				}
				// If data not exist for medicineID and  post ID =0 are means you are creating New Medicine For Not exist Duration so Insert
				elseif($medicineCount<=0 && $_POST['medicineID']==0)
				{
					$data = array(); $data['medicine_start_date'] = $startDate; $data['medicine_end_date'] = $endDate; $data['medicine_patientID'] = $patientId; $data['medicine_week'] =  $week;
					$flag = KD::getModel('client/medicine')->insert($data);
					if($flag)
					{
						$dataDetail = array();$dataDetail['med_det_patientID'] = $patientId;$dataDetail['med_det_medicineID'] = $flag;$dataDetail['med_det_isExtra'] = 'no';
						$weekDays = array(0=>'mon',1=>'tue',2=>'wed',3=>'thu',4=>'fri',5=>'sat',6=>'sun');
						foreach($weekDays as $counter=>$day)
						{
						  if(isset($_POST[$day]) && count($_POST[$day])>0)
						  {
							$medicineDetailArray = $_POST[$day];
							if(isset($medicineDetailArray) && count($medicineDetailArray)>0)
							{	
								$startDateForDate = $format->PrepareDateDB($startDate);
								$medicineDateObj = new DateTime($startDateForDate);
								$medicineDateObj->add(new DateInterval('P'.$counter.'D'));
								$medicineDate = $medicineDateObj->format("Y-m-d");
								$medicineDate = $format->FormatDate($medicineDate);
								
								$dataDetail['med_det_day'] = $day;//strtolower($medicineDateObj->format('l'));
								$dataDetail['med_det_date'] = $medicineDate;
								
								foreach($medicineDetailArray['med_det_medicine'] as $id=>$medicineName)
								{
									if(isset($medicineName,$medicineDetailArray['med_det_nos'][$id],$medicineDetailArray['med_det_time'][$id]) && $medicineName!='' && $medicineDetailArray['med_det_nos'][$id]!='' && $medicineDetailArray['med_det_time'][$id]!='')
									{
										$dataDetail['med_det_name'] = $medicineName;
										$dataDetail['med_det_nos'] = $medicineDetailArray['med_det_nos'][$id];
										$dataDetail['med_det_time'] = $medicineDetailArray['med_det_time'][$id];
										$flag = KD::getModel('client/medicinedetail')->insert($dataDetail);
									}
									elseif($medicineName=='' && $medicineDetailArray['med_det_nos'][$id]=='1' && $medicineDetailArray['med_det_time'][$id]=='')
									{
										
									}
									else
									{
										$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Some Fields are Empty while Inserting Medicine Name "%s" with Qty "%s" at "%s". So This Medicine are not addded the Medicine Plan',$medicineName,$medicineDetailArray['med_det_nos'][$id],$medicineDetailArray['med_det_time'][$id]));
									}
								}
							}
						  }
						}
						$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Medicine Plan Saved successfully'));
						$this->_redirect('/client/medicine/index/t/7/id/'.$patientId);
						
					}
					else
					{
						$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Error while creating Medicine Plan'));
						$this->_redirect('/client/index');
					}
				}
				// If data exist for medicineID and  post ID =0 are means you are creating Medicine For same Duration so send Error and redirect
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Medicine Plan Already Exist For that Duration'));
					$this->_redirect('/client/medicine/create/t/7/id/'.$patientId);
				}
				
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid From Date'));
				$this->_redirect('/client/index');
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Patient Id for Medicine Plan'));
			$this->_redirect('/client/index');
		}
		
	}
	public function copymedicineAction()
	{
		$isAjax = $this->getRequest()->getParam('isAjax');
		if($isAjax)
		{
			$this->_helper->layout->setLayout('layout_ajax');
		}
		$clientID = $this->getRequest()->getParam('id');//exit();
		$medicineID = $this->getRequest()->getParam('mdid');//exit();
		if(isset($clientID, $medicineID) && $clientID>0 && $medicineID)
		{
			$this->view->medicineID = $medicineID;
			$this->view->clientID = $clientID;

		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
	}
	public function copymedicinepostAction()
	{
		
		$clientID = $this->getRequest()->getParam('id');//exit();
		$medicineID = $this->getRequest()->getParam('mdid');//exit();
		if(isset($clientID, $medicineID) && $clientID>0 && $medicineID>0)
		{
			$medicineArray = KD::getModel('client/medicine')->load($medicineID);
			//print_r($medicineArray);exit();
			if($medicineArray['medicine_id'] == $_POST['medicineID']  && $medicineID == $_POST['medicineID'])
			{
				$format = KD::getModel('core/format');
				$noOfWeek = 0;
				if(isset($_POST['noofweek'])  && $_POST['noofweek']>0)
				{
					$noOfWeek = $_POST['noofweek'];
					$startDate = $medicineArray['medicine_start_date'];
					$startDateForCheck = new DateTime($startDate);
					$startDateForCheck->add(new DateInterval('P7D'));
					$startDateForCheck = $startDateForCheck->format("Y-m-d");
					$startDate = $format->FormatDate($startDateForCheck);
					
					$endDateObj = new DateTime($startDateForCheck);
					$endDateObj->add(new DateInterval('P6D'));
					$endDate = $endDateObj->format("Y-m-d");
					$endDate = $format->FormatDate($endDate);
					$week = $endDateObj->format("W");
					//exit();
					$errorFlag = false;
					for($i=1;$i<=$noOfWeek;$i++)
					{
						$medicineDate = $startDateForCheck;
						$medicineCheckArray = KD::getModel('client/medicine')->getMedicineByFromDate($clientID,$startDateForCheck);
						if(count($medicineCheckArray)<=0)
						{
							$data = array(); $data['medicine_start_date'] = $startDate; $data['medicine_end_date'] = $endDate; $data['medicine_patientID'] = $clientID; $data['medicine_week'] =  $week;
							
							$medicineInsertId = KD::getModel('client/medicine')->insert($data);
							if($medicineInsertId)
							{
								$dataDetail = array();$dataDetail['med_det_patientID'] = $clientID;$dataDetail['med_det_medicineID'] = $medicineInsertId;
								$weekDays = array(0=>'mon',1=>'tue',2=>'wed',3=>'thu',4=>'fri',5=>'sat',6=>'sun');
								foreach($weekDays as $counter=>$day)
								{
									// Getting All Medicine Detail Entery From where we are copying so its med_det_medicineID = $medicineArray['medicine_id']
									$medicineDetailExistArray = KD::getModel('client/medicinedetail')->getMedDetByDayMedID($clientID,$medicineArray['medicine_id'],$day); 
									foreach($medicineDetailExistArray as $medicineExist)
									{
										$data = $dataDetail;
										$data['med_det_name'] = $medicineExist['med_det_name'];
										$data['med_det_nos'] = $medicineExist['med_det_nos'];
										$data['med_det_time'] = $medicineExist['med_det_time'];
										$data['med_det_isExtra'] = $medicineExist['med_det_isExtra'];
										$data['med_det_date'] = $format->FormatDate($medicineDate);
										$data['med_det_day'] = $day;
										
										$flagMedicineDetail = KD::getModel('client/medicinedetail')->insert($data);
										if(!$flagMedicineDetail)
										{
											$errorFlag = true;
											$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Error while Inserting Medicine Name '.$data['med_det_name'].' with Qty '.$data['med_det_nos'].' at '.$data['med_det_time']));
										}
									}
									$medicineDateObj = new DateTime($medicineDate);
									$medicineDateObj->add(new DateInterval('P1D'));
									$medicineDate = $medicineDateObj->format("Y-m-d");
								}
								
							}
							else
							{
								$errorFlag = true;
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Error while creating Medicine Plan For Starts Date %s',$startDateForCheck));
							}
						}
						else
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Medicine Plan For Starts Date %s is Already Exist',$startDateForCheck));
						}
						$startDateForCheck = new DateTime($startDateForCheck);
						$startDateForCheck->add(new DateInterval('P7D'));
						$startDateForCheck = $startDateForCheck->format("Y-m-d");
						$startDate = $format->FormatDate($startDateForCheck);
						
						$endDateObj = new DateTime($startDateForCheck);
						$endDateObj->add(new DateInterval('P6D'));
						$endDate = $endDateObj->format("Y-m-d");
						$endDate = $format->FormatDate($endDate);
						$week = $endDateObj->format("W");
					}
					if($errorFlag)
					{
						$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('But Medicine Plan Copy successfully'));
						$this->_redirect('/client/medicine/index/t/7/id/'.$clientID);	
					}
					else
					{
						$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Medicine Plan Copy successfully'));
						$this->_redirect('/client/medicine/index/t/7/id/'.$clientID);
					}
				}
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Number of Week For Copy Medicine Plan'));
					$this->_redirect('/client/medicine/index/id/'.$clientID.'/mdid/'.$medicineID);
				}
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Medicine Plan'));
				$this->_redirect('/client/medicine/index/id/'.$clientID.'/mdid/'.$medicineID);
			}

		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Client'));
			$this->_redirect('/client/index/');
		}
		if($medicineID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Medicine Plan'));
			$this->_redirect('/client/medicine/index/id/'.$medicineID);
		}
	}
}

