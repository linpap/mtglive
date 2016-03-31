<?php
class Client_WeekplanController extends KD_Controller_Action {

    public function init() {
        parent::init();
    }
    public function indexAction() {
		$clientID = $this->getRequest()->getParam('id');//exit();
        $this->view->weekNumber = $this->getRequest()->has('week') ? $this->getRequest()->getParam('week') : date('W');
        $this->view->year = $this->getRequest()->has('year') ? $this->getRequest()->getParam('year') : date('Y');
        $date = new \DateTime('now', new \DateTimeZone('Europe/Oslo'));
        $date->setDate($this->view->year, 12, 27);
        $this->view->weeksInYear = $date->format('W');

		if(isset($clientID) && $clientID>0 )
		{
			$pageW = $this->getRequest()->getParam('pageW');
			if($pageW<=0){$pageW = 1;}      
		
			$this->view->id = $clientID;
			$this->view->className = 'PTCLEFTVAKTRAPORT';
			$this->view->title = $this->view->translate('Weekplan Plan For').' '.KD::getModel('client/client')->getClient($clientID,'name').'';
			$this->view->weekplanCollection = KD::getModel('client/weekplan')->loadByWeek(
                $clientID,
                $this->view->weekNumber,
                $this->view->year
            );

			$this->view->clientID = $clientID;
		}
		if($clientID<=0)
		{
			$this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
			$this->_redirect('/client/index/');
		}
    }
		
	public function storeWeekplanAction() {
        if(!$this->getRequest()->has('id')) {
            return $this->_httpErrorRedirect('Invalid Patient Id for Week Plan', '/client/index');
        }

        $data = $this->_saveHelper();
        $data['weekplan_patientID'] = $this->getRequest()->getParam('id');

        KD::getModel('client/weekplan')->insert($data);

        return $this->_httpSuccessRedirect(
            '%s was Successfully Updated' . $this->view->translate('Weekplan'),
            '/client/weekplan/index/t/8/id/'.$data['weekplan_patientID']
        );
	}

    public function createWeekplanAction() {
        $this->_helper->layout->setLayout('layout_ajax');
        $this->view->clientID = $this->getRequest()->getParam('user-id');
    }

	public function editweekplanAction()
	{
		$this->_helper->layout->setLayout('layout_ajax');
		$weekplanId = $this->getRequest()->getParam('id');//exit();
		if(!empty($weekplanId)) {
			$weekplan = KD::getModel('client/weekplan')->load($weekplanId);
			if($weekplan)
			{
				$this->view->weekplanCollection = $weekplan;
				$this->view->weekplanID = $weekplanId;
			}
            return true;
		}

        $this->view->weekplanID = -1;
        $this->view->weekplanCollection = array(
            'weekplan_date' => date('Y-m-d H:is'),
            'weekplan_title' => '',
            'weekplan_desc' => ''
        );
	}
	public function editweekplanpostAction()
	{
		$weekplanId = $this->getRequest()->getParam('id');//exit();
		$weekplanPatientId = $this->getRequest()->getParam('pid');//exit();
        $weekplan = KD::getModel('client/weekplan')->load(intval($weekplanId));
        if(empty($weekplan)) {
            return $this->_httpErrorRedirect('Invalid Request', '/client/weekplan/index/t/8/id/'.$weekplanPatientId);
        }

        if($weekplanPatientId != $weekplan['weekplan_patientID']) {
            return $this->_httpErrorRedirect(
                'Error While Updating %s ',$this->view->translate('Weekplan'),
                '/client/weekplan/index/t/8/id/'.$weekplanPatientId
            );
        }
        $data = $this->_saveHelper();
        KD::getModel('client/weekplan')->update($data, 'weekplan_id', $weekplanId);

        return $this->_httpSuccessRedirect(
            $this->view->translate('Weekplan') . ' was Successfully Updated',
            '/client/weekplan/index/t/8/id/'.$weekplanPatientId
        );
	}

    public function deleteAction() {
        #dd($this->getRequest()->getParam('id'), $this->getRequest()->getParams());
        $r = KD::getModel('client/weekplan')->softDelete($this->getRequest()->getParam('id'));
        $this->_httpSuccessRedirect(
            'Weekplan item successfully deleted',
            '/client/weekplan/index/t/8/id/'. $this->getRequest()->getParam('clientId')
        );
    }

    protected function _saveHelper() {
        $format = KD::getModel('core/format');

        $date = $this->getRequest()->getParam('date-alt-field');
        $time = $this->getRequest()->getParam('weekplan_time');

        $timestamp = empty($date) ? date('Y-m-d') : $date;
        $timestamp .= ' ' . (empty($time) ? '12:00:00' : $time);

        return array(
            'weekplan_title' => $this->getRequest()->getParam('weekplan_title'),
            'weekplan_desc' => $this->getRequest()->getParam('weekplan_desc'),
            'weekplan_date' => $format->formatTimestamp($timestamp),
        );
    }
}

