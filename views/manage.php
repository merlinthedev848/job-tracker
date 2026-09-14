<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <!-- Inbound Potentials Alert Banner (if any pending) -->
        <?php if (!empty($potentials)) { ?>
            <div class="row mbot15">
                <div class="col-md-12">
                    <div class="alert alert-success display-flex justify-between align-center p12 mbot0 ckm-potentials-banner">
                        <div class="display-flex align-center">
                            <span class="badge bg-warning font-medium p8 mright10"><i class="fa fa-envelope-open"></i> <?php echo count($potentials); ?></span>
                            <div>
                                <h4 class="bold mtop0 mbot5 text-dark">
                                    <?php echo count($potentials); ?> New Inbound Casting Opportunity(s) Detected!
                                </h4>
                                <span class="font-xs text-muted">The inbox monitor has parsed new audition breakdowns ready for your review.</span>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-success bold" onclick="$('#potentials_tray_modal').modal('show');">
                                <i class="fa fa-bolt"></i> Review & Accept Potentials
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Monthly Revenue & Goal Tracker Banner -->
        <div class="row mbot15">
            <div class="col-md-12">
                <div class="panel_s mbot0 ckm-goal-tracker-card">
                    <div class="panel-body p15">
                        <div class="row align-center display-flex flex-wrap">
                            <div class="col-md-3">
                                <span class="text-uppercase font-xs bold text-muted block">This Month's Earnings</span>
                                <h3 class="bold text-success mtop5 mbot0">
                                    <?php echo ckm_format_money($summary['month_net_revenue']); ?>
                                    <small class="font-xs text-muted">/ <?php echo ckm_format_money($summary['monthly_goal']); ?> goal</small>
                                </h3>
                            </div>
                            <div class="col-md-5">
                                <div class="display-flex justify-between font-xs bold mbot5">
                                    <span>Goal Progress</span>
                                    <span class="text-primary"><?php echo $summary['goal_percent']; ?>%</span>
                                </div>
                                <div class="progress mbot0" style="height: 12px;">
                                    <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar" style="width: <?php echo $summary['goal_percent']; ?>%;"></div>
                                </div>
                            </div>
                            <div class="col-md-3 text-center">
                                <span class="badge bg-info p8 font-xs" title="Calculated from your historical booking % and average deal size">
                                    <i class="fa fa-bullseye"></i> ~<?php echo $summary['needed_auditions']; ?> auditions needed to hit goal
                                </span>
                            </div>
                            <div class="col-md-1 text-right">
                                <button type="button" class="btn btn-default btn-xs" onclick="$('#goal_modal').modal('show');" title="Edit Monthly Goal">
                                    <i class="fa fa-pencil"></i> Goal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Directed Sessions Today / Upcoming Banner (if any) -->
        <?php if (!empty($upcoming_sessions)) { ?>
            <div class="row mbot15">
                <div class="col-md-12">
                    <div class="alert alert-info display-flex justify-between align-center p10 mbot0">
                        <div>
                            <i class="fa fa-bolt font-medium mright5"></i>
                            <strong>Upcoming Directed Sessions:</strong>
                            <?php foreach (array_slice($upcoming_sessions, 0, 3) as $sess) { ?>
                                <span class="badge bg-primary mleft5">
                                    <?php echo date('D, j M H:i', strtotime($sess['session_datetime'])); ?>: 
                                    <?php echo htmlspecialchars($sess['job_title']); ?> (<?php echo htmlspecialchars($sess['direction_type']); ?>)
                                    <?php if (!empty($sess['direction_link'])) { ?>
                                        <a href="<?php echo htmlspecialchars($sess['direction_link']); ?>" target="_blank" class="text-white"><i class="fa fa-external-link"></i></a>
                                    <?php } ?>
                                </span>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Top Metrics Row -->
        <div class="row mbot15">
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa fa-microphone text-info"></i> All-Time Auditions</p>
                    <p class="text-muted bold font-medium-xs"><?php echo $summary['total_auditions']; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa fa-bullseye text-success"></i> Audition-to-Booking %</p>
                    <p class="text-success bold font-medium-xs"><?php echo $summary['conversion_rate']; ?>%</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa fa-hourglass-half text-warning"></i> Active Pipeline Value</p>
                    <p class="text-warning bold font-medium-xs"><?php echo ckm_format_money($summary['pipeline_value']); ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa fa-trophy text-primary"></i> Net Lifetime Earnings</p>
                    <p class="text-primary bold font-medium-xs"><?php echo ckm_format_money($summary['net_revenue']); ?></p>
                </div>
            </div>
        </div>

        <!-- Main Tabbed Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <!-- Navigation Tabs -->
                        <ul class="nav nav-tabs mbot20" role="tablist">
                            <li role="presentation" class="active">
                                <a href="#tab_pipeline" aria-controls="tab_pipeline" role="tab" data-toggle="tab">
                                    <i class="fa fa-microphone"></i> <strong>Jobs & Audition Pipeline</strong>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_analytics" aria-controls="tab_analytics" role="tab" data-toggle="tab">
                                    <i class="fa fa-bar-chart"></i> <strong>Win/Loss & Analytics</strong>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_buyouts" aria-controls="tab_buyouts" role="tab" data-toggle="tab">
                                    <i class="fa fa-copyright"></i> <strong>Expiring Buyouts Radar</strong>
                                    <?php if (!empty($expiring_licenses)) { ?>
                                        <span class="badge bg-warning"><?php echo count($expiring_licenses); ?></span>
                                    <?php } ?>
                                </a>
                            </li>
                            <li role="presentation">
                                <a href="#tab_crm" aria-controls="tab_crm" role="tab" data-toggle="tab">
                                    <i class="fa fa-cogs"></i> <strong>Settings & Mailbox Monitor</strong>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- TAB 1: PIPELINE BOARD -->
                            <div role="tabpanel" class="tab-pane active" id="tab_pipeline">
                                <div class="ckm-command-bar mbot20">
                                    <div class="display-flex align-center flex-wrap gap-10">
                                        <button type="button" class="btn btn-primary" onclick="new_talent_job();">
                                            <i class="fa fa-plus"></i> <?php echo _l('ckm_tp_new_job'); ?>
                                        </button>
                                        
                                        <!-- Inbound Queue Tray Button -->
                                        <button type="button" class="btn btn-warning" onclick="$('#potentials_tray_modal').modal('show');">
                                            <i class="fa fa-inbox"></i> <strong>Inbound Casting Queue</strong>
                                            <?php if (!empty($potentials)) { ?>
                                                <span class="badge bg-danger"><?php echo count($potentials); ?></span>
                                            <?php } ?>
                                        </button>

                                        <!-- Smart Casting Email Parser -->
                                        <button type="button" class="btn btn-info" onclick="$('#smart_parser_modal').modal('show');">
                                            <i class="fa fa-bolt"></i> <strong>Paste Casting Call</strong>
                                        </button>

                                        <!-- VO Rate Calculator -->
                                        <button type="button" class="btn btn-success" onclick="$('#rate_calculator_modal').modal('show');">
                                            <i class="fa fa-calculator"></i> <strong>Rate Calculator</strong>
                                        </button>
                                    </div>

                                    <div class="display-flex align-center gap-10">
                                        <div class="ckm-live-search-box">
                                            <i class="fa fa-search ckm-search-icon"></i>
                                            <input type="text" id="ckm_search_input" class="form-control" placeholder="Quick search project, client, role...">
                                        </div>

                                        <div class="btn-group">
                                            <a href="<?php echo admin_url('ckm_talent_pipeline?view=kanban'); ?>" class="btn btn-default <?php echo ($view_mode == 'kanban') ? 'active' : ''; ?>">
                                                <i class="fa fa-th-large"></i> <?php echo _l('ckm_tp_kanban_view'); ?>
                                            </a>
                                            <a href="<?php echo admin_url('ckm_talent_pipeline?view=list'); ?>" class="btn btn-default <?php echo ($view_mode == 'list') ? 'active' : ''; ?>">
                                                <i class="fa fa-list"></i> <?php echo _l('ckm_tp_list_view'); ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="ckm-filter-pills-bar mbot15 display-flex align-center flex-wrap gap-5">
                                    <span class="font-xs bold text-muted mright5"><i class="fa fa-filter"></i> Genre:</span>
                                    <button type="button" class="btn btn-default btn-xs ckm-genre-filter-btn active" data-cat-id="all">All</button>
                                    <?php if (!empty($categories)) { ?>
                                        <?php foreach ($categories as $cat) { ?>
                                            <button type="button" class="btn btn-default btn-xs ckm-genre-filter-btn" data-cat-id="<?php echo $cat['id']; ?>">
                                                <span class="badge" style="background-color: <?php echo $cat['color']; ?>;">&nbsp;</span> <?php echo htmlspecialchars($cat['name']); ?>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>

                                <?php if ($view_mode == 'kanban') { ?>
                                    <?php include(__DIR__ . '/kanban.php'); ?>
                                <?php } else { ?>
                                    <?php include(__DIR__ . '/list.php'); ?>
                                <?php } ?>
                            </div>

                            <!-- TAB 2: ANALYTICS & REPORTS -->
                            <div role="tabpanel" class="tab-pane" id="tab_analytics">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="panel panel-default">
                                            <div class="panel-heading bold"><i class="fa fa-user-secret"></i> Agent & Platform Performance Scorecard</div>
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th>Source / Agency</th>
                                                                <th>Submissions</th>
                                                                <th>Booked</th>
                                                                <th>Win %</th>
                                                                <th>Net Earnings</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($source_stats)) { ?>
                                                                <?php foreach ($source_stats as $src) { 
                                                                    $win_pct = ($src['total_submissions'] > 0) ? round(($src['total_won'] / $src['total_submissions']) * 100, 1) : 0;
                                                                ?>
                                                                    <tr>
                                                                        <td class="bold"><?php echo htmlspecialchars($src['source_name'] ?: 'Direct / Unspecified'); ?></td>
                                                                        <td><?php echo $src['total_submissions']; ?></td>
                                                                        <td><span class="badge bg-success"><?php echo $src['total_won']; ?></span></td>
                                                                        <td><strong><?php echo $win_pct; ?>%</strong></td>
                                                                        <td class="text-success bold"><?php echo ckm_format_money($src['net_earnings'] ?: 0); ?></td>
                                                                    </tr>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <tr><td colspan="5" class="text-center text-muted">No data logged yet.</td></tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="panel panel-default">
                                            <div class="panel-heading bold"><i class="fa fa-pie-chart"></i> Revenue by Genre / Category</div>
                                            <div class="panel-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Category</th>
                                                            <th>Bookings</th>
                                                            <th>Net Revenue</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($category_stats)) { ?>
                                                            <?php foreach ($category_stats as $cat) { ?>
                                                                <tr>
                                                                    <td>
                                                                        <span class="badge" style="background-color: <?php echo $cat['category_color'] ?: '#03a9f4'; ?>;">
                                                                            <?php echo htmlspecialchars($cat['category_name'] ?: 'General'); ?>
                                                                        </span>
                                                                    </td>
                                                                    <td><?php echo $cat['total_jobs']; ?></td>
                                                                    <td class="text-success bold"><?php echo ckm_format_money($cat['total_revenue'] ?: 0); ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <tr><td colspan="3" class="text-center text-muted">No bookings recorded yet.</td></tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading bold text-danger"><i class="fa fa-times-circle"></i> Why Auditions / Quotes Were Lost</div>
                                            <div class="panel-body">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Reason</th>
                                                            <th>Count</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (!empty($loss_stats)) { ?>
                                                            <?php foreach ($loss_stats as $loss) { ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($loss['reason'] ?: 'Unspecified Reason'); ?></td>
                                                                    <td><span class="badge bg-danger"><?php echo $loss['count']; ?></span></td>
                                                                </tr>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <tr><td colspan="2" class="text-center text-muted">No lost auditions logged yet.</td></tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: EXPIRING BUYOUTS & RENEWALS RADAR -->
                            <div role="tabpanel" class="tab-pane" id="tab_buyouts">
                                <div class="alert alert-warning">
                                    <i class="fa fa-clock-o"></i> <strong>Passive Renewal Engine:</strong> These commercial and corporate buyout licenses are expiring within the next 90 days. Click <strong>"Pitch Buyout Extension"</strong> to duplicate the job into a new quote for license extension.
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Project Title</th>
                                                <th>Client</th>
                                                <th>Media / Territory</th>
                                                <th>Original Term</th>
                                                <th>Expiry Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($expiring_licenses)) { ?>
                                                <?php foreach ($expiring_licenses as $lic) { 
                                                    $days_left = round((strtotime($lic['usage_expiry_date']) - time()) / 86400);
                                                ?>
                                                    <tr>
                                                        <td class="bold"><?php echo htmlspecialchars($lic['job_title']); ?></td>
                                                        <td><?php echo htmlspecialchars($lic['client_company'] ?: '-'); ?></td>
                                                        <td><?php echo htmlspecialchars($lic['usage_medium'] . ' (' . $lic['usage_territory'] . ')'); ?></td>
                                                        <td><?php echo htmlspecialchars($lic['usage_duration']); ?></td>
                                                        <td>
                                                            <span class="label label-<?php echo ($days_left <= 30) ? 'danger' : 'warning'; ?>">
                                                                <?php echo _d($lic['usage_expiry_date']); ?> (<?php echo $days_left; ?> days)
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="<?php echo admin_url('ckm_talent_pipeline/duplicate/' . $lic['id']); ?>" class="btn btn-success btn-xs">
                                                                <i class="fa fa-refresh"></i> Pitch Buyout Extension
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr><td colspan="6" class="text-center text-muted p20">No buyout licenses expiring in the next 90 days.</td></tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- TAB 4: SETTINGS & MAILBOX MONITOR -->
                            <div role="tabpanel" class="tab-pane" id="tab_crm">
                                <!-- Automated IMAP Inbox Connection Setup -->
                                <div class="row mbot20">
                                    <div class="col-md-12">
                                        <div class="panel panel-primary">
                                            <div class="panel-heading bold"><i class="fa fa-envelope"></i> Automated Casting Mailbox Monitor (IMAP Connection)</div>
                                            <div class="panel-body">
                                                <p class="text-muted font-xs mbot15">
                                                    Configure your casting inbox below. When the background cron runs (or when you click Check Inbox), the monitor scans for casting keywords (e.g. <em>Audition, Casting, VO, BSF, Buyout</em>) and automatically extracts the project into your <strong>Inbound Casting Queue</strong>.
                                                </p>
                                                <?php echo form_open(admin_url('ckm_talent_pipeline/save_imap')); ?>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="control-label bold">IMAP Host Server:</label>
                                                        <input type="text" name="imap_host" class="form-control" value="<?php echo get_option('ckm_talent_imap_host'); ?>" placeholder="e.g. imap.gmail.com or mail.yourdomain.com">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label bold">Email / Username:</label>
                                                        <input type="text" name="imap_user" class="form-control" value="<?php echo get_option('ckm_talent_imap_user'); ?>" placeholder="casting@yourdomain.com">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="control-label bold">Password / App Password:</label>
                                                        <input type="password" name="imap_pass" class="form-control" placeholder="••••••••">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="control-label bold">Port & Encryption:</label>
                                                        <div class="input-group">
                                                            <input type="text" name="imap_port" class="form-control" value="<?php echo get_option('ckm_talent_imap_port') ?: '993'; ?>">
                                                            <span class="input-group-addon">SSL</span>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2 ptop25">
                                                        <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save IMAP</button>
                                                    </div>
                                                </div>
                                                 <?php echo form_close(); ?>

                                                 <hr class="mtop20 mbot15">
                                                 <div class="row">
                                                     <div class="col-md-12">
                                                         <h5 class="bold"><i class="fa fa-plug text-success"></i> Direct Inbound Webhook (Alternative to IMAP)</h5>
                                                         <p class="text-muted font-xs mbot10">
                                                             Connect Zapier, Make, CloudMailin, Mailgun, or SendGrid to instantly push incoming casting opportunities to this module with zero latency.
                                                         </p>
                                                         <div class="input-group">
                                                             <input type="text" class="form-control" id="ckm_webhook_url" readonly value="<?php echo site_url('ckm_talent_pipeline/webhook/' . get_option('ckm_talent_webhook_key')); ?>">
                                                             <span class="input-group-btn">
                                                                 <button type="button" class="btn btn-default" onclick="var c=document.getElementById('ckm_webhook_url');c.select();document.execCommand('copy');alert_float('success','Webhook URL copied to clipboard!');">
                                                                     <i class="fa fa-copy"></i> Copy Webhook URL
                                                                 </button>
                                                             </span>
                                                         </div>
                                                     </div>
                                                 </div>
                                             </div>
                                         </div>
                                     </div>
                                 </div>

                                <!-- Stay-in-Touch Clients Section -->
                                <div class="row mbot20">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading bold"><i class="fa fa-history text-info"></i> "Stay-in-Touch" Client Follow-Up Radar (No Bookings in 60+ Days)</div>
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Client / Production House</th>
                                                                <th>Last Worked Together</th>
                                                                <th>Lifetime Projects</th>
                                                                <th>Lifetime Revenue</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($dormant_clients)) { ?>
                                                                <?php foreach ($dormant_clients as $cl) { ?>
                                                                    <tr>
                                                                        <td class="bold"><?php echo htmlspecialchars($cl['company']); ?></td>
                                                                        <td><?php echo _d(date('Y-m-d', strtotime($cl['last_job_date']))); ?></td>
                                                                        <td><?php echo $cl['lifetime_jobs']; ?></td>
                                                                        <td class="text-success bold"><?php echo ckm_format_money($cl['lifetime_revenue']); ?></td>
                                                                        <td>
                                                                            <a href="<?php echo admin_url('clients/client/' . $cl['userid']); ?>" class="btn btn-default btn-xs">
                                                                                <i class="fa fa-envelope-o"></i> View Client Profile & Pitch
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <tr><td colspan="5" class="text-center text-muted">All active clients have worked with you recently!</td></tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lookup Configuration -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <h4 class="bold mbot15"><i class="fa fa-tags"></i> Project Genres & Categories</h4>
                                        <ul class="list-group">
                                            <?php if (!empty($categories)) { ?>
                                                <?php foreach ($categories as $cat) { ?>
                                                    <li class="list-group-item display-flex justify-between align-center">
                                                        <span>
                                                            <span class="badge" style="background-color: <?php echo $cat['color']; ?>;">&nbsp;</span>
                                                            <?php echo htmlspecialchars($cat['name']); ?>
                                                        </span>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                    </div>

                                    <div class="col-md-4">
                                        <h4 class="bold mbot15"><i class="fa fa-user-secret"></i> Lead Sources & Default Commissions</h4>
                                        <ul class="list-group">
                                            <?php if (!empty($sources)) { ?>
                                                <?php foreach ($sources as $src) { ?>
                                                    <li class="list-group-item display-flex justify-between align-center">
                                                        <span><?php echo htmlspecialchars($src['name']); ?></span>
                                                        <span class="label label-info"><?php echo $src['default_commission']; ?>% comm</span>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                    </div>

                                    <div class="col-md-4">
                                        <h4 class="bold mbot15"><i class="fa fa-times-circle text-danger"></i> Loss Reasons</h4>
                                        <ul class="list-group">
                                            <?php if (!empty($loss_reasons)) { ?>
                                                <?php foreach ($loss_reasons as $reason) { ?>
                                                    <li class="list-group-item">
                                                        <?php echo htmlspecialchars($reason['reason']); ?>
                                                    </li>
                                                <?php } ?>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Goal Setting Modal -->
<div class="modal fade" id="goal_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <?php echo form_open(admin_url('ckm_talent_pipeline/save_goal')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-bullseye"></i> Set Monthly Target</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label bold">Monthly Net Revenue Goal:</label>
                    <input type="number" name="monthly_goal" class="form-control" value="<?php echo $summary['monthly_goal']; ?>" step="any" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Goal</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- Load Modals via direct include -->
<?php include(__DIR__ . '/modals/job_modal.php'); ?>
<?php include(__DIR__ . '/modals/loss_reason_modal.php'); ?>
<?php include(__DIR__ . '/modals/smart_parser_modal.php'); ?>
<?php include(__DIR__ . '/modals/rate_calculator_modal.php'); ?>
<?php include(__DIR__ . '/modals/potentials_tray_modal.php'); ?>

<?php init_tail(); ?>
