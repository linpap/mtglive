<?php

class Bootstrap extends KD_Application_Bootstrap_Bootstrap 
{
        /**
         * Automatically load classes that are part of the default module.
         */
        protected function _initModuleAutoloader()
        {
            new Zend_Application_Module_Autoloader(array(
                'namespace' => 'Default',
                'basePath' => APPLICATION_PATH
            ));
        }
		
		protected function _initTranslate()
		{
			$translate = new Zend_Translate(array(
				'adapter' => 'csv',
				'content' => APPLICATION_PATH.'/locale/en_US.csv',
				'locale'  => 'en'
			));
			$translate->addTranslation(array(
				'content' => APPLICATION_PATH.'/locale/no_NO.csv',
				'locale'  => 'no'
			));
			$registry = Zend_Registry::getInstance();
			$translate->setLocale('no');
			$registry->set('Zend_Translate', $translate);
			
		}
		
 
        /**
         * Initialize our routes.
         */
        /*protected function _initRoutes()
        {
            $this->bootstrap('frontcontroller');
            $front = $this->getResource('frontcontroller');

            $router = $front->getRouter();
            $router->addRoute('index-action', new Zend_Controller_Router_Route(
                ':action/*',
                array(
                    'controller' => 'index',
                    'action' => 'index'
                )
            ));

            return $router;
        }*/

        /**
         * Get our database adapter and add it to our registry for easy access
         * throughout the application.
         */
        /*protected function _initDbAdapter()
        {
            $this->bootstrap('db');
            $db = $this->getPluginResource('db');

            Zend_Registry::set('db', $db->getDbAdapter());
        }*/

        /**
         * Initialize our view and add it to the ViewRenderer action helper.
         */
        protected function _initView()
        {
            // Initialize view
            $view = new Zend_View();

            // Add it to the ViewRenderer
            $viewRenderer =
                Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->setView($view);

            // Return it, so that it can be stored by the bootstrap
            return $view;
        }

        /**
         * Here we will initialize any view helpers.    This will also setup basic
         * head information for the view/layout.
         */
        protected function _initViewHelpers()
        {
            $this->bootstrap(array('frontcontroller', 'view'));
            $frontController = $this->getResource('frontcontroller');
            $view = $this->getResource('view');
			
            // Add helper paths.
            //$view->addHelperPath(APPLICATION_PATH . '/views/helpers', 'Default_View_Helper');
			
            // Setup our AssetUrl View Helper
            if ((bool) $frontController->getParam('cdnEnabled'))
                $view->getHelper('AssetUrl')->setBaseUrl($frontController->getParam('cdnHost'));
			
            // Set our DOCTYPE
            //$view->docType('XHTML1_STRICT');

            // Set our TITLE
            $view->headTitle()->setSeparator(' - ')->append('Site');

            // Add any META elements
            $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8');
            $view->headMeta()->appendHttpEquiv('Content-Style-Type', 'text/css');
            $view->headMeta()->appendHttpEquiv('imagetoolbar', 'no');

            // Add our favicon
            $view->headLink()->headLink(array(
                'rel' => 'favicon',
                'type' => 'image/ico',
                'href' => $view->baseUrl('favicon.ico')
            ));
		
            // Add Stylesheet's
            //$view->headLink()
            //    ->appendStylesheet($view->assetUrl('css/reset.css'));
			
            // Add JavaScript's
            //$view->headScript()->appendFile('http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js');
        }
		
		protected function _initAutoload()
		{
		/**	$autoloader = Zend_Loader_Autoloader::getInstance();
			$autoloader->registerNamespace(array('cms_'));
			$autoloader->setFallbackAutoloader(true);
			$autoloader->suppressNotFoundWarnings(false);   
			return $autoloader;
         **/
        }
}

