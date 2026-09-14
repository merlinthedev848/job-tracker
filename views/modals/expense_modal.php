<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Add / Edit Voice Actor Expense Modal -->
<div class="modal fade" id="expense_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <?php echo form_open(admin_url('ckm_talent_pipeline/save_expense')); ?>
        <input type="hidden" name="id" id="expense_id" value="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-shopping-cart text-warning"></i> Log Studio & Business Expense</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label bold">Expense Description:</label>
                    <input type="text" name="description" id="exp_desc" class="form-control input-sm" placeholder="e.g. iZotope RX 10 Audio Repair Suite / WhisperRoom Booth Service" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Category:</label>
                            <select name="category" id="exp_cat" class="form-control input-sm">
                                <option value="Studio Equipment">Studio Equipment & Microphones</option>
                                <option value="DAW & Software">DAW, Plugins & Software</option>
                                <option value="Coaching & Training">Voice Coaching & Training</option>
                                <option value="Union Dues">Union Dues (SAG-AFTRA / Equity)</option>
                                <option value="Website & Marketing">Website, Demos & Marketing</option>
                                <option value="Travel & Mileage">Travel & Studio Commute</option>
                                <option value="Subscriptions">P2P Subscriptions (Voice123/Spotlight)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Amount (<?php echo get_base_currency()->symbol; ?>):</label>
                            <input type="number" step="0.01" name="amount" id="exp_amount" class="form-control input-sm" placeholder="0.00" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Date Incurred:</label>
                            <input type="date" name="expense_date" id="exp_date" class="form-control input-sm" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6 ptop25">
                        <div class="checkbox checkbox-primary mtop5">
                            <input type="checkbox" name="tax_deductible" id="exp_tax_deductible" value="1" checked>
                            <label for="exp_tax_deductible" class="bold">Tax Deductible Business Expense</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary bold"><i class="fa fa-save"></i> Save Expense</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
