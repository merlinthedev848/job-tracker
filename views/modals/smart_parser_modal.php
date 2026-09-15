<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Smart Casting Email Parser Modal -->
<div class="modal fade" id="smart_parser_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-white">
                    <i class="fa fa-bolt"></i> <strong>Smart Casting Call & Email Parser</strong>
                </h4>
            </div>
            <div class="modal-body">
                <p class="text-muted font-medium-xs mbot15">
                    Paste the raw email, casting breakdown, or message from your agent/client below. The parser will automatically extract the <strong>Project Title, Role, Rates, Word Count, Delivery Deadline, and Script notes</strong>.
                </p>

                <div class="form-group">
                    <label for="raw_casting_text" class="control-label bold">Paste Raw Email / Breakdown Text:</label>
                    <textarea id="raw_casting_text" class="form-control" rows="8" placeholder="Example:
Subject: Audition - Nike Spring Campaign
Hi Chris, casting for Nike. Role: Confident Narrator.
Word count: approx 180 words.
Fee: BSF £350 + 1 Year UK TV & Web Buyout £1,200.
Deadline: Thursday 18th Sept by 5pm.
Script:
Just do it. Every morning starts with a decision..."></textarea>
                </div>

                <!-- Parsed Live Preview -->
                <div id="parser_preview_box" class="alert alert-info hidden mtop15">
                    <h5 class="bold mtop0"><i class="fa fa-check-circle text-success"></i> Extracted Project Breakdown</h5>
                    <div class="row font-xs">
                        <div class="col-md-6">
                            <p><strong>Title:</strong> <span id="parsed_title" class="text-primary">-</span></p>
                            <p><strong>Role:</strong> <span id="parsed_role" class="text-primary">-</span></p>
                            <p><strong>Word Count / Time:</strong> <span id="parsed_words" class="text-primary">-</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>BSF Fee:</strong> <span id="parsed_bsf" class="text-success bold">-</span></p>
                            <p><strong>Usage Buyout:</strong> <span id="parsed_usage" class="text-success bold">-</span></p>
                            <p><strong>Deadline:</strong> <span id="parsed_deadline" class="text-danger">-</span></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer display-flex justify-between align-center">
                <span class="text-muted font-xs">
                    <i class="fa fa-info-circle text-info"></i> Automatically detects BSF, Usage, WPM, and script lines.
                </span>
                <div>
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="button" class="btn btn-primary bold" onclick="execute_smart_parser();">
                        <i class="fa fa-magic"></i> Extract & Open Job Card
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
