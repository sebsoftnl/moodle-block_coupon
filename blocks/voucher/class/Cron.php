<?php

/*
 * File: Cron.php
 * Encoding: UTF-8
 * @package voucher
 * 
 * @Version 1.0.0
 * @Since 11-jul-2013
 * @copyright Sebsoft.nl
 * @author Menno de Ridder <menno@sebsoft.nl>
 */

class voucher_Cron
{

    /**
     * run Cron cron job for moodle
     * @return bool true or false indicating cron status. 
     * WARNING: ALWAYS return true or false, moodle only sets 'run status' based on return that evals to TRUE.
     */
    public function run()
    {
        
        // Call vouchers
        $vouchers = voucher_Db::GetVouchersToSend();
        if (!$vouchers) return true;
        
        foreach($vouchers as $voucher) {
            
            // Send to user 
            if (!is_null($voucher->for_user)) {
                
            }
            // else send to config?
            
            // Send to a certain email address
            
            
        }
        
        return true;
    }

}

?>