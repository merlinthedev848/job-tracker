<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_103 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        // Add monthly revenue goal option if not set
        if (!get_option('ckm_talent_monthly_goal')) {
            add_option('ckm_talent_monthly_goal', '3000');
        }
    }
}
