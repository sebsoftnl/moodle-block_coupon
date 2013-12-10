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
//        return true;
        
        // Call vouchers
        $vouchers = voucher_Db::GetVouchersToSend();
//        exit("<pre>" . print_r($vouchers, true) . "</pre>");
        
        if (!$vouchers || empty($vouchers)) return true; // return true to keep other crons running
        
        // Omdat we geen koppeltabel hebben...
        $sentVouchers = array();
        
        foreach($vouchers as $voucher) {
            
            // Check if we have an owner
            if (!is_null($voucher->ownerid)) {
                
                // And add to sentVouchers so we can check if all of them have been sent
                if (!isset($sentVouchers[$voucher->ownerid])) {
                    $sentVouchers[$voucher->ownerid] = array();
                }
                
                if (!in_array($voucher->timecreated, $sentVouchers[$voucher->ownerid])) {
                    $sentVouchers[$voucher->ownerid][] = $voucher->timecreated;
                }
                
            }
            
            voucher_Helper::MailVouchers(array($voucher), $voucher->for_user_email, null, $voucher->email_body);
            
            $voucher->issend = true;
            $voucher->timemodified = time();
            voucher_Db::UpdateVoucher($voucher);
        }
        
        // Check if all vouchers have been send
        if (!empty($sentVouchers)) {
            
            foreach($sentVouchers as $ownerid=>$vouchers) {
                
                foreach($vouchers as $voucherTimeCreated) {
                    
                    if (voucher_Db::HasSendAllVouchers($ownerid, $voucherTimeCreated)) {

                        // Mail confirmation
                        voucher_Helper::ConfirmVouchersSent($ownerid, $voucherTimeCreated);

                    }
                
                }
            }
        }
//        
        return true;
    }

}

?>