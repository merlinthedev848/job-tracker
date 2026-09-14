<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<?php
if (!function_exists('ckm_format_money')) {
    function ckm_format_money($amount) {
        $amount = (float)$amount;
        if (function_exists('get_base_currency') && function_exists('app_format_money')) {
            try {
                $currency = get_base_currency();
                if ($currency) {
                    return app_format_money($amount, $currency);
                }
            } catch (Exception $e) {} catch (Throwable $t) {}
        }
        return '$' . number_format($amount, 2);
    }
}
?>

<div id="wrapper">
    <div class="content">
        <!-- Top Metrics Row -->
        <div class="row mbot15">
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa fa-microphone text-info"></i> Total Auditions / Quotes</p>
                    <p class="text-muted bold font-medium-xs"><?php echo isset($summary['total_auditions']) ? $summary['total_auditions'] : 0; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa fa-bullseye text-success"></i> Audition-to-Booking %</p>
                    <p class="text-success bold font-medium-xs"><?php echo isset($summary['conversion_rate']) ? $summary['conversion_rate'] : 0; ?>%</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa fa-hourglass-half text-warning"></i> Active Pipeline Value</p>
                    <p class="text-warning bold font-medium-xs"><?php echo ckm_format_money(isset($summary['pipeline_value']) ? $summary['pipeline_value'] : 0); ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa fa-money text-primary"></i> Net Booked Revenue</p>
                    <p class="text-primary bold font-medium-xs"><?php echo ckm_format_money(isset($summary['net_revenue']) ? $summary['net_revenue'] : 0); ?></p>
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
                                <a href="#tab_settings" aria-controls="tab_settings" role="tab" data-toggle="tab">
                                    <i class="fa fa-sliders"></i> <strong>Settings & Lookups</strong>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- TAB 1: PIPELINE BOARD -->
                            <div role="tabpanel" class="tab-pane active" id="tab_pipeline">
                                <div class="_buttons mbot20 display-flex justify-between align-center">
                                    <div>
                                        <button type="button" class="btn btn-primary" onclick="new_talent_job();">
                                            <i class="fa fa-plus"></i> <?php echo _l('ckm_tp_new_job'); ?>
                                        </button>
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

                                <?php if ($view_mode == 'kanban') { ?>
                                    <?php include(__DIR__ . '/kanban.php'); ?>
                                <?php } else { ?>
                                    <?php include(__DIR__ . '/list.php'); ?>
                                <?php } ?>
                            </div>

                            <!-- TAB 2: ANALYTICS & REPORTS -->
                            <div role="tabpanel" class="tab-pane" id="tab_analytics">
                                <div class="row">
                                    <!-- Agent / Source Scorecard -->
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

                                    <!-- Revenue by Category -->
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

                                <!-- Loss Reasons Post-Mortem -->
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

                            <!-- TAB 3: SETTINGS & LOOKUPS -->
                            <div role="tabpanel" class="tab-pane" id="tab_settings">
                                <div class="row">
                                    <!-- Categories List -->
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

                                    <!-- Sources List -->
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

                                    <!-- Loss Reasons List -->
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

<!-- Load Modals via direct include -->
<?php include(__DIR__ . '/modals/job_modal.php'); ?>
<?php include(__DIR__ . '/modals/loss_reason_modal.php'); ?>

<?php init_tail(); ?>
