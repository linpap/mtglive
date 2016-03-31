<?php

class KD_Client_Helper_Mto extends Zend_View_Helper_Abstract 
{
	// function image is must its a constructor other wise $this->getHelper('Data'), $this->Data()->getTitle($str) will not work
	public function Mto()
	{
		return $this;
	}
	public function getObserRel($type = 'I', $id = '0')
	{
		return $type.' '.$id;
	} 
}

