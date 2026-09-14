<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="ai_rider_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-shield text-danger"></i> NAVA AI & Synthetic Voice Protection Rider
                </h4>
            </div>
            
            <div class="modal-body">
                <div class="alert alert-warning font-xs mbot15">
                    <i class="fa fa-exclamation-triangle"></i> <strong>Industry-Standard AI Protection:</strong> Based on the National Association of Voice Actors (NAVA) AI Rider guidelines. Attach this addendum to your casting quotes, invoices, and contracts to legally prohibit unauthorized voice cloning, digital doubles, TTS synthesis, and AI training models.
                </div>

                <div class="row mbot10">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label font-xs bold">Performer / Voice Actor Name:</label>
                            <input type="text" id="ai_rider_actor" class="form-control input-sm" value="<?php echo htmlspecialchars(get_option('ckm_tp_actor_name') ?: (get_option('companyname') ?: 'Voice Talent')); ?>" onkeyup="refresh_ai_rider_preview();">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label font-xs bold">Project / Client Name (Optional):</label>
                            <input type="text" id="ai_rider_project" class="form-control input-sm" placeholder="e.g. Acme Corp Spring Campaign" onkeyup="refresh_ai_rider_preview();">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="bold font-xs text-uppercase text-muted">
                        <i class="fa fa-file-text-o"></i> Generated Contract Addendum / Rider Text:
                    </label>
                    <textarea id="ai_rider_text_output" class="form-control" rows="12" style="font-family: monospace; font-size: 11px; background: #fffcf5;"></textarea>
                </div>
            </div>

            <div class="modal-footer display-flex justify-between align-center">
                <span class="text-muted font-xs">
                    <i class="fa fa-check-circle text-success"></i> Compliant with NAVA 4.0 Standard Guidelines
                </span>
                <div>
                    <button type="button" class="btn btn-danger bold" onclick="copy_ai_rider();">
                        <i class="fa fa-copy"></i> Copy AI Protection Rider
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refresh_ai_rider_preview() {
    var actor = $('#ai_rider_actor').val() || 'Voice Talent';
    var project = $('#ai_rider_project').val() || '[Project Name]';

    var rider = "=========================================================================\n"
              + "SYNTHETIC VOICE, SIMULATION & ARTIFICIAL INTELLIGENCE (AI) PROTECTION RIDER\n"
              + "=========================================================================\n\n"
              + "This Addendum applies to all voice recordings, stems, deliverables, and performance materials provided by " + actor + " (\"Performer\") for the project referenced as \"" + project + "\" (\"Project\").\n\n"
              + "1. LIMITED EXCLUSIVE USE: Client and Performer agree that all recorded audio, outtakes, and performances are licensed solely and exclusively for the specific project, media distribution channels, territories, and term length explicitly agreed in the booking quotation.\n\n"
              + "2. PROHIBITION OF ARTIFICIAL INTELLIGENCE & MACHINE LEARNING TRAINING: Client agrees that under no circumstances shall the audio, metadata, vocal timbre, acoustic stems, or likeness of Performer be used, uploaded, ingested, or processed to:\n"
              + "   a) Train, fine-tune, or develop any Artificial Intelligence (AI), Machine Learning (ML), Deep Learning, neural network, or algorithmic system.\n"
              + "   b) Synthesize, generate, or simulate Performer's voice or create a \"digital double\", \"voice clone\", text-to-speech (TTS) voice font, or automated vocal model.\n"
              + "   c) Sub-license, sell, or distribute the audio to third-party data aggregators or generative AI platforms.\n\n"
              + "3. REMEDIES & INJUNCTIVE RELIEF: Any unauthorized creation or commercial exploitation of a synthetic voice model using Performer's voice data constitutes an irreparable violation of Performer's right of publicity and intellectual property rights, entitling Performer to immediate injunctive relief and statutory/commercial damages.\n\n"
              + "4. RECOGNIZED STANDARD: This addendum is governed in alignment with the National Association of Voice Actors (NAVA) AI & Synthetic Voice Protection Standards.";

    $('#ai_rider_text_output').val(rider);
}

function open_ai_rider_modal(projectTitle, actorName) {
    if (projectTitle) $('#ai_rider_project').val(projectTitle);
    if (actorName) $('#ai_rider_actor').val(actorName);
    refresh_ai_rider_preview();
    $('#ai_rider_modal').modal('show');
}

function copy_ai_rider() {
    var copyText = document.getElementById("ai_rider_text_output");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert_float('success', 'NAVA AI Protection Rider copied to clipboard!');
}

$(document).ready(function() {
    refresh_ai_rider_preview();
});
</script>
