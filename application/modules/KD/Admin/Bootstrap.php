<?php

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap 
{

	protected function _initAutoloader ()
	{
		$autoloader = new KD_Application_Module_Autoloader(array(
			'namespace' => 'KD_Admin_',
			'basePath' => APPLICATION_PATH.'/modules/KD/Admin'
		));
		return $autoloader;
	}
    
}

