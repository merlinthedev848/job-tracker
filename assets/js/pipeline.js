/* CKM Talent Pipeline JavaScript Engine */

$(function() {
    init_kanban_drag_drop();
    init_rate_calculator();
    init_live_search_and_filters();
    init_vo_rate_widget();

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

    $('.ckm-genre-filter-btn').on('click', function() {
        $('.ckm-genre-filter-btn').removeClass('active');
        $(this).addClass('active');
        activeCategory = $(this).data('cat-id');
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
    $('#calc_words, #calc_genre, #calc_medium, #calc_commission').on('input change keyup', function() {
        calculate_vo_rate_widget();
    });
    calculate_vo_rate_widget();
}

function calculate_vo_rate_widget() {
    var words = parseInt($('#calc_words').val()) || 0;
    var baseBsf = parseFloat($('#calc_genre').find(':selected').data('bsf')) || 250;
    var usageMultiplier = parseFloat($('#calc_medium').val()) || 0;
    var commPercent = parseFloat($('#calc_commission').val()) || 0;

    var totalSeconds = Math.round((words / 150) * 60);
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

    $('#calc_bsf_display').text('£' + calculatedBsf.toFixed(2));
    $('#calc_usage_display').text('£' + calculatedUsage.toFixed(2));
    $('#calc_gross_display').text('£' + gross.toFixed(2));
    $('#calc_net_display').text('£' + net.toFixed(2));
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
    $('#btn_send_quote').prop('disabled', false).html('<i class="fa fa-paper-plane"></i> Send Quotation Email Now');
    
    $.get(admin_url + 'ckm_talent_pipeline/get_auto_quote/' + potentialId, function(res) {
        if (res && res.quote_text) {
            $('#quote_recipient').val(recipientEmail || '');
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
