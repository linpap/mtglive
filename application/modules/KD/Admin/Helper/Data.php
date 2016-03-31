<?php

class KD_System_Helper_Data extends Zend_View_Helper_Abstract 
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
	public function getPasswordSalt()
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$randstring = '';
		for ($i = 0; $i < 10; $i++) {
			$randstring1 = $characters[rand(0, strlen($characters)-1)];
			$randstring2 = $characters[rand(0, strlen($characters)-1)];
			$randstring3 = $characters[rand(0, strlen($characters)-1)];
		}
		
			$password_salt = $randstring1.$randstring2.$randstring3;	
		return $password_salt;
	}	   	   
}

