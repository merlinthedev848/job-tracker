<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Inbound Potentials Triage Modal -->
<div class="modal fade" id="potentials_tray_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1100px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <div class="display-flex justify-between align-center mright20">
                    <h4 class="modal-title text-white">
                        <i class="fa fa-envelope-open-o"></i> <strong>Inbound Casting Calls & Opportunities Queue</strong>
                    </h4>
                    <a href="<?php echo admin_url('ckm_talent_pipeline/poll_inbox'); ?>" class="btn btn-xs btn-default font-xs">
                        <i class="fa fa-refresh"></i> Check Inbox Now
                    </a>
                </div>
            </div>
            <div class="modal-body p20">
                <?php if (!empty($potentials)) { ?>
                    <p class="text-muted font-xs mbot15">
                        The email monitor has automatically detected and parsed the following casting opportunities from your inbox. Review the extracted fields and accept them directly into your pipeline.
                    </p>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr class="active">
                                    <th>Received</th>
                                    <th>Sender</th>
                                    <th>Project Title</th>
                                    <th>Role</th>
                                    <th>Word Count</th>
                                    <th>Estimated Rates</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($potentials as $pot) { ?>
                                    <tr>
                                        <td class="font-xs"><?php echo date('j M, H:i', strtotime($pot['created_at'])); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($pot['from_name'] ?: 'Unknown Sender'); ?></strong>
                                            <small class="text-muted block font-xs"><?php echo htmlspecialchars($pot['from_email']); ?></small>
                                        </td>
                                        <td class="bold text-primary">
                                            <?php echo htmlspecialchars($pot['parsed_title'] ?: $pot['subject']); ?>
                                        </td>
                                        <td><span class="label label-info"><?php echo htmlspecialchars($pot['parsed_role']); ?></span></td>
                                        <td><?php echo $pot['parsed_words'] > 0 ? number_format($pot['parsed_words']) . 'w' : '-'; ?></td>
                                        <td>
                                            <span class="bold text-success font-xs">
                                                BSF: <?php echo ckm_format_money($pot['parsed_bsf']); ?>
                                            </span>
                                            <?php if ($pot['parsed_usage'] > 0) { ?>
                                                <small class="text-muted block font-xs">Usage: <?php echo ckm_format_money($pot['parsed_usage']); ?></small>
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
                                    <tr class="bg-light">
                                        <td colspan="7" class="font-xs p10" style="background: #f8fafc;">
                                            <details>
                                                <summary class="text-primary bold cursor-pointer"><i class="fa fa-eye"></i> View Original Email Snippet & Script Notes</summary>
                                                <pre class="mtop5 p10 font-xs bg-white border" style="white-space: pre-wrap;"><?php echo htmlspecialchars($pot['raw_body']); ?></pre>
                                            </details>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
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
                <h4 class="modal-title text-white"><i class="fa fa-paper-plane"></i> <strong>Automated Quotation Response Dispatch</strong></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="quote_potential_id" value="">
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
                    <textarea id="quote_message_body" class="form-control" rows="12"></textarea>
                </div>
                <div id="quote_send_feedback" class="alert hide"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-default" onclick="copy_quote_to_clipboard();">
                    <i class="fa fa-copy"></i> Copy to Clipboard
                </button>
                <button type="button" class="btn btn-success" id="btn_send_quote" onclick="send_quote_email_ajax();">
                    <i class="fa fa-paper-plane"></i> Send Quotation Email Now
                </button>
            </div>
        </div>
    </div>
</div>
