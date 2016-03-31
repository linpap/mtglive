<?php

class Vaktrapport_IndexController extends KD_Controller_Action {

    public function init() {
        //echo 'asdg';exit();
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
		$this->view->title = $this->view->translate('Security Report For');
		$this->view->className = 'PTCLEFTVAKTRAPORT';
		
		
		
		$pageV = $this->getRequest()->getParam('pageV');
		if($pageV<=0){$pageV = 1;}
		
		$vaktrapportArray = KD::getModel('vaktrapport/vaktrapport')->loadAllVaktrapPageData($pageV,'active');;
		$this->view->vaktrapportCollection = $vaktrapportArray;	
		
    }
}