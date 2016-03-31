<?php

use Kardigan\MailFacade as Mail;

class KD_Vaktrapport_Middleware_Log {

    protected $responseText;
    protected $reportId;

    public function save($patientId, Zend_Controller_Request_Http $request) {
        if($patientId != $request->getParam('logg_patientID')) {
            $this->responseText = 'Invalid Vaktrapport Id for Logg';
            return false;
        }

        $vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->getCurrentVaktrap($patientId);
        $vaktrapId = $vaktrapArray['vaktrap_id'];
        $this->reportId = $vaktrapArray['vaktrap_id'];

        if(!isset($vaktrapId) || ($vaktrapId != $request->getParam('logg_vaktrapID'))) {
            $this->responseText = 'Invalid Vaktrapport Id for Logg';
            return false;
        }

        $loggArray = KD::getModel('client/logg')->getLoggByVaktrap($vaktrapId,$patientId);

        $data = $this->_getData($request->getPost());
        $loggArray = (count($loggArray) > 0) ? $loggArray[0] : $loggArray;

        if(count($loggArray)>0 && ($loggArray['logg_id'] == $_POST['logg_id'])) {
            $flag = KD::getModel('client/logg')->update($data,'logg_id',$data['logg_id']);
            $opName = 'Changed';
        } else {
            $opName = 'Created';
            $flag = KD::getModel('client/logg')->insert($data);
        }

        if($data['lock_logg']) {
            $opName = ($data['logg_status'] === 'no') ? 'Locked' : $opName;

            $this->_incrementLog($vaktrapId);
            $this->_notify($patientId);
        }

        $this->responseText = $flag
            ? 'Logg ' . $opName . ' Successfully'
            : 'There is a problem while '  . $opName . ' Logg';

        return (bool)$flag;
    }

    public function getResponseText(){
        return $this->responseText;
    }

    public function getReportId(){
        return $this->reportId;
    }

    protected function _notify($patientId) {
        $departmentId = KD::getModel('client/client')->getClient($patientId, 'patient_deptID');

        //SENDING MAIL FOR LOGG
        $patientDetail = KD::getModel('client/client')->load($patientId);
        $dataEmail = array();
        $dataEmail['type'] = 'Logg';
        $dataEmail['client'] = KD::getModel('client/client')->getClient($patientId, $departmentId);
        $dataEmail['department'] = KD::getModel('client/client')->getClient($departmentId);
        $dataEmail['date'] = KD::getModel('core/format')->FormatDate(date("Y-m-d"));
        $dataEmail['user'] = KD::getModel('user/user')->getUser($_SESSION['Acl']['userID']);
        $dataEmail['time'] = date('H:i:s');
        $dataEmail['identity'] = 'staff';
        $dataEmail['name'] = 'Admin';
        Mail::send(KD::getModel('system/system')->getEmail(),'System','email.phtml',$dataEmail);
    }

    protected function _getData(array $data) {
        if($data['lock_logg']) {
            $data['logg_status'] = 'no';
            $data['logg_locked_by'] = $_SESSION['Acl']['userID'];
            $data['logg_locked_at'] = date("Y-m-d H:i:s");
        }
        return $data;
    }

    protected function _incrementLog($reportId){
        $vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->load($reportId);
        $counters = unserialize($vaktrapDetail['vaktrap_counters']);
        $counters['logg'] = $counters['logg'] + 1;
        $counters = serialize($counters);
        $data = array('vaktrap_counters' => $counters);
        $vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$reportId);
    }
}