/* CKM Talent Pipeline JavaScript Engine */

$(function() {
    init_kanban_drag_drop();
    init_rate_calculator();
    init_live_search_and_filters();
    init_vo_rate_widget();

    // Prevent browser "Leave site?" prompts on form submit
    $(document).on('submit', 'form', function() {
        $(window).off('beforeunload');
        window.onbeforeunload = null;
    });

    // Sync tab pills with URL hash & activate on page load
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        var hash = $(e.target).attr('href');
        if (hash && hash.startsWith('#tab_')) {
            if (history.replaceState) {
                history.replaceState(null, null, hash);
            } else {
                location.hash = hash;
            }
        }
    });

    // Auto-switch to tab specified in hash
    var initialHash = window.location.hash;
    if (initialHash && $('a[href="' + initialHash + '"]').length > 0) {
        $('a[href="' + initialHash + '"]').tab('show');
    }

    // Auto-update commission when source changes in modal
    $('#source_id').on('change', function() {
        var selected = $(this).find(':selected');
        var comm = selected.data('commission');
        if (comm !== undefined) {
            $('#modal_commission').val(comm);
            calculate_modal_rates();
        }
    });
});

/**
 * Initialize Drag and Drop for Kanban Cards
 */
function init_kanban_drag_drop() {
    var cards = document.querySelectorAll('.ckm-card');
    var columns = document.querySelectorAll('.ckm-kanban-cards-container');

    cards.forEach(function(card) {
        card.setAttribute('draggable', 'true');

        card.addEventListener('dragstart', function(e) {
            card.classList.add('dragging');
            e.dataTransfer.setData('text/plain', card.getAttribute('data-job-id'));
        });

        card.addEventListener('dragend', function() {
            card.classList.remove('dragging');
        });
    });

    columns.forEach(function(col) {
        col.addEventListener('dragover', function(e) {
            e.preventDefault();
            col.classList.add('drag-over');
        });

        col.addEventListener('dragleave', function() {
            col.classList.remove('drag-over');
        });

        col.addEventListener('drop', function(e) {
            e.preventDefault();
            col.classList.remove('drag-over');
            var jobId = e.dataTransfer.getData('text/plain');
            var targetStatus = col.getAttribute('data-status');
            var card = document.querySelector('.ckm-card[data-job-id="' + jobId + '"]');

            if (card && targetStatus) {
                if (targetStatus === 'lost') {
                    trigger_loss_modal(jobId);
                } else {
                    update_job_status_ajax(jobId, targetStatus);
                    col.appendChild(card);
                }
            }
        });
    });
}

/**
 * Quick 1-Click Status Change from Card Hover
 */
function quick_change_card_status(jobId, targetStatus) {
    update_job_status_ajax(jobId, targetStatus);
    var card = document.querySelector('.ckm-card[data-job-id="' + jobId + '"]');
    var targetCol = document.getElementById('kanban-col-' + targetStatus);
    if (card && targetCol) {
        targetCol.appendChild(card);
    }
}

/**
 * Trigger Loss Modal
 */
function trigger_loss_modal(jobId) {
    $('#loss_job_id').val(jobId);
    $('#talent_loss_reason_modal').modal('show');
}

/**
 * Confirm Loss Reason
 */
function confirm_loss_status() {
    var jobId = $('#loss_job_id').val();
    var reasonId = $('#loss_reason_id').val();
    var notes = $('#loss_notes').val();

    update_job_status_ajax(jobId, 'lost', reasonId, notes);

    var card = document.querySelector('.ckm-card[data-job-id="' + jobId + '"]');
    var lostCol = document.getElementById('kanban-col-lost');
    if (card && lostCol) {
        lostCol.appendChild(card);
    }

    $('#talent_loss_reason_modal').modal('hide');
    $('#loss_reason_id').val('').selectpicker('refresh');
    $('#loss_notes').val('');
}

/**
 * AJAX Status Update
 */
function update_job_status_ajax(jobId, status, lossReasonId, lossNotes) {
    $.post(admin_url + 'ckm_talent_pipeline/change_status', {
        id: jobId,
        status: status,
        loss_reason_id: lossReasonId || '',
        loss_notes: lossNotes || ''
    }, function(response) {
        // Updated
    }, 'json');
}

/**
 * Column-Level Quick Add
 */
function quick_add_in_column(status) {
    new_talent_job();
    $('#status').val(status).selectpicker('refresh');
}

