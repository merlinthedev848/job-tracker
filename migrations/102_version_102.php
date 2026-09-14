<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_102 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        // Add audio_link column if missing
        if (!$CI->db->field_exists('audio_link', db_prefix() . 'ckm_talent_jobs')) {
            $CI->db->query('ALTER TABLE `' . db_prefix() . 'ckm_talent_jobs` ADD `audio_link` VARCHAR(255) DEFAULT NULL AFTER `audio_specs`;');
        }
    }
}
