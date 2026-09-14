<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Ckm_talent_pipeline_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->check_database_tables();
    }

    /**
     * Self-healing DB table installer
     */
    public function check_database_tables()
    {
        if (!$this->db->table_exists(db_prefix() . 'ckm_talent_jobs')) {
            if (file_exists(__DIR__ . '/../install.php')) {
                require_once(__DIR__ . '/../install.php');
            }
        }
    }

    /**
     * Get job by ID or all jobs
     */
    public function get($id = '')
    {
        $table_jobs        = db_prefix() . 'ckm_talent_jobs';
        $table_clients     = db_prefix() . 'clients';
        $table_categories  = db_prefix() . 'ckm_talent_categories';
        $table_sources     = db_prefix() . 'ckm_talent_sources';
        $table_loss        = db_prefix() . 'ckm_talent_loss_reasons';

        $this->db->select("$table_jobs.*, $table_clients.company as client_company, $table_categories.name as category_name, $table_categories.color as category_color, $table_sources.name as source_name, $table_loss.reason as loss_reason_name");
        $this->db->from($table_jobs);
        $this->db->join($table_clients, "$table_clients.userid = $table_jobs.client_id", 'left');
        $this->db->join($table_categories, "$table_categories.id = $table_jobs.category_id", 'left');
        $this->db->join($table_sources, "$table_sources.id = $table_jobs.source_id", 'left');
        $this->db->join($table_loss, "$table_loss.id = $table_jobs.loss_reason_id", 'left');

        if (is_numeric($id)) {
            $this->db->where("$table_jobs.id", $id);
            return $this->db->get()->row();
        }

        $this->db->order_by("$table_jobs.id", 'desc');
        $res = $this->db->get();
        return $res ? $res->result_array() : [];
    }

    /**
     * Add new job / audition
     */
    public function add($data)
    {
        $data['bsf_amount']          = !empty($data['bsf_amount']) ? (float)$data['bsf_amount'] : 0.00;
        $data['usage_amount']        = !empty($data['usage_amount']) ? (float)$data['usage_amount'] : 0.00;
        $data['commission_percent']  = !empty($data['commission_percent']) ? (float)$data['commission_percent'] : 0.00;
        
        $gross_total = $data['bsf_amount'] + $data['usage_amount'];
        $commission_deduction = ($gross_total * $data['commission_percent']) / 100;
        $net_total = $gross_total - $commission_deduction;

        $data['total_amount'] = $gross_total;
        $data['net_amount']   = $net_total;

        if (!empty($data['delivery_deadline'])) {
            $data['delivery_deadline'] = function_exists('to_sql_date') ? to_sql_date($data['delivery_deadline']) : date('Y-m-d', strtotime($data['delivery_deadline']));
        } else {
            $data['delivery_deadline'] = null;
        }

        if (!empty($data['usage_expiry_date'])) {
            $data['usage_expiry_date'] = function_exists('to_sql_date') ? to_sql_date($data['usage_expiry_date']) : date('Y-m-d', strtotime($data['usage_expiry_date']));
        } else {
            $data['usage_expiry_date'] = null;
        }

        if (!empty($data['session_datetime'])) {
            $data['session_datetime'] = date('Y-m-d H:i:s', strtotime($data['session_datetime']));
        } else {
            $data['session_datetime'] = null;
        }

        $data['date_created'] = date('Y-m-d H:i:s');
        $data['date_updated'] = date('Y-m-d H:i:s');

        $this->db->insert(db_prefix() . 'ckm_talent_jobs', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            if (function_exists('log_activity')) {
                log_activity('New Talent Job Created [ID: ' . $insert_id . ', ' . $data['job_title'] . ']');
            }
            return $insert_id;
        }

        return false;
    }

    /**
     * Update job
     */
    public function update($id, $data)
    {
        if (isset($data['bsf_amount']) || isset($data['usage_amount']) || isset($data['commission_percent'])) {
            $bsf = isset($data['bsf_amount']) ? (float)$data['bsf_amount'] : 0.00;
            $usage = isset($data['usage_amount']) ? (float)$data['usage_amount'] : 0.00;
            $comm = isset($data['commission_percent']) ? (float)$data['commission_percent'] : 0.00;

            $gross_total = $bsf + $usage;
            $commission_deduction = ($gross_total * $comm) / 100;
            $net_total = $gross_total - $commission_deduction;

            $data['total_amount'] = $gross_total;
            $data['net_amount']   = $net_total;
        }

        if (isset($data['delivery_deadline'])) {
            $data['delivery_deadline'] = !empty($data['delivery_deadline']) ? (function_exists('to_sql_date') ? to_sql_date($data['delivery_deadline']) : date('Y-m-d', strtotime($data['delivery_deadline']))) : null;
        }
        if (isset($data['usage_expiry_date'])) {
            $data['usage_expiry_date'] = !empty($data['usage_expiry_date']) ? (function_exists('to_sql_date') ? to_sql_date($data['usage_expiry_date']) : date('Y-m-d', strtotime($data['usage_expiry_date']))) : null;
        }
        if (isset($data['session_datetime'])) {
            $data['session_datetime'] = !empty($data['session_datetime']) ? date('Y-m-d H:i:s', strtotime($data['session_datetime'])) : null;
        }

        $data['date_updated'] = date('Y-m-d H:i:s');

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ckm_talent_jobs', $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete job
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'ckm_talent_jobs');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Change Status
     */
    public function change_status($id, $status, $loss_reason_id = null, $loss_notes = null)
    {
        $update = [
            'status'       => $status,
            'date_updated' => date('Y-m-d H:i:s')
        ];

        if ($status == 'lost') {
            $update['loss_reason_id'] = $loss_reason_id ?: null;
            $update['loss_notes']     = $loss_notes ?: null;
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'ckm_talent_jobs', $update);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Convert Job to Perfex Invoice
     */
    public function convert_to_invoice($id)
    {
        $job = $this->get($id);
        if (!$job || empty($job->client_id)) {
            return false;
        }

        $this->load->model('invoices_model');

        $items = [];
        $order = 1;

        if ($job->bsf_amount > 0) {
            $items[] = [
                'order'            => $order++,
                'description'      => 'Basic Session Fee (BSF) - ' . $job->job_title,
                'long_description' => 'Role: ' . ($job->role_name ?: 'Principal Voice/Actor') . 
                                      ($job->word_count > 0 ? ' (' . $job->word_count . ' words)' : ''),
                'qty'              => 1,
                'rate'             => $job->bsf_amount,
                'unit'             => 'fee'
            ];
        }

        if ($job->usage_amount > 0) {
            $items[] = [
                'order'            => $order++,
                'description'      => 'Licensing & Usage Rights - ' . $job->job_title,
                'long_description' => 'Media: ' . ($job->usage_medium ?: 'All Specified Media') . "\n" .
                                      'Territory: ' . ($job->usage_territory ?: 'National') . "\n" .
                                      'Term: ' . ($job->usage_duration ?: '1 Year'),
                'qty'              => 1,
                'rate'             => $job->usage_amount,
                'unit'             => 'buyout'
            ];
        }

        if (empty($items)) {
            $items[] = [
                'order'            => 1,
                'description'      => 'Voice / Performance Services - ' . $job->job_title,
                'long_description' => $job->role_name ?: '',
                'qty'              => 1,
                'rate'             => $job->total_amount,
                'unit'             => ''
            ];
        }

        $invoice_data = [
            'clientid'              => $job->client_id,
            'number'                => get_option('next_invoice_number'),
            'date'                  => _d(date('Y-m-d')),
            'duedate'               => _d(date('Y-m-d', strtotime('+30 days'))),
            'currency'              => get_base_currency()->id,
            'subtotal'              => $job->total_amount,
            'total'                 => $job->total_amount,
            'adminnote'             => 'Converted from CKM Talent Pipeline Job #' . $job->id . 
                                       ($job->agent_name ? ' [Agent: ' . $job->agent_name . ']' : ''),
            'clientnote'            => 'Thank you for your business! Audio delivered to specifications.',
            'terms'                 => get_option('predefined_terms_invoices'),
            'newitems'              => $items
        ];

        $invoice_id = $this->invoices_model->add($invoice_data);

        if ($invoice_id) {
            $this->db->where('id', $job->id);
            $this->db->update(db_prefix() . 'ckm_talent_jobs', ['perfex_invoice_id' => $invoice_id, 'status' => 'completed']);
            return $invoice_id;
        }

        return false;
    }

    /**
     * Expiring license notification
     */
    public function check_and_notify_expiring_licenses()
    {
        $today = date('Y-m-d');
        $in_30_days = date('Y-m-d', strtotime('+30 days'));

        $this->db->where('usage_expiry_date >=', $today);
        $this->db->where('usage_expiry_date <=', $in_30_days);
        $this->db->where('expiry_notified', 0);
        $expiring = $this->db->get(db_prefix() . 'ckm_talent_jobs')->result_array();

        if (!empty($expiring)) {
            $this->load->model('staff_model');
            $staff = $this->staff_model->get('', ['active' => 1, 'admin' => 1]);
            foreach ($expiring as $job) {
                foreach ($staff as $member) {
                    add_notification([
                        'description'     => 'License expiring in 30 days for: ' . $job['job_title'] . ' (Pitch Buyout Renewal)',
                        'touserid'        => $member['staffid'],
                        'link'            => 'ckm_talent_pipeline',
                        'additional_data' => serialize([$job['job_title']])
                    ]);
                }
                $this->db->where('id', $job['id']);
                $this->db->update(db_prefix() . 'ckm_talent_jobs', ['expiry_notified' => 1]);
            }
        }
    }

    public function get_categories()
    {
        $res = $this->db->where('is_active', 1)->get(db_prefix() . 'ckm_talent_categories');
        return $res ? $res->result_array() : [];
    }

    public function get_sources()
    {
        $res = $this->db->where('is_active', 1)->get(db_prefix() . 'ckm_talent_sources');
        return $res ? $res->result_array() : [];
    }

    public function get_loss_reasons()
    {
        $res = $this->db->get(db_prefix() . 'ckm_talent_loss_reasons');
        return $res ? $res->result_array() : [];
    }

    public function get_category_stats()
    {
        $table_jobs = db_prefix() . 'ckm_talent_jobs';
        $table_cat  = db_prefix() . 'ckm_talent_categories';

        $this->db->select("$table_cat.name as category_name, $table_cat.color as category_color, COUNT($table_jobs.id) as total_jobs, SUM($table_jobs.net_amount) as total_revenue");
        $this->db->from($table_jobs);
        $this->db->join($table_cat, "$table_cat.id = $table_jobs.category_id", 'left');
        $this->db->where_in("$table_jobs.status", ['won', 'in_progress', 'delivered', 'completed']);
        $this->db->group_by("$table_jobs.category_id");
        $res = $this->db->get();
        return $res ? $res->result_array() : [];
    }

    public function get_source_stats()
    {
        $table_jobs = db_prefix() . 'ckm_talent_jobs';
        $table_src  = db_prefix() . 'ckm_talent_sources';

        $this->db->select("$table_src.name as source_name, COUNT($table_jobs.id) as total_submissions, SUM(CASE WHEN $table_jobs.status IN ('won', 'in_progress', 'delivered', 'completed') THEN 1 ELSE 0 END) as total_won, SUM(CASE WHEN $table_jobs.status IN ('won', 'in_progress', 'delivered', 'completed') THEN $table_jobs.net_amount ELSE 0 END) as net_earnings");
        $this->db->from($table_jobs);
        $this->db->join($table_src, "$table_src.id = $table_jobs.source_id", 'left');
        $this->db->group_by("$table_jobs.source_id");
        $res = $this->db->get();
        return $res ? $res->result_array() : [];
    }

    public function get_loss_stats()
    {
        $table_jobs = db_prefix() . 'ckm_talent_jobs';
        $table_loss = db_prefix() . 'ckm_talent_loss_reasons';

        $this->db->select("$table_loss.reason, COUNT($table_jobs.id) as count");
        $this->db->from($table_jobs);
        $this->db->join($table_loss, "$table_loss.id = $table_jobs.loss_reason_id", 'left');
        $this->db->where("$table_jobs.status", 'lost');
        $this->db->group_by("$table_jobs.loss_reason_id");
        $res = $this->db->get();
        return $res ? $res->result_array() : [];
    }

    public function get_analytics_summary()
    {
        $table_jobs = db_prefix() . 'ckm_talent_jobs';

        $total_auditions = $this->db->count_all_results($table_jobs);
        
        $won_jobs = $this->db->where_in('status', ['won', 'in_progress', 'delivered', 'completed'])
                             ->count_all_results($table_jobs);
        
        $lost_jobs = $this->db->where('status', 'lost')
                              ->count_all_results($table_jobs);

        $conversion_rate = ($total_auditions > 0) ? round(($won_jobs / $total_auditions) * 100, 1) : 0;

        $this->db->select_sum('total_amount', 'gross_revenue');
        $this->db->select_sum('net_amount', 'net_revenue');
        $this->db->where_in('status', ['won', 'in_progress', 'delivered', 'completed']);
        $revenue = $this->db->get($table_jobs)->row();

        $this->db->select_sum('total_amount', 'pipeline_value');
        $this->db->where_in('status', ['quote_sent', 'shortlisted']);
        $pipeline = $this->db->get($table_jobs)->row();

        return [
            'total_auditions' => (int)$total_auditions,
            'won_jobs'        => (int)$won_jobs,
            'lost_jobs'       => (int)$lost_jobs,
            'conversion_rate' => $conversion_rate,
            'gross_revenue'   => ($revenue && $revenue->gross_revenue) ? (float)$revenue->gross_revenue : 0.00,
            'net_revenue'     => ($revenue && $revenue->net_revenue) ? (float)$revenue->net_revenue : 0.00,
            'pipeline_value'  => ($pipeline && $pipeline->pipeline_value) ? (float)$pipeline->pipeline_value : 0.00,
        ];
    }
}
