<?php

class User_Bootstrap extends Zend_Application_Module_Bootstrap 
{

	protected function _initAutoloader ()
	{
		$autoloader = new KD_Application_Module_Autoloader(array(
			'namespace' => 'KD_User_',
			'basePath' => APPLICATION_PATH.'/modules/KD/User'
		));
		return $autoloader;
	}
    
}

