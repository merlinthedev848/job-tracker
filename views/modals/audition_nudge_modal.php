<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="audition_nudge_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-paper-plane text-info"></i> Audition Follow-Up & Nudge Dispatcher
                </h4>
            </div>
            
            <div class="modal-body">
                <div class="alert alert-info font-xs mbot15">
                    <i class="fa fa-clock-o"></i> <strong>Polite Professional Check-in:</strong> Keep your audition top of mind with casting directors and agents without sounding pushy.
                </div>

                <div class="form-group">
                    <label class="control-label font-xs bold">Email Recipient:</label>
                    <input type="text" id="nudge_recipient" class="form-control input-sm" placeholder="casting@agency.example">
                </div>

                <div class="form-group">
                    <label class="control-label font-xs bold">Subject Line:</label>
                    <input type="text" id="nudge_subject" class="form-control input-sm" value="Quick Follow-Up: Voice Over Audition Submission">
                </div>

                <div class="form-group">
                    <label class="control-label font-xs bold">Message Body:</label>
                    <textarea id="nudge_body_text" class="form-control" rows="8" style="font-family: monospace; font-size: 12px;"></textarea>
                </div>
            </div>

            <div class="modal-footer display-flex justify-between align-center">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <div>
                    <button type="button" class="btn btn-info bold" onclick="copy_nudge_text();">
                        <i class="fa fa-copy"></i> Copy Message
                    </button>
                    <button type="button" class="btn btn-primary bold" onclick="open_nudge_mailto();">
                        <i class="fa fa-envelope-o"></i> Open in Email Client
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function open_audition_nudge_modal(jobId, recipientEmail, projectTitle) {
    if (recipientEmail) $('#nudge_recipient').val(recipientEmail);
    if (projectTitle) $('#nudge_subject').val('Quick Follow-Up: Audition for "' + projectTitle + '"');

    $.getJSON(admin_url + 'ckm_talent_pipeline/get_audition_nudge/' + jobId, function(data) {
        $('#nudge_body_text').val(data.nudge_text || '');
        $('#audition_nudge_modal').modal('show');
    });
}

function copy_nudge_text() {
    var copyText = document.getElementById("nudge_body_text");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert_float('success', 'Follow-up message copied to clipboard!');
}

function open_nudge_mailto() {
    var to = encodeURIComponent($('#nudge_recipient').val());
    var subj = encodeURIComponent($('#nudge_subject').val());
    var body = encodeURIComponent($('#nudge_body_text').val());
    window.location.href = 'mailto:' + to + '?subject=' + subj + '&body=' + body;
}
</script>
