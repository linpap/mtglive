<?php

class User_View_Helper_Data extends Zend_View_Helper_Abstract 
{
	public function data()
	{
		return $this;
	}
	public function getTitle($str)
	{
		return $str;
	}    
}

