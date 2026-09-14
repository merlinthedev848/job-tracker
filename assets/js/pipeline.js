/* CKM Talent Pipeline JavaScript */

$(function() {
    init_kanban_drag_drop();
    init_rate_calculator();

    // Auto-update commission when source changes
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
                // If moving to Lost, prompt for reason modal
                if (targetStatus === 'lost') {
                    $('#loss_job_id').val(jobId);
                    $('#talent_loss_reason_modal').modal('show');
                } else {
                    update_job_status_ajax(jobId, targetStatus);
                    col.appendChild(card);
                }
            }
        });
    });
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
        // Success
    }, 'json');
}

/**
 * Confirm loss reason from modal
 */
function confirm_loss_status() {
    var jobId = $('#loss_job_id').val();
    var reasonId = $('#loss_reason_id').val();
    var notes = $('#loss_notes').val();

    update_job_status_ajax(jobId, 'lost', reasonId, notes);

    // Move card in DOM to lost column
    var card = document.querySelector('.ckm-card[data-job-id="' + jobId + '"]');
    var lostCol = document.getElementById('kanban-col-lost');
    if (card && lostCol) {
        lostCol.appendChild(card);
    }

    $('#talent_loss_reason_modal').modal('hide');
    // Reset modal
    $('#loss_reason_id').val('').selectpicker('refresh');
    $('#loss_notes').val('');
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
        $('input[name="audio_specs"]').val(data.audio_specs);

        calculate_modal_rates();
        $('#talent_job_modal').modal('show');
    }, 'json');
}

/**
 * Rate Calculator
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
