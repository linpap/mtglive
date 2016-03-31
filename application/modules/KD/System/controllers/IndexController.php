<?php
class System_IndexController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {

		$this->view->title = $this->view->translate('System');
		$this->view->className = 'PTCLEFTSYSTEM';
		$systemArray = KD::getModel('system/system')->loadList();
		$this->view->systemCollection = $systemArray;
		#CODE FOR DEPT RESTRICTION
		$session = new Zend_Session_Namespace('Acl');
		if(isset($session->userRole) && in_array($session->userRole,array('A','S','D')))
		{
			$departmentArray = KD::getModel('department/department')->loadList('active','all');
		}
		else
		{
			$departmentArray = KD::getModel('department/department')->loadList('active','all');
		}
		#CODE FOR DEPT RESTRICTION
		
		$this->view->departmentCollection = $departmentArray; 	
		
		$teamDeptID = $this->getRequest()->getParam('tid');
		if(isset($teamDeptID) && $teamDeptID>0)
		{
			
			$userAvailableArray = KD::getModel('system/team')->loadAvailUserByDept($teamDeptID);			
			$userTeam1Array = KD::getModel('system/team')->loadList('1',$teamDeptID);
			$userTeam2Array = KD::getModel('system/team')->loadList('2',$teamDeptID);
			$userTeam3Array = KD::getModel('system/team')->loadList('3',$teamDeptID);
			$this->view->userAvailableCollection = $userAvailableArray;
			$this->view->userTeam1Collection = $userTeam1Array;
			$this->view->userTeam2Collection = $userTeam2Array;
			$this->view->userTeam3Collection = $userTeam3Array;			
			$this->view->deptId = $teamDeptID;
		}
		
		
		if($_POST)
		{
			$format = KD::getModel('core/format');
			if(isset($_POST['create_dept'])){
				$departmentArray = KD::getModel('department/department')->checkDepartment($_POST['department']['dept_code']);
				if($departmentArray)
				{
					$data = array(); $data = $_POST['department'];
					$codes = KD::getModel('department/department')->loadListByCode($_POST['department']['dept_code']);
					$codeLength = strlen($_POST['department']['dept_code']);
					$increment = 1;
					foreach($codes as $code)
					{
						$tmpIncrement = substr($code['dept_code'],$codeLength);
						if($increment<$tmpIncrement)
						{
							$increment = $tmpIncrement;
						}
					}
					$data['dept_code'] = $_POST['department']['dept_code'].($increment+1);
					$this->createdepartmentpost($data);
				}
				else
				{	
					$this->createdepartmentpost($_POST['department']);
				}
			}
			else if(isset($_POST['create_user'])){
				$userArray = KD::getModel('user/user')->checkUser($_POST['user']['user_code']);
				$userEmailArray = KD::getModel('user/user')->checkUserEmail($_POST['user']['user_email']);
				if($userArray || $userEmailArray)
				{
					if($userArray)
					{
						$data = array(); $data = $_POST['user'];
						$codes = KD::getModel('user/user')->loadListByCode($_POST['user']['user_code']);
						$codeLength = strlen($_POST['user']['user_code']);
						$increment = 1;
						foreach($codes as $code)
						{
							$tmpIncrement = substr($code['user_code'],$codeLength);
							if($increment<$tmpIncrement)
							{
								$increment = $tmpIncrement;
							}
						}
						$data['user_code'] = $_POST['user']['user_code'].($increment+1);
						$this->createuserpost($data);
					}
					else
					{
						$this->view->message['error'][]= 'User already exist';
					}
				}
				else
				{	
					//print_r($_POST['user']['user_deptidMul']);
					$data = array();
					$data = $_POST['user'];
					if($_POST['user']['user_role']=='L')
					{
						$data['user_deptid'] = implode(',',$_POST['user']['user_deptidMul']);
					}
					else
					{
						$data['user_deptid'] = $_POST['user']['user_deptid'];
					}
					//echo '<pre>';print_r($data);exit();
					$this->createuserpost($data);
				}
			}
			else if(isset($_POST['create_client'])){

				$departmentArray = KD::getModel('client/client')->checkClient($_POST['patient']['patient_birk_no']);
				if($departmentArray)
				{
					$this->view->message['error'][]= 'Client Birk No. already exist';
				}
				else
				{

					$this->createclientpost($_POST['patient']);
				}
			}
			else if(isset($_POST['create_report'])){
				$postData = $_POST['vaktrap'];
				$patientID = $postData['vaktrap_patientID'];
				$deptID = $postData['vaktrap_deptID'];
				if(isset($deptID,$patientID) && $deptID>0 && $patientID>0)
				{
					$data = array();
					$data = $_POST['patient'];
					$isPatientUpdated = KD::getModel('client/client')->update($data,'patient_id',$patientID);
					if($isPatientUpdated)
					{
						unset($_POST['vaktrap']['vaktrap_to_date']);
						$data = array();
						$data = $_POST['vaktrap'];
												
						$previousVaktrap = KD::getModel('vaktrapport/vaktrapport')->getPreviousVaktrap($data['vaktrap_patientID']);
						if(!empty($previousVaktrap))
						{
							$data['vaktrap_from_date'] = $format->FormatDate($previousVaktrap['vaktrap_to_date']);
						}	
						else
						{
							$data['vaktrap_from_date'] = $data['vaktrap_from_date'];
						}
						$toDateObj = new DateTime($format->PrepareDateDB($data['vaktrap_from_date']));
						$toDateObj->add(new DateInterval('P6D'));
						$data['vaktrap_to_date'] = $format->FormatDate($toDateObj->format('Y-m-d'));
						$this->createvaktraport($data,$patientID);
						$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Scorecard created Successfully'));
					}
					else
					{
						$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem while creating Scorecard'));
					}
				}
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Problem while creating Scorecard'));
					$this->_redirect('/system/index/index/t/1');
				}
			}
			else if(isset($_POST['set_config'])){
				$this->setConfig($_POST['config']);
			}				
		}
    }
	
	public function teamAction() {
		$this->_helper->layout->setLayout('layout_ajax');
	    $key = $this->getRequest()->getParam('key');
		if(isset($key) && $key>0)
		{
			
			$userAvailableArray = KD::getModel('system/team')->loadAvailUserByDept($key);
			$this->view->userAvailableCollection = $userAvailableArray;
			
			$userTeam1Array = KD::getModel('system/team')->loadList('1',$key);
			$this->view->userTeam1Collection = $userTeam1Array;
			$userTeam2Array = KD::getModel('system/team')->loadList('2',$key);
			$this->view->userTeam2Collection = $userTeam2Array;
			$userTeam3Array = KD::getModel('system/team')->loadList('3',$key);
			$this->view->userTeam3Collection = $userTeam3Array;
			$this->view->deptId = $key;
		}
		else
		{
			echo '';
			exit();
		}
	}
	
	public function moveteamAction() {
	    $teamID = $this->getRequest()->getParam('id');
		$team_deptID = $_POST['team_deptId'];
		if(isset($teamID) &&  isset($team_deptID) && $team_deptID>0)
		{
			$ids = $this->_archive($_POST,'on');
			if($ids)
			{	
				foreach($ids as $key=>$id):
					$team = KD::getModel('system/team')->load($id,'team_userID');
					$data = array('team_deptID'=>$team_deptID,'team_team'=>$teamID);
					if($team)
					{
						KD::getModel('system/team')->update($data,'team_userID',$id);
						$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Bruker er nå flyttet til Team-'.$teamID));
					}
					else
					{
						$data['team_userID'] = $id;
						$result = KD::getModel('system/team')->insert($data);
						$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('User Added Successfully to Team-'.$teamID));
					}
				endforeach;
				$this->_redirect('/system/index/index/t/5/tid/'.$team_deptID);
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Team Change Error'));
		$this->_redirect('/system/index/index/t/5/tid/'.$team_deptID);
	}
	
	public function createdepartmentpost($data) { 
		$departmentArray = KD::getModel('department/department')->checkDepartment($data['dept_code']);
		if($departmentArray)
		{
			$this->view->message['error'][]= 'User already exist';
			$this->_redirect('/system/index/');
		}
		else
		{
			KD::getModel('department/department')->insert($data);
			$this->flashMessenger->setNamespace('success')->addMessage('Avdeling er nå opprettet');
			$this->_redirect('/department/index/');
		}
	}
	public function createuserpost($data) { 
		$userArray = KD::getModel('user/user')->checkUser($data['user_code']);
		if($userArray)
		{
			$this->view->message['error'][]= 'User already exist';
            return false;
		}

        $format = KD::getModel('core/format');

        $userRegister = KD::getModel('user/user')->insert($data);
        if(!is_array($userRegister) || count($userRegister) <= 0) {
            $this->flashMessenger->setNamespace('success')->addMessage('Problem while creating User');
            $this->_redirect('/user/index/');
            return false;
        }

        parent::sendEmail($data['user_email'],'User Registration','Register.phtml',  array(
            'email' => $data['user_email'],
            'password' => $userRegister['user_password'],
            'name' => $format->FormatName($data['user_fname'],$data['user_mname'],$data['user_lname']
        )));

        $this->flashMessenger->setNamespace('success')->addMessage('Behandler er nå opprettet');
        $this->_redirect('/user/index/');
	}
	public function createvaktraport($data,$patientID) { 
		// Create Vaktrapport
		$format = KD::getModel('core/format');
		$vaktrapId = KD::getModel('vaktrapport/vaktrapport')->insert($data);
		if($vaktrapId)
		{
			$this->flashMessenger->setNamespace('success')->addMessage('Vaktrapporten er nå opprettet');
			// First time we have created some Institutional Tiltak before vaktrapport so we are not knowing the vaktrapport id so we are just assigning vaktrapId to inst tiltak
			$tiltakInsArray = KD::getModel('client/tiltakinst')->loadList($patientID);
			$tiltakIDs = array();
			foreach($tiltakInsArray as $tiltakIns)
			{
				$tiltakIDs[] = $tiltakIns['tilins_id'];
			}
			if(!empty($tiltakIDs))
			{
				$data = array();
				$data['tilins_vaktrapportID'] = $vaktrapId;
				$flag = KD::getModel('client/tiltakinst')->update($data,'tilins_id',$tiltakIDs,true);
				if(!$flag)
				{
					$this->flashMessenger->setNamespace('error')->addMessage('There is some problem while assiging some current Institutional tiltak to Vaktrapport, SO you can Edit them and save will reflected in Vaktrapport');
				}
			}
			
			
			
			$maalPlanInfo = array();
			$maalPlanInfo['maalplan_patientID'] = $patientID;
			$patientArray = KD::getModel('client/client')->load($patientID);
			if(isset($data['vaktrap_from_date']) && $data['vaktrap_from_date']!='')
			{
				$fromDate = $format->PrepareDateDB($data['vaktrap_from_date']);
			}
			elseif(is_array($patientArray) && count($patientArray)>0)
			{
				$fromDate = $patientArray['patient_date_of_joining'];
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage('ScoreCard is not stored Successfully');
				$this->_redirect('/system/index/');
			}

			$fromDateObj = new DateTime($fromDate);
			$maalPlanInfo['maalplan_deptID'] = $patientArray['patient_deptID'];
			$maalPlanInfo['maalplan_location'] = $patientArray['patient_location'];
			$maalPlanInfo['maalplan_actionplan'] = $patientArray['patient_actionplan'];
			$maalPlanInfo['maalplan_resource'] = $patientArray['patient_resource'];
			
			$fromYear = $fromDateObj->format('Y');
			$fromMonth = $fromDateObj->format('m');
			switch($fromMonth)
			{
				case '12':
				case '1':
				case '2':
					$toDateObj = new DateTime($fromYear.'-12-01');
					$toDateObj->add(new DateInterval('P3M'));
					$toDateObj->sub(new DateInterval('P1D'));
				break;
					
				case '3':
				case '4':
				case '5':
					$toDateObj = new DateTime($fromYear.'-03-01');
					$toDateObj->add(new DateInterval('P3M'));
					$toDateObj->sub(new DateInterval('P1D'));
				break;
					
				case '6':
				case '7':
				case '8':
					$toDateObj = new DateTime($fromYear.'-06-01');
					$toDateObj->add(new DateInterval('P3M'));
					$toDateObj->sub(new DateInterval('P1D'));
				break;
				
				case '9':
				case '10':
				case '11':
					$toDateObj = new DateTime($fromYear.'-09-01');
					$toDateObj->add(new DateInterval('P3M'));
					$toDateObj->sub(new DateInterval('P1D'));
				break;
			}
			$maalPlanInfo['maalplan_from_date'] = KD::getModel('core/format')->FormatDate($fromDateObj->format('Y-m-d'));
			$maalPlanInfo['maalplan_to_date'] = KD::getModel('core/format')->FormatDate($toDateObj->format('Y-m-d'));
			$maalPlanInfo['maalplan_status'] = 'yes';
			$maalPlanInfo['maalplan_patientID'] = $patientID;
			$maalplanId = KD::getModel('client/maalplan')->insert($maalPlanInfo);
		
			$this->_redirect('/client/report/index/id/'.$patientID);
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage('ScoreCard is not stored Successfully');
			$this->_redirect('/system/index/');
		}
	}
	public function createclientpost($data) { 
        //echo $data['patient_genogram'];
		//die;
		$data=KD::getModel('client/client')->insert($data);
//		echo $data;
//		die;


		$patient_id =$data;
		$genogram_str =$data['patient_genogram'];
		if(isset($patient_id) && $patient_id>0)
		{
			$data_ar = array(); $data['patient_genogram'] = $genogram_str;
			KD::getModel('client/client')->update($data_ar,'patient_id',$patient_id);
		}

		$this->flashMessenger->setNamespace('success')->addMessage('Klient er nå opprettet');
		$this->_redirect('/system/index');
	}	
	public function setConfig($data) { 
		KD::getModel('system/system')->update($data);
		$this->flashMessenger->setNamespace('success')->addMessage('System endringer er nå lagret');
		$this->_redirect('/system/index/index/tab/tab6');
	}

    public function eventsAction() {

		$session = new Zend_Session_Namespace('Acl');

        $this->_helper->layout->setLayout('layout_ajax');
        $date = explode('-', $this->getRequest()->getParam('date'));

        $dateTime = new \DateTime('now', new \DateTimeZone('Europe/Oslo'));
        $dateTime->setDate($date[2], $date[0], $date[1]);
        $department = null;


        if(isset($session->userDeptId[0]))  {
            $department = $session->userDeptId['0'] === 'all' ? null :  $session->userDeptId['0'];
        }
        $this->view->events = KD::getModel('client/weekplan')->getByDate($dateTime->format('Y-m-d'), $department);
    }

}