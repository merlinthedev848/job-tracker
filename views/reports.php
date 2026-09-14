<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <!-- Top Stats Summary -->
        <div class="row mbot15">
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa-solid fa-microphone text-info"></i> All-Time Auditions</p>
                    <p class="text-muted bold font-medium-xs"><?php echo $summary['total_auditions']; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa-solid fa-trophy text-success"></i> Won / Booked Jobs</p>
                    <p class="text-success bold font-medium-xs"><?php echo $summary['won_jobs']; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa-solid fa-bullseye text-primary"></i> Conversion Rate</p>
                    <p class="text-primary bold font-medium-xs"><?php echo $summary['conversion_rate']; ?>%</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="top_stats_wrapper">
                    <p class="text-uppercase mtop5"><i class="fa-solid fa-wallet text-success"></i> Net Lifetime Earnings</p>
                    <p class="text-success bold font-medium-xs"><?php echo app_format_money($summary['net_revenue'], get_base_currency()); ?></p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Source / Agency Scorecard -->
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa-solid fa-user-tie"></i> Agent & Platform Performance Scorecard</h4>
                    </div>
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
                                                <td class="text-success bold"><?php echo app_format_money($src['net_earnings'] ?: 0, get_base_currency()); ?></td>
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
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa-solid fa-chart-pie"></i> Revenue by Genre / Category</h4>
                    </div>
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
                                            <td class="text-success bold"><?php echo app_format_money($cat['total_revenue'] ?: 0, get_base_currency()); ?></td>
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
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa-solid fa-circle-xmark text-danger"></i> Why Auditions / Quotes Were Lost</h4>
                    </div>
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
</div>

<?php init_tail(); ?>
