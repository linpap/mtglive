<?php

class Client_StatistikkController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function indexAction() {
        $clientIDGet = $this->getRequest()->getParam('id');
        $this->view->totalItems = array('maal' => 0, 'gov_tiltak' => 0, 'inst_tiltak' => 0);
        //print_r($_POST);exit();
        if (isset($_POST['clientId']) && $_POST['clientId'] > 0 && $_POST['clientId'] == $clientIDGet) {
            $clientID = $_POST['clientId'];
            if (isset($_POST['period']) && $_POST['period'] != '') {
                $period = $_POST['period'];
            } else {
                $period = 'lifetime';
            }
        } elseif (isset($clientIDGet) && $clientIDGet > 0) {
            $clientID = $clientIDGet; //exit();
            $period = 'lifetime';
        } else {
            $clientID = 0;
        }

        if (isset($clientID) && $clientID > 0) {
            $this->view->id = $clientID;
            $this->view->className = 'PTCLEFTSTATISTIKK';
            $clientInfo = KD::getModel('client/client')->load($clientID);
            $format = KD::getModel('core/format');
            $clientName = $format->FormatName($clientInfo['patient_fname'], $clientInfo['patient_mname'], $clientInfo['patient_lname']);
            $this->view->title = $this->view->translate('Statistikk For') . ' ' . $clientName . '';
            if (isset($_POST['year']) && $_POST['year'] > 0) {
                $year = $_POST['year'];
            } else {
                $year = date('Y');
            }

            if (isset($period, $year) && $period != '' && in_array($period, array('curyear', 'quartal1', 'quartal2', 'quartal3', 'quartal4', 'lifetime')) && isset($year) && $year > 0) {

                //$showVaktrap = false; used for show emtpy report for first time when we create
                $group = 'date';
                $monthReports = array();
                if (isset($_POST['zoomFilter'], $_POST['zoomValue']) && $_POST['zoomFilter'] != '' && $_POST['zoomValue'] > 0) {
                    $group = $_POST['zoomFilter'];
                    $period = $_POST['zoomFilter'];
                    $zoom = $_POST['zoomValue'];
                    if ($group == 'week') {
                        $group = 'date';
                        $startDateObj = new DateTime();
                        $endDateObj = clone $startDateObj;
                        $startDateObj->setISODate($year, ($zoom + 1), 0);
                        $endDateObj->setISODate($year, ($zoom + 1), 6);
                        $startDate = $startDateObj->format('Y-m-d');
                        $endDate = $endDateObj->format('Y-m-d');
                    } elseif ($group == 'month') {
                        $group = 'week';
                        $startDate = date('Y-m-d', mktime(0, 0, 0, $zoom, 1, $year));
                        $startDateObj = new DateTime($startDate);
                        $endDateObj = clone $startDateObj;
                        $startDate = $startDateObj->format('Y-m-d');
                        $endDateObj->add(new DateInterval('P1M'));
                        $endDateObj->sub(new DateInterval('P1D'));
                        $endDate = $endDateObj->format('Y-m-d');
                    }
                } else {
                    switch ($period) {
                        case 'curyear':
                            $startDate = ($year - 1) . '-12-01';
                            $endDate = $year . '-11-30';
                            $group = 'month';
                            $monthReports = KD::getModel('vaktrapport/vaktrapport')->getVaktrapMonths($clientID, $year);
                            break;
                        case 'quartal1':
                        case 'quartal2':
                        case 'quartal3':
                        case 'quartal4':
                            if ($period == 'quartal1') {
                                $startDate = ($year - 1) . '-12-01';
                            } else {
                                $quartal = substr($period, 7);
                                $quartal = ($quartal - 1) * 3;
                                if ($quartal < 10)
                                    $quartal = '0' . $quartal;
                                $startDate = $year . '-' . $quartal . '-01';
                            }
                            $endDateObj = new DateTime($startDate);
                            $endDateObj->add(new DateInterval('P3M'));
                            $endDateObj->sub(new DateInterval('P1D'));
                            $endDate = $endDateObj->format('Y-m-d');
                            $group = 'week';
                            break;

                        case 'lifetime':
                            $startDate = $clientInfo['patient_date_of_joining'];
                            $endDate = date('Y-m-d');
                            $group = 'month';
                            break;
                    }
                }

                if (isset($startDate, $endDate)) {
                    $this->view->startDate = $startDate;
                    $this->view->endDate = $endDate;
                    $this->view->year = $year;
                    $this->view->period = $period;
                    $this->view->clientInfo = $clientInfo;
                    $this->view->monthReports = $monthReports;

                    $arrayResult = array('NOTCOMPLETE' => '0', 'PARTIALCOMPLETE' => '1', 'COMPLETE' => '2', 'NOTEVALUATED' => '3');
                    $arrayColor = array(0 => '#E40303', 1 => '#FDC600', 2 => '#7DC10F', 3 => '#4C70F5');
                    //$tiltakInstResult = array('10','0','1','2');//KD::getModel('client/tiltakinst')->loadListByVaktrap($clientID,$vaktrapIds,false,true);
                    $this->view->maalCountCollection = array();
                    foreach ($arrayResult as $key => $maalRes) {
                        $dataRes = KD::getModel('client/maalplanmaal')->loadListByResult($clientID, $maalRes, $startDate, $endDate);

                        $this->view->totalItems['maal'] += count($dataRes);

                        if (isset($maalRes) && $maalRes >= 0 && $maalRes <= 2) {
                            $this->view->maalCountCollection[$key] = array('count' => count($dataRes), 'result' => $maalRes, 'color' => $arrayColor[$maalRes], 'data' => $dataRes);
                        } else {
                            $this->view->maalCountCollection[$key] = array('count' => count($dataRes), 'result' => '3', 'color' => $arrayColor[3], 'data' => $dataRes);
                        }
                    }
                    // Collecting All vaktrapport Ids
                    $allEnkelVaktrapresult = KD::getModel('vaktrapport/vaktrapport')->getVaktrapFromDate($clientID, $startDate, $endDate);
                    $vaktrapIds = array();
                    foreach ($allEnkelVaktrapresult as $vaktrap) {
                        $vaktrapIds[] = $vaktrap['vaktrap_id'];
                    }
                    if (is_array($vaktrapIds) && count($vaktrapIds) > 0) {

                        //$tiltakGovResult = KD::getModel('vaktrapport/vaktraptilgov')->loadListByVaktrap($clientID,$vaktrapIds,false,true)
                        $this->view->govTiltakCountCollection = array();
                        foreach ($arrayResult as $key => $tiltakGovRes) {
                            $dataRes = KD::getModel('vaktrapport/vaktraptilgov')->loadListByResult($clientID, $tiltakGovRes, $vaktrapIds, false, true);

                            $this->view->totalItems['gov_tiltak'] += count($dataRes);

                            if (isset($tiltakGovRes) && $tiltakGovRes >= 0 && $tiltakGovRes <= 2) {
                                $this->view->govTiltakCountCollection[$key] = array('count' => count($dataRes), 'result' => $tiltakGovRes, 'color' => $arrayColor[$tiltakGovRes], 'data' => $dataRes);
                            } else {
                                $this->view->govTiltakCountCollection[$key] = array('count' => count($dataRes), 'result' => '3', 'color' => $arrayColor[3], 'data' => $dataRes);
                            }
                        }

                        //$tiltakInstResult = array('10','0','1','2');//KD::getModel('client/tiltakinst')->loadListByVaktrap($clientID,$vaktrapIds,false,true);
                        $this->view->instTiltakCountCollection = array();
                        foreach ($arrayResult as $key => $tiltakInstRes) {
                            $dataRes = KD::getModel('client/tiltakinst')->loadListByResult($clientID, $tiltakInstRes, $vaktrapIds, false, true);

                            $this->view->totalItems['inst_tiltak'] += count($dataRes);

                            if (isset($tiltakInstRes) && $tiltakInstRes >= 0 && $tiltakInstRes <= 2) {
                                $this->view->instTiltakCountCollection[$key] = array('count' => count($dataRes), 'result' => $tiltakInstRes, 'color' => $arrayColor[$tiltakInstRes], 'data' => $dataRes);
                            } else {
                                $this->view->instTiltakCountCollection[$key] = array('count' => count($dataRes), 'result' => '3', 'color' => $arrayColor[3], 'data' => $dataRes);
                            }
                        }
                    }
                    //Collection ALl Observation With result
                    $this->view->observationCollection = array();
                    $observations = KD::getModel('vaktrapport/vaktrapobser')->loadListByVaktrap($clientID, $startDate, $endDate, 'none');

                    foreach ($observations as $key => $observation) {
                        $dataRes = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationForReport($clientID, $startDate, $endDate, $observation['observation_id'], $group);
                        $checkArray = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationForReport($clientID, $startDate, $endDate, $observation['observation_id'], $group, true);

                        $this->view->observationCollection[$observation['observation_id']] = array('count' => count($checkArray), 'checked' => true, 'yTitle' => 'Observasjon', 'title' => $observation['observation_desc'], 'xTitle' => $group, 'id' => $observation['observation_id'], 'linedata' => $dataRes);

                        if (!empty($checkArray)) {
                            $observations[$key]['checked'] = true;
                            $this->view->observationCollection[$observation['observation_id']]['checked'] = true;
                        } else {
                            $observations[$key]['checked'] = false;
                            $this->view->observationCollection[$observation['observation_id']]['checked'] = false;
                        }
                    }
                    $observationAlls = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationAllForReport($clientID, $startDate, $endDate, $group);
                    $tmpPeriod = '';
                    $observationAllData = array();
                    $i=0;
                    foreach ($observationAlls as $observationAll) {
                        if ($observationAll['period'] != $tmpPeriod) {
                            $tmpPeriod = $observationAll['period'];
                            $observationAllData[$tmpPeriod] = KD::getModel('vaktrapport/vaktrapobser')->loadListByObservationAllForReport($clientID, $startDate, $endDate, $group, $tmpPeriod);
                            if ($i == 0) {
                                $counting = count($observationAllData[$tmpPeriod]);
                                $extraData = array_slice($observationAllData[$tmpPeriod], -1, 1, true);
                            }
                            if ($counting < count($observationAllData[$tmpPeriod])) {
                                unset($observationAllData[$tmpPeriod][$counting]);
                            }
                            else if($counting > count($observationAllData[$tmpPeriod])){
                                $observationAllData[$tmpPeriod] = array_merge($observationAllData[$tmpPeriod], $extraData);
                            }
                        }
                        $i++;
                    }
                    $this->view->observationAllData = $observationAllData;
                    $this->view->observations = $observations;
                } else {
                    $this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period & Year for statistik not set'));
                    $this->_redirect('/client/info/index/id/' . $clientID);
                }
            } else {

                $this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Period & Year for statistik not set'));
                $this->_redirect('/client/info/index/id/' . $clientID);
            }
        }
        if ($clientID <= 0) {
            $this->flashMessenger->setNamespace('error')->addMessage($this->view->translate('Invalid Request'));
            $this->_redirect('/client/index/');
        }
    }

}
