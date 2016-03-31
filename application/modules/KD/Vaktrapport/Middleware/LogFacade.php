<?php
use Kardigan\Facade;

class KD_Vaktrapport_Middleware_LogFacade extends Facade {
    static public function getFacadeAccessor() {
        return 'KD_Vaktrapport_Middleware_Log';
    }
}