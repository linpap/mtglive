<?php

class Vaktrapport_InitialController extends KD_Controller_Action {

    public function init() {
        //echo 'asdg';exit();
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
		$this->_helper->layout->setLayout('layout_ajax');
	    $key = $this->getRequest()->getParam('key');
		if(isset($key) && $key>0)
		{
			$clientInfoArray =  KD::getModel('client/client')->load($key);
			$this->view->clientInfoCollection = $clientInfoArray;
		}
		else
		{
			echo '';
			exit();
		}
    }
}

