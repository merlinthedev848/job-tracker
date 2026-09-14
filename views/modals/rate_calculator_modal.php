<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Built-In VO Rate & Buyout Calculator Modal -->
<div class="modal fade" id="rate_calculator_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title text-white">
                    <i class="fa fa-calculator"></i> <strong>VO Rate & Buyout Calculator</strong>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Calculator Inputs -->
                    <div class="col-md-6">
                        <h4 class="bold mbot15 text-primary"><i class="fa fa-sliders"></i> Project Parameters</h4>
                        
                        <div class="form-group">
                            <label class="control-label bold">Word Count:</label>
                            <input type="number" id="calc_words" class="form-control" value="250" placeholder="e.g. 250">
                            <small class="text-muted">Estimated Duration: <strong id="calc_est_time" class="text-info">~1 min 40 sec</strong> (at 150 wpm)</small>
                        </div>

                        <div class="form-group">
                            <label class="control-label bold">Genre / Project Type:</label>
                            <select id="calc_genre" class="form-control selectpicker" data-width="100%">
                                <option value="commercial" data-bsf="300">Commercial (TV / Radio / Web) - Base £300/BSF</option>
                                <option value="corporate" data-bsf="250" selected>Corporate / Explainer - Base £250/BSF</option>
                                <option value="elearning" data-bsf="200">E-Learning / Training - Base £200/BSF</option>
                                <option value="animation" data-bsf="350">Animation & Gaming - Base £350/BSF</option>
                                <option value="promo" data-bsf="400">Promo / Imaging - Base £400/BSF</option>
                                <option value="telephony" data-bsf="150">IVR / Telephony - Base £150/BSF</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label bold">Licensing / Usage Medium:</label>
                            <select id="calc_medium" class="form-control selectpicker" data-width="100%">
                                <option value="0" selected>Internal / Non-Broadcast (0% Usage)</option>
                                <option value="1.0">Web Non-Broadcast / Organic Social (100% BSF)</option>
                                <option value="2.0">Paid Digital Ads / YouTube / Meta (200% BSF)</option>
                                <option value="3.0">Regional TV / Radio (300% BSF)</option>
                                <option value="4.0">National TV (1 Year) (400% BSF)</option>
                                <option value="6.0">Worldwide / All Media (1 Year) (600% BSF)</option>
                                <option value="10.0">In Perpetuity / Full Buyout (1000% BSF)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="control-label bold">Agent / Platform Commission (%):</label>
                            <input type="number" id="calc_commission" class="form-control" value="20" min="0" max="100">
                        </div>
                    </div>

                    <!-- Calculated Results Box -->
                    <div class="col-md-6">
                        <div class="panel panel-primary mtop10">
                            <div class="panel-heading bold text-center"><i class="fa fa-coins"></i> Quote Breakdown</div>
                            <div class="panel-body text-center p20">
                                <div class="mbot15">
                                    <span class="text-muted text-uppercase block font-xs">Basic Session Fee (BSF)</span>
                                    <h3 id="calc_bsf_display" class="bold text-primary mtop5 mbot0">£250.00</h3>
                                </div>
                                <div class="mbot15">
                                    <span class="text-muted text-uppercase block font-xs">Usage / Buyout Fee</span>
                                    <h3 id="calc_usage_display" class="bold text-warning mtop5 mbot0">£0.00</h3>
                                </div>
                                <hr class="mtop10 mbot10">
                                <div class="mbot15">
                                    <span class="text-muted text-uppercase block font-xs">Gross Quote Total</span>
                                    <h2 id="calc_gross_display" class="bold text-success mtop5 mbot0">£250.00</h2>
                                </div>
                                <div class="alert alert-success p10 mbot0">
                                    <span class="font-xs bold">Your Net Take-Home (After Comm):</span>
                                    <h4 id="calc_net_display" class="bold mtop5 mbot0">£200.00</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-success btn-lg" onclick="apply_calculator_rates_to_job();">
                    <i class="fa fa-arrow-right"></i> Apply Rates to Job / Quote
                </button>
            </div>
        </div>
    </div>
</div>
