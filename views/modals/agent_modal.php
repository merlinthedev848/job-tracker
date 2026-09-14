<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Add / Edit Agency Representation Modal -->
<div class="modal fade" id="agent_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <?php echo form_open(admin_url('ckm_talent_pipeline/save_agent'), ['class' => 'no-unsaved-warning']); ?>
        <input type="hidden" name="id" id="agent_profile_id" value="">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-user-plus text-primary"></i> Add Talent Agency / Representative</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="control-label bold">Agency Name:</label>
                    <input type="text" name="agency_name" id="agent_agency_name" class="form-control input-sm" placeholder="e.g. Apex Talent Agency" required>
                </div>
                <div class="form-group">
                    <label class="control-label bold">Agent / Contact Person:</label>
                    <input type="text" name="name" id="agent_rep_name" class="form-control input-sm" placeholder="e.g. Sarah Jenkins" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Agent Email:</label>
                            <input type="email" name="email" id="agent_email" class="form-control input-sm" placeholder="sarah@apextalent.example">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Agent Phone:</label>
                            <input type="text" name="phone" id="agent_phone" class="form-control input-sm" placeholder="+44 20 7946 0123">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Territory / Division:</label>
                            <input type="text" name="territory" id="agent_territory" class="form-control input-sm" value="Commercial (UK / Europe)">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Commission Rate (%):</label>
                            <input type="number" step="0.01" name="commission_percent" id="agent_commission_pct" class="form-control input-sm" value="15.00" required>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label bold">Payment Terms / Remittance Flow:</label>
                    <select name="payment_terms" id="agent_payment_terms" class="form-control input-sm">
                        <option value="Direct Client Remittance">Direct Client Remittance (Client pays actor, actor pays agent)</option>
                        <option value="Agency Invoices & Remits">Agency Invoices & Remits (Agent collects full fee, remits net to actor)</option>
                        <option value="Paymaster / Payroll">Union Paymaster / Third-Party Payroll</option>
                    </select>
                </div>
                <div class="form-group mbot0">
                    <label class="control-label bold">Internal Notes:</label>
                    <textarea name="notes" id="agent_notes" class="form-control input-sm" rows="2" placeholder="Submission guidelines, slating preferences, contracts..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary bold"><i class="fa fa-save"></i> Save Agency Profile</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
