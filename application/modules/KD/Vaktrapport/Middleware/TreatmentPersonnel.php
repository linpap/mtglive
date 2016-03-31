<?php 
class KD_Vaktrapport_Middleware_TreatmentPersonnel {

    protected $crypt;
    protected $format;

    public function __construct() {
        $this->crypt = KD::getModel('Core/Endecrypt');
        $this->format = KD::getModel('Core/Format');
    }

    /**
     * Get all treatment personnel registered on a report. This function only return unique treatment
     * personnel, including the treatment personnel registered on the vaktrap
     *
     * @param int $reportId
     * @param int $patientId
     * @param array $report
     * @return array
     */
    public function getTreatmentPersonnel($reportId, $patientId, $report) {
        $logs = KD::getModel('client/logg')->getLoggByVaktrap($reportId, $patientId, false);

        $resultSet = [
            $report['vaktrap_userID'] => $this->_getPersonnelDetails($report['vaktrap_userID'], $report, false)
        ];

        foreach($logs as $personnel) {
            $id = $personnel['logg_userID'];
            $resultSet[$id] = $this->_getPersonnelDetails($id, $personnel);
        }
        sort($resultSet);
        return $resultSet;
    }
    public function getTreatmentPersonnele($reportId, $patientId, $report) {
        $logs = KD::getModel('client/logg')->getLoggByVaktrape($reportId, $patientId, false);

        $resultSet = [
            $report['vaktrap_userID'] => $this->_getPersonnelDetails($report['vaktrap_userID'], $report, false)
        ];

        foreach($logs as $personnel) {
            $id = $personnel['logg_userID'];
            $resultSet[$id] = $this->_getPersonnelDetails($id, $personnel);
        }
        sort($resultSet);
        return $resultSet;
    }

    /**
     * Helper function to build an array and decrypt user names
     *
     * @param int $id
     * @param array $personnel
     * @param bool $decrypt
     * @return array
     */
    protected function _getPersonnelDetails($id, $personnel, $decrypt = true) {
        return [
            'id' => $id,
            'name' => $this->format->formatName(
                ($decrypt ? $this->crypt->getDec($personnel['user_fname']) : $personnel['user_fname']),
                ($decrypt ? $this->crypt->getDec($personnel['user_mname']) : $personnel['user_mname']),
                ($decrypt ? $this->crypt->getDec($personnel['user_lname']) : $personnel['user_lname'])
            )
        ];
    }
}