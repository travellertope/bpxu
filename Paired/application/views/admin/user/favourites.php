<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="cards">
          <div class="card-header with-border">
              <h3 class="card-title pt-2"><?php echo trans('favourite-mentor')?> </h3>
          </div>
          
          <?php if(!empty($favourites)): ?>
            <div class="row">
              <?php foreach ($favourites as $favourite): ?>
                <?php 

                 
                  $id = $favourite->favourite_id;
                  $img = get_by_id($id, 'users')->image ;
                  $slug = get_by_id($id, 'users')->slug ;
                ?>


                <div class="col-md-2">
                  <div class="cards b1 mb-4 br-6">
                    <a href="<?php echo base_url('mentor/'. $slug) ?>">
                      <div class="favrt-uitem-bg" style="background-image: url(<?php echo base_url($img); ?>)">
                      </div>
                    </a>

                    <div class="card-body pl-1 pr-1 pb-1">

                      <div class="d-flex justify-content-between pr-2 pl-2">
                        <div class="favrt_mentor_title mr-2 pt-2"><b><?php echo get_by_id($id, 'users')->name; ?></b>
                        </div>
                        <?php $country = get_by_id($id, 'users')->country; ?>
                        <?php $code = get_by_id($country, 'country')->code; ?>
                        <div class="text-right pt-2">

                            <span data-tooltip="<?php echo get_by_id($country, 'country')->name; ?>" class=""><img class="flag-img-booking ml-1" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>"></span>
                        </div>
                      </div>

                      <div class="mt-2 mb-3 text-dark pl-2 pr-2">
                        <span><i class="bi bi-briefcase mr-1"></i> <?php echo get_by_id($id, 'users')->designation ?> at <?php echo get_by_id($id, 'users')->company ?>.</span>
                      </div>

                      <div class="d-flex justify-content-between fs-12 br-4 mb-0 pr-2 pl-2">
                        <div>
                          <p class="mt-2 mb-0 text-muted"><?php echo trans('experience') ?></p>
                          <p class="mt-0 mb-2 text-dark"><?php echo get_by_id($id, 'users')->experience_year; ?> <?php echo trans('years') ?></p>
                        </div>
                        <div class="brr-1"></div>
                        <div>
                          <p class="mt-2 mb-0 text-muted"><?php echo trans('attendence') ?></p>
                          <p class="mt-0 mb-2 text-dark"><?php echo get_user_attendence($id) ?>%</p>
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
              <?php endforeach ?>
            </div>
          <?php else: ?>
            <?php $this->load->view('admin/include/not-found'); ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
