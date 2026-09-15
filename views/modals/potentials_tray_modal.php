<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Inbound Potentials Triage Modal -->
<div class="modal fade" id="potentials_tray_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 92%; max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <div class="display-flex justify-between align-center mright20">
                    <h4 class="modal-title text-white">
                        <i class="fa fa-envelope-open-o"></i> <strong>Inbound Casting Calls & Opportunities Queue</strong>
                    </h4>
                    <div>
                        <a href="<?php echo admin_url('ckm_talent_pipeline/purge_spam_potentials'); ?>" class="btn btn-xs btn-warning font-xs mright5" title="Purge auto-replies, out-of-office, bounces and newsletter blasts">
                            <i class="fa fa-magic"></i> Purge Auto-Replies & Newsletters
                        </a>
                        <a href="<?php echo admin_url('ckm_talent_pipeline/clear_all_potentials'); ?>" class="btn btn-xs btn-danger font-xs _delete mright5" title="Dismiss all items currently in queue">
                            <i class="fa fa-trash"></i> Clear All
                        </a>
                        <a href="<?php echo admin_url('ckm_talent_pipeline/poll_inbox'); ?>" class="btn btn-xs btn-default font-xs">
                            <i class="fa fa-refresh"></i> Check Inbox Now
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-body p20">
                <?php if (!empty($potentials)) { ?>
                    <p class="text-muted font-xs mbot15">
                        The email monitor has automatically detected incoming inquiries from your mailbox. Review the extracted fields, quote with 1-click, or convert verified castings directly into active pipeline jobs.
                    </p>

                    <?php echo form_open(admin_url('ckm_talent_pipeline/bulk_potentials'), ['id' => 'bulk_potentials_form']); ?>
                    <div class="display-flex justify-between align-center mbot10 flex-wrap gap-10">
                        <div class="display-flex align-center">
                            <select name="bulk_action" class="form-control input-sm mright10" style="width: 170px;">
                                <option value="">-- Bulk Action --</option>
                                <option value="accept">Accept Selected</option>
                                <option value="dismiss">Dismiss Selected</option>
                            </select>
                            <button type="submit" class="btn btn-default btn-sm mright10">Apply</button>
                            <span class="text-muted font-xs">Showing <span id="potentials_count_display"><?php echo count($potentials); ?></span> inquiry(ies)</span>
                        </div>
                        <div>
                            <input type="text" id="potential_search_filter" class="form-control input-sm" placeholder="🔍 Search sender, project, role..." style="width: 250px;" onkeyup="filter_potentials_table(this.value);">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="potentials_data_table">
                            <thead>
                                <tr class="active">
                                    <th width="30" class="text-center"><input type="checkbox" id="select_all_potentials" onclick="$('.pot-checkbox').prop('checked', this.checked);"></th>
                                    <th>Received</th>
                                    <th>Sender</th>
                                    <th>Project Title</th>
                                    <th>Role</th>
                                    <th>Word Count</th>
                                    <th>Estimated Rates</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $CI = &get_instance();
                                foreach ($potentials as $pot) { 
                                    $is_suspect = $CI->ckm_talent_pipeline_model->is_spam_or_autoreply($pot['from_name'], $pot['from_email'], $pot['subject'], $pot['raw_body']);
                                ?>
                                    <tr class="potential-row <?php echo $is_suspect ? 'bg-warning-light' : ''; ?>" data-search="<?php echo htmlspecialchars(strtolower($pot['from_name'] . ' ' . $pot['from_email'] . ' ' . $pot['parsed_title'] . ' ' . $pot['subject'] . ' ' . $pot['parsed_role'])); ?>">
                                        <td class="text-center">
                                            <input type="checkbox" name="potential_ids[]" value="<?php echo $pot['id']; ?>" class="pot-checkbox">
                                        </td>
                                        <td class="font-xs">
                                            <strong><?php echo _dt($pot['created_at']); ?></strong>
                                            <small class="text-muted block font-xs"><?php echo time_ago($pot['created_at']); ?></small>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($pot['from_name'] ?: 'Unknown Sender'); ?></strong>
                                            <small class="text-muted block font-xs"><?php echo htmlspecialchars($pot['from_email']); ?></small>
                                            <?php if ($is_suspect) { ?>
                                                <span class="label label-warning font-xs mtop5 inline-block"><i class="fa fa-exclamation-triangle"></i> Auto-Reply / Newsletter</span>
                                            <?php } ?>
                                        </td>
                                        <td class="bold text-primary">
                                            <?php echo htmlspecialchars($pot['parsed_title'] ?: $pot['subject']); ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($pot['parsed_role'])) { ?>
                                                <span class="label label-info"><?php echo htmlspecialchars($pot['parsed_role']); ?></span>
                                            <?php } else { ?>
                                                <span class="text-muted font-xs">-</span>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $pot['parsed_words'] > 0 ? number_format($pot['parsed_words']) . 'w' : '-'; ?></td>
                                        <td>
                                            <?php if ($pot['parsed_bsf'] > 0) { ?>
                                                <span class="bold text-success font-xs">
                                                     BSF: <?php echo ckm_format_money($pot['parsed_bsf']); ?>
                                                </span>
                                            <?php } else { ?>
                                                <span class="text-muted font-xs">BSF: TBD</span>
                                            <?php } ?>
                                            <?php if ($pot['parsed_usage'] > 0) { ?>
                                                <small class="text-muted block font-xs bold">Usage: <?php echo ckm_format_money($pot['parsed_usage']); ?></small>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="<?php echo admin_url('ckm_talent_pipeline/convert_potential/' . $pot['id']); ?>" class="btn btn-success btn-xs" title="Accept & Add to Pipeline">
                                                    <i class="fa fa-check"></i> Accept
                                                </a>
                                                <button type="button" class="btn btn-info btn-xs" onclick="preview_auto_quote(<?php echo $pot['id']; ?>, '<?php echo htmlspecialchars(addslashes($pot['from_email'])); ?>');" title="Preview Auto-Quote Response">
                                                    <i class="fa fa-paper-plane-o"></i> Quote
                                                </button>
                                                <a href="<?php echo admin_url('ckm_talent_pipeline/dismiss_potential/' . $pot['id']); ?>" class="btn btn-default btn-xs text-danger _delete" title="Dismiss">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Email Raw Snippet Drawer -->
                                    <tr class="potential-drawer-row bg-light">
                                        <td colspan="8" class="font-xs p10" style="background: #f8fafc;">
                                            <details>
                                                <summary class="text-primary bold cursor-pointer"><i class="fa fa-eye"></i> View Original Email Snippet & Script Notes</summary>
                                                <pre class="mtop5 p10 font-xs bg-white border" style="white-space: pre-wrap; max-height: 200px; overflow-y: auto;"><?php echo htmlspecialchars($pot['raw_body']); ?></pre>
                                            </details>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <?php echo form_close(); ?>
                <?php } else { ?>
                    <div class="text-center p30">
                        <i class="fa fa-check-circle-o text-success font-large" style="font-size: 48px;"></i>
                        <h4 class="bold mtop15">Inbox Queue is Clean!</h4>
                        <p class="text-muted font-xs">No pending casting calls detected. The background monitor will automatically notify you when new casting opportunities arrive.</p>
                        <a href="<?php echo admin_url('ckm_talent_pipeline/poll_inbox'); ?>" class="btn btn-default btn-sm mtop10">
                            <i class="fa fa-refresh"></i> Check Inbox Now
                        </a>
                    </div>
                <?php } ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Auto-Quote Dispatch Preview Modal -->
<div class="modal fade" id="auto_quote_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-white"><i class="fa fa-paper-plane"></i> <strong>Automated Quotation & Response Engine</strong></h4>
            </div>
            <div class="modal-body p20">
                <input type="hidden" id="quote_potential_id" value="">
                
                <!-- Quote Template Selector Bar -->
                <div class="form-group bg-light p10 border rounded mbot15">
                    <label class="control-label bold font-xs text-uppercase text-muted"><i class="fa fa-magic"></i> Response Template:</label>
                    <select id="quote_template_selector" class="form-control input-sm" onchange="load_auto_quote_template();">
                        <option value="standard" selected>📄 Standard Professional Quote (BSF + Usage + NAVA Rider)</option>
                        <option value="fast_avail">⚡ Fast Availability Check & Day Rate Confirmation</option>
                        <option value="counter_offer">🤝 Rate Negotiation & Minimum Session Counter-Offer</option>
                        <option value="decline_polite">⛔ Polite Schedule Decline & Referral</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Recipient Email:</label>
                            <input type="email" id="quote_recipient" class="form-control" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Email Subject:</label>
                            <input type="text" id="quote_subject" class="form-control" value="Quote & Availability - Voice Over Services">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label bold">Quote Email Message:</label>
                    <textarea id="quote_message_body" class="form-control" rows="12" style="font-family: monospace; font-size: 12px;"></textarea>
                </div>
                <div id="quote_send_feedback" class="alert hide"></div>
            </div>
            <div class="modal-footer display-flex justify-between align-center">
                <span class="text-muted font-xs">
                    <i class="fa fa-info-circle text-info"></i> Easily edit text or switch templates above before dispatching.
                </span>
                <div>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="button" class="btn btn-default" onclick="copy_quote_to_clipboard();">
                        <i class="fa fa-copy"></i> Copy to Clipboard
                    </button>
                    <button type="button" class="btn btn-success bold" id="btn_send_quote" onclick="send_quote_email_ajax();">
                        <i class="fa fa-paper-plane"></i> Send Quotation Email
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filter_potentials_table(query) {
    var q = (query || '').toLowerCase().trim();
    var matchCount = 0;
    $('#potentials_data_table tbody tr.potential-row').each(function() {
        var row = $(this);
        var drawer = row.next('tr.potential-drawer-row');
        var searchData = row.attr('data-search') || '';
        if (!q || searchData.indexOf(q) !== -1) {
            row.show();
            drawer.show();
            matchCount++;
        } else {
            row.hide();
            drawer.hide();
        }
    });
    $('#potentials_count_display').text(matchCount);
}

function load_auto_quote_template() {
    var potentialId = $('#quote_potential_id').val();
    var template = $('#quote_template_selector').val() || 'standard';
    if (!potentialId) return;

    var templateSubjects = {
        'standard': 'Voice Over Quote & Availability',
        'fast_avail': 'Availability Confirmation - Voice Over',
        'counter_offer': 'Voice Over Rate Proposal & Options',
        'decline_polite': 'Voice Over Availability Update'
    };
    if (templateSubjects[template]) {
        $('#quote_subject').val(templateSubjects[template]);
    }

    $.get(admin_url + 'ckm_talent_pipeline/get_auto_quote/' + potentialId + '?template=' + encodeURIComponent(template), function(res) {
        if (res && res.quote_text) {
            $('#quote_message_body').val(res.quote_text);
        }
    }, 'json');
}
</script>
