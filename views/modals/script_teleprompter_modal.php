<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Script Teleprompter & Live Recording Studio Modal -->
<div class="modal fade" id="script_teleprompter_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 92%; max-width: 1100px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <div class="display-flex justify-between align-center mright20">
                    <h4 class="modal-title text-white">
                        <i class="fa fa-microphone"></i> <strong>Voice Over Script Teleprompter, Take Marker & Cue Sheet</strong>
                    </h4>
                    <span id="prompter_active_job_label" class="badge bg-warning font-xs">Booth Mode</span>
                </div>
            </div>
            <div class="modal-body p20">
                <input type="hidden" id="prompter_job_id" value="">
                
                <div class="row">
                    <!-- Script Controls Toolbar -->
                    <div class="col-md-12 mbot15">
                        <div class="display-flex justify-between align-center flex-wrap p10 bg-light border" style="border-radius: 8px;">
                            <div class="display-flex align-center gap-10 flex-wrap">
                                <!-- Teleprompter Scroll Controller -->
                                <button type="button" id="btn_teleprompter_play" class="btn btn-success btn-sm" onclick="toggle_teleprompter();" title="Shortcut: Spacebar">
                                    <i class="fa fa-play"></i> Auto-Scroll
                                </button>
                                <button type="button" class="btn btn-default btn-sm" onclick="reset_teleprompter();" title="Return to Top">
                                    <i class="fa fa-fast-backward"></i> Top
                                </button>
                                <div class="display-flex align-center mleft5">
                                    <span class="font-xs bold text-muted mright5">Speed:</span>
                                    <input type="range" id="prompter_speed" min="1" max="10" value="3" style="width: 75px;">
                                </div>
                                <div class="display-flex align-center mleft5">
                                    <span class="font-xs bold text-muted mright5">Font:</span>
                                    <button type="button" class="btn btn-default btn-xs" onclick="adjust_prompter_font(-2);"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-default btn-xs" onclick="adjust_prompter_font(2);"><i class="fa fa-plus"></i></button>
                                </div>
                                <div class="display-flex align-center gap-5 mleft5">
                                    <span class="badge bg-light text-muted border font-xs" title="Press Space to Play/Pause Scroll"><kbd>Space</kbd> Scroll</span>
                                    <span class="badge bg-light text-muted border font-xs" title="Press T to Mark Take"><kbd>T</kbd> Mark</span>
                                </div>
                            </div>

                            <!-- Live Take Stopwatch & Marker -->
                            <div class="display-flex align-center gap-10 flex-wrap">
                                <span class="font-xs bold text-uppercase text-muted"><i class="fa fa-clock-o"></i> Take Timer:</span>
                                <h3 id="prompter_timer_display" class="bold text-danger mtop0 mbot0 font-medium" style="font-family: monospace;">00:00.0</h3>
                                <button type="button" id="btn_timer_toggle" class="btn btn-danger btn-xs" onclick="toggle_take_timer();">
                                    <i class="fa fa-circle"></i> Record
                                </button>
                                <button type="button" class="btn btn-default btn-xs" onclick="reset_take_timer();">
                                    <i class="fa fa-undo"></i>
                                </button>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-warning btn-xs bold" onclick="mark_current_take();" title="Shortcut: T key">
                                        <i class="fa fa-bookmark"></i> Mark Take
                                    </button>
                                    <button type="button" class="btn btn-warning btn-xs dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right font-xs">
                                        <li><a href="#" onclick="mark_custom_take('Best Read / Master ⭐'); return false;"><i class="fa fa-star text-warning"></i> Mark Best Take ⭐</a></li>
                                        <li><a href="#" onclick="mark_custom_take('Director Alternate Pick ⚡'); return false;"><i class="fa fa-bolt text-primary"></i> Mark Alternate Pick ⚡</a></li>
                                        <li><a href="#" onclick="mark_custom_take('Pickup Needed (Line Stumble) 🔁'); return false;"><i class="fa fa-refresh text-danger"></i> Mark Pickup Needed 🔁</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Script Teleprompter View Area -->
                    <div class="col-md-7">
                        <div class="form-group mbot5 display-flex justify-between align-center">
                            <label class="control-label bold">Script View & Markup (Editable):</label>
                            <span id="prompter_word_count" class="font-xs text-muted">Words: ~0 | Est: 0s</span>
                        </div>
                        <div id="teleprompter_viewport" style="height: 400px; overflow-y: auto; background: #1e293b; color: #f8fafc; padding: 25px; border-radius: 8px; font-size: 22px; line-height: 1.6; border: 2px solid #334155;">
                            <div id="teleprompter_text" contenteditable="true" style="outline: none; min-height: 100%; white-space: pre-wrap;" onkeyup="calculate_prompter_stats();">[Paste or type your voice over script here...]

Welcome to the voice over booth! You can adjust the auto-scroll speed, change the font size, and use the stopwatch on the top right to time your delivery.

