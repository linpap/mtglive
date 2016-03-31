<?php

class Default_Model_Index extends Zend_Application_Module_Bootstrap 
{
	public function getSting($str = ' Abhiijt ')
	{
		return $str;
	}
	/*protected function _initAutoloader ()
	{
		$autoloader = new Zend_Application_Module_Autoloader(array(
			'namespace' => '',
			'basePath' => APPLICATION_PATH.'/modules/user'
		));
		return $autoloader;
	}*/
    
}

