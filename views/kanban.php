<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php
$pipeline_columns = [
    'quote_sent'   => ['name' => _l('ckm_tp_status_quote_sent'), 'color' => '#0288d1', 'icon' => 'fa-paper-plane-o'],
    'shortlisted'  => ['name' => _l('ckm_tp_status_shortlisted'), 'color' => '#fbc02d', 'icon' => 'fa-star-o'],
    'won'          => ['name' => _l('ckm_tp_status_won'), 'color' => '#43a047', 'icon' => 'fa-trophy'],
    'in_progress'  => ['name' => _l('ckm_tp_status_in_progress'), 'color' => '#8e24aa', 'icon' => 'fa-microphone'],
    'delivered'    => ['name' => _l('ckm_tp_status_delivered'), 'color' => '#fb8c00', 'icon' => 'fa-file-audio-o'],
    'completed'    => ['name' => _l('ckm_tp_status_completed'), 'color' => '#00897b', 'icon' => 'fa-check-circle'],
    'lost'         => ['name' => _l('ckm_tp_status_lost'), 'color' => '#757575', 'icon' => 'fa-times-circle'],
];

// Group jobs by status
$grouped_jobs = [];
foreach ($pipeline_columns as $status_key => $column_data) {
    $grouped_jobs[$status_key] = [];
}
if (!empty($jobs)) {
    foreach ($jobs as $job) {
        $st = $job['status'];
        if (isset($grouped_jobs[$st])) {
            $grouped_jobs[$st][] = $job;
        } else {
            $grouped_jobs['quote_sent'][] = $job;
        }
    }
}
?>

