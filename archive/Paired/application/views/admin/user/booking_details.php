<div class="content-wrapper">
  <?php $check_payment = check_booking_payment($booking->id) ?>
  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container">
      <div class="card">

        <div class="card-header with-border ">
          <div class="card-title">
            <h5 class="fs-20 pull-left"><?php echo trans('session-booking') ?></h5>
          </div>

          <div class="card-tools pull-right">
            <a class="btn btn-outline-secondary pull-right" href="<?php echo html_escape($_SERVER['HTTP_REFERER']) ?>"><i class="bi bi-arrow-left"></i> <?php echo trans('back') ?></a>
          </div>
        </div>

        <div class="card-body">
          
          <div class="row mt-2">
            <div class="col-8">
              <p class="fs-14 mb-0 text-muted"><?php echo trans('booking-number') ?></p>
              <p class="fs-20 font-weight-bold"># <?php echo html_escape($booking->booking_number) ?></p>
            </div>
            
            <div class="col-4 text-right pt-3">
              <?php if($booking->price != 0): ?>
                <?php if($check_payment == FALSE): ?>
                  <button class="btn btn-light-warning btn-sm mr-2"><i class="bi bi-clock"></i> <?php echo trans('pending') ?></button>
                <?php else: ?>
                  <button class="btn border-success text-success btn-sm mr-2"><i class="bi bi-check-circle"></i> <?php echo trans('paid') ?></button>
                <?php endif; ?>
              <?php else: ?>
                <button class="btn border-info text-info btn-sm mr-2"><i class="bi bi-check-circle"></i> <?php echo trans('free') ?></button>
              <?php endif ?>

              <?php if (!empty(settings()->google_client_id) && !empty(settings()->google_client_secret)): ?>
                <?php if (is_user() && $booking->sync_calendar_user == 1): ?>
                  <a class="btn border-primary text-primary disableds btn-sm ml-2" href="javascript:;"><i class="mr-1 bi bi-calendar2-check"></i> <?php echo trans('sync-successfully') ?></a>
                <?php endif; ?>

                <?php if (is_user() && $booking->sync_calendar_user == 0): ?>
                  <a class="btn btn-light-danger btn-sm ml-2" href="<?php echo base_url('admin/sessions/sync/'.md5($booking->id)) ?>"><i class="bi bi-arrow-repeat"></i> <?php echo trans('sync-google-calednder') ?></a>
                <?php endif ?>

                <?php if (is_mentee() && $booking->sync_calendar == 1): ?>
                  <a class="btn border-primary text-primary disableds btn-sm ml-2" href="javascript:;"><i class="mr-1 bi bi-calendar2-check"></i> <?php echo trans('sync-successfully') ?></a>
                <?php endif ?>

                <?php if (is_mentee() && $booking->sync_calendar == 0): ?>
                  <a class="btn btn-light-danger btn-sm ml-2" href="<?php echo base_url('admin/sessions/sync/'.md5($booking->id)) ?>"><i class="bi bi-arrow-repeat"></i> <?php echo trans('sync-google-calednder') ?></a>
                <?php endif ?>
                
              <?php endif; ?>

            </div>
          </div>

          <div class="mt-4 booking_heeadline mb-0">
            <p class="mb-0 fs-16 font-weight-bold"><?php echo trans('booking-info') ?></p>
          </div>

          <hr class="divider mt-0">

          <div class="row mt-3">
            <div class="col-6">
              <div class="mb-3">
                <p class="text-muted mb-1"><i class="bi bi-calendar2-check"></i> <?php echo trans('date') ?></p>
                <p class="fs-15"><?php echo html_escape($booking->date) ?></p>
              </div>
              <div>
                <p class="text-muted mb-1"><i class="bi bi-view-list"></i> <?php echo trans('session') ?></p>
                <p class="fs-15"> <?php echo html_escape($session->name) ?></p>
              </div>
            </div>

            <div class="col-6">
              <div class="mb-3">
                <p class="text-muted mb-1"><i class="bi bi-clock"></i> <?php echo trans('time') ?></p>
                <p class="fs-15"><?php echo html_escape($booking->time) ?></p>
              </div>
              <div class="mb-2">
                <p class="text-muted mb-1"><i class="bi bi-person-workspace"></i> <?php echo trans('mentor') ?></p>
                <p class="fs-15"><?php echo get_by_id($booking->user_id, 'users')->name ?></p>
              </div>
            </div>
          </div>

          <div class="mt-4 mb-0 booking_heeadline">
            <p class="mb-0 fs-16 font-weight-bold"><?php echo trans('mentee-info') ?></p>
          </div>

          <hr class="divider mt-0">

          <div class="row mt-3">
            <div class="col-6">
              <div>
                <p class="fs-14 text-muted mb-1"><?php echo trans('name') ?></p>
                <p class="fs-15"><?php echo get_by_id($booking->mentee_id, 'users')->name ?></p>
              </div>
            </div>

            <div class="col-6">
              <div class="mb-2">
                <p class="fs-14 text-muted mb-1"><?php echo trans('email') ?></p>
                <p class="fs-15"><?php echo get_by_id($booking->mentee_id, 'users')->email ?></p>
              </div>
            </div>
          </div>

          <div class="mt-4 booking_heeadline mb-0">
            <p class="mb-0 fs-16 font-weight-bold"><?php echo trans('payment-info') ?></p>
          </div>
          <hr class="divider mt-0">

         
          <?php if($booking->price != 0 ): ?>
            <div class="d-flex justify-content-between mt-3 mb-0">
              <div class="mb-2">
                <p class="fs-14 mb-1 pr-5"><?php echo trans('price') ?></p>
              </div>
              <div>
                <p class="fs-15 font-weight-bold mb-1">
                  <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?>
                  <?php echo number_format($booking->price, settings()->num_format) ?>
                  <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                 </p>
              </div>
            </div>


            <?php 
              $check_discount = $this->admin_model->check_discount_by_session($booking->session_id, 'discounts');
              $check_mentee = check_coupon_mentee($booking->session_id, $booking->mentee_id);

              $discount_amount = ($check_mentee->discount * $booking->price) / 100 ;

              if(!empty($check_mentee)){
                $price = $booking->price - $discount_amount;
              }else{
                $price = $booking->price ;
              }
            ?>

            <?php if(!empty($check_discount) && empty($check_mentee) ): ?>
              <div class="d-flex justify-content-between align-items-center mt-2 mb-1">
                <div>
                    <p class="fs-14 mb-0"><?php echo trans('add-coupon') ?></p>
                </div>
                <div>
                  <div class="input-group input-group-sm">
                    <input type="text" name="coupon_code" class="form-control form-control-sm coupon_code" placeholder="Code here" aria-label="Apply Code here" aria-describedby="basic-addon2">
                    <div class="input-group-append">
                        <input type="hidden" name="booking_id" class="booking_id" value="<?php echo html_escape($booking->id) ?>">
                        <button class="btn btn-primary apply_coupon" type="button" <?php if(!empty($check_mentee)){ echo 'disabled';} ?>><?php echo trans('apply') ?></button>
                    </div>
                  </div>
                </div>
              </div>
            <?php endif; ?>

            <div class="d-flexss apply_msg text-right">
                <span class="text-success mb-2 mt-2 apply_msg_success mr-2"></span>
                <span class="text-danger mb-2 mt-2 apply_msg_error"></span>
            </div>
          
            <div class="coupon_area  <?php if(!empty($check_mentee)){ echo 'show';}else{ echo 'hide';} ?>">
              <?php if(!empty($check_discount)): ?>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <div>
                    <p class="mb-0 fs-14"><?php echo trans('discount') ?> (<span class="percent"><?php echo html_escape($check_discount->discount); ?>%</span>)</p>
                </div>
                <div>
                    <p class="fs-15 font-weight-bold mb-0">
                        <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?><span class="discount_amount"><?php echo number_format($discount_amount, settings()->num_format); ?></span> <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                    </p>
                </div>
              </div>
              <?php endif; ?>
            </div>

            <div class="d-flex justify-content-between align-items-center pt-2 mb-2 btm-1">
              <div class="mb-1">
                  <p class="fs-14 mb-1"><?php echo trans('total') ?></p>
              </div>
              <div>
                  <p class="fs-15 font-weight-bold mb-1">
                      <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?> <span class="final_amount"><?php echo number_format($price, settings()->num_format) ?></span> <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                  </p>
              </div>
            </div>
          <?php else: ?>

            <div class="d-flex justify-content-between mt-3 mb-0">
              <div class="mb-2">
                <p class="fs-14 mb-1 pr-5"><?php echo trans('price') ?></p>
              </div>
              <div>
                <p class="fs-15 font-weight-bold mb-1 badge badge-info">
                  <?php echo trans('free') ?>
                 </p>
              </div>
            </div>

          <?php endif; ?>
        </div>

        <?php if($booking->price != 0 ): ?> 
          <?php if(is_mentee() && $check_payment == FALSE): ?>
            <div class="mt-3">
              <a class="btn btn-primary btn-block p-2" href="<?php echo base_url('admin/sessions/booking_payment/'.md5($booking->id)) ?>"> <?php echo trans('complete-payment') ?> <i class="bi bi-arrow-right"></i></a>
            </div>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>