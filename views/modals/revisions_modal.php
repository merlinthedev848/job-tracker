<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="revisions_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <div class="display-flex justify-between align-center mright25">
                    <h4 class="modal-title">
                        <i class="fa fa-refresh text-warning"></i> Pickups & Revision Rounds Log
                    </h4>
                    <span class="badge bg-warning" id="rev_job_title_badge">Project</span>
                </div>
            </div>
            
            <div class="modal-body">
                <!-- Revision Policy Banner -->
                <div class="alert alert-info font-xs mbot15 display-flex justify-between align-center">
                    <div>
                        <i class="fa fa-info-circle"></i> <strong>Standard VO Pickup Policy:</strong> 1 Free round of minor pacing/tone adjustments within original script direction. Client script modifications after recording are billable per pickup.
                    </div>
                </div>

                <!-- Add New Revision Form -->
                <div class="panel panel-default mbot15">
                    <div class="panel-heading bold bg-light">
                        <i class="fa fa-plus-circle text-primary"></i> Log New Pickup / Revision Request
                    </div>
                    <div class="panel-body p10">
                        <form id="new_revision_form">
                            <input type="hidden" name="job_id" id="rev_job_id" value="">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="font-xs bold">Round #:</label>
                                        <input type="number" name="round_number" id="rev_round_num" class="form-control input-sm" value="1" min="1">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="font-xs bold">Revision Type:</label>
                                        <select name="revision_type" id="rev_type" class="form-control input-sm" onchange="on_rev_type_change(this.value);">
                                            <option value="Free Artistic Tweak">Free Artistic / Tone Tweak</option>
                                            <option value="Paid Script Revision">Paid Script Revision</option>
                                            <option value="Client Pickup / Retake">Client Pickup / Retake</option>
                                            <option value="Wild Alternate Takes">Wild Alternate Takes</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="font-xs bold">Timecodes (if any):</label>
                                        <input type="text" name="timecodes" id="rev_timecodes" class="form-control input-sm" placeholder="e.g. 00:14 - 00:22">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="font-xs bold">Pickup Fee (<?php echo get_base_currency()->symbol; ?>):</label>
                                        <input type="number" step="0.01" name="fee" id="rev_fee" class="form-control input-sm" value="0.00">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="font-xs bold">Status:</label>
                                        <select name="status" id="rev_status" class="form-control input-sm">
                                            <option value="Pending">Pending</option>
                                            <option value="Recorded">Recorded</option>
                                            <option value="Delivered">Delivered</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mbot10">
                                <label class="font-xs bold">Director / Client Pickup Notes:</label>
                                <textarea name="notes" id="rev_notes" class="form-control input-sm" rows="2" placeholder="e.g. Please re-record line 4 with more enthusiasm on 'revolutionary technology', and fix pronunciation of brand name."></textarea>
                            </div>

                            <div class="text-right">
                                <button type="button" class="btn btn-info btn-sm bold" onclick="submit_new_revision();">
                                    <i class="fa fa-save"></i> Save Pickup Round
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Existing Revisions History Table -->
                <label class="bold font-xs text-uppercase text-muted">
                    <i class="fa fa-history"></i> Revision & Pickup History:
                </label>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped font-xs" id="revisions_table">
                        <thead>
                            <tr class="bg-light">
                                <th style="width: 70px;">Round</th>
                                <th style="width: 150px;">Type</th>
                                <th style="width: 110px;">Timecodes</th>
                                <th>Director Notes</th>
                                <th style="width: 90px;">Fee</th>
                                <th style="width: 90px;">Status</th>
                                <th style="width: 50px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="revisions_table_body">
                            <tr>
                                <td colspan="7" class="text-center text-muted p15">No pickups recorded for this job.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function on_rev_type_change(val) {
    if (val === 'Paid Script Revision') {
        if (parseFloat($('#rev_fee').val()) === 0) {
            $('#rev_fee').val('50.00');
        }
    } else if (val === 'Free Artistic Tweak') {
        $('#rev_fee').val('0.00');
    }
}

