<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Version_104 extends App_module_migration
{
    public function up()
    {
        $CI = &get_instance();
        
        // 1. Create Potentials Inbound Queue Table
        if (!$CI->db->table_exists(db_prefix() . 'ckm_talent_potentials')) {
            $CI->db->query("CREATE TABLE `" . db_prefix() . "ckm_talent_potentials` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `email_uid` VARCHAR(255) DEFAULT NULL,
                `from_name` VARCHAR(150) DEFAULT NULL,
                `from_email` VARCHAR(150) DEFAULT NULL,
                `subject` VARCHAR(255) DEFAULT NULL,
                `raw_body` LONGTEXT DEFAULT NULL,
                `parsed_title` VARCHAR(255) DEFAULT NULL,
                `parsed_role` VARCHAR(150) DEFAULT NULL,
                `parsed_words` INT(11) DEFAULT 0,
                `parsed_bsf` DECIMAL(15,2) DEFAULT '0.00',
                `parsed_usage` DECIMAL(15,2) DEFAULT '0.00',
                `parsed_deadline` DATE DEFAULT NULL,
                `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
                `job_id` INT(11) DEFAULT NULL,
                `created_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                KEY `email_uid` (`email_uid`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        }

        // 2. Add IMAP & Webhook Configuration options
        if (!get_option('ckm_talent_imap_host')) {
            add_option('ckm_talent_imap_host', '');
        }
        if (!get_option('ckm_talent_imap_user')) {
            add_option('ckm_talent_imap_user', '');
        }
        if (!get_option('ckm_talent_imap_pass')) {
            add_option('ckm_talent_imap_pass', '');
        }
        if (!get_option('ckm_talent_imap_port')) {
            add_option('ckm_talent_imap_port', '993');
        }
        if (!get_option('ckm_talent_imap_encryption')) {
            add_option('ckm_talent_imap_encryption', 'ssl');
        }
        if (!get_option('ckm_talent_webhook_key')) {
            add_option('ckm_talent_webhook_key', bin2hex(random_bytes(16)));
        }
    }
}