/**
 * Open Modal for New Job
 */
function new_talent_job() {
    $('#talent_job_form')[0].reset();
    $('#job_id').val('');
    $('#talent_job_modal_title').text('New Job / Audition');
    $('#status').val('quote_sent').selectpicker('refresh');
    $('#client_id').val('').selectpicker('refresh');
    $('#category_id').val('').selectpicker('refresh');
    $('#source_id').val('').selectpicker('refresh');
    $('#direction_type').val('Self-Record').selectpicker('refresh');
    $('input[name="audio_specs"]').val('48kHz / 24-bit Mono WAV');
    $('input[name="audio_link"]').val('');
    
    calculate_modal_rates();
    $('#talent_job_modal').modal('show');
}

/**
 * Open Modal for Editing Job
 */
function edit_talent_job(id) {
    $.get(admin_url + 'ckm_talent_pipeline/get_job/' + id, function(data) {
        if (!data) return;

        $('#job_id').val(data.id);
        $('#talent_job_modal_title').text('Edit: ' + data.job_title);
        $('input[name="job_title"]').val(data.job_title);
        $('input[name="agent_name"]').val(data.agent_name);
        $('input[name="role_name"]').val(data.role_name);
        $('input[name="word_count"]').val(data.word_count);
        $('input[name="duration_seconds"]').val(data.duration_seconds);
        $('textarea[name="notes"]').val(data.notes);

        $('#status').val(data.status).selectpicker('refresh');
        $('#client_id').val(data.client_id).selectpicker('refresh');
        $('#category_id').val(data.category_id).selectpicker('refresh');
        $('#source_id').val(data.source_id).selectpicker('refresh');

        $('#modal_bsf').val(data.bsf_amount);
        $('#modal_usage').val(data.usage_amount);
        $('#modal_commission').val(data.commission_percent);

        $('input[name="usage_medium"]').val(data.usage_medium);
        $('input[name="usage_territory"]').val(data.usage_territory);
        $('input[name="usage_duration"]').val(data.usage_duration);
        $('input[name="usage_expiry_date"]').val(data.usage_expiry_date);

        $('#direction_type').val(data.direction_type || 'Self-Record').selectpicker('refresh');
        $('input[name="direction_link"]').val(data.direction_link);
        $('input[name="session_datetime"]').val(data.session_datetime);
        $('input[name="delivery_deadline"]').val(data.delivery_deadline);
        $('input[name="audio_specs"]').val(data.audio_specs || '48kHz / 24-bit Mono WAV');
        $('input[name="audio_link"]').val(data.audio_link || '');

        calculate_modal_rates();
        $('#talent_job_modal').modal('show');
    }, 'json');
}

/**
 * Modal Rate Calculations
 */
function init_rate_calculator() {
    $('#modal_bsf, #modal_usage, #modal_commission').on('input keyup change', function() {
        calculate_modal_rates();
    });
}

function calculate_modal_rates() {
    var bsf = parseFloat($('#modal_bsf').val()) || 0;
    var usage = parseFloat($('#modal_usage').val()) || 0;
    var comm = parseFloat($('#modal_commission').val()) || 0;

    var gross = bsf + usage;
    var net = gross - ((gross * comm) / 100);

    $('#modal_gross_preview').text(gross.toFixed(2));
    $('#modal_net_preview').text(net.toFixed(2));
}

/**
 * Live Search and Genre Filter Pills
 */
function init_live_search_and_filters() {
    var activeCategory = 'all';
    var searchQuery = '';

    function filterCards() {
        var cards = $('.ckm-card');
        cards.each(function() {
            var card = $(this);
            var cardCat = card.data('category-id');
            var cardSearch = (card.data('search') || '').toString();

            var matchesCat = (activeCategory === 'all' || cardCat == activeCategory);
            var matchesSearch = (searchQuery === '' || cardSearch.indexOf(searchQuery) !== -1);

            if (matchesCat && matchesSearch) {
                card.show();
            } else {
                card.hide();
            }
        });
    }

    $('#ckm_search_input').on('keyup input', function() {
        searchQuery = $(this).val().toLowerCase().trim();
        filterCards();
    });

    window.filter_by_genre_select = function(val) {
        activeCategory = val;
        filterCards();
    };

    $('#ckm_genre_select').on('change', function() {
        activeCategory = $(this).val();
        filterCards();
    });
}

