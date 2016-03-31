<?php

class Vaktrapport_ForceController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }
 
    public function infoAction() {
		$clientID = $this->getRequest()->getParam('id');
		$forceID = $this->getRequest()->getParam('fid');        
		if(isset($clientID) && $clientID > 0)
		{
			if(isset($forceID) && $forceID > 0)
			{
				// Force Collection
				$forceArray = KD::getModel('client/force')->getForceByForceId($forceID);
				if(count($forceArray)>0) $forceArray = $forceArray[0] ;
				
				$force214Array = KD::getModel('client/force214')->getForceByForceId($forceID);
				if(count($force214Array)>0) $force214Array = $force214Array[0] ;
				$forceArray = $forceArray + $force214Array;
				
				$force2511Array = KD::getModel('client/force2511')->getForceByForceId($forceID);
				if(count($force2511Array)>0) $force2511Array = $force2511Array[0] ;
				$forceArray = $forceArray + $force2511Array;

				$forceArray['vaktrapId'] = $forceArray['force_vaktrapID'];
				$forceArray['patientId'] = $forceArray['force_patientID'];
				$this->view->forceInfo = $forceArray;
				
				$this->view->title = $this->view->translate('Force For').'  "'.KD::getModel('client/client')->getClient($clientID,'name').'"';
				$this->view->className = 'PTCLEFTVAKTRAPORT';
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Force'));
				$this->_redirect('/client/info/index/id/'.$clientID);
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index');
		}
    }

	public function saveforceAction()
	{
		
		$patientId = $this->getRequest()->getParam('id'); 
		$force_id = $this->getRequest()->getParam('fid'); 
	 	//echo '<pre>';print_r($_POST);exit(); 
		if(isset($patientId) && ($patientId == $_POST['force_patientID']))
		{
			if(isset($force_id) && $force_id>0) 
			{
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
					
					
				if($force_id>0 && $force_id == $_POST['force_id'])
				{					
					
				
					$flag = KD::getModel('client/force')->update($data,'force_id',$_POST['force_id']);
					
					$force214Array = KD::getModel('client/force214')->getForceByForceId($vaktrapId,$patientId);
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
					
					$force2511Array = KD::getModel('client/force2511')->getForceByForceId($vaktrapId,$patientId);
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
							$this->_redirect('/client/info/index/id/'.$patientId);
						}
					}
					/*if(!($flagForce214 && $flagForce2511))
					{
						$this->_redirect('/vaktrapport/info/index/t/2/id/'.$vaktrapId);
					}*/
					
					$opName = $this->view->translate('Changed');
					
					if($_POST['continue_force'])
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
							$this->_redirect('/client/info/index/id/'.$patientId);
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
							$this->_redirect('/client/info/index/id/'.$patientId);
						}
						
						$data2511['force2511_patientID'] = $_POST['force_patientID'];$data2511['force2511_vaktrapID'] = $_POST['force_vaktrapID'];$data2511['force2511_forceID'] = $flag;
						
						$flagForce2511 = KD::getModel('client/force2511')->insert($data2511);
						if(!$flagForce2511)
						{
							$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Force 2.5-2.11',$opName));
							$this->_redirect('/client/info/index/id/'.$patientId);
						}
						
						if($_POST['continue_force'])
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
						$this->_redirect('/client/info/index/id/'.$patientId);
					}
				}
				if($flag)
				{
					$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Force %s Successfully',$opName));
					$this->_redirect('/client/info/index/id/'.$patientId);
				}
				else
				{
					$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('There is a problem while %s Force',$opName));
					$this->_redirect('/client/info/index/id/'.$patientId);
				}
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Force Id for Force'));
				$this->_redirect('/client/info/index/id/'.$patientId);
			}
		}
		else
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Patient Id for Force'));
			$this->_redirect('/client/info/index/id/'.$patientId);
		}
	}
}