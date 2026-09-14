<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ckm_talent_pipeline extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        if (file_exists(__DIR__ . '/../models/Ckm_talent_pipeline_model.php')) {
            require_once(__DIR__ . '/../models/Ckm_talent_pipeline_model.php');
        }
        $this->ckm_talent_pipeline_model = new Ckm_talent_pipeline_model();
        
        $this->load->model('clients_model');
    }

    /**
     * Unified Tabbed Module Dashboard
     */
    public function index()
    {
        if (!has_permission('ckm_talent_pipeline', '', 'view') && !is_admin()) {
            access_denied('ckm_talent_pipeline');
        }

        $data = [];
        $data['title']             = _l('ckm_tp_menu_pipeline');
        $data['jobs']              = $this->ckm_talent_pipeline_model->get();
        $data['clients']           = $this->clients_model->get();
        $data['categories']        = $this->ckm_talent_pipeline_model->get_categories();
        $data['sources']           = $this->ckm_talent_pipeline_model->get_sources();
        $data['loss_reasons']      = $this->ckm_talent_pipeline_model->get_loss_reasons();
        $data['summary']           = $this->ckm_talent_pipeline_model->get_analytics_summary();
        $data['category_stats']    = $this->ckm_talent_pipeline_model->get_category_stats();
        $data['source_stats']      = $this->ckm_talent_pipeline_model->get_source_stats();
        $data['loss_stats']        = $this->ckm_talent_pipeline_model->get_loss_stats();
        
        // Creative Pro Modules: Sessions, Buyouts Radar, Stay-in-Touch
        $data['upcoming_sessions'] = $this->ckm_talent_pipeline_model->get_upcoming_sessions();
        $data['expiring_licenses'] = $this->ckm_talent_pipeline_model->get_expiring_licenses();
        $data['dormant_clients']   = $this->ckm_talent_pipeline_model->get_dormant_clients();
        
        $data['view_mode']         = $this->input->get('view') ?: 'kanban';
        $data['active_tab']        = $this->input->get('tab') ?: 'pipeline';

        $this->load->view('manage', $data);
    }

    /**
     * 1-Click Duplicate / Repeat Booking
     */
    public function duplicate($id)
    {
        if (!has_permission('ckm_talent_pipeline', '', 'create') && !is_admin()) {
            access_denied('ckm_talent_pipeline');
        }

        $new_id = $this->ckm_talent_pipeline_model->duplicate($id);
        if ($new_id) {
            set_alert('success', 'Job duplicated for repeat booking successfully!');
        }
        redirect(admin_url('ckm_talent_pipeline'));
    }

    /**
     * Save Monthly Revenue Goal
     */
    public function save_goal()
    {
        if ($this->input->post()) {
            $goal = (float)$this->input->post('monthly_goal');
            update_option('ckm_talent_monthly_goal', $goal);
            set_alert('success', 'Monthly revenue goal updated successfully!');
            redirect(admin_url('ckm_talent_pipeline'));
        }
    }

    /**
     * Save (Add / Edit) Job
     */
    public function save()
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            $id = isset($data['id']) ? $data['id'] : '';
            unset($data['id']);

            if ($id == '') {
                if (!has_permission('ckm_talent_pipeline', '', 'create') && !is_admin()) {
                    access_denied('ckm_talent_pipeline');
                }
                $insert_id = $this->ckm_talent_pipeline_model->add($data);
                if ($insert_id) {
                    set_alert('success', _l('added_successfully', _l('ckm_tp_module_name')));
                }
            } else {
                if (!has_permission('ckm_talent_pipeline', '', 'edit') && !is_admin()) {
                    access_denied('ckm_talent_pipeline');
                }
                $success = $this->ckm_talent_pipeline_model->update($id, $data);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('ckm_tp_module_name')));
                }
            }
            redirect(admin_url('ckm_talent_pipeline'));
        }
    }

    /**
     * Fetch Single Job via AJAX for editing
     */
    public function get_job($id)
    {
        if ($this->input->is_ajax_request()) {
            $job = $this->ckm_talent_pipeline_model->get($id);
            echo json_encode($job);
            die();
        }
    }

    /**
     * AJAX Update Status on Kanban Drag & Drop
     */
    public function change_status()
    {
        if ($this->input->is_ajax_request()) {
            $id             = $this->input->post('id');
            $status         = $this->input->post('status');
            $loss_reason_id = $this->input->post('loss_reason_id');
            $loss_notes     = $this->input->post('loss_notes');

            $success = $this->ckm_talent_pipeline_model->change_status($id, $status, $loss_reason_id, $loss_notes);
            echo json_encode(['success' => $success]);
            die();
        }
    }

    /**
     * 1-Click Convert Job to Perfex Invoice
     */
    public function convert_to_invoice($id)
    {
        if (!has_permission('invoices', '', 'create') && !is_admin()) {
            access_denied('invoices');
        }

        $invoice_id = $this->ckm_talent_pipeline_model->convert_to_invoice($id);

        if ($invoice_id) {
            set_alert('success', sprintf(_l('ckm_tp_invoice_converted_success'), $invoice_id));
            redirect(admin_url('invoices/invoice/' . $invoice_id));
        } else {
            set_alert('danger', 'Unable to create invoice. Make sure a client is selected for this job.');
            redirect(admin_url('ckm_talent_pipeline'));
        }
    }

    /**
     * Delete Job
     */
    public function delete($id)
    {
        if (!has_permission('ckm_talent_pipeline', '', 'delete') && !is_admin()) {
            access_denied('ckm_talent_pipeline');
        }

        if ($this->ckm_talent_pipeline_model->delete($id)) {
            set_alert('success', _l('deleted', _l('ckm_tp_module_name')));
        }
        redirect(admin_url('ckm_talent_pipeline'));
    }
}
