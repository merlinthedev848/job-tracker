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
    if (!$CI->db->field_exists('agent_id', db_prefix() . 'ckm_talent_jobs')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ckm_talent_jobs` ADD `agent_id` INT(11) DEFAULT NULL AFTER `client_id`;');
    }
    if (!$CI->db->field_exists('perfex_estimate_id', db_prefix() . 'ckm_talent_jobs')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ckm_talent_jobs` ADD `perfex_estimate_id` INT(11) DEFAULT NULL AFTER `perfex_invoice_id`;');
    }
    if (!$CI->db->field_exists('slate_info', db_prefix() . 'ckm_talent_jobs')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ckm_talent_jobs` ADD `slate_info` VARCHAR(255) DEFAULT NULL AFTER `role_name`;');
    }
    if (!$CI->db->field_exists('file_naming_rule', db_prefix() . 'ckm_talent_jobs')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ckm_talent_jobs` ADD `file_naming_rule` VARCHAR(255) DEFAULT NULL AFTER `slate_info`;');
    }
    if (!$CI->db->field_exists('ai_rider_included', db_prefix() . 'ckm_talent_jobs')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ckm_talent_jobs` ADD `ai_rider_included` TINYINT(1) DEFAULT 1 AFTER `usage_duration`;');
    }
    if (!$CI->db->field_exists('script_text', db_prefix() . 'ckm_talent_jobs')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ckm_talent_jobs` ADD `script_text` LONGTEXT DEFAULT NULL AFTER `notes`;');
    }
    if (!$CI->db->field_exists('take_notes', db_prefix() . 'ckm_talent_jobs')) {
        $CI->db->query('ALTER TABLE `' . db_prefix() . 'ckm_talent_jobs` ADD `take_notes` LONGTEXT DEFAULT NULL AFTER `script_text`;');
    }
}

// 2. Inbound Potentials Queue Table
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

// 3. Lookup Categories
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

// 4. Lookup Sources
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

// 5. Loss Reasons
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

// 6. Job Pickups & Revisions Table
if (!$CI->db->table_exists(db_prefix() . 'ckm_talent_job_revisions')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "ckm_talent_job_revisions` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `job_id` INT(11) NOT NULL,
        `round_number` INT(11) NOT NULL DEFAULT 1,
        `request_date` DATE NOT NULL,
        `revision_type` VARCHAR(100) NOT NULL DEFAULT 'Free Minor Tweak',
        `timecodes` VARCHAR(255) DEFAULT NULL,
        `notes` TEXT DEFAULT NULL,
        `fee` DECIMAL(15,2) DEFAULT '0.00',
        `status` VARCHAR(50) NOT NULL DEFAULT 'Pending',
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        KEY `job_id` (`job_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// 7. Agency Roster & Multi-Agent Representation Table
if (!$CI->db->table_exists(db_prefix() . 'ckm_talent_agents')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "ckm_talent_agents` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(150) NOT NULL,
        `agency_name` VARCHAR(150) NOT NULL,
        `email` VARCHAR(150) DEFAULT NULL,
        `phone` VARCHAR(50) DEFAULT NULL,
        `territory` VARCHAR(100) DEFAULT 'Commercial (UK)',
        `commission_percent` DECIMAL(5,2) DEFAULT '15.00',
        `payment_terms` VARCHAR(100) DEFAULT 'Direct Client Remittance',
        `notes` TEXT DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

    $default_agents = [
        ['name' => 'Commercial Agent', 'agency_name' => 'Prime Talent Agency', 'email' => 'commercial@agency.example', 'phone' => '+44 20 7946 0912', 'territory' => 'UK Commercial', 'commission_percent' => 20.00, 'payment_terms' => 'Direct Client Remittance', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')],
        ['name' => 'US Voice Rep', 'agency_name' => 'Apex Voices LA', 'email' => 'la@apexvoices.example', 'phone' => '+1 310 555 0149', 'territory' => 'US & Global Voiceover', 'commission_percent' => 10.00, 'payment_terms' => 'Agency Invoices & Remits', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')]
    ];
    $CI->db->insert_batch(db_prefix() . 'ckm_talent_agents', $default_agents);
}

// 8. Voice Actor Expenses & Gear Ledger Table
if (!$CI->db->table_exists(db_prefix() . 'ckm_talent_expenses')) {
    $CI->db->query("CREATE TABLE `" . db_prefix() . "ckm_talent_expenses` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `job_id` INT(11) DEFAULT NULL,
        `category` VARCHAR(100) NOT NULL DEFAULT 'Studio Equipment',
        `description` VARCHAR(255) NOT NULL,
        `amount` DECIMAL(15,2) NOT NULL DEFAULT '0.00',
        `expense_date` DATE NOT NULL,
        `tax_deductible` TINYINT(1) DEFAULT 1,
        `receipt_url` VARCHAR(255) DEFAULT NULL,
        `created_at` DATETIME NOT NULL,
        PRIMARY KEY (`id`),
        KEY `job_id` (`job_id`),
        KEY `expense_date` (`expense_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
}

// 9. Voice Actor Profile & Studio Default Settings
add_option('ckm_tp_actor_name', 'Professional Voice Actor');
add_option('ckm_tp_actor_email', get_option('smtp_email'));
add_option('ckm_tp_actor_phone', '');
add_option('ckm_tp_actor_website', '');
add_option('ckm_tp_mic_chain', 'Sennheiser MKH 416 / Neumann TLM 103 -> Universal Audio Apollo Twin X');
add_option('ckm_tp_daw_booth', 'Reaper / Pro Tools | Custom Acoustically Treated Isolation Booth (-62dB Noise Floor)');
add_option('ckm_tp_source_connect_id', 'myvoiceactor_id');
add_option('ckm_tp_cleanfeed_link', 'https://cleanfeed.net/');
add_option('ckm_tp_ipdtl_id', '');
add_option('ckm_tp_sessionlink_id', '');
add_option('ckm_tp_zoom_riverside', '');
add_option('ckm_tp_demo_reels_json', json_encode([
    ['title' => 'Commercial Voice Reel', 'url' => 'https://soundcloud.com/sample/commercial-reel'],
    ['title' => 'Corporate & E-Learning Reel', 'url' => 'https://soundcloud.com/sample/corporate-reel'],
    ['title' => 'Animation & Gaming Reel', 'url' => 'https://soundcloud.com/sample/gaming-reel']
]));
add_option('ckm_tp_default_free_revisions', '1 Round of minor artistic pickups included. Script changes billed separately.');