When recording commercial spots, aim for 150-175 words per minute.
For corporate explainers and e-learning, aim for 130-150 words per minute.</div>
                        </div>
                    </div>

                    <!-- Take Logger & Direction Drawer -->
                    <div class="col-md-5">
                        <!-- Take Markers Sheet -->
                        <div class="panel panel-default mbot15">
                            <div class="panel-heading bold bg-light display-flex justify-between align-center">
                                <span><i class="fa fa-list-ol text-warning"></i> Studio Take Logger</span>
                                <button type="button" class="btn btn-default btn-xs" onclick="clear_take_logs();"><i class="fa fa-trash"></i> Clear</button>
                            </div>
                            <div class="panel-body p10">
                                <div class="form-group mbot5">
                                    <textarea id="prompter_take_notes" class="form-control font-xs" rows="5" style="font-family: monospace; font-size: 11px;" placeholder="Take log will appear here when you click 'Mark Take'...&#10;e.g.&#10;Take 1 [00:32.4] - Master Clean Read&#10;Take 2 [00:30.1] - Faster Pacing (Director Pick)&#10;Take 3 [00:12.0] - Pickup line 4"></textarea>
                                </div>
                                <div class="display-flex justify-between align-center font-xs">
                                    <button type="button" class="btn btn-xs btn-info" onclick="export_prompter_take_sheet();">
                                        <i class="fa fa-file-text-o"></i> Export Take Sheet (.txt)
                                    </button>
                                    <button type="button" class="btn btn-xs btn-success" onclick="save_script_to_job();">
                                        <i class="fa fa-save"></i> Save to Job Card
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Character & Direction Notes -->
                        <div class="panel panel-default">
                            <div class="panel-heading bold bg-light"><i class="fa fa-user-circle-o text-info"></i> Direction & Pronunciation</div>
                            <div class="panel-body p10">
                                <div class="form-group mbot10">
                                    <label class="control-label font-xs bold">Character / Tone:</label>
                                    <input type="text" id="prompter_char_name" class="form-control input-sm" placeholder="e.g. Conversational, Warm, Tech Specialist">
                                </div>
                                <div class="form-group mbot0">
                                    <label class="control-label font-xs bold">Pronunciation & Guide Notes:</label>
                                    <textarea id="prompter_direction_notes" class="form-control font-xs" rows="4" placeholder="e.g. Pronounce brand name as 'KEE-mo'. Emphasize tagline."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer display-flex justify-between align-center">
                <div>
                    <button type="button" class="btn btn-default btn-sm" onclick="$('#file_namer_modal').modal('show');">
                        <i class="fa fa-tag"></i> Slate & File Namers
                    </button>
                    <button type="button" class="btn btn-default btn-sm" onclick="$('#studio_tech_specs_modal').modal('show');">
                        <i class="fa fa-sliders"></i> Studio Specs
                    </button>
                </div>
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>

<script>
var prompterScrollInterval = null;
var prompterFontSize = 22;
var takeTimerInterval = null;
var takeTimerSeconds = 0;
var takeCount = 1;

function toggle_teleprompter() {
    var btn = $('#btn_teleprompter_play');
    if (prompterScrollInterval) {
        clearInterval(prompterScrollInterval);
        prompterScrollInterval = null;
        btn.removeClass('btn-danger').addClass('btn-success').html('<i class="fa fa-play"></i> Auto-Scroll');
    } else {
        var viewport = document.getElementById('teleprompter_viewport');
        var speed = parseInt($('#prompter_speed').val()) || 3;
        var step = speed;
        prompterScrollInterval = setInterval(function() {
            viewport.scrollTop += step;
            if (viewport.scrollTop + viewport.clientHeight >= viewport.scrollHeight) {
                toggle_teleprompter();
            }
        }, 50);
        btn.removeClass('btn-success').addClass('btn-danger').html('<i class="fa fa-pause"></i> Pause');
    }
}

function reset_teleprompter() {
    if (prompterScrollInterval) {
        toggle_teleprompter();
    }
    document.getElementById('teleprompter_viewport').scrollTop = 0;
}

function adjust_prompter_font(delta) {
    prompterFontSize = Math.max(14, Math.min(48, prompterFontSize + delta));
    $('#teleprompter_viewport').css('font-size', prompterFontSize + 'px');
}

function toggle_take_timer() {
    var btn = $('#btn_timer_toggle');
    if (takeTimerInterval) {
        clearInterval(takeTimerInterval);
        takeTimerInterval = null;
        btn.removeClass('btn-warning').addClass('btn-danger').html('<i class="fa fa-circle"></i> Record');
    } else {
        var startTime = Date.now() - (takeTimerSeconds * 1000);
        takeTimerInterval = setInterval(function() {
            var elapsedMs = Date.now() - startTime;
            takeTimerSeconds = elapsedMs / 1000;
            var mins = Math.floor(takeTimerSeconds / 60);
            var secs = Math.floor(takeTimerSeconds % 60);
            var tenths = Math.floor((elapsedMs % 1000) / 100);
            $('#prompter_timer_display').text(
                (mins < 10 ? '0' : '') + mins + ':' + 
                (secs < 10 ? '0' : '') + secs + '.' + tenths
            );
        }, 100);
        btn.removeClass('btn-danger').addClass('btn-warning').html('<i class="fa fa-square"></i> Stop');
    }
}

