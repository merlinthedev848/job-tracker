<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="talent_loss_reason_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa-solid fa-circle-xmark text-danger"></i> <?php echo _l('ckm_tp_mark_as_lost'); ?></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="loss_job_id" value="">
                <input type="hidden" id="loss_target_status" value="lost">

                <div class="form-group">
                    <label for="loss_reason_id" class="control-label"><?php echo _l('ckm_tp_loss_reason'); ?></label>
                    <select id="loss_reason_id" class="form-control selectpicker" data-width="100%">
                        <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                        <?php foreach ($loss_reasons as $reason) { ?>
                            <option value="<?php echo $reason['id']; ?>"><?php echo htmlspecialchars($reason['reason']); ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="loss_notes" class="control-label"><?php echo _l('ckm_tp_loss_notes'); ?></label>
                    <textarea id="loss_notes" class="form-control" rows="3" placeholder="Optional casting feedback or rate notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-danger" onclick="confirm_loss_status();"><?php echo _l('confirm'); ?></button>
            </div>
        </div>
    </div>
</div>
