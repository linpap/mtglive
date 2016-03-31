<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Core_Model_Format extends Zend_Db_Table_Abstract {


    public function formatTime($date = null) {
        $time_format = KD::getModel('system/system')->load('time_format','system_key');
        $date = new DateTime($date);
        return $date->format($time_format['system_value']);
    }

    public function formatTimestamp($dateTime) {
        $date = new \DateTime($dateTime, new \DateTimeZone('Europe/Oslo'));
        return $date->format('Y-m-d H:i:s');
    }

    public function FormatDate($dateval='') {
		if(isset($dateval) && $dateval!='' && (strpos($dateval,'0000')===false))
		{
			$result = '';
			$date_format = KD::getModel('system/system')->load('date_format','system_key');
            $date = new DateTime($dateval);
			switch($date_format['system_value'])
			{
				case 'dd/mm-yy':
				  $result = $date->format('d/m-Y');
				break;
				case 'dd-mm-yy':
				  $result = $date->format('d-m-Y');
				break;
				case 'mm-dd-yy':
				  $result = $date->format('m-d-Y');
				break;
				case 'yyyy-mm-dd':
				  $result = $date->format('Y-m-d');
				break;
			}
			return $result;
		}
		else
		{
			return '';
		}
    }
	
	public function getColorStyle($property) {
		
		if(isset($property) && $property!='' && (strpos($property,'0000')===false))
		{
			preg_match("/(\d{4})-(\d{2})-(\d{2})/",$property, $matches);
			//print_r($matches);exit();
			$date = new DateTime("$matches[3]-$matches[2]-$matches[1]");
			//print_r($date);exit();
            $now = new DateTime();
            $dateMin2Weeks = clone $date;
            $dateMin2Weeks->sub(new DateInterval('P14D'));
			$style = '';
            if ($now > $dateMin2Weeks && $now <= $date) $style = 'color: #ffea00';
            elseif ($date < $now) $style = 'color: #ff0000;';
			
			return $style;
		}
		else
		{
			return '';
		}
    }
	
	public function PrepareDateDB($date)
	{
		if(isset($date) && $date!='' && (strpos($date,'0000')===false))
		{
			$dateformat = $this->getFormatDate();
			$switch = strtolower(substr($dateformat,2,3));
			$returnDate = '';
			switch($switch)
			{
				case '-mm':
					preg_match("/(\d{2})-(\d{2})-(\d{4})/",$date, $matches);
					$returnDate = $matches[3].'-'.$matches[2].'-'.$matches[1];
				break;
				case '/mm':
					preg_match("/(\d{2})\/(\d{2})-(\d{4})/",$date, $matches);
					$returnDate = $matches[3].'-'.$matches[2].'-'.$matches[1];
				break;
				case '-dd':
					preg_match("/(\d{2})-(\d{2})-(\d{4})/",$date, $matches);
					$returnDate = $matches[3].'-'.$matches[1].'-'.$matches[2];
				break;
				default:
					$returnDate = $date;
				break;
			}
	
			$returnDate = new DateTime($returnDate);
			return $returnDate->format('Y-m-d');
		}
		else
		{
			return '';
		}
	}
	
	public function getDateFormatJS() {
		$format = $this->getFormatDate();
		$format = str_replace('yyyy','yy',$format);
		return $format;
		/*if($format=='yyyy-mm-dd')
			return 'yy-mm-dd';
		else
			return $this->getFormatDate();*/
	}
	
	public function getFormatDate($dateval='') {
		$result = '';
		$date_format = KD::getModel('system/system')->load('date_format','system_key');
        return $date_format['system_value'];
    }
	
	public function PageSize() {
        $page_size = KD::getModel('system/system')->load('page_size','system_key');
		return (isset($page_size['system_value']) && $page_size['system_value']>0)?$page_size['system_value']:10;
    }
	
	public function FormatName($firstName='',$middleName='',$lastName='') {
		$result = '';
		$name_format = KD::getModel('system/system')->load('name_format','system_key');
        switch($name_format['system_value'])
		{
			case 'F L':
			  $result = $firstName.' '.$lastName;
			break;
			case 'F M L':
			  $result = $firstName.' '.$middleName.' '.$lastName;
			break;
			case 'L F':
			  $result = $lastName.' '.$firstName;
			break;
		}
        return $result;
    }
}