function open_revisions_modal(jobId, jobTitle) {
    $('#rev_job_id').val(jobId);
    $('#rev_job_title_badge').text(jobTitle || ('Job #' + jobId));
    load_revisions_data(jobId);
    $('#revisions_modal').modal('show');
}

function load_revisions_data(jobId) {
    $.getJSON(admin_url + 'ckm_talent_pipeline/get_revisions/' + jobId, function(data) {
        var tbody = $('#revisions_table_body');
        tbody.empty();

        if (!data || data.length === 0) {
            tbody.append('<tr><td colspan="7" class="text-center text-muted p15">No pickups recorded for this project yet.</td></tr>');
            $('#rev_round_num').val(1);
            return;
        }

        $('#rev_round_num').val(data.length + 1);

        $.each(data, function(idx, rev) {
            var statusBadge = '<span class="badge bg-warning">Pending</span>';
            if (rev.status === 'Recorded') statusBadge = '<span class="badge bg-info">Recorded</span>';
            if (rev.status === 'Delivered') statusBadge = '<span class="badge bg-success">Delivered</span>';

            var quickStatusBtns = '<div class="btn-group btn-group-xs">' +
                '<button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                statusBadge + ' <span class="caret"></span>' +
                '</button>' +
                '<ul class="dropdown-menu dropdown-menu-right font-xs">' +
                '<li><a href="#" onclick="update_revision_status(' + rev.id + ', \'Pending\', ' + jobId + '); return false;"><i class="fa fa-clock-o text-warning"></i> Set as Pending</a></li>' +
                '<li><a href="#" onclick="update_revision_status(' + rev.id + ', \'Recorded\', ' + jobId + '); return false;"><i class="fa fa-microphone text-info"></i> Set as Recorded</a></li>' +
                '<li><a href="#" onclick="update_revision_status(' + rev.id + ', \'Delivered\', ' + jobId + '); return false;"><i class="fa fa-check text-success"></i> Set as Delivered</a></li>' +
                '</ul></div>';

            var row = '<tr>' +
                '<td class="bold text-center">Round ' + rev.round_number + '</td>' +
                '<td>' + rev.revision_type + '</td>' +
                '<td><code>' + (rev.timecodes || 'Full Track') + '</code></td>' +
                '<td>' + (rev.notes || '<span class="text-muted">No notes</span>') + '</td>' +
                '<td class="bold text-success">' + (parseFloat(rev.fee) > 0 ? ('+' + rev.fee) : 'Free') + '</td>' +
                '<td>' + quickStatusBtns + '</td>' +
                '<td class="text-center"><a href="#" onclick="delete_revision_round(' + rev.id + ', ' + jobId + '); return false;" class="text-danger" title="Delete pickup"><i class="fa fa-trash"></i></a></td>' +
                '</tr>';
            tbody.append(row);
        });
    });
}

function update_revision_status(revId, newStatus, jobId) {
    $.post(admin_url + 'ckm_talent_pipeline/save_revision', {
        id: revId,
        job_id: jobId,
        status: newStatus
    }, function() {
        alert_float('success', 'Pickup status updated to ' + newStatus);
        load_revisions_data(jobId);
    });
}

function submit_new_revision() {
    var jobId = $('#rev_job_id').val();
    if (!jobId) return;

    var formData = $('#new_revision_form').serialize();
    $.post(admin_url + 'ckm_talent_pipeline/save_revision', formData, function(res) {
        alert_float('success', 'Pickup round logged successfully!');
        $('#rev_notes').val('');
        $('#rev_timecodes').val('');
        load_revisions_data(jobId);
    }, 'json');
}

function delete_revision_round(id, jobId) {
    if (confirm('Delete this pickup record?')) {
        $.getJSON(admin_url + 'ckm_talent_pipeline/delete_revision/' + id, function(res) {
            alert_float('warning', 'Pickup record removed.');
            load_revisions_data(jobId);
        });
    }
}
</script>
