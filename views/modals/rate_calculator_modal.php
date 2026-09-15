<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<!-- Comprehensive VO Rate, Buyout & IVR Calculator Modal -->
<div class="modal fade" id="rate_calculator_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%; max-width: 1050px;">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <div class="display-flex justify-between align-center mright20">
                    <h4 class="modal-title text-white">
                        <i class="fa fa-calculator"></i> <strong>Voice Over Rate Engine & Industry Guide</strong>
                    </h4>
                </div>
            </div>
            <div class="modal-body p20">
                <!-- Tabs inside Calculator -->
                <ul class="nav nav-tabs mbot20" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#calc_tab_custom" aria-controls="calc_tab_custom" role="tab" data-toggle="tab">
                            <i class="fa fa-sliders"></i> <strong>BSF & Usage Calculator</strong>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#calc_tab_ivr" aria-controls="calc_tab_ivr" role="tab" data-toggle="tab">
                            <i class="fa fa-phone"></i> <strong>IVR / Telephony Prompts</strong>
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="#calc_tab_guide" aria-controls="calc_tab_guide" role="tab" data-toggle="tab">
                            <i class="fa fa-book"></i> <strong>GVAA & Equity Rate Guide Reference</strong>
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- TAB 1: CUSTOM BSF & USAGE CALCULATOR -->
                    <div role="tabpanel" class="tab-pane active" id="calc_tab_custom">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="bold mbot15 text-primary"><i class="fa fa-sliders"></i> Project Parameters</h4>
                                
                                <div class="form-group">
                                    <label class="control-label bold">Word Count & Delivery Pacing:</label>
                                    <div class="row">
                                        <div class="col-xs-6">
                                            <input type="number" id="calc_words" class="form-control" value="250" placeholder="e.g. 250">
                                        </div>
                                        <div class="col-xs-6">
                                            <select id="calc_pacing" class="form-control">
                                                <option value="130">Slow / Dramatic (130 WPM)</option>
                                                <option value="150" selected>Conversational (150 WPM)</option>
                                                <option value="175">Fast / Commercial (175 WPM)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <small class="text-muted">Estimated Duration: <strong id="calc_est_time" class="text-info">~1 min 40 sec</strong></small>
                                </div>

                                <!-- Quick Rate Preset Buttons -->
                                <div class="form-group mbot15">
                                    <label class="control-label font-xs bold text-muted block">1-Click GVAA & Industry Presets:</label>
                                    <div class="display-flex flex-wrap gap-5">
                                        <button type="button" class="btn btn-default btn-xs" onclick="apply_rate_preset('tv_national');">📺 National TV (1yr)</button>
                                        <button type="button" class="btn btn-default btn-xs" onclick="apply_rate_preset('paid_social');">📱 Paid Social (3mo)</button>
                                        <button type="button" class="btn btn-default btn-xs" onclick="apply_rate_preset('corp_explainer');">🏢 Corp Explainer</button>
                                        <button type="button" class="btn btn-default btn-xs" onclick="apply_rate_preset('game_principal');">🎮 Game Principal</button>
                                        <button type="button" class="btn btn-default btn-xs" onclick="apply_rate_preset('elearning_module');">🎓 E-Learning</button>
                                    </div>
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

                                <div class="row">
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label class="control-label bold">Commission (%):</label>
                                            <input type="number" id="calc_commission" class="form-control" value="20" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="col-xs-6">
                                        <div class="form-group">
                                            <label class="control-label bold">Tax Reserve (%):</label>
                                            <input type="number" id="calc_tax_reserve_rate" class="form-control" value="25" min="0" max="100">
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                                        <div class="alert alert-success p10 mbot10">
                                            <span class="font-xs bold">Your Net Take-Home (After Comm):</span>
                                            <h4 id="calc_net_display" class="bold mtop5 mbot0">£200.00</h4>
                                        </div>
                                        <div class="alert alert-info p8 font-xs mbot0">
                                            <i class="fa fa-bank"></i> Recommended Tax Reserve: <strong id="calc_tax_reserve_display">£50.00</strong> (<span id="calc_tax_pct_label">25</span>%)
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: IVR / TELEPHONY BATCH CALCULATOR -->
                    <div role="tabpanel" class="tab-pane" id="calc_tab_ivr">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="bold mbot15 text-primary"><i class="fa fa-phone"></i> IVR / On-Hold Prompt Batching</h4>
                                <div class="form-group">
                                    <label class="control-label bold">Base Session Fee (Includes up to 10 Prompts):</label>
                                    <input type="number" id="ivr_base_fee" class="form-control" value="150.00">
                                </div>
                                <div class="form-group">
                                    <label class="control-label bold">Total Number of Prompts / Audio Files:</label>
                                    <input type="number" id="ivr_total_prompts" class="form-control" value="25" min="1" oninput="calculate_ivr_rate();">
                                </div>
                                <div class="form-group">
                                    <label class="control-label bold">Fee Per Extra Prompt (Over 10):</label>
                                    <input type="number" id="ivr_extra_prompt_fee" class="form-control" value="10.00" oninput="calculate_ivr_rate();">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-info mtop10">
                                    <div class="panel-heading bold text-center"><i class="fa fa-calculator"></i> IVR Prompt Quote</div>
                                    <div class="panel-body text-center p20">
                                        <div class="mbot15">
                                            <span class="text-muted text-uppercase block font-xs">Included Prompts (1-10)</span>
                                            <h4 id="ivr_base_display" class="bold text-primary mtop5 mbot0">£150.00</h4>
                                        </div>
                                        <div class="mbot15">
                                            <span class="text-muted text-uppercase block font-xs">Extra Prompts (<span id="ivr_extra_count">15</span> @ £10.00)</span>
                                            <h4 id="ivr_extra_display" class="bold text-warning mtop5 mbot0">£150.00</h4>
                                        </div>
                                        <hr class="mtop10 mbot10">
                                        <div class="mbot15">
                                            <span class="text-muted text-uppercase block font-xs">Total IVR Package Quote</span>
                                            <h2 id="ivr_total_display" class="bold text-success mtop5 mbot0">£300.00</h2>
                                        </div>
                                        <button type="button" class="btn btn-primary btn-sm btn-block" onclick="apply_ivr_to_calculator();">
                                            <i class="fa fa-arrow-left"></i> Transfer to Main Quote Calculator
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: INDUSTRY STANDARD RATE MATRIX GUIDE -->
                    <div role="tabpanel" class="tab-pane" id="calc_tab_guide">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped font-xs">
                                <thead>
                                    <tr class="active">
                                        <th>Category</th>
                                        <th>Industry Standard BSF</th>
                                        <th>Standard Usage / Buyout Guidance</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="bold">UK TV Commercial (National)</td>
                                        <td>£250 - £350 / hr</td>
                                        <td>400% - 800% BSF (1 Year)</td>
                                        <td>TVRs based on audience reach</td>
                                    </tr>
                                    <tr>
                                        <td class="bold">Web / Online Paid Adverts</td>
                                        <td>£250 - £300</td>
                                        <td>100% - 300% BSF (1 Year Digital)</td>
                                        <td>Paid YouTube, Meta, TikTok</td>
                                    </tr>
                                    <tr>
                                        <td class="bold">Corporate Video / Explainer</td>
                                        <td>£250 - £350 BSF</td>
                                        <td>Included for internal / +100% for public web</td>
                                        <td>Per finished minute or tiered words</td>
                                    </tr>
                                    <tr>
                                        <td class="bold">E-Learning / Training Modules</td>
                                        <td>£150 - £200 / hr</td>
                                        <td>0% (Internal Corporate Use)</td>
                                        <td>Bulk word tiers (e.g. £0.15 - £0.25 / word)</td>
                                    </tr>
                                    <tr>
                                        <td class="bold">Audiobooks (PFH)</td>
                                        <td>£200 - £400 PFH</td>
                                        <td>Per Finished Hour (Includes editing)</td>
                                        <td>~9,000 - 10,000 words per finished hour</td>
                                    </tr>
                                    <tr>
                                        <td class="bold">Video Games / Animation</td>
                                        <td>£300 - £400 / hr</td>
                                        <td>In-game buyout standard (unless AAA franchise)</td>
                                        <td>2-4 hour standard booking blocks</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="button" class="btn btn-success" onclick="apply_calculator_rates_to_job();">
                    <i class="fa fa-arrow-right"></i> Apply Rates to Job / Quote
                </button>
            </div>
        </div>
    </div>
</div>
