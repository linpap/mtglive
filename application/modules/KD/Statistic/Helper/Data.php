<?php

class KD_Statistic_Helper_Data extends Zend_View_Helper_Abstract 
{	
	// Data is must its a constructor other wise $this->getHelper('Data'), $this->Data()->getTitle($str) will not work for view
	public function Data()
	{
		return $this;
	}
	public function getTitle($str)
	{
		return $str;
	}    
}

