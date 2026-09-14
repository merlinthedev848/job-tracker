<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="table-responsive">
    <table class="table dt-table table-talent-jobs" data-order-col="8" data-order-type="desc">
        <thead>
            <tr>
                <th>#</th>
                <th><?php echo _l('ckm_tp_job_title'); ?></th>
                <th><?php echo _l('ckm_tp_client'); ?></th>
                <th><?php echo _l('ckm_tp_category'); ?></th>
                <th><?php echo _l('ckm_tp_source'); ?></th>
                <th>Role / Specs</th>
                <th>Gross Total</th>
                <th>Net Total</th>
                <th>Status</th>
                <th>Invoiced?</th>
                <th><?php echo _l('options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($jobs)) { ?>
                <?php foreach ($jobs as $job) { ?>
                    <tr>
                        <td><?php echo $job['id']; ?></td>
                        <td>
                            <a href="#" onclick="edit_talent_job(<?php echo $job['id']; ?>); return false;" class="bold">
                                <?php echo htmlspecialchars($job['job_title']); ?>
                            </a>
                            <?php if (!empty($job['audio_link'])) { ?>
                                <a href="<?php echo htmlspecialchars($job['audio_link']); ?>" target="_blank" class="text-info mleft5" title="Listen Audio Take"><i class="fa fa-play-circle"></i></a>
                            <?php } ?>
                        </td>
                        <td><?php echo htmlspecialchars($job['client_company'] ?: ($job['agent_name'] ?: '-')); ?></td>
                        <td>
                            <?php if (!empty($job['category_name'])) { ?>
                                <span class="badge" style="background-color: <?php echo $job['category_color'] ?: '#03a9f4'; ?>;">
                                    <?php echo htmlspecialchars($job['category_name']); ?>
                                </span>
                            <?php } ?>
                        </td>
                        <td><?php echo htmlspecialchars($job['source_name'] ?: '-'); ?></td>
                        <td>
                            <?php echo htmlspecialchars($job['role_name'] ?: ''); ?>
                            <?php if (!empty($job['revisions_count']) && $job['revisions_count'] > 0) { ?>
                                <span class="badge bg-warning text-dark mleft5" style="cursor: pointer;" onclick="open_revisions_modal(<?php echo $job['id']; ?>, '<?php echo htmlspecialchars(addslashes($job['job_title'])); ?>');" title="Pickup Rounds">
                                    <i class="fa fa-refresh"></i> <?php echo $job['revisions_count']; ?>
                                </span>
                            <?php } ?>
                            <?php if (!empty($job['audio_specs'])) { ?>
                                <small class="text-muted block font-xs"><?php echo htmlspecialchars($job['audio_specs']); ?></small>
                            <?php } ?>
                        </td>
                        <td class="bold"><?php echo ckm_format_money($job['total_amount']); ?></td>
                        <td class="text-success bold"><?php echo ckm_format_money($job['net_amount']); ?></td>
                        <td>
                            <span class="label label-default ckm-status-<?php echo $job['status']; ?>">
                                <?php echo _l('ckm_tp_status_' . $job['status']); ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($job['perfex_invoice_id'])) { ?>
                                <a href="<?php echo admin_url('invoices/invoice/' . $job['perfex_invoice_id']); ?>" class="label label-success">
                                    #<?php echo $job['perfex_invoice_id']; ?>
                                </a>
                            <?php } else { ?>
                                <span class="text-muted font-xs">No</span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-default btn-xs" onclick="open_teleprompter_for_job(<?php echo $job['id']; ?>, '<?php echo htmlspecialchars(addslashes($job['job_title'])); ?>', <?php echo json_encode($job['script_text'] ?? $job['notes'] ?? ''); ?>, <?php echo json_encode($job['take_notes'] ?? ''); ?>);" title="Teleprompter & Take Timer">
                                    <i class="fa fa-microphone text-primary"></i>
                                </button>
                                <button class="btn btn-default btn-xs" onclick="open_revisions_modal(<?php echo $job['id']; ?>, '<?php echo htmlspecialchars(addslashes($job['job_title'])); ?>');" title="Pickups & Revisions">
                                    <i class="fa fa-refresh text-warning"></i>
                                </button>
                                <button class="btn btn-default btn-xs" onclick="edit_talent_job(<?php echo $job['id']; ?>); return false;" title="<?php echo _l('edit'); ?>">
                                    <i class="fa fa-pencil"></i>
                                </button>
                                <a href="<?php echo admin_url('ckm_talent_pipeline/duplicate/' . $job['id']); ?>" class="btn btn-default btn-xs" title="Duplicate / Repeat Booking">
                                    <i class="fa fa-clone text-info"></i>
                                </a>
                                <?php if (empty($job['perfex_estimate_id']) && !empty($job['client_id'])) { ?>
                                    <a href="<?php echo admin_url('ckm_talent_pipeline/convert_to_estimate/' . $job['id']); ?>" class="btn btn-default btn-xs" title="Create Perfex Quote / Estimate">
                                        <i class="fa fa-file-pdf-o text-primary"></i>
                                    </a>
                                <?php } ?>
                                <?php if (empty($job['perfex_invoice_id']) && !in_array($job['status'], ['lost']) && !empty($job['client_id'])) { ?>
                                    <a href="<?php echo admin_url('ckm_talent_pipeline/convert_to_invoice/' . $job['id']); ?>" class="btn btn-success btn-xs" title="<?php echo _l('ckm_tp_convert_to_invoice'); ?>">
                                        <i class="fa fa-file-text-o"></i>
                                    </a>
                                <?php } ?>
                                <a href="<?php echo admin_url('ckm_talent_pipeline/delete/' . $job['id']); ?>" class="btn btn-danger btn-xs _delete" title="<?php echo _l('delete'); ?>">
                                    <i class="fa fa-trash"></i>
                                </a>
                            </div>
                        </td>
                </tr>
            <?php } ?>
        <?php } ?>
    </tbody>
</table>
</div>
