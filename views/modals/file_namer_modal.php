<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="file_namer_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-tag text-success"></i> Audio Slate & File Naming Generator
                </h4>
            </div>
            
            <div class="modal-body">
                <div class="alert alert-info font-xs mbot15">
                    <i class="fa fa-info-circle"></i> Audio engineers and casting directors require strict, unambiguous file naming and verbal slates. Generate formatted file names and spoken slates instantly.
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label font-xs bold">Actor / Talent Name:</label>
                            <input type="text" id="fn_actor" class="form-control input-sm" value="<?php echo htmlspecialchars(get_option('ckm_tp_actor_name') ?: 'VoiceActor'); ?>" onkeyup="generate_file_names();">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label font-xs bold">Project / Campaign Title:</label>
                            <input type="text" id="fn_project" class="form-control input-sm" placeholder="e.g. Nike_SummerLaunch" onkeyup="generate_file_names();">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label font-xs bold">Character / Role Name:</label>
                            <input type="text" id="fn_role" class="form-control input-sm" placeholder="e.g. LeadNarrator" onkeyup="generate_file_names();">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label font-xs bold">Agency / Source (Optional):</label>
                            <input type="text" id="fn_agency" class="form-control input-sm" placeholder="e.g. PrimeTalent" onkeyup="generate_file_names();">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label font-xs bold">Audio Format / Extension:</label>
                            <select id="fn_ext" class="form-control input-sm" onchange="generate_file_names();">
                                <option value=".wav">.wav (Broadcast Master - Recommended)</option>
                                <option value=".mp3">.mp3 (320kbps Audition Preview)</option>
                                <option value=".aiff">.aiff (Apple Lossless)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="control-label font-xs bold">Take Numbering:</label>
                            <select id="fn_take" class="form-control input-sm" onchange="generate_file_names();">
                                <option value="Take01">Take 01 (Master)</option>
                                <option value="Take02_Alt">Take 02 (Alternate Pacing)</option>
                                <option value="Take03_Wild">Take 03 (Wild Lines)</option>
                                <option value="Pickup01">Pickup 01</option>
                                <option value="RawStems">Raw Stems (Dry)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="mtop10 mbot15">

                <!-- Generated File Names & Slates -->
                <div class="row">
                    <div class="col-md-6">
                        <label class="bold font-xs text-uppercase text-primary">
                            <i class="fa fa-file-audio-o"></i> Recommended File Names:
                        </label>
                        
                        <div class="form-group">
                            <label class="font-xs text-muted">1. Commercial / Agency Format:</label>
                            <div class="input-group">
                                <input type="text" id="fn_out_standard" class="form-control input-sm" readonly style="font-family: monospace;">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-sm" type="button" onclick="copy_text_from('fn_out_standard');"><i class="fa fa-copy"></i></button>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-xs text-muted">2. Gaming / Animation (Character-First):</label>
                            <div class="input-group">
                                <input type="text" id="fn_out_char" class="form-control input-sm" readonly style="font-family: monospace;">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-sm" type="button" onclick="copy_text_from('fn_out_char');"><i class="fa fa-copy"></i></button>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-xs text-muted">3. E-Learning / Multi-Module Format:</label>
                            <div class="input-group">
                                <input type="text" id="fn_out_module" class="form-control input-sm" readonly style="font-family: monospace;">
                                <span class="input-group-btn">
                                    <button class="btn btn-default btn-sm" type="button" onclick="copy_text_from('fn_out_module');"><i class="fa fa-copy"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="bold font-xs text-uppercase text-success">
                            <i class="fa fa-microphone"></i> Spoken Slate Prompts (Voice-Over Slate):
                        </label>
                        
                        <div class="form-group">
                            <label class="font-xs text-muted">1. Standard Audition Slate:</label>
                            <div class="input-group">
                                <input type="text" id="fn_slate_standard" class="form-control input-sm" readonly style="font-family: monospace; background: #f0fdf4;">
                                <span class="input-group-btn">
                                    <button class="btn btn-success btn-sm" type="button" onclick="copy_text_from('fn_slate_standard');"><i class="fa fa-copy"></i></button>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-xs text-muted">2. Agency Represented Slate:</label>
                            <div class="input-group">
                                <input type="text" id="fn_slate_agency" class="form-control input-sm" readonly style="font-family: monospace; background: #f0fdf4;">
                                <span class="input-group-btn">
                                    <button class="btn btn-success btn-sm" type="button" onclick="copy_text_from('fn_slate_agency');"><i class="fa fa-copy"></i></button>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="font-xs text-muted">3. Character / Animation Slate:</label>
                            <div class="input-group">
                                <input type="text" id="fn_slate_char" class="form-control input-sm" readonly style="font-family: monospace; background: #f0fdf4;">
                                <span class="input-group-btn">
                                    <button class="btn btn-success btn-sm" type="button" onclick="copy_text_from('fn_slate_char');"><i class="fa fa-copy"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer display-flex justify-between align-center">
                <span class="text-muted font-xs">
                    <i class="fa fa-info-circle text-info"></i> Standardized naming conventions eliminate casting confusion and missing audio files.
                </span>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function sanitize_fn_token(str) {
    if (!str) return '';
    return str.replace(/[^a-zA-Z0-9_-]/g, '_').replace(/_+/g, '_');
}

function generate_file_names() {
    var raw_actor = $('#fn_actor').val() || 'VoiceActor';
    var raw_proj = $('#fn_project').val() || 'ProjectTitle';
    var raw_role = $('#fn_role').val() || 'Lead';
    var raw_agency = $('#fn_agency').val() || '';
    var ext = $('#fn_ext').val() || '.wav';
    var take = $('#fn_take').val() || 'Take01';

    var actor = sanitize_fn_token(raw_actor);
    var proj = sanitize_fn_token(raw_proj);
    var role = sanitize_fn_token(raw_role);
    var agency = sanitize_fn_token(raw_agency);

    // Standard: [Project]_[Role]_[Actor]_[Take].wav
    var standard = proj + '_' + role + '_' + actor + '_' + take + ext;
    $('#fn_out_standard').val(standard);

    // Character first: [Role]_[Project]_[Take]_[Actor].wav
    var charFormat = role + '_' + proj + '_' + take + '_' + actor + ext;
    $('#fn_out_char').val(charFormat);

    // Module: [Project]_Module01_[Role]_[Actor].wav
    var moduleFormat = proj + '_Mod01_' + role + '_' + actor + ext;
    $('#fn_out_module').val(moduleFormat);

    // Slates
    $('#fn_slate_standard').val('"Hi, this is ' + raw_actor + ' reading for ' + raw_role + '."');
    $('#fn_slate_agency').val('"This is ' + raw_actor + ' with ' + (raw_agency || 'Agent') + ', reading for ' + raw_role + '."');
    $('#fn_slate_char').val('"' + raw_actor + ' as ' + raw_role + '."');
}

function open_file_namer_modal(actor, project, role, agency) {
    if (actor) $('#fn_actor').val(actor);
    if (project) $('#fn_project').val(project);
    if (role) $('#fn_role').val(role);
    if (agency) $('#fn_agency').val(agency);
    generate_file_names();
    $('#file_namer_modal').modal('show');
}

function copy_text_from(elemId) {
    var copyText = document.getElementById(elemId);
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert_float('success', 'Copied to clipboard!');
}

$(document).ready(function() {
    generate_file_names();
});
</script>
