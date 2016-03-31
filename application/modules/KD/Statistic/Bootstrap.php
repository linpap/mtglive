<?php

class Statistic_Bootstrap extends Zend_Application_Module_Bootstrap 
{

	protected function _initAutoloader ()
	{
		$autoloader = new KD_Application_Module_Autoloader(array(
			'namespace' => 'KD_Statistic_',
			'basePath' => APPLICATION_PATH.'/modules/KD/Statistic'
		));
		return $autoloader;
	}
    
}

