<?php

/*
 * Cron.php
 * 
 * @copyright D-SoNE Software
 * @author menno <menno@dsone.nl>
 * @encoding UTF-8
 * 
 * @version 1.0.0
 * @since 11-jul-2013
 */

// add requirements here

/**
 * voucher_Cron
 *
 * @author menno <menno@dsone.nl>
 * @copyright D-SoNE Software
 * @since 3-okt-2012
 * @version 1.0.0
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
        // run the cron job(s)
        return true;
    }

}

?>