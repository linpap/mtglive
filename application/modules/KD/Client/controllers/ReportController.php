<?php
class Client_ReportController extends KD_Controller_Action {

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
			$this->view->title = $this->view->translate('Report For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
			$pageV = $this->getRequest()->getParam('pageV');
			if($pageV<=0){$pageV = 1;}
			$pageSize = $this->getRequest()->getParam('pgs');
			$periodVaktrap = $this->getRequest()->getParam('period');
			$VaktrapSizesession = new Zend_Session_Namespace('Acl');

			if(isset($pageSize) && $pageSize > 0)
			{	
				$VaktrapSizesession->vaktrapSize = $pageSize;
			}
			if(isset($periodVaktrap) && $periodVaktrap != '')
			{	
				$VaktrapSizesession->vaktrapPeriod = $periodVaktrap;
			}

			$pageSize = $VaktrapSizesession->vaktrapSize;
			$periodVaktrap = $VaktrapSizesession->vaktrapPeriod;
			
			if(!(isset($pageSize) && $pageSize>0)){ $pageSize=10;}
			if(!(isset($periodVaktrap) && $periodVaktrap!='')){ $periodVaktrap='1M';}
			
			$vaktrapportArray = KD::getModel('vaktrapport/vaktrapport')->loadPageData($clientID, $pageV, 'all', $pageSize, $periodVaktrap);

            $this->view->vaktrapportCollection = $vaktrapportArray;
			$vaktrapportMonthArray = KD::getModel('vaktrapport/vaktrapport')->loadPageData($clientID, $pageV, 'all', $pageSize, $periodVaktrap, 'maaned'); 
			 
			$this->view->vaktrapportMonthCollection = $vaktrapportMonthArray;	
			
			$vaktrapportQuartalArray = KD::getModel('vaktrapport/vaktrapport')->loadPageData($clientID, $pageV, 'all', $pageSize, $periodVaktrap, 'kvartal'); 
			$this->view->vaktrapportQuartalCollection = $vaktrapportQuartalArray;	
			
			$quartalMaalPlanArray = KD::getModel('client/maalplan')->loadPageData($clientID, $pageV, 'archive', $pageSize, $periodVaktrap); 
			$this->view->quartalMaalPlanCollection = $quartalMaalPlanArray;	
			
			$this->view->clientID = $clientID;	
			$this->view->pageSize = $pageSize;
            $this->view->page = $pageV;
			$this->view->periodVaktrap = $periodVaktrap;
			


		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
}

