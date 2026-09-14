<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// 1. Main Talent Jobs & Auditions Table
if (!$CI->db->table_exists(db_prefix() . 'ckm_talent_jobs')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "ckm_talent_jobs` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `job_title` VARCHAR(255) NOT NULL,
        `client_id` INT(11) DEFAULT NULL,
        `agent_name` VARCHAR(150) DEFAULT NULL,
        `source_id` INT(11) DEFAULT NULL,
        `category_id` INT(11) DEFAULT NULL,
        `status` VARCHAR(50) NOT NULL DEFAULT 'quote_sent',
        `loss_reason_id` INT(11) DEFAULT NULL,
        `loss_notes` TEXT DEFAULT NULL,
        `role_name` VARCHAR(150) DEFAULT NULL,
        `word_count` INT(11) DEFAULT 0,
        `duration_seconds` INT(11) DEFAULT 0,
        `bsf_amount` DECIMAL(15,2) DEFAULT '0.00',
        `usage_amount` DECIMAL(15,2) DEFAULT '0.00',
        `commission_percent` DECIMAL(5,2) DEFAULT '0.00',
        `total_amount` DECIMAL(15,2) DEFAULT '0.00',
        `net_amount` DECIMAL(15,2) DEFAULT '0.00',
        `usage_medium` VARCHAR(150) DEFAULT NULL,
        `usage_territory` VARCHAR(150) DEFAULT NULL,
        `usage_duration` VARCHAR(150) DEFAULT NULL,
        `usage_expiry_date` DATE DEFAULT NULL,
        `expiry_notified` TINYINT(1) DEFAULT 0,
        `session_datetime` DATETIME DEFAULT NULL,
        `direction_type` VARCHAR(100) DEFAULT 'Self-Record',
        `direction_link` VARCHAR(255) DEFAULT NULL,
        `audio_specs` VARCHAR(100) DEFAULT '48kHz / 24-bit WAV',
        `audio_link` VARCHAR(255) DEFAULT NULL,
        `delivery_deadline` DATE DEFAULT NULL,
        `perfex_invoice_id` INT(11) DEFAULT NULL,
        `notes` TEXT DEFAULT NULL,
        `date_created` DATETIME NOT NULL,
        `date_updated` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        KEY `client_id` (`client_id`),
        KEY `status` (`status`),
        KEY `source_id` (`source_id`),
        KEY `category_id` (`category_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
} else {
    if (!$CI->db->field_exists('audio_link', db_prefix() . 'ckm_talent_jobs')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ckm_talent_jobs` ADD `audio_link` VARCHAR(255) DEFAULT NULL AFTER `audio_specs`;');
    }
}

// 2. Lookup Categories (Commercial, E-Learning, Animation, etc.)
if (!$CI->db->table_exists(db_prefix() . 'ckm_talent_categories')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "ckm_talent_categories` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `color` VARCHAR(10) DEFAULT '#03a9f4',
        `is_active` TINYINT(1) DEFAULT 1,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $default_categories = [
        ['name' => 'Commercial (TV / Radio / Web)', 'color' => '#e91e63'],
        ['name' => 'Corporate / E-Learning / Explainer', 'color' => '#009688'],
        ['name' => 'Animation & Character', 'color' => '#9c27b0'],
        ['name' => 'Video Games & Interactive', 'color' => '#ff5722'],
        ['name' => 'Audiobooks & Narration', 'color' => '#795548'],
        ['name' => 'Promo & Trailer', 'color' => '#f44336'],
        ['name' => 'Dubbing & ADR', 'color' => '#3f51b5'],
        ['name' => 'Film, TV & On-Screen Acting', 'color' => '#ff9800'],
        ['name' => 'IVR & Telephony', 'color' => '#607d8b'],
    ];
    $CI->db->insert_batch(db_prefix() . 'ckm_talent_categories', $default_categories);
}

// 3. Lookup Sources (Direct Client, Agent, Voice123, Voices, Spotlight, Backstage)
if (!$CI->db->table_exists(db_prefix() . 'ckm_talent_sources')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "ckm_talent_sources` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(100) NOT NULL,
        `default_commission` DECIMAL(5,2) DEFAULT '0.00',
        `is_active` TINYINT(1) DEFAULT 1,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $default_sources = [
        ['name' => 'Direct Client', 'default_commission' => 0.00],
        ['name' => 'Voice / Talent Agent (Commercial)', 'default_commission' => 20.00],
        ['name' => 'Voice / Talent Agent (Non-Commercial)', 'default_commission' => 10.00],
        ['name' => 'Voice123', 'default_commission' => 0.00],
        ['name' => 'Voices.com', 'default_commission' => 0.00],
        ['name' => 'Backstage / Casting Networks', 'default_commission' => 0.00],
        ['name' => 'Spotlight', 'default_commission' => 0.00],
        ['name' => 'Referral / Word of Mouth', 'default_commission' => 0.00],
    ];
    $CI->db->insert_batch(db_prefix() . 'ckm_talent_sources', $default_sources);
}

// 4. Loss Reasons
if (!$CI->db->table_exists(db_prefix() . 'ckm_talent_loss_reasons')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "ckm_talent_loss_reasons` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `reason` VARCHAR(150) NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $default_reasons = [
        ['reason' => 'Client selected another voice/actor'],
        ['reason' => 'Rate/Quote was above client budget'],
        ['reason' => 'Project cancelled by client'],
        ['reason' => 'Client did not respond / Ghosted'],
        ['reason' => 'Turnaround deadline was too tight'],
        ['reason' => 'Usage terms could not be agreed upon'],
    ];
    $CI->db->insert_batch(db_prefix() . 'ckm_talent_loss_reasons', $default_reasons);
}
