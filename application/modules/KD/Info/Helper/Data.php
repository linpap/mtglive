<?php

class KD_User_Helper_Data extends Zend_View_Helper_Abstract 
{	
	// Data is must its a constructor other wise $this->getHelper('Data'), $this->Data()->getTitle($str) will not work for view
	public function Data()
	{
		return $this;
	}
	public function getPasswordSalt($length = 3)
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$password = substr( str_shuffle( $chars ), 0, $length );
		return $password;
	} 
	
	public function getPassword()
	{
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-=+;,?";
		$length = rand(8,18);
		$password = substr( str_shuffle( $chars ), 0, $length );
		return $password;
	}    
}