<div class="ckm-kanban-board">
    <div class="ckm-kanban-row">
        <?php foreach ($pipeline_columns as $status_key => $col) { ?>
            <div class="ckm-kanban-col" data-status="<?php echo $status_key; ?>">
                <!-- Column Header with + Quick Add Button -->
                <div class="ckm-kanban-col-header" style="border-top: 4px solid <?php echo $col['color']; ?>;">
                    <div class="display-flex justify-between align-center">
                        <span class="bold font-medium">
                            <i class="fa <?php echo $col['icon']; ?>" style="color: <?php echo $col['color']; ?>"></i> 
                            <?php echo $col['name']; ?>
                        </span>
                        <div class="display-flex align-center">
                            <span class="badge mright5" style="background-color: <?php echo $col['color']; ?>;">
                                <?php echo count($grouped_jobs[$status_key]); ?>
                            </span>
                            <button type="button" class="btn btn-default btn-xs ckm-col-add-btn" onclick="quick_add_in_column('<?php echo $status_key; ?>');" title="Quick Add in this stage">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Column Body / Drop Zone -->
                <div class="ckm-kanban-cards-container" id="kanban-col-<?php echo $status_key; ?>" data-status="<?php echo $status_key; ?>">
                    <?php if (!empty($grouped_jobs[$status_key])) { ?>
                        <?php foreach ($grouped_jobs[$status_key] as $job) { 
                            // Urgency calculations
                            $urgency_class = '';
                            $urgency_label = '';
                            if (!empty($job['delivery_deadline']) && !in_array($job['status'], ['completed', 'lost'])) {
                                $diff_days = round((strtotime($job['delivery_deadline']) - time()) / (60 * 60 * 24));
                                if ($diff_days < 0) {
                                    $urgency_class = 'ckm-urgency-overdue';
                                    $urgency_label = 'Overdue';
                                } elseif ($diff_days == 0) {
                                    $urgency_class = 'ckm-urgency-today';
                                    $urgency_label = 'Due Today';
                                } elseif ($diff_days <= 2) {
                                    $urgency_class = 'ckm-urgency-soon';
                                    $urgency_label = 'Due in ' . $diff_days . 'd';
                                }
                            }
                        ?>
                            <div class="ckm-card <?php echo $urgency_class; ?>" 
                                 data-job-id="<?php echo $job['id']; ?>"
                                 data-category-id="<?php echo $job['category_id'] ?: '0'; ?>"
                                 data-search="<?php echo htmlspecialchars(strtolower($job['job_title'] . ' ' . $job['client_company'] . ' ' . $job['agent_name'] . ' ' . $job['role_name'])); ?>">
                                
                                <!-- Card Header: Category & Options -->
                                <div class="display-flex justify-between align-center mbot8">
                                    <div class="display-flex align-center flex-wrap gap-5">
                                        <?php if (!empty($job['category_name'])) { ?>
                                            <span class="badge ckm-cat-pill" style="background-color: <?php echo $job['category_color'] ?: '#03a9f4'; ?>;">
                                                <?php echo htmlspecialchars($job['category_name']); ?>
                                            </span>
                                        <?php } ?>

                                        <?php if (!empty($urgency_label)) { ?>
                                            <span class="label label-danger font-xs bold"><i class="fa fa-clock-o"></i> <?php echo $urgency_label; ?></span>
                                        <?php } ?>
                                    </div>

                                    <div class="dropdown">
                                        <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li><a href="#" onclick="edit_talent_job(<?php echo $job['id']; ?>); return false;"><i class="fa fa-pencil"></i> <?php echo _l('edit'); ?></a></li>
                                            <?php if (empty($job['perfex_invoice_id']) && !in_array($job['status'], ['lost'])) { ?>
                                                <li><a href="<?php echo admin_url('ckm_talent_pipeline/convert_to_invoice/' . $job['id']); ?>"><i class="fa fa-file-text-o text-success"></i> <?php echo _l('ckm_tp_convert_to_invoice'); ?></a></li>
                                            <?php } elseif (!empty($job['perfex_invoice_id'])) { ?>
                                                <li><a href="<?php echo admin_url('invoices/invoice/' . $job['perfex_invoice_id']); ?>"><i class="fa fa-file-text"></i> <?php echo _l('ckm_tp_view_invoice'); ?></a></li>
                                            <?php } ?>
                                            <li class="divider"></li>
                                            <li><a href="<?php echo admin_url('ckm_talent_pipeline/delete/' . $job['id']); ?>" class="text-danger _delete"><i class="fa fa-trash"></i> <?php echo _l('delete'); ?></a></li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Title -->
                                <h4 class="ckm-card-title mtop0 mbot5 font-medium bold">
                                    <a href="#" onclick="edit_talent_job(<?php echo $job['id']; ?>); return false;">
                                        <?php echo htmlspecialchars($job['job_title']); ?>
                                    </a>
                                </h4>

                                <!-- Client & Agent -->
                                <div class="text-muted font-xs mbot5">
                                    <?php if (!empty($job['client_company'])) { ?>
                                        <span><i class="fa fa-building-o"></i> <?php echo htmlspecialchars($job['client_company']); ?></span>
                                    <?php } ?>
                                    <?php if (!empty($job['agent_name'])) { ?>
                                        <span class="mleft5"><i class="fa fa-user-secret"></i> <?php echo htmlspecialchars($job['agent_name']); ?></span>
                                    <?php } elseif (!empty($job['source_name'])) { ?>
                                        <span class="mleft5"><i class="fa fa-tag"></i> <?php echo htmlspecialchars($job['source_name']); ?></span>
                                    <?php } ?>
                                </div>

                                <!-- Role, Word Count & Audio Take -->
                                <div class="font-xs mbot8 text-dark display-flex justify-between align-center flex-wrap">
                                    <div>
                                        <?php if (!empty($job['role_name'])) { ?>
                                            <strong>Role:</strong> <?php echo htmlspecialchars($job['role_name']); ?>
                                        <?php } ?>
                                        <?php if (!empty($job['word_count'])) { ?>
                                            <span class="label label-default mleft5"><?php echo number_format($job['word_count']); ?>w</span>
                                        <?php } ?>
                                    </div>

                                    <?php if (!empty($job['audio_link'])) { ?>
                                        <a href="<?php echo htmlspecialchars($job['audio_link']); ?>" target="_blank" class="btn btn-xs btn-info" title="Play Audition Take / Audio File">
                                            <i class="fa fa-play-circle"></i> Audio
                                        </a>
                                    <?php } ?>
                                </div>

                                <!-- Directed Session Badge -->
                                <?php if (!empty($job['direction_type']) && $job['direction_type'] != 'Self-Record') { ?>
                                    <div class="mbot8">
                                        <span class="label label-primary font-xs">
                                            <i class="fa fa-bolt"></i> <?php echo htmlspecialchars($job['direction_type']); ?>
                                            <?php if (!empty($job['direction_link'])) { ?>
                                                <a href="<?php echo htmlspecialchars($job['direction_link']); ?>" target="_blank" class="text-white bold mleft5"><i class="fa fa-external-link"></i></a>
                                            <?php } ?>
                                        </span>
                                    </div>
                                <?php } ?>

                                <!-- Loss Reason if lost -->
                                <?php if ($job['status'] == 'lost' && !empty($job['loss_reason_name'])) { ?>
                                    <div class="alert alert-warning font-xs p4 mbot5">
                                        <i class="fa fa-info-circle"></i> <?php echo htmlspecialchars($job['loss_reason_name']); ?>
                                    </div>
                                <?php } ?>

                                <!-- Hover Quick-Action Bar -->
                                <div class="ckm-card-quick-actions">
                                    <?php if ($job['status'] != 'won') { ?>
                                        <button type="button" class="btn btn-xs btn-success" onclick="quick_change_card_status(<?php echo $job['id']; ?>, 'won');" title="Mark as Won">
                                            <i class="fa fa-trophy"></i> Won
                                        </button>
                                    <?php } ?>
                                    <?php if ($job['status'] != 'in_progress') { ?>
                                        <button type="button" class="btn btn-xs btn-info" onclick="quick_change_card_status(<?php echo $job['id']; ?>, 'in_progress');" title="Start Production">
                                            <i class="fa fa-microphone"></i> Record
                                        </button>
                                    <?php } ?>
                                    <?php if ($job['status'] != 'lost') { ?>
                                        <button type="button" class="btn btn-xs btn-default text-danger" onclick="trigger_loss_modal(<?php echo $job['id']; ?>);" title="Mark as Lost">
                                            <i class="fa fa-times"></i> Lost
                                        </button>
                                    <?php } ?>
                                </div>

                                <!-- Card Footer: Rate & Invoicing Status -->
                                <div class="ckm-card-footer display-flex justify-between align-center mtop10 ptop8">
                                    <div>
                                        <span class="bold text-success font-medium">
                                            <?php echo ckm_format_money($job['total_amount']); ?>
                                        </span>
                                        <?php if ($job['commission_percent'] > 0) { ?>
                                            <small class="text-muted block font-xs">
                                                Net: <?php echo ckm_format_money($job['net_amount']); ?> (-<?php echo $job['commission_percent']; ?>%)
                                            </small>
                                        <?php } ?>
                                    </div>

                                    <div>
                                        <?php if (!empty($job['perfex_invoice_id'])) { ?>
                                            <span class="label label-success"><i class="fa fa-check"></i> Invoiced</span>
                                        <?php } elseif (in_array($job['status'], ['won', 'completed'])) { ?>
                                            <a href="<?php echo admin_url('ckm_talent_pipeline/convert_to_invoice/' . $job['id']); ?>" class="btn btn-success btn-xs" title="Convert to Invoice">
                                                <i class="fa fa-file-text-o"></i> Bill
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <div class="ckm-empty-col text-center text-muted p15 font-xs">
                            <span>No jobs in this stage</span>
                            <button type="button" class="btn btn-default btn-xs block mtop10 center-block" onclick="quick_add_in_column('<?php echo $status_key; ?>');">
                                <i class="fa fa-plus"></i> Add
                            </button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
