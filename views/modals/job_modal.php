<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="talent_job_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <?php echo form_open(admin_url('ckm_talent_pipeline/save'), ['id' => 'talent_job_form']); ?>
        <input type="hidden" name="id" id="job_id" value="">
        
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="display-flex justify-between align-center mright25">
                    <h4 class="modal-title" id="talent_job_modal_title"><?php echo _l('ckm_tp_new_job'); ?></h4>
                    <button type="button" class="btn btn-xs btn-info" onclick="$('#rate_calculator_modal').modal('show');">
                        <i class="fa fa-calculator"></i> Rate Calculator
                    </button>
                </div>
            </div>
            
            <div class="modal-body">
                <!-- Nav Tabs -->
                <ul class="nav nav-tabs mbot15" role="tablist">
                    <li role="presentation" class="active"><a href="#tab_general" aria-controls="tab_general" role="tab" data-toggle="tab"><i class="fa fa-file-text-o"></i> Project & Role</a></li>
                    <li role="presentation"><a href="#tab_financials" aria-controls="tab_financials" role="tab" data-toggle="tab"><i class="fa fa-money"></i> Rates & Commission</a></li>
                    <li role="presentation"><a href="#tab_usage" aria-controls="tab_usage" role="tab" data-toggle="tab"><i class="fa fa-copyright"></i> Usage & Buyout</a></li>
                    <li role="presentation"><a href="#tab_studio" aria-controls="tab_studio" role="tab" data-toggle="tab"><i class="fa fa-microphone"></i> Studio & Audio</a></li>
                </ul>

                <div class="tab-content">
                    <!-- Tab 1: General Project Info -->
                    <div role="tabpanel" class="tab-pane active" id="tab_general">
                        <div class="row">
                            <div class="col-md-8">
                                <?php echo render_input('job_title', 'ckm_tp_job_title', '', 'text', ['required' => true, 'placeholder' => 'e.g., Nike - Global Summer Campaign']); ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status" class="control-label">Pipeline Stage</label>
                                    <select name="status" id="status" class="selectpicker" data-width="100%">
                                        <option value="quote_sent">🔵 Quote / Audition Sent</option>
                                        <option value="shortlisted">🟡 Shortlisted / On Hold</option>
                                        <option value="won">🟢 Won / Booked</option>
                                        <option value="in_progress">🟣 In Production / Recording</option>
                                        <option value="delivered">🟠 Delivered / In Review</option>
                                        <option value="completed">✅ Completed</option>
                                        <option value="lost">⚪ Lost / Passed</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="client_id" class="control-label"><?php echo _l('ckm_tp_client'); ?></label>
                                    <select name="client_id" id="client_id" class="selectpicker" data-live-search="true" data-width="100%">
                                        <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                        <?php if (!empty($clients)) { ?>
                                            <?php foreach ($clients as $client) { ?>
                                                <option value="<?php echo $client['userid']; ?>"><?php echo htmlspecialchars($client['company']); ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input('agent_name', 'ckm_tp_agent_name', '', 'text', ['placeholder' => 'e.g., Creative Artists Agency / VO Agent']); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category_id" class="control-label"><?php echo _l('ckm_tp_category'); ?></label>
                                    <select name="category_id" id="category_id" class="selectpicker" data-width="100%">
                                        <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                        <?php if (!empty($categories)) { ?>
                                            <?php foreach ($categories as $cat) { ?>
                                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="source_id" class="control-label"><?php echo _l('ckm_tp_source'); ?></label>
                                    <select name="source_id" id="source_id" class="selectpicker" data-width="100%">
                                        <option value=""><?php echo _l('dropdown_non_selected_tex'); ?></option>
                                        <?php if (!empty($sources)) { ?>
                                            <?php foreach ($sources as $src) { ?>
                                                <option value="<?php echo $src['id']; ?>" data-commission="<?php echo $src['default_commission']; ?>">
                                                    <?php echo htmlspecialchars($src['name']); ?> (<?php echo $src['default_commission']; ?>% comm)
                                                </option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_input('role_name', 'ckm_tp_role_name', '', 'text', ['placeholder' => 'e.g., Energetic Narrator']); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('word_count', 'ckm_tp_word_count', '', 'number', ['placeholder' => 'e.g., 350']); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('duration_seconds', 'ckm_tp_duration_seconds', '', 'number', ['placeholder' => 'e.g., 30']); ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <?php echo render_textarea('notes', 'ckm_tp_notes', '', ['rows' => 3, 'placeholder' => 'Character direction, tone, pronunciation guides, script text...']); ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 2: Financials & Rates -->
                    <div role="tabpanel" class="tab-pane" id="tab_financials">
                        <div class="row">
                            <div class="col-md-4">
                                <?php echo render_input('bsf_amount', 'ckm_tp_bsf_amount', '0.00', 'number', ['step' => 'any', 'id' => 'modal_bsf']); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('usage_amount', 'ckm_tp_usage_amount', '0.00', 'number', ['step' => 'any', 'id' => 'modal_usage']); ?>
                            </div>
                            <div class="col-md-4">
                                <?php echo render_input('commission_percent', 'ckm_tp_commission_percent', '0.00', 'number', ['step' => 'any', 'id' => 'modal_commission']); ?>
                            </div>
                        </div>

                        <div class="alert alert-info display-flex justify-between align-center mtop10">
                            <div>
                                <strong>Gross Total:</strong> <span id="modal_gross_preview" class="bold font-medium">0.00</span>
                            </div>
                            <div>
                                <strong>Net Take-Home:</strong> <span id="modal_net_preview" class="bold font-medium text-success">0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 3: Usage & Buyout -->
                    <div role="tabpanel" class="tab-pane" id="tab_usage">
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('usage_medium', 'ckm_tp_usage_medium', '', 'text', ['placeholder' => 'e.g., Broadcast TV + Paid Social']); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input('usage_territory', 'ckm_tp_usage_territory', '', 'text', ['placeholder' => 'e.g., National (UK) / Worldwide']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('usage_duration', 'ckm_tp_usage_duration', '', 'text', ['placeholder' => 'e.g., 1 Year / In Perpetuity']); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_date_input('usage_expiry_date', 'ckm_tp_usage_expiry_date', ''); ?>
                                <small class="text-muted">Perfex will alert you 30 days before this date to pitch a renewal.</small>
                            </div>
                        </div>
                    </div>

                    <!-- Tab 4: Studio & Audio Specs -->
                    <div role="tabpanel" class="tab-pane" id="tab_studio">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direction_type" class="control-label">Direction Method</label>
                                    <select name="direction_type" id="direction_type" class="selectpicker" data-width="100%">
                                        <option value="Self-Record">Self-Record & Deliver</option>
                                        <option value="Cleanfeed">Cleanfeed</option>
                                        <option value="Source-Connect">Source-Connect (Standard/Now)</option>
                                        <option value="Zoom / Teams">Zoom / Microsoft Teams</option>
                                        <option value="Riverside.fm">Riverside.fm</option>
                                        <option value="SessionLinkPRO">SessionLinkPRO</option>
                                        <option value="In-Person Studio">In-Person External Studio</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input('direction_link', 'ckm_tp_direction_link', '', 'text', ['placeholder' => 'e.g., Cleanfeed link or Source-Connect ID']); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_datetime_input('session_datetime', 'ckm_tp_session_datetime', ''); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_date_input('delivery_deadline', 'ckm_tp_delivery_deadline', ''); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <?php echo render_input('audio_specs', 'ckm_tp_audio_specs', '48kHz / 24-bit Mono WAV', 'text', ['placeholder' => 'e.g., 48kHz / 24-bit WAV raw, no processing']); ?>
                            </div>
                            <div class="col-md-6">
                                <?php echo render_input('audio_link', 'Audition Audio / Take Link', '', 'text', ['placeholder' => 'e.g. Dropbox / Google Drive link to submitted MP3']); ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
