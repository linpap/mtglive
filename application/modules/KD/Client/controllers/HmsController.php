<?php
class Client_HmsController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }
    public function indexAction() {
		$clientID = $this->getRequest()->getParam('id');//exit();
		if(isset($clientID) && $clientID>0 )
		{
			$this->view->id = $clientID;
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
}

