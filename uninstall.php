<?php

defined('BASEPATH') or exit('No direct script access allowed');

// Uninstall script - cleans up or leaves data intact based on user policy
$CI = &get_instance();

// Uncomment below if you want complete table drop on uninstall
/*
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'ckm_talent_jobs`');
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'ckm_talent_categories`');
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'ckm_talent_sources`');
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'ckm_talent_loss_reasons`');
*/
