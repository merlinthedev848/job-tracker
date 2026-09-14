<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-heading">
                        <h4 class="panel-title"><i class="fa-solid fa-sliders"></i> <?php echo _l('ckm_tp_menu_settings'); ?></h4>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <!-- Categories List -->
                            <div class="col-md-4">
                                <h4 class="bold mbot15"><i class="fa-solid fa-tags"></i> Project Genres & Categories</h4>
                                <ul class="list-group">
                                    <?php foreach ($categories as $cat) { ?>
                                        <li class="list-group-item display-flex justify-between align-center">
                                            <span>
                                                <span class="badge" style="background-color: <?php echo $cat['color']; ?>;">&nbsp;</span>
                                                <?php echo htmlspecialchars($cat['name']); ?>
                                            </span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <!-- Sources List -->
                            <div class="col-md-4">
                                <h4 class="bold mbot15"><i class="fa-solid fa-user-tie"></i> Lead Sources & Default Commissions</h4>
                                <ul class="list-group">
                                    <?php foreach ($sources as $src) { ?>
                                        <li class="list-group-item display-flex justify-between align-center">
                                            <span><?php echo htmlspecialchars($src['name']); ?></span>
                                            <span class="label label-info"><?php echo $src['default_commission']; ?>% comm</span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>

                            <!-- Loss Reasons List -->
                            <div class="col-md-4">
                                <h4 class="bold mbot15"><i class="fa-solid fa-circle-xmark text-danger"></i> Loss Reasons</h4>
                                <ul class="list-group">
                                    <?php foreach ($loss_reasons as $reason) { ?>
                                        <li class="list-group-item">
                                            <?php echo htmlspecialchars($reason['reason']); ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php init_tail(); ?>
