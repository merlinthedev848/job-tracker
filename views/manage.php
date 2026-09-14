<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <!-- Sleek Directed Sessions Strip (Only if there are upcoming directed sessions today) -->
        <?php if (!empty($upcoming_sessions)) { ?>
            <div class="alert alert-info display-flex justify-between align-center p10 mbot15" style="border-left: 4px solid #0288d1;">
                <div>
                    <i class="fa fa-bolt text-warning font-medium mright5"></i>
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
        <?php } ?>

        <!-- Main Tabbed Panel -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body p15">
                        <!-- Top Header & Modern Navigation Tabs -->
                        <div class="display-flex justify-between align-center flex-wrap mbot20 pb10 border-bottom">
                            <div class="display-flex align-center gap-10">
                                <h4 class="bold mtop0 mbot0 text-dark">
                                    <i class="fa fa-microphone text-info"></i> Talent Pipeline
                                </h4>
                                <span class="badge bg-light text-muted border font-xs mleft10">
                                    Pipeline: <strong class="text-warning"><?php echo ckm_format_money($summary['pipeline_value']); ?></strong>
                                </span>
                                <span class="badge bg-light text-muted border font-xs">
                                    This Month: <strong class="text-success"><?php echo ckm_format_money($summary['month_net_revenue']); ?></strong>
                                </span>
                            </div>

                            <ul class="nav nav-pills" role="tablist">
                                <li role="presentation" class="<?php echo ($active_tab == 'pipeline') ? 'active' : ''; ?>">
                                    <a href="#tab_pipeline" aria-controls="tab_pipeline" role="tab" data-toggle="tab">
                                        <i class="fa fa-th-large"></i> <strong>Pipeline</strong>
                                    </a>
                                </li>
                                <li role="presentation" class="<?php echo ($active_tab == 'analytics') ? 'active' : ''; ?>">
                                    <a href="#tab_analytics" aria-controls="tab_analytics" role="tab" data-toggle="tab">
                                        <i class="fa fa-bar-chart"></i> <strong>Analytics & Goals</strong>
                                    </a>
                                </li>
                                <li role="presentation" class="<?php echo ($active_tab == 'buyouts') ? 'active' : ''; ?>">
                                    <a href="#tab_buyouts" aria-controls="tab_buyouts" role="tab" data-toggle="tab">
                                        <i class="fa fa-copyright"></i> <strong>Buyouts Radar</strong>
                                        <?php if (!empty($expiring_licenses)) { ?>
                                            <span class="badge bg-warning"><?php echo count($expiring_licenses); ?></span>
                                        <?php } ?>
                                    </a>
                                </li>
                                <li role="presentation" class="<?php echo ($active_tab == 'crm') ? 'active' : ''; ?>">
                                    <a href="#tab_crm" aria-controls="tab_crm" role="tab" data-toggle="tab">
                                        <i class="fa fa-cog"></i> <strong>Settings</strong>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="tab-content">
                            <!-- TAB 1: PIPELINE BOARD -->
                            <div role="tabpanel" class="tab-pane <?php echo ($active_tab == 'pipeline') ? 'active' : ''; ?>" id="tab_pipeline">
                                <!-- Unified Action & Filter Toolbar -->
                                <div class="ckm-command-bar mbot20">
                                    <div class="ckm-toolbar-left">
                                        <button type="button" class="btn btn-info bold" onclick="new_talent_job();">
                                            <i class="fa fa-plus"></i> <?php echo _l('ckm_tp_new_job'); ?>
                                        </button>
                                        
                                        <!-- Inbound Queue Button -->
                                        <button type="button" class="btn <?php echo !empty($potentials) ? 'btn-warning' : 'btn-default'; ?>" onclick="$('#potentials_tray_modal').modal('show');">
                                            <i class="fa fa-inbox"></i> Inbound Queue
                                            <?php if (!empty($potentials)) { ?>
                                                <span class="badge bg-danger mleft5"><?php echo count($potentials); ?></span>
                                            <?php } ?>
                                        </button>

                                        <!-- VO Tools Dropdown -->
                                        <div class="dropdown">
                                            <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                                <i class="fa fa-wrench"></i> VO Tools <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="#" onclick="$('#script_teleprompter_modal').modal('show'); return false;">
                                                        <i class="fa fa-microphone text-primary"></i> Script Teleprompter & Live Take Timer
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="$('#file_namer_modal').modal('show'); return false;">
                                                        <i class="fa fa-tag text-success"></i> Audio Slate & File Naming Generator
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="$('#studio_tech_specs_modal').modal('show'); return false;">
                                                        <i class="fa fa-sliders text-info"></i> Studio Specs & Remote Profile
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="$('#ai_rider_modal').modal('show'); return false;">
                                                        <i class="fa fa-shield text-danger"></i> NAVA AI & Voice Protection Rider
                                                    </a>
                                                </li>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="#" onclick="$('#rate_calculator_modal').modal('show'); return false;">
                                                        <i class="fa fa-calculator text-success"></i> VO Rate Engine & GVAA Guide
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" onclick="$('#smart_parser_modal').modal('show'); return false;">
                                                        <i class="fa fa-bolt text-warning"></i> Paste Casting Call (Smart Parser)
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Compact Genre Filter Select -->
                                        <div class="display-flex align-center mleft5">
                                            <i class="fa fa-filter text-muted mright5"></i>
                                            <select id="ckm_genre_select" class="form-control input-sm" style="width: 170px; border-radius: 16px;" onchange="filter_by_genre_select(this.value);">
                                                <option value="all">All Genres</option>
                                                <?php if (!empty($categories)) { ?>
                                                    <?php foreach ($categories as $cat) { ?>
                                                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="ckm-toolbar-right">
                                        <div class="ckm-live-search-box">
                                            <i class="fa fa-search ckm-search-icon"></i>
                                            <input type="text" id="ckm_search_input" class="form-control input-sm" placeholder="Search auditions...">
                                        </div>

                                        <div class="btn-group">
                                            <a href="<?php echo admin_url('ckm_talent_pipeline?view=kanban'); ?>" class="btn btn-default btn-sm <?php echo ($view_mode == 'kanban') ? 'active btn-primary text-white' : ''; ?>" title="Kanban Board">
                                                <i class="fa fa-th-large"></i>
                                            </a>
                                            <a href="<?php echo admin_url('ckm_talent_pipeline?view=list'); ?>" class="btn btn-default btn-sm <?php echo ($view_mode == 'list') ? 'active btn-primary text-white' : ''; ?>" title="List Table">
                                                <i class="fa fa-list"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($view_mode == 'kanban') { ?>
                                    <?php include(__DIR__ . '/kanban.php'); ?>
                                <?php } else { ?>
                                    <?php include(__DIR__ . '/list.php'); ?>
                                <?php } ?>
                            </div>

                            <!-- TAB 2: GOALS & ANALYTICS -->
                            <div role="tabpanel" class="tab-pane <?php echo ($active_tab == 'analytics') ? 'active' : ''; ?>" id="tab_analytics">
                                <div class="display-flex justify-between align-center mbot15">
                                    <h4 class="bold mtop0 mbot0 text-dark"><i class="fa fa-line-chart text-success"></i> Performance, Goals & Financials</h4>
                                    <a href="<?php echo admin_url('ckm_talent_pipeline/export_csv'); ?>" class="btn btn-default btn-sm" title="Export all projects for tax and financial tracking">
                                        <i class="fa fa-file-excel-o text-success"></i> Export Accounting & Tax CSV
                                    </a>
                                </div>

                                <!-- Monthly Revenue Goal Pace Banner -->
                                <div class="panel_s mbot20 ckm-goal-tracker-card">
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

                                <!-- Top Metrics Summary Row -->
                                <div class="row mbot20">
                                    <div class="col-md-3">
                                        <div class="top_stats_wrapper text-center p15 bg-light border">
                                            <p class="text-uppercase font-xs text-muted mtop0 mbot5"><i class="fa fa-microphone text-info"></i> All-Time Auditions</p>
                                            <p class="text-dark bold font-medium-xs mbot0"><?php echo $summary['total_auditions']; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="top_stats_wrapper text-center p15 bg-light border">
                                            <p class="text-uppercase font-xs text-muted mtop0 mbot5"><i class="fa fa-bullseye text-success"></i> Audition-to-Booking %</p>
                                            <p class="text-success bold font-medium-xs mbot0"><?php echo $summary['conversion_rate']; ?>%</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="top_stats_wrapper text-center p15 bg-light border">
                                            <p class="text-uppercase font-xs text-muted mtop0 mbot5"><i class="fa fa-hourglass-half text-warning"></i> Active Pipeline Value</p>
                                            <p class="text-warning bold font-medium-xs mbot0"><?php echo ckm_format_money($summary['pipeline_value']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="top_stats_wrapper text-center p15 bg-light border">
                                            <p class="text-uppercase font-xs text-muted mtop0 mbot5"><i class="fa fa-trophy text-primary"></i> Net Lifetime Earnings</p>
                                            <p class="text-primary bold font-medium-xs mbot0"><?php echo ckm_format_money($summary['net_revenue']); ?></p>
                                        </div>
                                    </div>
                                </div>

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
                                                <table class="table table-bordered font-xs">
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

                                    <!-- Voice Actor Business Expenses & Gear Ledger -->
                                    <div class="col-md-6">
                                        <div class="panel panel-default">
                                            <div class="panel-heading bold display-flex justify-between align-center">
                                                <span><i class="fa fa-shopping-cart text-warning"></i> VO Studio Expenses & Deductions</span>
                                                <button type="button" class="btn btn-default btn-xs" onclick="$('#expense_modal').modal('show');">
                                                    <i class="fa fa-plus"></i> Log Expense
                                                </button>
                                            </div>
                                            <div class="panel-body p10">
                                                <div class="display-flex justify-between font-xs mbot10">
                                                    <span>Year-to-Date Deductions: <strong class="text-danger"><?php echo ckm_format_money($expense_summary['year_expenses']); ?></strong></span>
                                                    <span>Total All-Time: <strong class="text-muted"><?php echo ckm_format_money($expense_summary['total_expenses']); ?></strong></span>
                                                </div>
                                                <div class="table-responsive" style="max-height: 220px; overflow-y: auto;">
                                                    <table class="table table-bordered table-striped font-xs">
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Category</th>
                                                                <th>Item</th>
                                                                <th>Amount</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($expenses)) { ?>
                                                                <?php foreach ($expenses as $exp) { ?>
                                                                    <tr>
                                                                        <td><?php echo _d($exp['expense_date']); ?></td>
                                                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($exp['category']); ?></span></td>
                                                                        <td><?php echo htmlspecialchars($exp['description']); ?></td>
                                                                        <td class="bold text-danger"><?php echo ckm_format_money($exp['amount']); ?></td>
                                                                        <td class="text-center">
                                                                            <a href="<?php echo admin_url('ckm_talent_pipeline/delete_expense/' . $exp['id']); ?>" class="text-danger _delete"><i class="fa fa-trash"></i></a>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <tr><td colspan="5" class="text-center text-muted">No studio expenses logged yet.</td></tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- TAB 3: EXPIRING BUYOUTS & RENEWALS RADAR -->
                            <div role="tabpanel" class="tab-pane <?php echo ($active_tab == 'buyouts') ? 'active' : ''; ?>" id="tab_buyouts">
                                <div class="alert alert-warning">
                                    <i class="fa fa-clock-o"></i> <strong>Passive Renewal Engine:</strong> These commercial and corporate buyout licenses are expiring within the next 90 days. Click <strong>"Pitch Buyout Extension"</strong> to draft a personalized renewal email.
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
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-warning btn-xs" onclick="open_buyout_pitch_modal(<?php echo $lic['id']; ?>);" title="Draft & Send Renewal Pitch">
                                                                    <i class="fa fa-envelope-o"></i> Pitch Renewal
                                                                </button>
                                                                <a href="<?php echo admin_url('ckm_talent_pipeline/duplicate/' . $lic['id']); ?>" class="btn btn-default btn-xs" title="Duplicate into New Quote">
                                                                    <i class="fa fa-clone"></i>
                                                                </a>
                                                            </div>
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

                            <!-- TAB 4: SETTINGS, STUDIO PROFILE & MAILBOX MONITOR -->
                            <div role="tabpanel" class="tab-pane <?php echo ($active_tab == 'crm') ? 'active' : ''; ?>" id="tab_crm">
                                <!-- Voice Actor Home Studio Profile & Tech Specs -->
                                <div class="row mbot20">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading bold"><i class="fa fa-microphone text-info"></i> Voice Actor Studio Profile & Tech Specs</div>
                                            <div class="panel-body">
                                                <?php echo form_open(admin_url('ckm_talent_pipeline/save_studio_profile')); ?>
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label class="control-label bold">Performer / Stage Name:</label>
                                                        <input type="text" name="ckm_tp_actor_name" class="form-control input-sm" value="<?php echo htmlspecialchars(get_option('ckm_tp_actor_name') ?: (get_option('companyname') ?: 'Voice Actor')); ?>">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label bold">Studio Email:</label>
                                                        <input type="email" name="ckm_tp_actor_email" class="form-control input-sm" value="<?php echo htmlspecialchars(get_option('ckm_tp_actor_email')); ?>">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label bold">Source-Connect ID:</label>
                                                        <input type="text" name="ckm_tp_source_connect_id" class="form-control input-sm" value="<?php echo htmlspecialchars(get_option('ckm_tp_source_connect_id')); ?>" placeholder="e.g. your_sc_handle">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label bold">Cleanfeed Pro Link:</label>
                                                        <input type="text" name="ckm_tp_cleanfeed_link" class="form-control input-sm" value="<?php echo htmlspecialchars(get_option('ckm_tp_cleanfeed_link')); ?>" placeholder="https://cleanfeed.net/...">
                                                    </div>
                                                </div>
                                                <div class="row mtop10">
                                                    <div class="col-md-6">
                                                        <label class="control-label bold">Microphone & Preamp Chain:</label>
                                                        <input type="text" name="ckm_tp_mic_chain" class="form-control input-sm" value="<?php echo htmlspecialchars(get_option('ckm_tp_mic_chain')); ?>" placeholder="e.g. Sennheiser MKH 416 / Neumann TLM 103 -> Apollo Twin X">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="control-label bold">DAW & Acoustic Booth Treatment:</label>
                                                        <input type="text" name="ckm_tp_daw_booth" class="form-control input-sm" value="<?php echo htmlspecialchars(get_option('ckm_tp_daw_booth')); ?>" placeholder="e.g. Reaper / Pro Tools | Custom Acoustic Isolation Booth (-62dB Noise Floor)">
                                                    </div>
                                                </div>
                                                <div class="row mtop10">
                                                    <div class="col-md-12">
                                                        <label class="control-label bold">Standard Pickup Policy Statement:</label>
                                                        <input type="text" name="ckm_tp_default_free_revisions" class="form-control input-sm" value="<?php echo htmlspecialchars(get_option('ckm_tp_default_free_revisions')); ?>">
                                                    </div>
                                                </div>
                                                <div class="mtop15 text-right">
                                                    <button type="submit" class="btn btn-primary btn-sm bold"><i class="fa fa-save"></i> Save Studio Profile</button>
                                                </div>
                                                <?php echo form_close(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Agency Roster & Multi-Agent Representation -->
                                <div class="row mbot20">
                                    <div class="col-md-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading bold display-flex justify-between align-center">
                                                <span><i class="fa fa-users text-primary"></i> Agency Roster & Multi-Agent Representation</span>
                                                <button type="button" class="btn btn-default btn-xs" onclick="$('#agent_modal').modal('show');">
                                                    <i class="fa fa-plus"></i> Add Agency / Agent
                                                </button>
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped font-xs">
                                                        <thead>
                                                            <tr>
                                                                <th>Agency Name</th>
                                                                <th>Agent / Contact</th>
                                                                <th>Territory / Division</th>
                                                                <th>Commission %</th>
                                                                <th>Payment Terms</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php if (!empty($agents)) { ?>
                                                                <?php foreach ($agents as $ag) { ?>
                                                                    <tr>
                                                                        <td class="bold"><?php echo htmlspecialchars($ag['agency_name']); ?></td>
                                                                        <td><?php echo htmlspecialchars($ag['name']); ?> (<?php echo htmlspecialchars($ag['email'] ?: '-'); ?>)</td>
                                                                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($ag['territory']); ?></span></td>
                                                                        <td class="bold text-primary"><?php echo $ag['commission_percent']; ?>%</td>
                                                                        <td><?php echo htmlspecialchars($ag['payment_terms']); ?></td>
                                                                        <td class="text-center">
                                                                            <a href="<?php echo admin_url('ckm_talent_pipeline/delete_agent/' . $ag['id']); ?>" class="text-danger _delete"><i class="fa fa-trash"></i></a>
                                                                        </td>
                                                                    </tr>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <tr><td colspan="6" class="text-center text-muted">No agencies configured. Direct submissions default to 0% commission.</td></tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

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
                                                        <label class="control-label bold">Port:</label>
                                                        <input type="text" name="imap_port" class="form-control" value="<?php echo get_option('ckm_talent_imap_port') ?: '993'; ?>">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="control-label bold">Encryption:</label>
                                                        <select name="imap_encryption" class="form-control">
                                                            <?php $cur_enc = strtolower(get_option('ckm_talent_imap_encryption') ?: 'ssl'); ?>
                                                            <option value="ssl" <?php echo $cur_enc === 'ssl' ? 'selected' : ''; ?>>SSL (993)</option>
                                                            <option value="tls" <?php echo $cur_enc === 'tls' ? 'selected' : ''; ?>>TLS (143/993)</option>
                                                            <option value="notls" <?php echo $cur_enc === 'notls' ? 'selected' : ''; ?>>None / Plain</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="row mtop15">
                                                    <div class="col-md-3">
                                                        <label class="control-label bold">Email Search Scope:</label>
                                                        <select name="imap_search_mode" class="form-control">
                                                            <?php $smode = get_option('ckm_talent_imap_search_mode') ?: 'unseen_and_recent'; ?>
                                                            <option value="unseen_and_recent" <?php echo $smode === 'unseen_and_recent' ? 'selected' : ''; ?>>Unread &amp; Recent Emails (Recommended)</option>
                                                            <option value="unseen_only" <?php echo $smode === 'unseen_only' ? 'selected' : ''; ?>>Unread Emails Only (UNSEEN)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label bold">Content Filter Mode:</label>
                                                        <select name="ingest_filter_mode" class="form-control">
                                                            <?php $fmode = get_option('ckm_talent_ingest_filter_mode') ?: 'keywords'; ?>
                                                            <option value="keywords" <?php echo $fmode === 'keywords' ? 'selected' : ''; ?>>Smart Keyword Match (Casting, VO, BSF, Buyouts)</option>
                                                            <option value="all" <?php echo $fmode === 'all' ? 'selected' : ''; ?>>Ingest ALL Inbound Emails to this Box</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 ptop20">
                                                        <div class="checkbox checkbox-primary">
                                                            <input type="checkbox" name="auto_ingest_enabled" id="auto_ingest_enabled" value="1" <?php echo (get_option('ckm_talent_auto_ingest_enabled') === '' || (int)get_option('ckm_talent_auto_ingest_enabled') === 1) ? 'checked' : ''; ?>>
                                                            <label for="auto_ingest_enabled" class="bold">Auto-Ingest Active (Cron + Live)</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 ptop20">
                                                        <div class="checkbox checkbox-success">
                                                            <input type="checkbox" name="auto_convert_to_jobs" id="auto_convert_to_jobs" value="1" <?php echo ((int)get_option('ckm_talent_auto_convert_to_jobs') === 1) ? 'checked' : ''; ?>>
                                                            <label for="auto_convert_to_jobs" class="bold">Auto-Convert to Active Jobs</label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mtop15 display-flex justify-between align-center">
                                                    <div>
                                                        <a href="<?php echo admin_url('ckm_talent_pipeline/check_inbox'); ?>" class="btn btn-default btn-sm">
                                                            <i class="fa fa-refresh"></i> Check Inbox Now
                                                        </a>
                                                        <a href="<?php echo admin_url('ckm_talent_pipeline/test_webhook_simulator'); ?>" class="btn btn-default btn-sm mleft5" title="Generate simulated casting call">
                                                            <i class="fa fa-magic text-warning"></i> Simulate Casting Email
                                                        </a>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary btn-sm bold">
                                                        <i class="fa fa-save"></i> Save IMAP Settings
                                                    </button>
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
                                                                 <a href="<?php echo admin_url('ckm_talent_pipeline/simulate_webhook'); ?>" class="btn btn-info">
                                                                     <i class="fa fa-paper-plane"></i> Send Test Inbound Ping
                                                                 </a>
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
<?php include(__DIR__ . '/modals/script_teleprompter_modal.php'); ?>
<?php include(__DIR__ . '/modals/buyout_pitch_modal.php'); ?>
<?php include(__DIR__ . '/modals/studio_tech_specs_modal.php'); ?>
<?php include(__DIR__ . '/modals/ai_rider_modal.php'); ?>
<?php include(__DIR__ . '/modals/file_namer_modal.php'); ?>
<?php include(__DIR__ . '/modals/revisions_modal.php'); ?>
<?php include(__DIR__ . '/modals/audition_nudge_modal.php'); ?>
<?php include(__DIR__ . '/modals/agent_modal.php'); ?>
<?php include(__DIR__ . '/modals/expense_modal.php'); ?>

<?php init_tail(); ?>
