<?php namespace Kardigan;

class Mail {

    public function send($to, $subject, $template, $data = array()){
        // create view object
        $html = new \Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/email/template/');
        $html->assign('data', $data);

        $mail = new \Zend_Mail('utf-8');
        $bodyText = $html->render($template);
        $mail->addTo($to);
        \KD::getModel('system/system')->getEmail();
        \KD::getModel('system/system')->getIdentity();

        $mail->setSubject($subject);
        $mail->setFrom(\KD::getModel('system/system')->getEmail(), \KD::getModel('system/system')->getIdentity());
        $mail->setBodyHtml($bodyText);


        try {
            $result = $mail->send();
        } catch(\Exception $e) {
            //TODO 2015-06-03 Kardigan: failures to send out mail should be logged in a system log
            if(APPLICATION_ENV === 'development') {
                dd($e);
            }
        }
    }

}