/**
 * Smart Casting Call & Email Parser Engine
 */
function execute_smart_parser() {
    var text = $('#raw_casting_text').val().trim();
    if (!text) {
        alert('Please paste some email or breakdown text first.');
        return;
    }

    var lines = text.split('\n');
    var parsed = {
        title: '',
        role: '',
        words: 0,
        bsf: 0,
        usage: 0,
        deadline: '',
        notes: text
    };

    var titleMatch = text.match(/(?:Subject|Project|Campaign|Title|Job):\s*([^\n\r]+)/i);
    if (titleMatch) {
        parsed.title = titleMatch[1].replace(/^(Re:\s*|Fwd:\s*|Audition:\s*|Casting:\s*)/i, '').trim();
    } else if (lines.length > 0) {
        parsed.title = lines[0].replace(/^(Re:\s*|Fwd:\s*|Subject:\s*)/i, '').trim();
    }

    var roleMatch = text.match(/(?:Role|Character|Voice|Persona):\s*([^\n\r]+)/i);
    if (roleMatch) {
        parsed.role = roleMatch[1].trim();
    }

    var wordMatch = text.match(/(\d+)\s*(?:words|word|w)\b/i);
    if (wordMatch) {
        parsed.words = parseInt(wordMatch[1]);
    }

    var bsfMatch = text.match(/(?:BSF|Session Fee|Base Fee|Fee):\s*[£$€]?\s*(\d+(?:\.\d{2})?)/i);
    if (bsfMatch) {
        parsed.bsf = parseFloat(bsfMatch[1]);
    }

    var usageMatch = text.match(/(?:Usage|Buyout|Licensing):\s*[£$€]?\s*(\d+(?:\.\d{2})?)/i);
    if (usageMatch) {
        parsed.usage = parseFloat(usageMatch[1]);
    }

    if (!parsed.bsf && !parsed.usage) {
        var budgetMatch = text.match(/(?:Budget|Rate|Total Fee):\s*[£$€]?\s*(\d+(?:\.\d{2})?)/i);
        if (budgetMatch) {
            parsed.bsf = parseFloat(budgetMatch[1]);
        }
    }

    $('#smart_parser_modal').modal('hide');
    new_talent_job();

    if (parsed.title) $('input[name="job_title"]').val(parsed.title);
    if (parsed.role) $('input[name="role_name"]').val(parsed.role);
    if (parsed.words) $('input[name="word_count"]').val(parsed.words);
    if (parsed.bsf) $('#modal_bsf').val(parsed.bsf);
    if (parsed.usage) $('#modal_usage').val(parsed.usage);
    $('textarea[name="notes"]').val(text);

    calculate_modal_rates();
}

/**
 * Built-In VO Rate & Buyout Calculator Widget
 */
function init_vo_rate_widget() {
    $('#calc_words, #calc_pacing, #calc_genre, #calc_medium, #calc_commission, #calc_tax_reserve_rate').on('input change keyup', function() {
        calculate_vo_rate_widget();
    });
    calculate_vo_rate_widget();
}

function apply_rate_preset(presetKey) {
    if (presetKey === 'tv_national') {
        $('#calc_genre').val('commercial');
        $('#calc_words').val(75);
        $('#calc_pacing').val(175);
        $('#calc_medium').val('4.0');
    } else if (presetKey === 'paid_social') {
        $('#calc_genre').val('commercial');
        $('#calc_words').val(120);
        $('#calc_pacing').val(150);
        $('#calc_medium').val('2.0');
    } else if (presetKey === 'corp_explainer') {
        $('#calc_genre').val('corporate');
        $('#calc_words').val(450);
        $('#calc_pacing').val(150);
        $('#calc_medium').val('0');
    } else if (presetKey === 'game_principal') {
        $('#calc_genre').val('animation');
        $('#calc_words').val(600);
        $('#calc_pacing').val(130);
        $('#calc_medium').val('1.0');
    } else if (presetKey === 'elearning_module') {
        $('#calc_genre').val('elearning');
        $('#calc_words').val(1500);
        $('#calc_pacing').val(150);
        $('#calc_medium').val('0');
    }

    if ($.fn.selectpicker) {
        $('#calc_genre, #calc_medium').selectpicker('refresh');
    }
    calculate_vo_rate_widget();
}

