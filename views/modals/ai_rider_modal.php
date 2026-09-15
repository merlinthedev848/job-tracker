<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="ai_rider_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-white">
                    <i class="fa fa-shield"></i> <strong>NAVA AI & Synthetic Voice Protection Rider Pro</strong>
                </h4>
            </div>
            
            <div class="modal-body p20">
                <div class="alert alert-warning font-xs mbot15">
                    <i class="fa fa-exclamation-triangle"></i> <strong>NAVA 4.0 Industry Standard:</strong> Attach this addendum to your casting quotes, invoices, and production agreements to legally prohibit unauthorized voice cloning, neural network ingestion, digital doubles, TTS synthesis, and automated machine learning training.
                </div>

                <!-- Tier Selection Buttons -->
                <div class="form-group mbot15">
                    <label class="control-label font-xs bold text-uppercase text-muted block">Select Protection Tier / Genre:</label>
                    <div class="btn-group btn-group-justified" role="group" id="ai_rider_tier_group">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-default active bold btn-ai-tier" data-tier="commercial" onclick="select_ai_rider_tier('commercial');">
                                <i class="fa fa-tv"></i> Commercial & Corporate
                            </button>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-default bold btn-ai-tier" data-tier="gaming" onclick="select_ai_rider_tier('gaming');">
                                <i class="fa fa-gamepad"></i> Games & Animation
                            </button>
                        </div>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-default bold btn-ai-tier" data-tier="strict" onclick="select_ai_rider_tier('strict');">
                                <i class="fa fa-ban text-danger"></i> Strict Zero-AI Opt-Out
                            </button>
                        </div>
                    </div>
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
                            <input type="text" id="ai_rider_project" class="form-control input-sm" placeholder="e.g. Acme Corp National Campaign" onkeyup="refresh_ai_rider_preview();">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="display-flex justify-between align-center mbot5">
                        <label class="bold font-xs text-uppercase text-muted mbot0">
                            <i class="fa fa-file-text-o"></i> Contract Addendum / Rider Text:
                        </label>
                        <span class="badge bg-danger" id="ai_rider_tier_badge">Commercial & Corporate Tier</span>
                    </div>
                    <textarea id="ai_rider_text_output" class="form-control" rows="13" style="font-family: monospace; font-size: 11px; background: #fffcf5;"></textarea>
                </div>
            </div>

            <div class="modal-footer display-flex justify-between align-center">
                <span class="text-muted font-xs">
                    <i class="fa fa-check-circle text-success"></i> Compliant with NAVA 4.0 & UK CDPA §29A Guidelines
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
var current_ai_rider_tier = 'commercial';

function select_ai_rider_tier(tier) {
    current_ai_rider_tier = tier;
    $('.btn-ai-tier').removeClass('active btn-danger btn-primary').addClass('btn-default');
    $('.btn-ai-tier[data-tier="' + tier + '"]').addClass('active btn-primary');

    var tierTitles = {
        'commercial': 'Commercial & Corporate Tier',
        'gaming': 'Interactive, Games & Animation Tier',
        'strict': 'Strict Zero-AI Opt-Out (Statutory Protection)'
    };
    $('#ai_rider_tier_badge').text(tierTitles[tier] || 'Standard Protection');
    refresh_ai_rider_preview();
}

