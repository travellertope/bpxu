<?php if(is_user()): ?>
  <div class="content-wrapper">
    <div class="content pt-4 mb-4">
      <div class="container">

        <?php if (settings()->enable_kyc == 1 && user()->kyc_verified == 0): ?>
          <div class="row">
            <div class="col-md-12">
              <div class="alert alert-warning-soft" data-aos="fade-up">
                <i class="bi bi-info-circle-fill"></i> <?php echo trans('kyc-verify-alert-user') ?> <a class="text-muted" href="<?php echo base_url('admin/verification'); ?>">Submit Documents <i class="bi bi-arrow-right"></i></a>
              </div>
            </div>
          </div>
        <?php endif ?>

        <div class="row box-dash-areas pt-4">
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12 pr-md-3">
            <div class="info-box bg-success-softs" data-aos="fade-up" data-aos-delay="150">
              <div class="info-box-content">
                <span class="fs-30 fw-500 mb-3 pt-2"><?php echo sprintf("%02d", get_count_by_user('sessions')) ?></span>
                <span class="info-box-text"><?php echo trans('sessions') ?></span>
              </div>
              <span class="info-box-icon bg-lblue fs-25"><i class="bi bi-view-list"></i></span>
            </div>
            <!-- /.info-box -->
          </div>

          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12 pr-md-3">
            <div class="info-box bg-primary-softs" data-aos="fade-up" data-aos-delay="200">
              <div class="info-box-content">
                <span class="fs-30 fw-500 mb-3 pt-2"><?php echo sprintf("%02d", get_count_by_user('session_booking')) ?></span>
                <span class="info-box-text"><?php echo trans('bookings') ?></span>
              </div>
              <span class="info-box-icon bg-lpurple fs-25"><i class="bi bi-calendar2-check"></i></span>
            </div>
            <!-- /.info-box -->
          </div>

          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12 pr-md-3">
            <div class="info-box bg-info-softs" data-aos="fade-up" data-aos-delay="250">
              <div class="info-box-content">
                <span class="fs-30 fw-500 mb-3 pt-2"><?php echo sprintf("%02d", get_count_minute_by_user(user()->id)) ?>+</span>
                <span class="info-box-text"><?php echo trans('total-minutes') ?></span>
              </div>
              <span class="info-box-icon bg-lgreen fs-25"><i class="bi bi-clock"></i></span>
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <div class="col-md-3 col-sm-6 col-12 pr-md-3">
            <div class="info-box bg-danger-softs" data-aos="fade-up" data-aos-delay="300">
              <div class="info-box-content">
                <span class="fs-30 fw-500 mb-3 pt-2"><?php //echo sprintf("%02d", get_count_by_user('session_booking')) ?> <?php echo count($user_mentees) ?></span>
                <span class="info-box-text"><?php echo trans('mentees') ?></span>
              </div>
              <span class="info-box-icon bg-lorange fs-25"><i class="bi bi-people"></i></span>
            </div>
            <!-- /.info-box -->
          </div>

        </div>
      </div>
    </div>

    <div class="content">
      <div class="container">
        <div class="row">
            
            <!-- Hide the Income Chart on Mentors Panel
            
            <div class="col-md-12">
              <div class="card" data-aos="fade-up">
                <div class="card-header">
                  <h5 class="mb-0"><?php echo trans('last-12-months-income') ?></h5>
                </div>
                
                <div class="card-body">
                  <div id="userIncomeChart"></div>
                </div>
              </div>
            </div>
            
            -->

          <div class="col-md-12">
          
            
              <div class="card" data-aos="fade-up">
                <div class="card-header">
                  <h5 class="mb-0"><?php echo trans('latest-bookings') ?></h5>
                </div>
                <?php if (!empty($bookings)): ?>
                    <div class="card-body table-responsive p-0">
                      <table class="table table-hover table-valign-middle">
                        <thead>
                        <tr>
                          <th><?php echo trans('mentee') ?></th>
                          <th><?php echo trans('session') ?></th>
                          <th><?php echo trans('date') ?></th>
                        </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($bookings as $booking): ?>
                            <tr>
                              <td>
                                <div class="d-flex">
                                  <div class="mr-3">
                                    <img class="img-circle mt-1" width="30px" src="<?php echo base_url($booking->thumb) ?>">
                                  </div>
                                  <div>
                                    <p class="mb-0 font-weight-bold"><?php echo html_escape($booking->name) ?></p>
                                    <p class="mb-0"><?php echo html_escape($booking->email) ?></p>
                                  </div>
                                </div>
                              </td>

                              <td>
                                  <p class="mb-0 font-weight-bold"><?php echo get_by_id($booking->session_id, 'sessions')->name ?></p>
                                  
                                  <p class="mb-0 text-muted">
                                    <?php 
                                      $coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);

                                      if(empty($coupon)){
                                        $price = $booking->price;
                                        $text = '';
                                      }else{
                                        $discount = $coupon->discount;
                                        $discount_amount = ($booking->price * $discount)/ 100 ;
                                        $price = $booking->price - $discount_amount;
                                        $text = 'Coupon applied';
                                      }
                                   ?>

                                    <span class="mr-2">
                                      <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?>
                                        <?php echo number_format($price, settings()->num_format) ?>
                                      <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                                      <?php if (!empty($text)): ?>(<?php echo html_escape($text); ?>)<?php endif ?>
                                    </span>

                                    <span><i class="bi bi-clock text-muted"></i> <?php echo html_escape($booking->duration) ?> <?php echo trans('minutes') ?></span>
                                  </p>
                              </td>


                              <td>
                                  <p class="mb-1"><b><?php echo my_date_show($booking->date) ?></b></p>
                                  <p class="mb-0"><span class="small"><?php echo format_time($booking->time, settings()->time_format) ?></span></p>
                              </td>

                            </tr>
                          <?php endforeach ?>
                        </tbody>
                      </table>
                    </div>
                <?php else: ?>
                  <?php $this->load->view('admin/include/not-found'); ?>
                <?php endif ?>
              </div>
            <?php if (!empty($bookings)): ?>
              <div class="text-center mb-4">
                <a href="<?php echo base_url('admin/sessions/booking') ?>" class="btn btn-secondary btn-xs"><?php echo trans('see-all') ?> <i class="lnib lni-arrow-right"></i></a>
              </div>
            <?php endif ?>
          </div>
          
        </div>
        <!-- /.row -->
      </div>
    </div>
  </div>