function calculate_vo_rate_widget() {
    var words = parseInt($('#calc_words').val()) || 0;
    var pacing = parseInt($('#calc_pacing').val()) || 150;
    var baseBsf = parseFloat($('#calc_genre').find(':selected').data('bsf')) || 250;
    var usageMultiplier = parseFloat($('#calc_medium').val()) || 0;
    var commPercent = parseFloat($('#calc_commission').val()) || 0;
    var taxRate = parseFloat($('#calc_tax_reserve_rate').val()) || 25;

    var totalSeconds = Math.round((words / pacing) * 60);
    var mins = Math.floor(totalSeconds / 60);
    var secs = totalSeconds % 60;
    var timeStr = (mins > 0 ? mins + ' min ' : '') + secs + ' sec';
    $('#calc_est_time').text('~' + timeStr);

    var calculatedBsf = baseBsf;
    if (words > 500) {
        var extraWords = words - 500;
        calculatedBsf += Math.ceil(extraWords / 100) * 25;
    }

    var calculatedUsage = calculatedBsf * usageMultiplier;
    var gross = calculatedBsf + calculatedUsage;
    var net = gross - ((gross * commPercent) / 100);
    var taxReserve = net * (taxRate / 100);

    $('#calc_bsf_display').text('£' + calculatedBsf.toFixed(2));
    $('#calc_usage_display').text('£' + calculatedUsage.toFixed(2));
    $('#calc_gross_display').text('£' + gross.toFixed(2));
    $('#calc_net_display').text('£' + net.toFixed(2));
    $('#calc_tax_reserve_display').text('£' + taxReserve.toFixed(2));
    $('#calc_tax_pct_label').text(taxRate);
}

function apply_calculator_rates_to_job() {
    var bsf = parseFloat($('#calc_bsf_display').text().replace(/[£$€]/g, '')) || 0;
    var usage = parseFloat($('#calc_usage_display').text().replace(/[£$€]/g, '')) || 0;
    var comm = parseFloat($('#calc_commission').val()) || 0;
    var words = parseInt($('#calc_words').val()) || 0;

    $('#rate_calculator_modal').modal('hide');

    if (!$('#talent_job_modal').hasClass('in')) {
        new_talent_job();
    }

    $('#modal_bsf').val(bsf);
    $('#modal_usage').val(usage);
    $('#modal_commission').val(comm);
    if (words > 0) $('input[name="word_count"]').val(words);

    calculate_modal_rates();
}

/**
 * Preview Auto-Quotation Draft
 */
function preview_auto_quote(potentialId, recipientEmail) {
    $('#quote_potential_id').val(potentialId || '');
    $('#quote_send_feedback').addClass('hide').removeClass('alert-success alert-danger').text('');
    $('#btn_send_quote').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Quotation Email');
    $('#quote_template_selector').val('standard');
    
    $.get(admin_url + 'ckm_talent_pipeline/get_auto_quote/' + potentialId + '?template=standard', function(res) {
        if (res && res.quote_text) {
            $('#quote_recipient').val(recipientEmail || '');
            $('#quote_subject').val('Voice Over Quote & Availability');
            $('#quote_message_body').val(res.quote_text);
            $('#auto_quote_modal').modal('show');
        }
    }, 'json');
}

/**
 * Send Quote Email via AJAX
 */
function send_quote_email_ajax() {
    var potentialId = $('#quote_potential_id').val();
    var recipient   = $('#quote_recipient').val().trim();
    var subject     = $('#quote_subject').val().trim();
    var message     = $('#quote_message_body').val();

    if (!recipient) {
        alert('Please provide a recipient email address.');
        return;
    }

    var $btn = $('#btn_send_quote');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
    $('#quote_send_feedback').addClass('hide');

    $.post(admin_url + 'ckm_talent_pipeline/send_quote_email', {
        potential_id: potentialId,
        recipient: recipient,
        subject: subject,
        message: message
    }, function(res) {
        $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Quotation Email Now');
        if (res.success) {
            $('#quote_send_feedback').removeClass('hide alert-danger').addClass('alert-success').text(res.message);
            setTimeout(function() {
                $('#auto_quote_modal').modal('hide');
                window.location.reload();
            }, 1200);
        } else {
            $('#quote_send_feedback').removeClass('hide alert-success').addClass('alert-danger').text(res.message);
        }
    }, 'json').fail(function() {
        $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Quotation Email Now');
        $('#quote_send_feedback').removeClass('hide alert-success').addClass('alert-danger').text('An error occurred while connecting to the email server.');
    });
}

