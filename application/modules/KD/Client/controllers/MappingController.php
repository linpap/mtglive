<?php
class Client_MappingController extends KD_Controller_Action {

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
			$clientObg = KD::getModel('client/client')->load($clientID);
			$this->view->title = $this->view->translate('Mapping for').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
			if(isset($clientObg['patient_genogram']) && $clientObg['patient_genogram']!='')
			{
				$this->view->initString = $clientObg['patient_genogram'];
			}
			else
			{
				$this->view->initString = '{"class":"go.GraphLinksModel","linkFromPortIdProperty": "fromPort","linkToPortIdProperty":"toPort"}';
			}
			$this->view->clientID = $clientID;
			$this->view->plassering = $clientObg['patient_location'];
			$type1Array = KD::getModel('client/genoextra')->loadList($clientID,'type1');
			$type2Array = KD::getModel('client/genoextra')->loadList($clientID,'type2');
			$this->view->type1Collection = $type1Array;
			$this->view->type2Collection = $type2Array;
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
	public function savegenogramAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$patient_id =$_POST['patient_id']; 
		$genogram_str =$_POST['genogram_str']; 
		if(isset($patient_id) && $patient_id>0)
		{
			$data = array(); $data['patient_genogram'] = $genogram_str;
			$result = KD::getModel('client/client')->update($data,'patient_id',$patient_id);			
			echo ('Genogram Saved Successfully');   
		}
		else
		{
			echo ('Genogram Updatation Not allowed');
		}
		exit();
	}
	
	 public function saveAction() {
	   //echo '<pre>';print_r($_POST);exit();
       $clientID = $this->getRequest()->getParam('id');//exit();
	   
		if(isset($clientID) && $clientID>0 )
		{
			if($clientID == $_POST['client_id'])
			{
				if(isset($_POST['patient_location']))
				{
					$data = array();
					$data['patient_location'] = $_POST['patient_location'];
					KD::getModel('client/client')->update($data,'patient_id',$clientID);
				}
				if(isset($_POST['geno']) && count($_POST['geno'])>0)
				{
					if(isset($_POST['geno']['desc1']) && count($_POST['geno']['desc1'])>0)
					{
						$desc1s = $_POST['geno']['desc1'];
						foreach($desc1s as $key => $desc1)
						{
							if(isset($desc1) && $desc1!='' && isset($_POST['geno']['desc2'][$key]) && $_POST['geno']['desc2'][$key]!='')
							{
								
								$data = array();$data['geno_extra_patientID'] = $clientID;$data['geno_extra_type'] = 'type1';$data['geno_extra_desc1'] = $desc1;$data['geno_extra_desc2'] = $_POST['geno']['desc2'][$key];
								KD::getModel('client/genoextra')->insert($data);
							}
							else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Data So Unable to Add One row'));
							}
						}
					}
					
					if(isset($_POST['geno']['desc3']) && count($_POST['geno']['desc3'])>0)
					{
						$desc3s = $_POST['geno']['desc3'];
						foreach($desc3s as $key => $desc3)
						{
							if(isset($desc3) && $desc3!='' && isset($_POST['geno']['desc4'][$key]) && $_POST['geno']['desc4'][$key]!='' && isset($_POST['geno']['desc5'][$key]) && $_POST['geno']['desc5'][$key]!='')
							{
								
								$data = array();$data['geno_extra_patientID'] = $clientID;$data['geno_extra_type'] = 'type2';$data['geno_extra_desc3'] = $desc3;$data['geno_extra_desc4'] = $_POST['geno']['desc4'][$key];$data['geno_extra_desc5'] = $_POST['geno']['desc5'][$key];
								KD::getModel('client/genoextra')->insert($data);
							}
							else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Data So Unable to Add One row'));
							}

						}
					}
				}
				
				if(isset($_POST['type1']) && count($_POST['type1'])>0)
				{
					if(isset($_POST['type1']['desc1']) && count($_POST['type1']['desc1'])>0)
					{
						$desc1s = $_POST['type1']['desc1'];
						foreach($desc1s as $key => $desc1)
						{
							if(isset($desc1) && $desc1!='' && isset($_POST['type1']['desc2'][$key]) && $_POST['type1']['desc2'][$key]!='')
							{
								
								$data = array();$data['geno_extra_patientID'] = $clientID;$data['geno_extra_type'] = 'type1';$data['geno_extra_desc1'] = $desc1;$data['geno_extra_desc2'] = $_POST['type1']['desc2'][$key];
								KD::getModel('client/genoextra')->update($data,'geno_extra_id',$key);
							}
							else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Data So Unable to Add One row'));
							}
						}
					}
				}
				if(isset($_POST['type2']) && count($_POST['type2'])>0)
				{	
					if(isset($_POST['type2']['desc3']) && count($_POST['type2']['desc3'])>0)
					{
						$desc3s = $_POST['type2']['desc3'];
						foreach($desc3s as $key => $desc3)
						{
							if(isset($desc3) && $desc3!='' && isset($_POST['type2']['desc4'][$key]) && $_POST['type2']['desc4'][$key]!='' && isset($_POST['type2']['desc5'][$key]) && $_POST['type2']['desc5'][$key]!='')
							{
								
								$data = array();$data['geno_extra_patientID'] = $clientID;$data['geno_extra_type'] = 'type2';$data['geno_extra_desc3'] = $desc3;$data['geno_extra_desc4'] = $_POST['type2']['desc4'][$key];$data['geno_extra_desc5'] = $_POST['type2']['desc5'][$key];
								KD::getModel('client/genoextra')->update($data,'geno_extra_id',$key);
							}
							else
							{
								$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Data So Unable to Add One row'));
							}

						}
					}
				}
				
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Data Saved Successfully'));
				$this->_redirect('/client/mapping/index/id/'.$clientID);
			}
			else
			{
				$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
				$this->_redirect('/client/index/');
			}
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
}

