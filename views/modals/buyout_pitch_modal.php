<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- 1-Click Buyout Renewal Pitch Modal -->
<div class="modal fade" id="buyout_pitch_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <h4 class="modal-title text-white">
                    <i class="fa fa-copyright"></i> <strong>Pitch License Extension / Renewal</strong>
                </h4>
            </div>
            <div class="modal-body p20">
                <p class="text-muted font-xs mbot15">
                    This license is expiring. Send a polite, professional renewal pitch offering a 1-year extension, 2-year extension, or full buyout option directly to the client.
                </p>

                <input type="hidden" id="pitch_job_id" value="">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Recipient Email:</label>
                            <input type="email" id="pitch_recipient" class="form-control" value="">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label bold">Email Subject:</label>
                            <input type="text" id="pitch_subject" class="form-control" value="Voice Over License Renewal & Extension Options">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label bold">Renewal Pitch Message:</label>
                    <textarea id="pitch_message_body" class="form-control" rows="12"></textarea>
                </div>

                <div id="pitch_send_feedback" class="alert hide"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-default" onclick="copy_pitch_to_clipboard();">
                    <i class="fa fa-copy"></i> Copy to Clipboard
                </button>
                <button type="button" class="btn btn-success" id="btn_send_pitch" onclick="send_pitch_email_ajax();">
                    <i class="fa fa-paper-plane"></i> Send Renewal Pitch Email Now
                </button>
            </div>
        </div>
    </div>
</div>