/**
 * Copy Auto-Quote to Clipboard
 */
function copy_quote_to_clipboard() {
    var copyText = document.getElementById("quote_message_body");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert_float('success', "Quotation response copied to clipboard! You can paste it directly into your email reply.");
    $('#auto_quote_modal').modal('hide');
}

/**
 * Test IMAP Connection via AJAX
 */
function test_imap_connection_ajax() {
    var $btn = $('#btn_test_imap');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Testing Connection...');

    $.get(admin_url + 'ckm_talent_pipeline/test_imap', function(res) {
        $btn.prop('disabled', false).html('<i class="fa fa-plug"></i> Test Connection &amp; Count Messages');
        if (res.success) {
            alert_float('success', res.message);
        } else {
            alert_float('warning', res.message);
        }
    }, 'json').fail(function() {
        $btn.prop('disabled', false).html('<i class="fa fa-plug"></i> Test Connection &amp; Count Messages');
        alert_float('danger', 'Failed to reach server to test IMAP connection.');
    });
}

/**
 * Teleprompter & Live Take Timer Engine
 */
var prompterScrollInterval = null;
var prompterTimerInterval = null;
var prompterTimerStartTime = 0;
var prompterTimerElapsed = 0;
var prompterIsRunning = false;
var prompterFontSize = 22;

function toggle_teleprompter() {
    var viewport = document.getElementById('teleprompter_viewport');
    var $btn = $('#btn_teleprompter_play');

    if (prompterScrollInterval) {
        clearInterval(prompterScrollInterval);
        prompterScrollInterval = null;
        $btn.html('<i class="fa fa-play"></i> Auto-Scroll').removeClass('btn-warning').addClass('btn-success');
    } else {
        var speed = parseInt($('#prompter_speed').val()) || 3;
        prompterScrollInterval = setInterval(function() {
            viewport.scrollTop += speed;
            if (viewport.scrollTop + viewport.clientHeight >= viewport.scrollHeight) {
                toggle_teleprompter(); // Reached bottom
            }
        }, 50);
        $btn.html('<i class="fa fa-pause"></i> Pause').removeClass('btn-success').addClass('btn-warning');
    }
}

function reset_teleprompter() {
    if (prompterScrollInterval) {
        toggle_teleprompter();
    }
    var viewport = document.getElementById('teleprompter_viewport');
    viewport.scrollTop = 0;
}

function adjust_prompter_font(delta) {
    prompterFontSize = Math.max(14, Math.min(48, prompterFontSize + delta));
    $('#teleprompter_viewport').css('font-size', prompterFontSize + 'px');
}

function toggle_take_timer() {
    var $btn = $('#btn_timer_toggle');
    if (prompterIsRunning) {
        clearInterval(prompterTimerInterval);
        prompterIsRunning = false;
        $btn.html('<i class="fa fa-circle"></i> Record').removeClass('btn-warning').addClass('btn-danger');
    } else {
        prompterTimerStartTime = Date.now() - prompterTimerElapsed;
        prompterTimerInterval = setInterval(function() {
            prompterTimerElapsed = Date.now() - prompterTimerStartTime;
            var totalSeconds = Math.floor(prompterTimerElapsed / 1000);
            var minutes = Math.floor(totalSeconds / 60);
            var seconds = totalSeconds % 60;
            var millis = Math.floor((prompterTimerElapsed % 1000) / 100);

            var timeStr = (minutes < 10 ? '0' : '') + minutes + ':' + 
                          (seconds < 10 ? '0' : '') + seconds + '.' + millis;
            $('#prompter_timer_display').text(timeStr);
        }, 100);
        prompterIsRunning = true;
        $btn.html('<i class="fa fa-pause"></i> Stop').removeClass('btn-danger').addClass('btn-warning');
    }
}

function reset_take_timer() {
    clearInterval(prompterTimerInterval);
    prompterIsRunning = false;
    prompterTimerElapsed = 0;
    $('#prompter_timer_display').text('00:00.0');
    $('#btn_timer_toggle').html('<i class="fa fa-circle"></i> Record').removeClass('btn-warning').addClass('btn-danger');
}

