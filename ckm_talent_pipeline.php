<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: CKM Talent Pipeline & Jobs Tracker
Description: Specialized Voice Over, Actor & Creative Performer Job, Quote & Audition Pipeline with BSF/Usage tracking and 1-click Perfex Invoicing.
Version: 1.0.4
Requires at least: 2.3.0
Author: CKM Solutions
*/

define('CKM_TALENT_PIPELINE_MODULE_NAME', 'ckm_talent_pipeline');

// Load helper
require_once(__DIR__ . '/helpers/ckm_talent_pipeline_helper.php');

/**
 * Register activation hook
 */
register_activation_hook(CKM_TALENT_PIPELINE_MODULE_NAME, 'ckm_talent_pipeline_activation_hook');

function ckm_talent_pipeline_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files
 */
register_language_files(CKM_TALENT_PIPELINE_MODULE_NAME, [CKM_TALENT_PIPELINE_MODULE_NAME]);

/**
 * Admin Init Hook - Injects a SINGLE menu item under existing "CKM Modules"
 */
hooks()->add_action('admin_init', 'ckm_talent_pipeline_init_menu_items', 99);

function ckm_talent_pipeline_init_menu_items()
{
    $CI = &get_instance();

    $parent_slug = null;
    $sidebar_items = $CI->app_menu->get_sidebar_menu_items();

    if (!empty($sidebar_items)) {
        foreach ($sidebar_items as $slug => $item) {
            $name = is_array($item) ? ($item['name'] ?? '') : ($item->name ?? '');
            $item_slug = is_array($item) ? ($item['slug'] ?? $slug) : ($item->slug ?? $slug);

            if (strtolower(trim($name)) === 'ckm modules' || in_array($item_slug, ['ckm_modules', 'ckm-modules', 'ckm_modules_parent', 'ckmmodules', 'ckm'])) {
                $parent_slug = $item_slug;
                break;
            }
        }
    }

    if (!$parent_slug) {
        $parent_slug = 'ckm_modules';
        $CI->app_menu->add_sidebar_menu_item($parent_slug, [
            'name'     => 'CKM Modules',
            'icon'     => 'fa fa-cubes',
            'position' => 15,
        ]);
    }

    $CI->app_menu->add_sidebar_children_item($parent_slug, [
        'slug'     => 'ckm_talent_pipeline_board',
        'name'     => _l('ckm_tp_menu_pipeline'),
        'icon'     => 'fa fa-microphone',
        'href'     => admin_url('ckm_talent_pipeline'),
        'position' => 1,
    ]);
}

/**
 * Register module permissions
 */
hooks()->add_action('admin_init', 'ckm_talent_pipeline_permissions');

function ckm_talent_pipeline_permissions()
{
    $capabilities = [];
    $capabilities['capabilities'] = [
        'view'   => _l('permission_view') . '(' . _l('permission_global') . ')',
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    register_staff_capabilities('ckm_talent_pipeline', $capabilities, _l('ckm_tp_module_name'));
}

/**
 * Load CSS assets on admin head
 */
hooks()->add_action('app_admin_head', 'ckm_talent_pipeline_load_assets');

function ckm_talent_pipeline_load_assets()
{
    $CI = &get_instance();
    $module = $CI->router->fetch_module();
    $class  = $CI->router->fetch_class();

    if ($module == 'ckm_talent_pipeline' || $class == 'ckm_talent_pipeline') {
        echo '<link href="' . module_dir_url(CKM_TALENT_PIPELINE_MODULE_NAME, 'assets/css/pipeline.css') . '?v=' . time() . '" rel="stylesheet" type="text/css" />';
    }
}

/**
 * Load JS scripts in footer
 */
hooks()->add_action('app_admin_footer', 'ckm_talent_pipeline_load_js');

function ckm_talent_pipeline_load_js()
{
    $CI = &get_instance();
    $module = $CI->router->fetch_module();
    $class  = $CI->router->fetch_class();

    if ($module == 'ckm_talent_pipeline' || $class == 'ckm_talent_pipeline') {
        echo '<script src="' . module_dir_url(CKM_TALENT_PIPELINE_MODULE_NAME, 'assets/js/pipeline.js') . '?v=' . time() . '"></script>';
    }
}

/**
 * Hook into Perfex Cron to check for expiring licenses AND poll casting inbox
 */
hooks()->add_action('before_cron_run', 'ckm_talent_pipeline_cron_tasks');

function ckm_talent_pipeline_cron_tasks()
{
    $CI = &get_instance();
    if (file_exists(__DIR__ . '/models/Ckm_talent_pipeline_model.php')) {
        $CI->load->model('ckm_talent_pipeline/ckm_talent_pipeline_model');
        $CI->ckm_talent_pipeline_model->check_and_notify_expiring_licenses();
        $CI->ckm_talent_pipeline_model->poll_inbox_for_castings();
    }
}
