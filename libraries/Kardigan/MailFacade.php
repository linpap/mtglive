<?php namespace Kardigan;
class MailFacade extends Facade {

    static public function getFacadeAccessor() {
        return 'Kardigan\Mail';
    }
}