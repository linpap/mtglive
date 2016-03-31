<?php

class Department_Bootstrap extends Zend_Application_Module_Bootstrap 
{

	protected function _initAutoloader ()
	{
		$autoloader = new KD_Application_Module_Autoloader(array(
			'namespace' => 'KD_Department_',
			'basePath' => APPLICATION_PATH.'/modules/KD/Department'
		));
		return $autoloader;
	}
    
}

