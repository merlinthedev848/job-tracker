<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Script Teleprompter & Live Recording Stopwatch Modal -->
<div class="modal fade" id="script_teleprompter_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1000px;">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                <div class="display-flex justify-between align-center mright20">
                    <h4 class="modal-title text-white">
                        <i class="fa fa-microphone"></i> <strong>Voice Over Script Teleprompter & Live Take Timer</strong>
                    </h4>
                </div>
            </div>
            <div class="modal-body p20">
                <div class="row">
                    <!-- Script Controls Toolbar -->
                    <div class="col-md-12 mbot15">
                        <div class="display-flex justify-between align-center flex-wrap p10 bg-light border" style="border-radius: 8px;">
                            <div class="display-flex align-center gap-10">
                                <!-- Teleprompter Scroll Controller -->
                                <button type="button" id="btn_teleprompter_play" class="btn btn-success btn-sm" onclick="toggle_teleprompter();">
                                    <i class="fa fa-play"></i> Auto-Scroll
                                </button>
                                <button type="button" class="btn btn-default btn-sm" onclick="reset_teleprompter();">
                                    <i class="fa fa-fast-backward"></i> Top
                                </button>
                                <div class="display-flex align-center mleft10">
                                    <span class="font-xs bold text-muted mright5">Speed:</span>
                                    <input type="range" id="prompter_speed" min="1" max="10" value="3" style="width: 100px;">
                                </div>
                                <div class="display-flex align-center mleft10">
                                    <span class="font-xs bold text-muted mright5">Font:</span>
                                    <button type="button" class="btn btn-default btn-xs" onclick="adjust_prompter_font(-2);"><i class="fa fa-minus"></i></button>
                                    <button type="button" class="btn btn-default btn-xs" onclick="adjust_prompter_font(2);"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>

                            <!-- Live Take Stopwatch -->
                            <div class="display-flex align-center gap-10">
                                <span class="font-xs bold text-uppercase text-muted"><i class="fa fa-clock-o"></i> Take Timer:</span>
                                <h3 id="prompter_timer_display" class="bold text-danger mtop0 mbot0 font-medium" style="font-family: monospace;">00:00.0</h3>
                                <button type="button" id="btn_timer_toggle" class="btn btn-danger btn-xs" onclick="toggle_take_timer();">
                                    <i class="fa fa-circle"></i> Record
                                </button>
                                <button type="button" class="btn btn-default btn-xs" onclick="reset_take_timer();">
                                    <i class="fa fa-undo"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Script Teleprompter View Area -->
                    <div class="col-md-8">
                        <div class="form-group mbot5">
                            <label class="control-label bold">Paste / Edit Script:</label>
                        </div>
                        <div id="teleprompter_viewport" style="height: 380px; overflow-y: auto; background: #1e293b; color: #f8fafc; padding: 25px; border-radius: 8px; font-size: 22px; line-height: 1.6; border: 2px solid #334155;">
                            <div id="teleprompter_text" contenteditable="true" style="outline: none; min-height: 100%; white-space: pre-wrap;">[Paste or type your voice over audition script here...]

Welcome to the voice over booth! You can adjust the auto-scroll speed, change the font size, and use the stopwatch on the top right to time your delivery.

When recording commercial spots, aim for 150-175 words per minute.
For corporate explainers and e-learning, aim for 130-150 words per minute.</div>
                        </div>
                        <div class="display-flex justify-between align-center mtop10 font-xs text-muted">
                            <span id="prompter_word_count">Words: ~65 | Est: ~26s (at 150 WPM)</span>
                            <span>Click inside box to edit text directly</span>
                        </div>
                    </div>

                    <!-- Character Notes & Accent Direction Drawer -->
                    <div class="col-md-4">
                        <div class="panel panel-default">
                            <div class="panel-heading bold"><i class="fa fa-user-circle-o text-info"></i> Performance & Accent Direction</div>
                            <div class="panel-body p15">
                                <div class="form-group">
                                    <label class="control-label font-xs bold">Character / Persona:</label>
                                    <input type="text" id="prompter_char_name" class="form-control input-sm" placeholder="e.g. Warm Friendly Narrator / Tech Expert">
                                </div>
                                <div class="form-group">
                                    <label class="control-label font-xs bold">Accent & Tone:</label>
                                    <input type="text" id="prompter_tone" class="form-control input-sm" placeholder="e.g. Neutral British (RP), Conversational, Upbeat">
                                </div>
                                <div class="form-group">
                                    <label class="control-label font-xs bold">Client Pronunciation & Direction Notes:</label>
                                    <textarea id="prompter_direction_notes" class="form-control font-xs" rows="6" placeholder="e.g. Pronounce brand name as 'KEE-mo'. Keep energy high on the CTA in the final line."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            </div>
        </div>
    </div>
</div>
