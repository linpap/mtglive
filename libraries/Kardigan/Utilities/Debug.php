<?php

/**
 * Simple dump die function
 */
if(!function_exists('dd')) {
    function dd() {
        $isCli = !is_array($_SERVER);
        $backTrace = debug_backtrace();
        $lineBreak = $isCli ? "\n" : '<br/>';
        echo '-----------------------------------------------------------------------------------------------------------------------------------------' . $lineBreak;
        echo 'DEBUG: dd() triggered' . $lineBreak;
        if(isset($backTrace[0]['file'])) {
            echo 'File: ' . $backTrace[0]['file'] . $lineBreak;
            echo 'Line: ' . $backTrace[0]['line'] . $lineBreak;
        }
        echo '-----------------------------------------------------------------------------------------------------------------------------------------' . $lineBreak;
        echo $isCli ? '' : '<pre>';
        call_user_func_array('var_dump', func_get_args());
        echo $isCli ? '' : '</pre>';
        die();
    }
}