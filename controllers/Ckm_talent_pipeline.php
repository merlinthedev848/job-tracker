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
        
        // Creative Pro Modules: Sessions, Buyouts Radar, Stay-in-Touch, Inbound Potentials
        $data['upcoming_sessions'] = $this->ckm_talent_pipeline_model->get_upcoming_sessions();
        $data['expiring_licenses'] = $this->ckm_talent_pipeline_model->get_expiring_licenses();
        $data['dormant_clients']   = $this->ckm_talent_pipeline_model->get_dormant_clients();
        $data['potentials']        = $this->ckm_talent_pipeline_model->get_pending_potentials();
        
        $data['view_mode']         = $this->input->get('view') ?: 'kanban';
        $data['active_tab']        = $this->input->get('tab') ?: 'pipeline';

        $this->load->view('manage', $data);
    }

    /**
     * Accept & Convert Inbound Potential to Active Job
     */
    public function convert_potential($id)
    {
        if (!has_permission('ckm_talent_pipeline', '', 'create') && !is_admin()) {
            access_denied('ckm_talent_pipeline');
        }

        $job_id = $this->ckm_talent_pipeline_model->convert_potential_to_job($id);
        if ($job_id) {
            set_alert('success', 'Inbound casting breakdown converted to active job card successfully!');
        }
        redirect(admin_url('ckm_talent_pipeline'));
    }

    /**
     * Dismiss Inbound Potential
     */
    public function dismiss_potential($id)
    {
        if (!has_permission('ckm_talent_pipeline', '', 'delete') && !is_admin()) {
            access_denied('ckm_talent_pipeline');
        }

        $this->ckm_talent_pipeline_model->dismiss_potential($id);
        set_alert('warning', 'Casting potential dismissed.');
        redirect(admin_url('ckm_talent_pipeline'));
    }

    /**
     * 1-Click Purge of Auto-Replies, Bounces & Newsletters
     */
    public function purge_spam_potentials()
    {
        if (!has_permission('ckm_talent_pipeline', '', 'delete') && !is_admin()) {
            access_denied('ckm_talent_pipeline');
        }

        $count = $this->ckm_talent_pipeline_model->purge_spam_potentials();
        set_alert('success', 'Purged ' . $count . ' non-casting email(s), auto-replies, and newsletters from the queue.');
        redirect(admin_url('ckm_talent_pipeline'));
    }

    /**
     * Clear / Dismiss All Pending Potentials
     */
    public function clear_all_potentials()
    {
        if (!has_permission('ckm_talent_pipeline', '', 'delete') && !is_admin()) {
            access_denied('ckm_talent_pipeline');
        }

        $count = $this->ckm_talent_pipeline_model->dismiss_all_potentials();
        set_alert('warning', 'Cleared ' . $count . ' casting opportunities from the queue.');
        redirect(admin_url('ckm_talent_pipeline'));
    }

    /**
     * Bulk Action on Potentials (Batch Accept / Batch Dismiss)
     */
    public function bulk_potentials()
    {
        if ($this->input->post()) {
            $action = $this->input->post('bulk_action');
            $ids    = $this->input->post('potential_ids');

            if (!empty($ids) && is_array($ids)) {
                $processed = 0;
                foreach ($ids as $id) {
                    if ($action === 'accept') {
                        if ($this->ckm_talent_pipeline_model->convert_potential_to_job($id)) {
                            $processed++;
                        }
                    } elseif ($action === 'dismiss') {
                        if ($this->ckm_talent_pipeline_model->dismiss_potential($id)) {
                            $processed++;
                        }
                    }
                }
                set_alert('success', 'Bulk ' . ($action === 'accept' ? 'accepted ' : 'dismissed ') . $processed . ' casting leads.');
            }
            redirect(admin_url('ckm_talent_pipeline'));
        }
    }

    /**
     * Manual Trigger to Poll Inbox
     */
    public function poll_inbox()
    {
        if (!is_admin()) {
            access_denied('ckm_talent_pipeline');
        }

        $count = $this->ckm_talent_pipeline_model->poll_inbox_for_castings();
        if ($count !== false) {
            set_alert('success', 'Checked inbox successfully. Ingested ' . $count . ' new casting email(s)!');
        } else {
            set_alert('warning', 'Could not connect to mailbox. Please check your IMAP settings in the Settings tab.');
        }
        redirect(admin_url('ckm_talent_pipeline'));
    }

    /**
     * Save IMAP Configuration Settings
     */
    public function save_imap()
    {
        if ($this->input->post()) {
            if (!is_admin()) {
                access_denied('ckm_talent_pipeline');
            }

            update_option('ckm_talent_imap_host', trim($this->input->post('imap_host')));
            update_option('ckm_talent_imap_user', trim($this->input->post('imap_user')));
            if ($this->input->post('imap_pass')) {
                update_option('ckm_talent_imap_pass', $this->input->post('imap_pass'));
            }
            update_option('ckm_talent_imap_port', trim($this->input->post('imap_port')));
            update_option('ckm_talent_imap_encryption', trim($this->input->post('imap_encryption')));
            
            update_option('ckm_talent_auto_ingest_enabled', $this->input->post('auto_ingest_enabled') ? 1 : 0);
            update_option('ckm_talent_auto_convert_to_jobs', $this->input->post('auto_convert_to_jobs') ? 1 : 0);
            update_option('ckm_talent_ingest_filter_mode', trim($this->input->post('ingest_filter_mode') ?: 'keywords'));
            update_option('ckm_talent_imap_search_mode', trim($this->input->post('imap_search_mode') ?: 'unseen_and_recent'));

            set_alert('success', 'Casting email IMAP & Auto-Ingestion settings updated successfully!');
            redirect(admin_url('ckm_talent_pipeline?tab=crm'));
        }
    }

    /**
     * AJAX IMAP Connection Test & Diagnostics
     */
    public function test_imap()
    {
        if (!is_admin()) {
            access_denied('ckm_talent_pipeline');
        }

        $result = $this->ckm_talent_pipeline_model->test_and_diagnose_imap();
        echo json_encode($result);
        die();
    }

    /**
     * Fetch Auto-Generated Quote Draft via AJAX
     */
    public function get_auto_quote($potential_id)
    {
        if ($this->input->is_ajax_request()) {
            $quote_text = $this->ckm_talent_pipeline_model->generate_auto_quote_text($potential_id);
            echo json_encode(['quote_text' => $quote_text]);
            die();
        }
    }

    /**
     * Send Quotation Email directly via Perfex CRM mail system
     */
    public function send_quote_email()
    {
        if ($this->input->is_ajax_request()) {
            $potential_id = $this->input->post('potential_id');
            $recipient    = trim($this->input->post('recipient'));
            $subject      = trim($this->input->post('subject'));
            $message      = $this->input->post('message');

            if (empty($recipient) || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Please provide a valid recipient email address.']);
                die();
            }

            if (empty($message)) {
                echo json_encode(['success' => false, 'message' => 'Email message body cannot be empty.']);
                die();
            }

            $this->load->library('email');
            $this->email->clear(true);
            $from_email = get_option('smtp_email') ?: get_option('active_language');
            $from_name  = get_option('companyname') ?: 'Voice Over Talent';
            
            $this->email->from($from_email, $from_name);
            $this->email->to($recipient);
            $this->email->subject($subject ?: 'Quotation & Availability - Voice Over Services');
            $this->email->message(nl2br(htmlspecialchars($message)));

            if ($this->email->send()) {
                if ($potential_id) {
                    $this->db->where('id', $potential_id);
                    $this->db->update(db_prefix() . 'ckm_talent_potentials', ['status' => 'quoted']);
                }
                echo json_encode(['success' => true, 'message' => 'Quotation email dispatched successfully!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to send email. Check your SMTP settings in Setup -> Settings -> Email.']);
            }
            die();
        }
    }

    /**
     * Inbound Casting Email Webhook Listener (for Zapier, Make, Cloudmailin, Mailgun, SendGrid)
     */
    public function webhook($key = '')
    {
        $expected_key = get_option('ckm_talent_webhook_key');
        if (!empty($expected_key) && $key !== $expected_key) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid Webhook Security Key']);
            die();
        }

        $raw_input = file_get_contents('php://input');
        $json_data = json_decode($raw_input, true);

        $subject    = '';
        $from_name  = '';
        $from_email = '';
        $body       = '';

        if (!empty($json_data)) {
            $subject    = isset($json_data['subject']) ? $json_data['subject'] : (isset($json_data['headers']['Subject']) ? $json_data['headers']['Subject'] : '');
            $from_name  = isset($json_data['from_name']) ? $json_data['from_name'] : (isset($json_data['sender']) ? $json_data['sender'] : '');
            $from_email = isset($json_data['from_email']) ? $json_data['from_email'] : (isset($json_data['from']) ? $json_data['from'] : '');
            $body       = isset($json_data['body']) ? $json_data['body'] : (isset($json_data['plain']) ? $json_data['plain'] : (isset($json_data['text']) ? $json_data['text'] : $raw_input));
        } else {
            $subject    = $this->input->post('subject');
            $from_name  = $this->input->post('from_name');
            $from_email = $this->input->post('from_email') ?: $this->input->post('from');
            $body       = $this->input->post('body') ?: $this->input->post('plain') ?: $this->input->post('text');
        }

        if (empty($body) && empty($subject)) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'No message payload received']);
            die();
        }

        $potential_id = $this->ckm_talent_pipeline_model->ingest_inbound_message($from_name, $from_email, $subject, $body);

        $job_id = null;
        if ($potential_id && (int)get_option('ckm_talent_auto_convert_to_jobs') === 1) {
            $job_id = $this->ckm_talent_pipeline_model->convert_potential_to_job($potential_id);
        }

        echo json_encode([
            'status'       => 'success',
            'potential_id' => $potential_id,
            'job_id'       => $job_id,
            'message'      => $job_id ? 'Inbound casting call ingested and converted to active job card' : 'Inbound casting call ingested into potential queue'
        ]);
        die();
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
