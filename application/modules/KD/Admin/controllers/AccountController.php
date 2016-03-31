<?php

class User_AccountController extends KD_Controller_Action {

    public function init() {
        /* Initialize action controller here */
        parent::init();
    }

    public function loginAction() {
        //echo 'tsedZTG';exit();
        // action body
        $loginForm = new KD_User_Form_Login();
        //print_r($loginForm);exit();
        //print_r($this->view->setActionUrl('test'));//exit();
        //$this->setActionUrl($this->makeUrl('user/account/postLogin'));
        if ($loginForm->isValid($_POST)) {
 
            $adapter = new Zend_Auth_Adapter_DbTable(
                $db,
                'users',
                'username',
                'password',
                'MD5(CONCAT(?, password_salt))'
                );
 
            $adapter->setIdentity($loginForm->getValue('username'));
            $adapter->setCredential($loginForm->getValue('password'));
 
            $auth   = Zend_Auth::getInstance();
            $result = $auth->authenticate($adapter);
 
            if ($result->isValid()) {
                $this->_helper->FlashMessenger('Successful Login');
                $this->_redirect('/');
                return;
            }
 
        }
 
        $this->view->loginForm1 = $loginForm;
    }

}