function reset_take_timer() {
    if (takeTimerInterval) {
        toggle_take_timer();
    }
    takeTimerSeconds = 0;
    $('#prompter_timer_display').text('00:00.0');
}

function mark_current_take() {
    mark_custom_take('');
}

function mark_custom_take(customLabel) {
    var timeStr = $('#prompter_timer_display').text();
    var notesArea = $('#prompter_take_notes');
    var currentText = notesArea.val();
    var label = customLabel ? ' - ' + customLabel : ' - ';
    var logEntry = 'Take ' + takeCount + ' [' + timeStr + ']' + label;
    takeCount++;
    if (currentText.length > 0 && !currentText.endsWith('\n')) {
        currentText += '\n';
    }
    notesArea.val(currentText + logEntry);
    notesArea.focus();
}

function clear_take_logs() {
    if (confirm('Clear all take notes?')) {
        $('#prompter_take_notes').val('');
        takeCount = 1;
    }
}

function calculate_prompter_stats() {
    var text = $('#teleprompter_text').text() || '';
    var words = text.trim().split(/\s+/).filter(function(w) { return w.length > 0; }).length;
    var estSeconds = Math.round((words / 150) * 60);
    var mins = Math.floor(estSeconds / 60);
    var secs = estSeconds % 60;
    var timeFormatted = (mins > 0 ? mins + 'm ' : '') + secs + 's';
    $('#prompter_word_count').text('Words: ~' + words + ' | Est: ~' + timeFormatted + ' (at 150 WPM)');
}

function open_teleprompter_for_job(jobId, jobTitle, scriptText, notesText) {
    $('#prompter_job_id').val(jobId || '');
    $('#prompter_active_job_label').text(jobTitle || 'Booth Mode');
    if (scriptText) {
        $('#teleprompter_text').text(scriptText);
    }
    if (notesText) {
        $('#prompter_take_notes').val(notesText);
    }
    calculate_prompter_stats();
    $('#script_teleprompter_modal').modal('show');
}

function save_script_to_job() {
    var jobId = $('#prompter_job_id').val();
    if (!jobId) {
        alert_float('warning', 'Open teleprompter from a specific job card to save.');
        return;
    }

    var scriptText = $('#teleprompter_text').text();
    var takeNotes = $('#prompter_take_notes').val();

    $.post(admin_url + 'ckm_talent_pipeline/save_script_takes', {
        job_id: jobId,
        script_text: scriptText,
        take_notes: takeNotes
    }, function(res) {
        alert_float('success', 'Script and take notes saved to job card!');
    }, 'json');
}

function export_prompter_take_sheet() {
    var jobId = $('#prompter_job_id').val();
    if (jobId) {
        window.location.href = admin_url + 'ckm_talent_pipeline/export_take_sheet/' + jobId;
    } else {
        var text = "=== RECORDING TAKE SHEET ===\nDate: " + new Date().toISOString() + "\n\n" +
                   "SCRIPT:\n" + $('#teleprompter_text').text() + "\n\n" +
                   "TAKE LOGS:\n" + $('#prompter_take_notes').val();
        var blob = new Blob([text], { type: "text/plain;charset=utf-8" });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = "Take_Sheet_" + Date.now() + ".txt";
        a.click();
    }
}

$(document).ready(function() {
    calculate_prompter_stats();

    // Hotkey bindings when teleprompter modal is active
    $(document).on('keydown', function(e) {
        if (!$('#script_teleprompter_modal').hasClass('in')) return;
        var tag = (e.target.tagName || '').toLowerCase();
        var isEditing = (tag === 'input' || tag === 'textarea' || $(e.target).attr('contenteditable') === 'true');

        // Spacebar toggles teleprompter if not actively typing inside input/textarea
        if (e.which === 32 && tag !== 'input' && tag !== 'textarea') {
            e.preventDefault();
            toggle_teleprompter();
        } else if ((e.key === 't' || e.key === 'T') && !isEditing) {
            e.preventDefault();
            mark_current_take();
        } else if (e.which === 38 && !isEditing) { // Up arrow increases speed
            e.preventDefault();
            var sp = Math.min(10, parseInt($('#prompter_speed').val()) + 1);
            $('#prompter_speed').val(sp);
        } else if (e.which === 40 && !isEditing) { // Down arrow decreases speed
            e.preventDefault();
            var sp2 = Math.max(1, parseInt($('#prompter_speed').val()) - 1);
            $('#prompter_speed').val(sp2);
        }
    });
});
</script>
