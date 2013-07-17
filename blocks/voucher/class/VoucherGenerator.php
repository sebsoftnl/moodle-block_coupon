<?php

/*
 * File: VoucherGenerator.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

/**
 * Description of VoucherGenerator
 *
 * @author Menno
 */
final class VoucherGenerator
{
    const NUMERIC = 1;
    const LETTERS = 2;
    const CAPITALS = 4;
    //const SPECIAL = 8;
    
    const ALNUM = 3;
    const ALL = 7;
    
    static final function GenerateUniqueCode($size, $flags = self::ALL, $exclude=array('i', 'I', 'l', 'L', 1, 0 , 'o', 'O')) {
        global $DB;
        
        $vcode = self::GenerateCode($size, $flags = self::ALL, $exclude=array('i', 'I', 'l', 'L', 1, 0 , 'o', 'O'));
        while ($DB->get_record('vouchers', array('submission_code'=>$vcode))) {
            $vcode = VoucherCodeGenerator::GenerateCode($size, $flags = self::ALL, $exclude=array('i', 'I', 'l', 'L', 1, 0 , 'o', 'O'));
        }
        
        return $vcode;
    }
    
    /**
     * Generate voucher code
     * @param int $size code size
     * @param int $flags generator flags
     * @param array $exclude charachters to exclude
     */
    static final function GenerateCode($size, $flags = self::ALL, $exclude=array('i', 'I', 'l', 'L', 1, 0 , 'o', 'O'))
    {
        $chars = '';
        if (self::IsFlag($flags, self::NUMERIC))
        {
            $chars .= '0123456789';
        }
        if (self::IsFlag($flags, self::LETTERS))
        {
            $chars .= 'abcdefghijklmnopqrstuvwxyz';
        }
        if (self::IsFlag($flags, self::CAPITALS))
        {
            $chars .= 'ABCDEFGHIJKLMNOPQRTSUVWXYZ';
        }
        
        $chars = implode('',array_diff(str_split($chars), $exclude));
        
        $code = '';
        $max = strlen($chars);
        while(true && (strlen($code) < $size))
        {
            $n = rand(0, $max-1);
            if (strlen($code == 0))
            {
                // do not use number as first char
                if (!is_numeric( $chars{$n} ))
                {
                    $code .= $chars{$n};
                }
            }
            else
            {
                $code .= $chars{$n};
            }
        }
        
        return $code;
    }
    
    /**
     * check whether or not a specified flag is active in the given flags
     * 
     * @param int $value combined flags value
     * @param int $flag specific flag
     * @return bool 
     */
    static private function IsFlag($value, $flag)
    {
        return (($value & $flag) === $flag);
    }
}

?>