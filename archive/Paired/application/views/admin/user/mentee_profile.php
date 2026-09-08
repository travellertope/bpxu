<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-7 pt-2 mx-auto">
            <div class="card">
              <div class="card-header px-3 mb-0">
                <h5 class="card-title mb-0">Mentees profile</h5>
                <?php $url = $_SERVER['HTTP_REFERER'] ?>
                <div class="card-tools pull-right"><a class="pull-right btn btn-secondary btn-sm" href="<?php echo $url ?>"><i class="fas fa-angle-left"></i> Back</a></div>
              </div>
              <div class="card-body box-profile pt-4 mt-0 p-0">

                <?php if(!empty($mentee->thumb)): ?>
                  <div class="avatar-md  mx-auto" style="background-image: url(<?php echo base_url($mentee->thumb) ?>);"></div>
                <?php else: ?>
                  <div class="avatar-md  mx-auto" style="background-image: url(<?php echo base_url('assets/images/no-photo.png') ?>);"></div>
                <?php endif; ?>

                <div class="text-center">
                  <span class="profile-username font-weight-bold mb-1"><?php echo html_escape($mentee->name) ?></span>

                  <?php $code = get_by_id($mentee->country, 'country')->code; ?>
                  <span data-tooltip="<?php echo get_by_id($mentee->country, 'country')->name; ?>" class="text-dark mb-0 font-weight-bold"><img class="flag-img-mentee ml-1" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>"></span>
                </div>

                <p class="text-muted text-center mb-1"><?php echo html_escape($mentee->email) ?></p>
                <p class="text-muted text-center mb-1"><?php echo html_escape($mentee->phone) ?></p>
                <?php if(!empty($mentee->created_at)): ?>
                <p class="text-muted text-center strong"><?php echo trans('joined') ?>: <?php echo get_time_ago($mentee->created_at) ?></p>
                <?php endif; ?>

                <ul class="list-group list-group-unbordered pt-3 px-5">

                  <?php if(!empty($mentee->gender)): ?>
                  <li class="list-group-item text-dark">
                    <span class="font-weight-bold"><?php echo trans('gender') ?></span>
                    <span class="float-right "><?php echo html_escape($mentee->gender); ?></span>
                  </li>
                  <?php endif; ?>

                  <?php if(!empty($mentee->mentorship_availability)): ?>
                    <li class="list-group-item text-dark">
                      <span class="font-weight-bold">Preferred Mentorship Availability</span>
                      <span class="float-right"><?php echo html_escape($mentee->mentorship_availability); ?></span>
                    </li>
                  <?php endif; ?>

                  <?php if(!empty($mentee->expertise_industry)): ?>
                    <li class="list-group-item text-dark">
                      <span class="font-weight-bold">Field of Expertise</span>
                      <span class="float-right mr-1"><?php    echo html_escape($mentee->expertise_industry) ?></span>
                    </li>
                  <?php endif; ?>

                  <?php if(!empty($mentee->employment_status)): ?>
                    <li class="list-group-item text-dark">
                      <span class="font-weight-bold">Employment Status</span>
                      <span class="float-right mr-1"><?php echo html_escape($mentee->employment_status) ?></span>
                    </li>
                  <?php endif; ?>

                  <?php if(!empty($mentee->linkedin_profile)): ?>
                    <li class="list-group-item text-dark">
                      <span class="font-weight-bold">LinkedIn Profile</span>
                      <span class="float-right mr-1">
                        <a href="<?php echo prep_url($mentee->linkedin_profile) ?>"><?php echo html_escape($mentee->linkedin_profile) ?></a>
                      </span>
                    </li>
                  <?php endif; ?>

                  <?php if(!empty($mentee->experience_year)): ?>
                    <li class="list-group-item text-dark">
                      <span class="font-weight-bold">Experience</span>
                      <span class="float-right mr-1"><?php echo html_escape($mentee->experience_year) ?> Years</span>
                    </li>
                  <?php endif; ?>

                  <?php if(!empty($mentee->membershhip_locale)): ?>
                    <li class="list-group-item text-dark">
                      <span class="font-weight-bold">Membership Locale</span>
                      <span class="float-right mr-1"><?php echo html_escape($mentee->membershhip_locale) ?> Years</span>
                    </li>
                  <?php endif; ?>
                </ul>

                <div class="my-3 px-5">
                  <p class="mt-o mb-0"><b>About Mentee</b></p>
                  <p class="mt-0 mb-1"><?php echo html_escape($mentee->about_me) ?></p>
                </div>

                <div class="mt-3 mb-5 px-5">
                  <p class="mt-o mb-0"><b>Career Goals</b></p>
                  <p class="mt-0 mb-1"><?php echo html_escape($mentee->career_goals) ?></p>
                </div>
              </div>
            </div>
          </div>

        </div>
    </div>
  </div>
</div>