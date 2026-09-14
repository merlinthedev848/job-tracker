<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="modal fade" id="studio_tech_specs_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <i class="fa fa-sliders text-info"></i> Home Studio Tech Specs & Delivery Profile
                </h4>
            </div>
            
            <div class="modal-body">
                <div class="alert alert-info font-xs mbot20">
                    <i class="fa fa-info-circle"></i> <strong>Casting & Audio Engineer Ready:</strong> Use this profile to quickly send your broadcast-quality studio capabilities, microphone chain, and remote directed connectivity details to casting directors and audio engineers.
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading bold bg-light">
                                <i class="fa fa-microphone text-primary"></i> Audio Chain & Acoustics
                            </div>
                            <div class="panel-body font-xs">
                                <p><strong>Microphones & Preamp:</strong><br>
                                <span class="text-muted" id="spec_mic_display"><?php echo htmlspecialchars(get_option('ckm_tp_mic_chain')); ?></span></p>
                                
                                <p><strong>DAW & Studio Booth:</strong><br>
                                <span class="text-muted" id="spec_daw_display"><?php echo htmlspecialchars(get_option('ckm_tp_daw_booth')); ?></span></p>

                                <p><strong>Standard Deliverables:</strong><br>
                                <span class="badge bg-light text-dark border">48kHz / 24-bit WAV</span>
                                <span class="badge bg-light text-dark border">44.1kHz / 16-bit WAV</span>
                                <span class="badge bg-light text-dark border">320kbps MP3</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading bold bg-light">
                                <i class="fa fa-globe text-success"></i> Remote Directed Session Connectivity
                            </div>
                            <div class="panel-body font-xs">
                                <p><strong>Source-Connect Standard:</strong><br>
                                <code class="text-primary bold"><?php echo htmlspecialchars(get_option('ckm_tp_source_connect_id') ?: 'Not configured'); ?></code></p>
                                
                                <p><strong>Cleanfeed Studio Pro:</strong><br>
                                <span class="text-muted"><?php echo htmlspecialchars(get_option('ckm_tp_cleanfeed_link') ?: 'Available on request'); ?></span></p>

                                <p><strong>Other Live Directed Options:</strong><br>
                                <span class="badge bg-primary">Zoom / Riverside</span>
                                <span class="badge bg-info">ipDTL / SessionLinkPRO</span>
                                <span class="badge bg-success">Skype / Google Meet</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Formatted Clipboard Output Preview -->
                <div class="form-group mtop10">
                    <label class="bold font-xs text-uppercase text-muted">
                        <i class="fa fa-copy"></i> Copyable Broadcast Spec Sheet (Formatted for Email / Proposals)
                    </label>
                    <textarea id="studio_spec_clipboard_text" class="form-control" rows="7" readonly style="font-family: monospace; font-size: 12px; background: #f8fafc;"><?php
$actor_name = get_option('ckm_tp_actor_name') ?: (get_option('companyname') ?: 'Voice Actor');
echo "=== VOICE OVER HOME STUDIO SPECIFICATIONS ===\n";
echo "Talent: " . $actor_name . "\n";
echo "Microphone & Preamp: " . get_option('ckm_tp_mic_chain') . "\n";
echo "DAW & Treatment: " . get_option('ckm_tp_daw_booth') . "\n";
echo "Source-Connect ID: " . (get_option('ckm_tp_source_connect_id') ?: 'Available on request') . "\n";
echo "Cleanfeed / Remote Link: " . (get_option('ckm_tp_cleanfeed_link') ?: 'Available on request') . "\n";
echo "Standard Deliverables: 48kHz / 24-bit broadcast WAV (clean, de-breathed, mastered to -23 LUFS or raw stems)\n";
echo "Turnaround: 12-24 hours for standard scripts | Same-day for urgent broadcast";
?></textarea>
                </div>
            </div>

            <div class="modal-footer display-flex justify-between align-center">
                <button type="button" class="btn btn-default" onclick="$('#studio_tech_specs_modal').modal('hide'); $('a[href=\'#tab_crm\']').tab('show');">
                    <i class="fa fa-cog"></i> Edit Studio Settings
                </button>
                <div>
                    <button type="button" class="btn btn-info bold" onclick="copy_studio_specs();">
                        <i class="fa fa-copy"></i> Copy Spec Sheet to Clipboard
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copy_studio_specs() {
    var copyText = document.getElementById("studio_spec_clipboard_text");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert_float('success', 'Studio tech spec sheet copied to clipboard!');
}
</script>
