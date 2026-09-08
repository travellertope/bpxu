
    <div class="cards b1 p-2 mb-4 br-6">
       <a href="<?php echo base_url('mentor/slug/'. $mentor->id) ?>">
        <div class="uitem-bg" style="background-image: url(<?php echo base_url($mentor->image); ?>">
        </div>
       </a>

      <div class="card-body pl-1 pr-1 pb-1">

        <div class="d-flex justify-content-between">
            <div class="mentor_title mr-2"><b><?php echo html_escape($mentor->name); ?></b></div>

            <div class="text-right">
                <img data-toggle="tooltip" data-placement="top" title="United State of America" class="flag-img" src="<?php echo base_url('assets/images/flags/'.strtolower('us').'.png') ?>">
            </div>
        </div>

        <div class="mt-2 mb-3 text-dark">
            <span><i class="bi bi-briefcase mr-1"></i> <?php echo get_by_id($mentor->id, 'experiences')->title ?> at <?php echo get_by_id($mentor->id, 'experiences')->company ?>.</span>
        </div>

        <div class="d-flex justify-content-between fs-12 br-4 mb-0">
            <div>
                <p class="mt-2 mb-0 text-muted">Experience</p>
                <p class="mt-0 mb-2 text-dark"><?php echo html_escape($mentor->experience_year); ?></p>
            </div>
            <div class="brr-1"></div>
            <div>
                <p class="mt-2 mb-0 text-muted">Attendence</p>
                <p class="mt-0 mb-2 text-dark">100%</p>
            </div>
        </div>
        
      </div>
    </div>