<?php
 
class Info_IndexController extends KD_Controller_Action {

    public function init() {
        //echo 'asdg';exit();
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
	    $pageI = $this->getRequest()->getParam('pageI');
		$pageIA = $this->getRequest()->getParam('pageIA');
		if($pageI<=0){$pageI = 1;}
		if($pageIA<=0){$pageIA = 1;}
		$this->view->title = $this->view->translate('%s List',$this->view->translate('Info'));
		$infoAllArray = KD::getModel('info/info')->loadPageData($pageI,'main');
		$this->view->infoAllCollection = $infoAllArray;
		
		$infoDeptArray = KD::getModel('info/info')->loadPageData($pageIA,'cur');
		$this->view->infoDeptCollection = $infoDeptArray; 		 
    }
	public function archiveAction()
	{
		$ids = $this->_archive($_POST,'no');
		if($ids)
		{
			$result = KD::getModel('info/info')->archiveList($ids,'no');
			if($result)
			{
				$this->flashMessenger->setNamespace('success')->addMessage($this->view->translate('Archived Successfully'));
				$this->_redirect('/info/index/index/t/1');
			}
		}
		$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Archived Error'));
		$this->_redirect('/info/index/index/t/1');
	}
}

