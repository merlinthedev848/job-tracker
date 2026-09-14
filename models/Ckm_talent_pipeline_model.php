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
        if (!$this->db->table_exists(db_prefix() . 'ckm_talent_jobs') || !$this->db->table_exists(db_prefix() . 'ckm_talent_potentials')) {
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
     * Get Pending Inbound Potentials
     */
    public function get_pending_potentials()
    {
        $this->db->where('status', 'pending');
        $this->db->order_by('id', 'desc');
        $res = $this->db->get(db_prefix() . 'ckm_talent_potentials');
        return $res ? $res->result_array() : [];
    }

    /**
     * Ingest Inbound Email / Webhook Message
     */
    public function ingest_inbound_message($from_name, $from_email, $subject, $body, $email_uid = null)
    {
        // Prevent duplicate ingestion if email_uid provided
        if (!empty($email_uid)) {
            $this->db->where('email_uid', $email_uid);
            if ($this->db->count_all_results(db_prefix() . 'ckm_talent_potentials') > 0) {
                return false;
            }
        }

        // Smart Extraction Logic
        $parsed = $this->parse_raw_casting_text($subject . "\n" . $body);

        $data = [
            'email_uid'       => $email_uid ?: md5($from_email . $subject . time()),
            'from_name'       => $from_name,
            'from_email'      => $from_email,
            'subject'         => $subject,
            'raw_body'        => $body,
            'parsed_title'    => $parsed['title'] ?: $subject,
            'parsed_role'     => $parsed['role'] ?: 'Voice Talent / Performer',
            'parsed_words'    => $parsed['words'] ?: 0,
            'parsed_bsf'      => $parsed['bsf'] ?: 0.00,
            'parsed_usage'    => $parsed['usage'] ?: 0.00,
            'parsed_deadline' => $parsed['deadline'] ?: null,
            'status'          => 'pending',
            'created_at'      => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . 'ckm_talent_potentials', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Notify Admin Staff
            $this->load->model('staff_model');
            $staff = $this->staff_model->get('', ['active' => 1, 'admin' => 1]);
            foreach ($staff as $member) {
                add_notification([
                    'description'     => 'New Casting Inbound: ' . ($data['parsed_title'] ?: $subject) . ' from ' . $from_name,
                    'touserid'        => $member['staffid'],
                    'link'            => 'ckm_talent_pipeline?tab=potentials',
                    'additional_data' => serialize([$data['parsed_title']])
                ]);
            }
            return $insert_id;
        }
        return false;
    }

    /**
     * Parse Casting Text Heuristics
     */
    public function parse_raw_casting_text($text)
    {
        $lines = explode("\n", $text);
        $parsed = [
            'title'    => '',
            'role'     => '',
            'words'    => 0,
            'bsf'      => 0.00,
            'usage'    => 0.00,
            'deadline' => null
        ];

        // 1. Extract Project Title
        if (preg_match('/(?:Subject|Project|Campaign|Title|Job):\s*([^\n\r]+)/i', $text, $matches)) {
            $parsed['title'] = trim(preg_replace('/^(Re:\s*|Fwd:\s*|Audition:\s*|Casting:\s*)/i', '', $matches[1]));
        } elseif (!empty($lines[0])) {
            $parsed['title'] = trim(preg_replace('/^(Re:\s*|Fwd:\s*|Subject:\s*)/i', '', $lines[0]));
        }

        // 2. Extract Role
        if (preg_match('/(?:Role|Character|Voice|Persona):\s*([^\n\r]+)/i', $text, $matches)) {
            $parsed['role'] = trim($matches[1]);
        }

        // 3. Extract Word Count
        if (preg_match('/(\d+)\s*(?:words|word|w)\b/i', $text, $matches)) {
            $parsed['words'] = (int)$matches[1];
        }

        // 4. Extract Rates
        if (preg_match('/(?:BSF|Session Fee|Base Fee|Fee):\s*[£$€]?\s*(\d+(?:\.\d{2})?)/i', $text, $matches)) {
            $parsed['bsf'] = (float)$matches[1];
        }
        if (preg_match('/(?:Usage|Buyout|Licensing):\s*[£$€]?\s*(\d+(?:\.\d{2})?)/i', $text, $matches)) {
            $parsed['usage'] = (float)$matches[1];
        }
        if ($parsed['bsf'] == 0 && preg_match('/(?:Budget|Rate|Total Fee):\s*[£$€]?\s*(\d+(?:\.\d{2})?)/i', $text, $matches)) {
            $parsed['bsf'] = (float)$matches[1];
        }

        return $parsed;
    }

    /**
     * Convert Potential to Active Job
     */
    public function convert_potential_to_job($potential_id)
    {
        $this->db->where('id', $potential_id);
        $potential = $this->db->get(db_prefix() . 'ckm_talent_potentials')->row();
        if (!$potential) return false;

        // Try to match or create Client by email
        $client_id = null;
        if (!empty($potential->from_email)) {
            $this->db->select('userid');
            $this->db->where('company', $potential->from_name);
            $client_check = $this->db->get(db_prefix() . 'clients')->row();
            if ($client_check) {
                $client_id = $client_check->userid;
            }
        }

        $gross = (float)$potential->parsed_bsf + (float)$potential->parsed_usage;

        $job_data = [
            'job_title'          => $potential->parsed_title ?: $potential->subject,
            'client_id'          => $client_id,
            'agent_name'         => $potential->from_name ?: $potential->from_email,
            'status'             => 'quote_sent',
            'role_name'          => $potential->parsed_role,
            'word_count'         => $potential->parsed_words,
            'bsf_amount'         => $potential->parsed_bsf,
            'usage_amount'       => $potential->parsed_usage,
            'total_amount'       => $gross,
            'net_amount'         => $gross,
            'delivery_deadline'  => $potential->parsed_deadline,
            'notes'              => "--- INBOUND CASTING EMAIL ---\nFrom: " . $potential->from_name . " <" . $potential->from_email . ">\nSubject: " . $potential->subject . "\n\n" . $potential->raw_body,
            'date_created'       => date('Y-m-d H:i:s'),
            'date_updated'       => date('Y-m-d H:i:s')
        ];

        $this->db->insert(db_prefix() . 'ckm_talent_jobs', $job_data);
        $job_id = $this->db->insert_id();

        if ($job_id) {
            $this->db->where('id', $potential_id);
            $this->db->update(db_prefix() . 'ckm_talent_potentials', ['status' => 'converted', 'job_id' => $job_id]);
            return $job_id;
        }
        return false;
    }

    /**
     * Dismiss Potential
     */
    public function dismiss_potential($potential_id)
    {
        $this->db->where('id', $potential_id);
        $this->db->update(db_prefix() . 'ckm_talent_potentials', ['status' => 'dismissed']);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Poll IMAP Inbox for Castings (Cron or Manual Trigger)
     */
    public function poll_inbox_for_castings()
    {
        $host = get_option('ckm_talent_imap_host');
        $user = get_option('ckm_talent_imap_user');
        $pass = get_option('ckm_talent_imap_pass');
        $port = get_option('ckm_talent_imap_port') ?: '993';
        $enc  = strtolower(get_option('ckm_talent_imap_encryption') ?: 'ssl');

        if (empty($host) || empty($user) || empty($pass)) {
            return false; // Not configured yet
        }

        if (!function_exists('imap_open')) {
            return false;
        }

        $enc_flag = '';
        if ($enc === 'ssl') {
            $enc_flag = '/imap/ssl/novalidate-cert';
        } elseif ($enc === 'tls') {
            $enc_flag = '/imap/tls/novalidate-cert';
        } else {
            $enc_flag = '/imap/notls';
        }

        $mailbox = "{" . $host . ":" . $port . $enc_flag . "}INBOX";
        $inbox = @imap_open($mailbox, $user, $pass, OP_READONLY, 1);

        if (!$inbox) {
            // Fallback retry with default flags if custom cert flag failed
            $mailbox_fallback = "{" . $host . ":" . $port . "/imap/" . $enc . "}INBOX";
            $inbox = @imap_open($mailbox_fallback, $user, $pass, OP_READONLY, 1);
            if (!$inbox) {
                return false;
            }
        }

        $search_mode = get_option('ckm_talent_imap_search_mode') ?: 'unseen_and_recent';
        $filter_mode = get_option('ckm_talent_ingest_filter_mode') ?: 'keywords';
        $auto_convert = (int)get_option('ckm_talent_auto_convert_to_jobs') === 1;

        // 1. Search for UNSEEN emails first
        $emails = @imap_search($inbox, 'UNSEEN');

        // 2. If no unseen emails or search mode includes recent, check the latest messages
        if ((empty($emails) || !is_array($emails)) && $search_mode === 'unseen_and_recent') {
            $total_msgs = @imap_num_msg($inbox);
            if ($total_msgs > 0) {
                $start_msg = max(1, $total_msgs - 30);
                $emails = range($total_msgs, $start_msg);
            }
        }

        $count = 0;

        if (!empty($emails) && is_array($emails)) {
            rsort($emails);
            $emails_to_process = array_slice($emails, 0, 30);

            foreach ($emails_to_process as $email_number) {
                $header = @imap_headerinfo($inbox, $email_number);
                if (!$header) continue;

                $uid = @imap_uid($inbox, $email_number) ?: (string)$email_number;

                // Skip if already ingested
                $this->db->where('email_uid', (string)$uid);
                if ($this->db->count_all_results(db_prefix() . 'ckm_talent_potentials') > 0) {
                    continue;
                }

                $from_name = isset($header->from[0]->personal) ? mb_decode_mimeheader($header->from[0]->personal) : '';
                $from_email = isset($header->from[0]->mailbox) && isset($header->from[0]->host) ? $header->from[0]->mailbox . '@' . $header->from[0]->host : '';
                $subject = isset($header->subject) ? mb_decode_mimeheader($header->subject) : 'No Subject';
                $body = $this->extract_email_body($inbox, $email_number);

                // Filter check
                $is_match = false;
                if ($filter_mode === 'all') {
                    $is_match = true;
                } else {
                    $full_text = $subject . ' ' . $body;
                    $casting_pattern = '/(audition|casting|voiceover|voice-over|voice\s*over|voice\s*actor|voice\s*talent|audiobook|narrat|commercial|explainer|dubbing|e-learning|elearning|animation|videogame|video\s*game|promo|podcast|ivr|on-hold|bsf|buyout|self-tape|selftape|sides|mp3|wav|script|performer|actor|actress|voice\s*sample|session\s*fee|usage\s*fee|rate\s*card)/i';
                    if (preg_match($casting_pattern, $full_text)) {
                        $is_match = true;
                    }
                }

                if ($is_match && !empty($body)) {
                    $potential_id = $this->ingest_inbound_message($from_name, $from_email, $subject, $body, (string)$uid);
                    if ($potential_id) {
                        $count++;
                        if ($auto_convert) {
                            $this->convert_potential_to_job($potential_id);
                        }
                    }
                }
            }
        }

        @imap_close($inbox);
        return $count;
    }

    /**
     * Test and diagnose IMAP connection with detailed diagnostic reporting
     */
    public function test_and_diagnose_imap()
    {
        $host = get_option('ckm_talent_imap_host');
        $user = get_option('ckm_talent_imap_user');
        $pass = get_option('ckm_talent_imap_pass');
        $port = get_option('ckm_talent_imap_port') ?: '993';
        $enc  = strtolower(get_option('ckm_talent_imap_encryption') ?: 'ssl');

        if (empty($host) || empty($user) || empty($pass)) {
            return [
                'success' => false,
                'message' => 'IMAP Host, User, and Password must all be configured before testing.'
            ];
        }

        if (!function_exists('imap_open')) {
            return [
                'success' => false,
                'message' => 'The PHP IMAP extension (php_imap) is not enabled on this server. Please enable it in php.ini or use the Webhook listener.'
            ];
        }

        $enc_flag = ($enc === 'ssl') ? '/imap/ssl/novalidate-cert' : (($enc === 'tls') ? '/imap/tls/novalidate-cert' : '/imap/notls');
        $mailbox = "{" . $host . ":" . $port . $enc_flag . "}INBOX";

        $inbox = @imap_open($mailbox, $user, $pass, OP_READONLY, 1);
        if (!$inbox) {
            $last_error = imap_last_error();
            return [
                'success' => false,
                'message' => 'Could not connect to IMAP server: ' . ($last_error ?: 'Authentication failed or host unreachable')
            ];
        }

        $total_msgs = @imap_num_msg($inbox);
        $unseen_msgs = @imap_search($inbox, 'UNSEEN');
        $unseen_count = is_array($unseen_msgs) ? count($unseen_msgs) : 0;

        @imap_close($inbox);

        return [
            'success'      => true,
            'message'      => "Connected successfully! Mailbox contains {$total_msgs} total message(s), with {$unseen_count} unread email(s).",
            'total_msgs'   => $total_msgs,
            'unseen_count' => $unseen_count
        ];
    }

    /**
     * Helper to extract clean plain text from email structure (supports Base64, Quoted-Printable, and Multiparts)
     */
    private function extract_email_body($inbox, $email_number)
    {
        $structure = @imap_fetchstructure($inbox, $email_number);
        if (!$structure) {
            $raw = @imap_body($inbox, $email_number);
            return $raw ? trim(strip_tags($raw)) : '';
        }

        $body = '';
        if (empty($structure->parts)) {
            // Single part message
            $raw = @imap_body($inbox, $email_number);
            $body = $this->decode_mime_part($raw, $structure->encoding ?? 0);
            if (isset($structure->subtype) && strtolower($structure->subtype) === 'html') {
                $body = strip_tags($body);
            }
        } else {
            // Multipart message
            $body = $this->extract_multipart_body($inbox, $email_number, $structure);
        }

        if (function_exists('mb_convert_encoding') && !empty($body)) {
            $body = mb_convert_encoding($body, 'UTF-8', 'UTF-8, ISO-8859-1, Windows-1252, ASCII');
        }

        return trim(html_entity_decode(strip_tags($body)));
    }

    /**
     * Recursively traverse multipart structures for text content
     */
    private function extract_multipart_body($inbox, $email_number, $structure, $part_prefix = '')
    {
        $plain_text = '';
        $html_text = '';

        if (!empty($structure->parts)) {
            foreach ($structure->parts as $idx => $part) {
                $part_num = empty($part_prefix) ? (string)($idx + 1) : ($part_prefix . '.' . ($idx + 1));
                
                if ($part->type == 0) { // Text part
                    $data = @imap_fetchbody($inbox, $email_number, $part_num);
                    $decoded = $this->decode_mime_part($data, $part->encoding ?? 0);
                    
                    if (isset($part->subtype) && strtolower($part->subtype) === 'plain') {
                        $plain_text .= $decoded . "\n";
                    } elseif (isset($part->subtype) && strtolower($part->subtype) === 'html') {
                        $html_text .= $decoded . "\n";
                    }
                } elseif ($part->type == 1 && !empty($part->parts)) { // Sub-multipart
                    $nested = $this->extract_multipart_body($inbox, $email_number, $part, $part_num);
                    if (!empty($nested)) {
                        $plain_text .= $nested . "\n";
                    }
                }
            }
        }

        if (!empty($plain_text)) {
            return $plain_text;
        }

        return !empty($html_text) ? strip_tags($html_text) : '';
    }

    /**
     * Decode MIME part based on encoding flag
     */
    private function decode_mime_part($data, $encoding)
    {
        switch ((int)$encoding) {
            case 3: // Base64
                return base64_decode($data);
            case 4: // Quoted-Printable
                return quoted_printable_decode($data);
            case 0: // 7bit
            case 1: // 8bit
            case 2: // Binary
            default:
                return $data;
        }
    }

    /**
     * Generate Auto Quotation Draft Response
     */
    public function generate_auto_quote_text($potential_id)
    {
        $this->db->where('id', $potential_id);
        $p = $this->db->get(db_prefix() . 'ckm_talent_potentials')->row();
        if (!$p) return '';

        $salutation = !empty($p->from_name) ? "Hi " . explode(' ', $p->from_name)[0] . "," : "Hi there,";
        $bsf = ($p->parsed_bsf > 0) ? "£" . number_format($p->parsed_bsf, 2) : "£300.00";
        $usage = ($p->parsed_usage > 0) ? "£" . number_format($p->parsed_usage, 2) : "Included / To be confirmed";
        $total = ($p->parsed_bsf > 0 || $p->parsed_usage > 0) ? "£" . number_format($p->parsed_bsf + $p->parsed_usage, 2) : "£300.00";

        $draft = "$salutation\n\n" .
                 "Thank you for reaching out regarding the \"{$p->parsed_title}\" project!\n\n" .
                 "I would be delighted to provide voice-over services for the role of {$p->parsed_role}.\n\n" .
                 "--- QUOTE & USAGE BREAKDOWN ---\n" .
                 "• Basic Session Fee (BSF): $bsf\n" .
                 "• Licensing & Usage Rights: $usage\n" .
                 "• Total Proposed Rate: $total\n" .
                 "• Studio Delivery Specs: 48kHz / 24-bit Broadcast Quality WAV (Raw or Edited)\n" .
                 "• Live Direction Available via: Cleanfeed / Source-Connect / Zoom\n\n" .
                 "Please let me know if this works for your schedule, and I will reserve studio time accordingly.\n\n" .
                 "Best regards,\n" .
                 get_staff_full_name(get_staff_user_id());

        return $draft;
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

        // Auditions needed calculation
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
