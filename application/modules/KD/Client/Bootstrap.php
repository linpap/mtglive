<?php

class Client_Bootstrap extends Zend_Application_Module_Bootstrap 
{

	protected function _initAutoloader ()
	{
		$autoloader = new KD_Application_Module_Autoloader(array(
			'namespace' => 'KD_Client_',
			'basePath' => APPLICATION_PATH.'/modules/KD/Client'
		));
		return $autoloader;
	}
    
}

