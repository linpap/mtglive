<?php

class KD_User_Helper_Image extends Zend_View_Helper_Abstract 
{
	// function image is must its a constructor other wise $this->getHelper('Data'), $this->Data()->getTitle($str) will not work
	public function Image()
	{
		return $this;
	} 
	
	public function getTitle($str)
	{
		return $str;
	}    
}