// Live word count updater for teleprompter
$(document).on('input', '#teleprompter_text', function() {
    var text = $(this).text().trim();
    var words = text ? text.split(/\s+/).length : 0;
    var estSeconds = Math.round((words / 150) * 60);
    var mins = Math.floor(estSeconds / 60);
    var secs = estSeconds % 60;
    var timeFormatted = (mins > 0 ? mins + 'm ' : '') + secs + 's';
    $('#prompter_word_count').text('Words: ~' + words + ' | Est: ~' + timeFormatted + ' (at 150 WPM)');
});

/**
 * IVR Prompt Batch Calculator
 */
function calculate_ivr_rate() {
    var baseFee = parseFloat($('#ivr_base_fee').val()) || 150.00;
    var totalPrompts = parseInt($('#ivr_total_prompts').val()) || 1;
    var extraFee = parseFloat($('#ivr_extra_prompt_fee').val()) || 10.00;

    var extraPrompts = Math.max(0, totalPrompts - 10);
    var extraTotal = extraPrompts * extraFee;
    var grandTotal = baseFee + extraTotal;

    $('#ivr_extra_count').text(extraPrompts);
    $('#ivr_extra_display').text('£' + extraTotal.toFixed(2));
    $('#ivr_total_display').text('£' + grandTotal.toFixed(2));
}

function apply_ivr_to_calculator() {
    var baseFee = parseFloat($('#ivr_base_fee').val()) || 150.00;
    var totalPrompts = parseInt($('#ivr_total_prompts').val()) || 1;
    var extraFee = parseFloat($('#ivr_extra_prompt_fee').val()) || 10.00;
    var extraPrompts = Math.max(0, totalPrompts - 10);
    var grandTotal = baseFee + (extraPrompts * extraFee);

    $('#calc_genre').val('telephony').selectpicker('refresh');
    $('#calc_bsf_display').text('£' + grandTotal.toFixed(2));
    $('#calc_gross_display').text('£' + grandTotal.toFixed(2));
    var comm = parseFloat($('#calc_commission').val()) || 0;
    var net = grandTotal - ((grandTotal * comm) / 100);
    $('#calc_net_display').text('£' + net.toFixed(2));

    $('a[href="#calc_tab_custom"]').tab('show');
    alert_float('success', 'IVR batch rate transferred to quote builder!');
}

/**
 * Buyout Renewal Pitch Engine
 */
function open_buyout_pitch_modal(jobId) {
    $('#pitch_job_id').val(jobId);
    $('#pitch_send_feedback').addClass('hide').removeClass('alert-success alert-danger').text('');
    $('#btn_send_pitch').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Renewal Pitch Email Now');

    $.get(admin_url + 'ckm_talent_pipeline/get_buyout_pitch/' + jobId, function(res) {
        if (res && res.pitch_text) {
            $('#pitch_recipient').val(res.email || '');
            $('#pitch_subject').val('Voice Over License Renewal: ' + (res.title || 'Project Campaign'));
            $('#pitch_message_body').val(res.pitch_text);
            $('#buyout_pitch_modal').modal('show');
        }
    }, 'json');
}

function send_pitch_email_ajax() {
    var jobId     = $('#pitch_job_id').val();
    var recipient = $('#pitch_recipient').val().trim();
    var subject   = $('#pitch_subject').val().trim();
    var message   = $('#pitch_message_body').val();

    if (!recipient) {
        alert('Please provide a recipient email address.');
        return;
    }

    var $btn = $('#btn_send_pitch');
    $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Sending...');
    $('#pitch_send_feedback').addClass('hide');

    $.post(admin_url + 'ckm_talent_pipeline/send_buyout_pitch', {
        job_id: jobId,
        recipient: recipient,
        subject: subject,
        message: message
    }, function(res) {
        $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Renewal Pitch Email Now');
        if (res.success) {
            $('#pitch_send_feedback').removeClass('hide alert-danger').addClass('alert-success').text(res.message);
            setTimeout(function() {
                $('#buyout_pitch_modal').modal('hide');
            }, 1500);
        } else {
            $('#pitch_send_feedback').removeClass('hide alert-success').addClass('alert-danger').text(res.message);
        }
    }, 'json').fail(function() {
        $btn.prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Renewal Pitch Email Now');
        $('#pitch_send_feedback').removeClass('hide alert-success').addClass('alert-danger').text('An error occurred while communicating with the mail server.');
    });
}

function copy_pitch_to_clipboard() {
    var copyText = document.getElementById("pitch_message_body");
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert_float('success', "Renewal pitch copied to clipboard! You can paste it directly into your email reply.");
    $('#buyout_pitch_modal').modal('hide');
}