function refresh_ai_rider_preview() {
    var actor = $('#ai_rider_actor').val() || 'Voice Talent';
    var project = $('#ai_rider_project').val() || '[Project Name]';

    var rider = "";
    if (current_ai_rider_tier === 'gaming') {
        rider = "=========================================================================\n"
              + "NAVA 4.0 SYNTHETIC VOICE RIDER (INTERACTIVE, GAMES & ANIMATION)\n"
              + "=========================================================================\n\n"
              + "Project: " + project + "\n"
              + "Performer: " + actor + "\n\n"
              + "1. SCOPE OF GRANT: Performance, voice stems, and character vocal assets provided by " + actor + " are licensed exclusively for use within the specific game, interactive title, or animated production named in this agreement.\n\n"
              + "2. IN-GAME GENERATIVE AI PROHIBITION: Client agrees that voice assets shall NOT be ingested into runtime generative AI systems, dynamic text-to-speech engines, or real-time voice synthesizers to generate unscripted dialogue without explicit written rider and union/paymaster parity.\n\n"
              + "3. TRAINING RESTRICTION: No vocal data may be utilized for training deep learning, neural speech synthesis, or foundational voice models.\n\n"
              + "4. RECOGNIZED STANDARDS: Governed in accordance with NAVA 4.0 Interactive & Video Game Rider Guidelines.";
    } else if (current_ai_rider_tier === 'strict') {
        rider = "=========================================================================\n"
              + "STRICT ZERO-AI OPT-OUT CLAUSE (UK CDPA §29A & US COPYRIGHT)\n"
              + "=========================================================================\n\n"
              + "Project: " + project + "\n"
              + "Performer: " + actor + "\n\n"
              + "1. STATUTORY OPT-OUT: Performer (" + actor + ") hereby explicitly reserves all rights and opts out of any Text & Data Mining (TDM), Machine Learning ingestion, artificial intelligence training, or digital simulation under all applicable copyright and performance statutes.\n\n"
              + "2. ABSOLUTE PROHIBITION: Ingestion of delivered audio or video files into any generative AI pipeline, voice cloning tool, or automated dubbing model is strictly prohibited and constitutes a material breach of contract with liquidated damages.\n\n"
              + "3. MORAL RIGHTS & BIOMETRICS: Performer strictly retains all biometric rights to their vocal frequency, cadence, and likeness in perpetuity.";
    } else {
        rider = "=========================================================================\n"
              + "SYNTHETIC VOICE, SIMULATION & ARTIFICIAL INTELLIGENCE (AI) PROTECTION RIDER\n"
              + "=========================================================================\n\n"
              + "This Addendum applies to all voice recordings, stems, deliverables, and performance materials provided by " + actor + " (\"Performer\") for the project referenced as \"" + project + "\" (\"Project\").\n\n"
              + "1. LIMITED EXCLUSIVE USE: Client and Performer agree that all recorded audio, outtakes, and performances are licensed solely and exclusively for the specific project, media distribution channels, territories, and term length explicitly agreed in the booking quotation.\n\n"
              + "2. PROHIBITION OF ARTIFICIAL INTELLIGENCE & MACHINE LEARNING TRAINING: Client agrees that under no circumstances shall the audio, metadata, vocal timbre, acoustic stems, or likeness of Performer be used, uploaded, ingested, or processed to:\n"
              + "   a) Train, fine-tune, or develop any Artificial Intelligence (AI), Machine Learning (ML), Deep Learning, neural network, or algorithmic system.\n"
              + "   b) Synthesize, generate, or simulate Performer's voice or create a \"digital double\", \"voice clone\", text-to-speech (TTS) voice font, or automated vocal model.\n"
              + "   c) Sub-license, sell, or distribute the audio to third-party data aggregators or generative AI platforms.\n\n"
              + "3. REMEDIES & INJUNCTIVE RELIEF: Any unauthorized creation or commercial exploitation of a synthetic voice model using Performer's voice data constitutes an irreparable violation of Performer's right of publicity and intellectual property rights, entitling Performer to immediate injunctive relief and statutory/commercial damages.\n\n"
              + "4. RECOGNIZED STANDARD: This addendum is governed in alignment with the National Association of Voice Actors (NAVA 4.0) AI & Synthetic Voice Protection Standards.";
    }

    $('#ai_rider_text_output').val(rider);
}

function open_ai_rider_modal(projectTitle, actorName) {
    if (projectTitle) $('#ai_rider_project').val(projectTitle);
    if (actorName) $('#ai_rider_actor').val(actorName);
    select_ai_rider_tier('commercial');
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
