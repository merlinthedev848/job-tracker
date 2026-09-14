<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ckm_talent_reports extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ckm_talent_pipeline_model');
    }

    public function index()
    {
        if (!has_permission('ckm_talent_pipeline', '', 'view')) {
            access_denied('ckm_talent_pipeline');
        }

        $data['title']   = _l('ckm_tp_menu_reports');
        $data['summary'] = $this->ckm_talent_pipeline_model->get_analytics_summary();

        // 1. Revenue & Jobs by Category
        $this->db->select(db_prefix() . 'ckm_talent_categories.name as category_name, ' .
            db_prefix() . 'ckm_talent_categories.color as category_color, ' .
            'COUNT(' . db_prefix() . 'ckm_talent_jobs.id) as total_jobs, ' .
            'SUM(' . db_prefix() . 'ckm_talent_jobs.net_amount) as total_revenue');
        $this->db->from(db_prefix() . 'ckm_talent_jobs');
        $this->db->join(db_prefix() . 'ckm_talent_categories', db_prefix() . 'ckm_talent_categories.id = ' . db_prefix() . 'ckm_talent_jobs.category_id', 'left');
        $this->db->where_in(db_prefix() . 'ckm_talent_jobs.status', ['won', 'in_progress', 'delivered', 'completed']);
        $this->db->group_by(db_prefix() . 'ckm_talent_jobs.category_id');
        $data['category_stats'] = $this->db->get()->result_array();

        // 2. Source / Agency Performance Scorecard
        $this->db->select(db_prefix() . 'ckm_talent_sources.name as source_name, ' .
            'COUNT(' . db_prefix() . 'ckm_talent_jobs.id) as total_submissions, ' .
            'SUM(CASE WHEN ' . db_prefix() . 'ckm_talent_jobs.status IN ("won", "in_progress", "delivered", "completed") THEN 1 ELSE 0 END) as total_won, ' .
            'SUM(CASE WHEN ' . db_prefix() . 'ckm_talent_jobs.status IN ("won", "in_progress", "delivered", "completed") THEN ' . db_prefix() . 'ckm_talent_jobs.net_amount ELSE 0 END) as net_earnings');
        $this->db->from(db_prefix() . 'ckm_talent_jobs');
        $this->db->join(db_prefix() . 'ckm_talent_sources', db_prefix() . 'ckm_talent_sources.id = ' . db_prefix() . 'ckm_talent_jobs.source_id', 'left');
        $this->db->group_by(db_prefix() . 'ckm_talent_jobs.source_id');
        $data['source_stats'] = $this->db->get()->result_array();

        // 3. Loss Reasons Breakdown
        $this->db->select(db_prefix() . 'ckm_talent_loss_reasons.reason, COUNT(' . db_prefix() . 'ckm_talent_jobs.id) as count');
        $this->db->from(db_prefix() . 'ckm_talent_jobs');
        $this->db->join(db_prefix() . 'ckm_talent_loss_reasons', db_prefix() . 'ckm_talent_loss_reasons.id = ' . db_prefix() . 'ckm_talent_jobs.loss_reason_id', 'left');
        $this->db->where(db_prefix() . 'ckm_talent_jobs.status', 'lost');
        $this->db->group_by(db_prefix() . 'ckm_talent_jobs.loss_reason_id');
        $data['loss_stats'] = $this->db->get()->result_array();

        $this->load->view('ckm_talent_pipeline/reports', $data);
    }
}
