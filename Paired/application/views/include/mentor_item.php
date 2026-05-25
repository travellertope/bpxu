<?php foreach ($mentors as $mentor): ?>
    <div class="<?php if(isset($page_title) && $page_title == 'Mentors'){echo "col-md-3 col-xs-6";} ?>">
        <div class="cards b1 mb-4 br-6 p-2 lift-sm hover-shadow">
           <a href="<?php echo base_url('mentor/'. $mentor->slug) ?>">
                <?php if (empty($mentor->image)): ?>
                    <?php $mentor_img = base_url('assets/images/no-photo-sm.png'); ?>
                <?php else: ?>
                    <?php $mentor_img = base_url($mentor->image); ?>
                <?php endif ?>
                <div class="uitem-bg" style="background-image: url(<?php echo $mentor_img; ?>">
                    <?php if ($mentor->kyc_verified == 1): ?>
                        <span class="verified-badge" data-toggle="tooltip" data-title="<?php echo trans('kyc').' '.trans('verified') ?>">
                            <span><i class="bi bi-patch-check-fill"></i> <?php echo strtolower(trans('verified')) ?></span>
                        </span>
                    <?php endif ?>
                </div>
           </a>

          <div class="card-body pl-1 pr-1 pb-1">

            <div class="d-flex justify-content-between pr-2 pl-2">
                <div class="mentor_title mr-2"><b><?php echo html_escape($mentor->name); ?></b></div>
                <?php $code = get_by_id($mentor->country, 'country')->code; ?>
                <div class="text-right">
                    <img data-toggle="tooltip" data-placement="top" title="<?php echo get_by_id($mentor->country, 'country')->name; ?>" class="flag-cimg" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>">
                </div>
            </div>

            <div class="mt-2 mb-3 text-dark pl-2 pr-2 fs-13">
                <?php if (!empty($mentor->designation)): ?>
                    <span><i class="bi bi-briefcase mr-1"></i> <?php echo html_escape($mentor->designation) ?>
                <?php endif ?> 
                <?php if (!empty($mentor->company)): ?>
                    <span class="text-muted fw-500"><?php echo trans('at') ?></span> <?php echo html_escape($mentor->company) ?>.</span>
                <?php endif ?>
            </div>

            <div class="d-flex justify-content-between fs-12 br-4 mb-0 pr-2 pl-2">
                <div>
                    <!--
                    <p class="mt-2 mb-0 fw-500 text-muted"><?php echo trans('experience') ?></p>
                    <p class="mt-0 mb-2 text-dark fs-16">
                        <?php if (empty($mentor->experience_year)): ?>
                            1 <?php echo trans('years') ?>
                        <?php else: ?>
                            <?php echo html_escape($mentor->experience_year); ?> <?php echo trans('years') ?>
                        <?php endif ?>
                    </p> 
                    -->
                </div>

                <div class="brr-1"></div>
<!--                <div>
                    <p class="mt-2 mb-0 fw-500 text-muted"><?php echo trans('attendence') ?></p>
                    <p class="mt-0 mb-2 text-dark fs-16"><?php echo get_user_attendence($mentor->id) ?>%</p>
                </div> -->
            </div>
            
          </div>
        </div>
    </div>
<?php endforeach ?>