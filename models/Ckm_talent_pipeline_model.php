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
     * Duplicate / Clone a Job for Repeat Bookings
     */
    public function duplicate($id)
    {
        $job = $this->get($id);
        if (!$job) return false;

        $clone = [
            'job_title'          => $job->job_title . ' (Repeat)',
            'client_id'          => $job->client_id,
            'agent_name'         => $job->agent_name,
            'source_id'          => $job->source_id,
            'category_id'        => $job->category_id,
            'status'             => 'quote_sent',
            'role_name'          => $job->role_name,
            'word_count'         => $job->word_count,
            'duration_seconds'   => $job->duration_seconds,
            'bsf_amount'         => $job->bsf_amount,
            'usage_amount'       => $job->usage_amount,
            'commission_percent' => $job->commission_percent,
            'total_amount'       => $job->total_amount,
            'net_amount'         => $job->net_amount,
            'usage_medium'       => $job->usage_medium,
            'usage_territory'    => $job->usage_territory,
            'usage_duration'     => $job->usage_duration,
            'direction_type'     => $job->direction_type,
            'direction_link'     => $job->direction_link,
            'audio_specs'        => $job->audio_specs,
            'notes'              => $job->notes,
            'date_created'       => date('Y-m-d H:i:s'),
            'date_updated'       => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . 'ckm_talent_jobs', $clone);
        $insert_id = $this->db->insert_id();

        if ($insert_id && function_exists('log_activity')) {
            log_activity('Duplicated Talent Job [Original ID: ' . $id . ' to New ID: ' . $insert_id . ']');
        }
        return $insert_id;
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
     * Expiring Buyouts Radar (Next 90 Days)
     */
    public function get_expiring_licenses()
    {
        $today = date('Y-m-d');
        $in_90_days = date('Y-m-d', strtotime('+90 days'));

        $this->db->select(db_prefix() . 'ckm_talent_jobs.*, ' . db_prefix() . 'clients.company as client_company');
        $this->db->from(db_prefix() . 'ckm_talent_jobs');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'ckm_talent_jobs.client_id', 'left');
        $this->db->where('usage_expiry_date >=', $today);
        $this->db->where('usage_expiry_date <=', $in_90_days);
        $this->db->order_by('usage_expiry_date', 'asc');
        $res = $this->db->get();
        return $res ? $res->result_array() : [];
    }

    /**
     * Upcoming Directed Sessions (Next 7 Days)
     */
    public function get_upcoming_sessions()
    {
        $now = date('Y-m-d 00:00:00');
        $in_7_days = date('Y-m-d 23:59:59', strtotime('+7 days'));

        $this->db->select(db_prefix() . 'ckm_talent_jobs.*, ' . db_prefix() . 'clients.company as client_company');
        $this->db->from(db_prefix() . 'ckm_talent_jobs');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'ckm_talent_jobs.client_id', 'left');
        $this->db->where('session_datetime >=', $now);
        $this->db->where('session_datetime <=', $in_7_days);
        $this->db->order_by('session_datetime', 'asc');
        $res = $this->db->get();
        return $res ? $res->result_array() : [];
    }

    /**
     * Stay-in-Touch Radar (Clients with no activity in 60+ days)
     */
    public function get_dormant_clients()
    {
        $table_jobs    = db_prefix() . 'ckm_talent_jobs';
        $table_clients = db_prefix() . 'clients';

        $query = "SELECT c.userid, c.company, MAX(j.date_created) as last_job_date, COUNT(j.id) as lifetime_jobs, SUM(j.net_amount) as lifetime_revenue
                  FROM $table_clients c
                  JOIN $table_jobs j ON j.client_id = c.userid
                  GROUP BY c.userid
                  HAVING last_job_date < DATE_SUB(NOW(), INTERVAL 60 DAY)
                  ORDER BY lifetime_revenue DESC
                  LIMIT 10";

        $res = $this->db->query($query);
        return $res ? $res->result_array() : [];
    }

    /**
     * Expiring license notification cron
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

        // Lifetime Revenue
        $this->db->select_sum('total_amount', 'gross_revenue');
        $this->db->select_sum('net_amount', 'net_revenue');
        $this->db->where_in('status', ['won', 'in_progress', 'delivered', 'completed']);
        $revenue = $this->db->get($table_jobs)->row();

        // This Month's Booked Net Revenue
        $first_day_month = date('Y-m-01 00:00:00');
        $this->db->select_sum('net_amount', 'month_net_revenue');
        $this->db->where_in('status', ['won', 'in_progress', 'delivered', 'completed']);
        $this->db->where('date_created >=', $first_day_month);
        $month_rev = $this->db->get($table_jobs)->row();

        // Pipeline Value
        $this->db->select_sum('total_amount', 'pipeline_value');
        $this->db->where_in('status', ['quote_sent', 'shortlisted']);
        $pipeline = $this->db->get($table_jobs)->row();

        // Monthly Target Goal
        $monthly_goal = (float)get_option('ckm_talent_monthly_goal') ?: 3000.00;
        $current_month_net = ($month_rev && $month_rev->month_net_revenue) ? (float)$month_rev->month_net_revenue : 0.00;
        $goal_percent = ($monthly_goal > 0) ? min(100, round(($current_month_net / $monthly_goal) * 100)) : 0;

        // Auditions needed calculation (Goal remainder ÷ avg revenue per win ÷ conversion rate)
        $avg_deal_size = ($won_jobs > 0 && $revenue && $revenue->net_revenue) ? ($revenue->net_revenue / $won_jobs) : 350.00;
        $remaining_goal = max(0, $monthly_goal - $current_month_net);
        $needed_wins = ceil($remaining_goal / max(100, $avg_deal_size));
        $needed_auditions = ($conversion_rate > 0) ? ceil($needed_wins / ($conversion_rate / 100)) : ($needed_wins * 5);

        return [
            'total_auditions'    => (int)$total_auditions,
            'won_jobs'           => (int)$won_jobs,
            'lost_jobs'          => (int)$lost_jobs,
            'conversion_rate'    => $conversion_rate,
            'gross_revenue'      => ($revenue && $revenue->gross_revenue) ? (float)$revenue->gross_revenue : 0.00,
            'net_revenue'        => ($revenue && $revenue->net_revenue) ? (float)$revenue->net_revenue : 0.00,
            'month_net_revenue'  => $current_month_net,
            'monthly_goal'       => $monthly_goal,
            'goal_percent'       => $goal_percent,
            'needed_auditions'   => $needed_auditions,
            'avg_deal_size'      => round($avg_deal_size, 2),
            'pipeline_value'     => ($pipeline && $pipeline->pipeline_value) ? (float)$pipeline->pipeline_value : 0.00,
        ];
    }
}
