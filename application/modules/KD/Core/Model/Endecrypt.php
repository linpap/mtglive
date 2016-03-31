<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class KD_Core_Model_Endecrypt extends Zend_Filter_Encrypt {
    
    const ENC_KEY = "meebouYuf9neezaf5eme";
    const VECTOR  = "myvector";

    public static function getEnc($input)
    {
        $filter = new Zend_Filter_Encrypt(array('adapter' => 'mcrypt', 'key' => self::ENC_KEY));
        $filter->setVector(self::VECTOR);
        $encrypted = $filter->filter($input);
        return bin2hex($encrypted); //hints: rawurlencode(..) works
        return $encrypted;
    }

    public static function getDec($input)
    {

        $filter = new Zend_Filter_Decrypt(array('adapter' => 'mcrypt', 'key' => self::ENC_KEY));
        $filter->setVector(self::VECTOR);	
        $decoded = pack('H*', $input);
        $decrypted = trim(@$filter->filter($decoded));
        return $decrypted;
    }

}
?>