<?php endif; ?>

<?php if(is_mentee()): ?>
  <div class="content-wrapper">
    <div class="content pt-4 mb-4">
      <div class="container">
        <div class="row">

            <div class="col-md-4">
              <div class="box-dash-areas pt-4">
                <div class="info-box bg-success-softs" data-aos="fade-up" data-aos-delay="150">
                  <div class="info-box-content">
                    <span class="fs-30 fw-500 mb-3 pt-2"><?php echo sprintf("%02d", get_count_by_user('session_booking')) ?></span>
                    <span class="info-box-text"><?php echo trans('sessions') ?></span>
                  </div>
                  <span class="info-box-icon bg-lblue fs-25"><i class="bi bi-view-list"></i></span>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="box-dash-areas pt-4">
                <div class="info-box bg-primary-softs" data-aos="fade-up" data-aos-delay="200">
                  <div class="info-box-content">
                    <span class="fs-30 fw-500 mb-3 pt-2"><?php echo sprintf("%02d", get_count_by_user('session_booking')) ?></span>
                    <span class="info-box-text"><?php echo trans('bookings') ?></span>
                  </div>
                  <span class="info-box-icon bg-lpurple fs-25"><i class="bi bi-calendar2-check"></i></span>
                </div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="box-dash-areas pt-4">
                <div class="info-box bg-danger-softs" data-aos="fade-up" data-aos-delay="300">
                  <div class="info-box-content">
                    <span class="fs-30 fw-500 mb-3 pt-2"><?php echo count($mentee_mentors) ?></span>
                    <span class="info-box-text"><?php echo trans('mentors') ?></span>
                  </div>
                  <span class="info-box-icon bg-lorange fs-25"><i class="bi bi-people"></i></span>
                </div>
              </div>
            </div>
          

          <div class="col-md-12 mt-5">
          
            <?php if (empty($bookings)): ?>
              <?php $this->load->view('admin/include/not-found'); ?>
            <?php else: ?>
              <div class="card" data-aos="fade-up">
                <div class="card-header">
                  <h5 class="mb-0"><?php echo trans('upcoming-bookings') ?></h5>
                </div>
                
                <div class="card-body table-responsive p-0">
                  <table class="table table-hover table-valign-middle">
                    <thead>
                    <tr>
                      <th><?php echo trans('mentor') ?></th>
                      <th><?php echo trans('session') ?></th>
                      <th><?php echo trans('date') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($bookings as $booking): ?>
                        <tr>
                          <td>
                            <div class="d-flex">
                              <div class="mr-3">
                                <img class="img-circle mt-1" width="30px" src="<?php echo base_url($booking->thumb) ?>">
                              </div>
                              <div>
                                <p class="mb-0 font-weight-bold"><?php echo html_escape($booking->name) ?></p>
                                <p class="mb-0"><?php echo html_escape($booking->email) ?></p>
                              </div>
                            </div>
                          </td>

                          <td>
                              <p class="mb-0 font-weight-bold"><?php echo get_by_id($booking->session_id, 'sessions')->name ?></p>
                              
                              <p class="mb-0 ">
                                <?php 
                                  $coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);

                                  if(empty($coupon)){
                                    $price = $booking->price;
                                    $text = '';
                                  }else{
                                    $discount = $coupon->discount;
                                    $discount_amount = ($booking->price * $discount)/ 100 ;
                                    $price = $booking->price - $discount_amount;
                                    $text = 'Coupon applied';
                                  }
                               ?>

                                <span class="mr-2">
                                  <strong><?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?>
                                    <?php echo number_format($price, settings()->num_format) ?>
                                  <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?> </strong>
                                  <?php if (!empty($text)): ?>(<?php echo html_escape($text); ?>)<?php endif ?>
                                </span>

                                <span><i class="bi bi-clock text-muted"></i> <?php echo html_escape($booking->duration) ?> <?php echo trans('minutes') ?></span>
                              </p>
                          </td>


                          <td>
                              <p class="mb-0"><b><?php echo my_date_show($booking->date) ?></b></p>
                              <p class="mb-0 mt-0"><span class="small"><?php echo format_time($booking->time, settings()->time_format) ?></span></p>
                          </td>

                        </tr>
                      <?php endforeach ?>
                    </tbody>
                  </table>
                </div>
              </div>

              <div class="text-center mb-4 mt-5" data-aos="fade-up">
                <a href="<?php echo base_url('admin/sessions/booking') ?>" class="btn btn-secondary btn-xs"><?php echo trans('see-all') ?> <i class="lnib lni-arrow-right"></i></a>
              </div>
            <?php endif ?>
          </div>
          
        </div>
        <!-- /.row -->
      </div><!-- /.container -->
    </div>
  </div>
<?php endif; ?>