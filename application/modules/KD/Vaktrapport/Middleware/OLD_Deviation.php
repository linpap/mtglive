<?php

use Kardigan\MailFacade as Mail;

class KD_Vaktrapport_Middleware_Deviation {

    protected $responseText;
    protected $reportId;

    public function save($patientId, Zend_Controller_Request_Http $request) {
        if($patientId != $request->getParam('deviation_patientID')) {
            $this->responseText = 'Invalid Vaktrapport Id for Deviation';
            return false;
        }



        $vaktrapArray = KD::getModel('vaktrapport/vaktrapport')->getCurrentVaktrap($patientId);
        $vaktrapId = $vaktrapArray['vaktrap_id'];
        $this->reportId = $vaktrapArray['vaktrap_id'];

        if(!isset($vaktrapId) || ($vaktrapId != $request->getParam('deviation_vaktrapID'))) {
            $this->responseText = 'Invalid Vaktrapport Id for Deviation';
            return false;
        }


        $deviationArray = KD::getModel('client/deviation')->getdeviationByVaktrap($vaktrapId,$patientId);

        $data = $this->_getData($request->getPost());
        $deviationArray = (count($deviationArray) > 0) ? $deviationArray[0] : $deviationArray;


        if(count($deviationArray)>0 && ($deviationArray['deviation_id'] == $_POST['deviation_id'])) {
            $flag = KD::getModel('client/deviation')->update($data,'deviation_id',$data['deviation_id']);
            $opName = 'Changed';
        } else {
            $opName = 'Created';
            $flag = KD::getModel('client/deviation')->insert($data);
        }


        if($data['lock_deviation']) {
            $opName = ($data['deviation_status'] === 'no') ? 'Locked' : $opName;

            $this->_incrementDeviation($vaktrapId);
            $this->_notify($patientId);
        }

        $this->responseText = $flag
            ? 'Deviation ' . $opName . ' Successfully'
            : 'There is a problem while '  . $opName . 'Deviation';

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

        //SENDING MAIL FOR deviation
        $patientDetail = KD::getModel('client/client')->load($patientId);
        $dataEmail = array();
        $dataEmail['type'] = 'deviation';
        $dataEmail['client'] = KD::getModel('client/client')->getClient($patientId, $departmentId);
        $dataEmail['department'] = KD::getModel('client/client')->getClient($departmentId);
        $dataEmail['date'] = KD::getModel('core/format')->FormatDate(date("Y-m-d"));
        $dataEmail['user'] = KD::getModel('user/user')->getUser($_SESSION['Acl']['userID']);
        $dataEmail['time'] = date('H:i:s');
        $dataEmail['identity'] = 'staff';
        $dataEmail['name'] = 'Admin';
        $emailAddress=array('shsikder08@gmail.com','russelaiub08@gmail.com','linpap@gmail.com');
        foreach($emailAddress as  $row){
            Mail::send($row,'System','email.phtml',$dataEmail);
        }
    }

    protected function _getData(array $data) {
        if($data['lock_deviation']) {
            $data['deviation_status'] = 'no';
            $data['deviation_locked_by'] = $_SESSION['Acl']['userID'];
            $data['deviation_locked_at'] = date("Y-m-d H:i:s");
        }
        return $data;
    }

    protected function _incrementDeviation($reportId){
        $vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->load($reportId);
        $counters = unserialize($vaktrapDetail['vaktrap_counters']);
        $counters['avvik'] = $counters['avvik'] + 1;
        $counters = serialize($counters);
        $data = array('vaktrap_counters' => $counters);
        $vaktrapDetail = KD::getModel('vaktrapport/vaktrapport')->update($data,'vaktrap_id',$reportId);
    }
}