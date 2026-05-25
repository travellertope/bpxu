<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3 pt-2">
          <div class="card-body box-profile pt-4 mt-5 p-0">

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

            <p class="text-muted text-center mb-1"><?php echo html_escape($mentee->phone) ?></p>
            <p class="text-muted text-center strong"><?php echo trans('joined') ?>: <?php echo get_time_ago($mentee->created_at) ?></p>

            <ul class="list-group list-group-unbordered pt-3">
              <li class="list-group-item pl-3 pr-3 text-dark">
                <span class="font-weight-bold fs-12"><?php echo trans('total-sessions') ?></span> <a class="float-right badge badge-secondary-soft"><?php echo count($sessions); ?></a>
              </li>
              <li class="list-group-item pl-3 pr-3 text-dark">
                <span class="font-weight-bold fs-12"><?php echo trans('pending-sessions') ?></span> <a class="float-right badge badge-secondary-soft"><?php echo count_mentee_booking($mentee->id, 0) ?></a>
              </li>
              <li class="list-group-item pl-3 pr-3 text-dark">
                <span class="font-weight-bold fs-12"><?php echo trans('completed-sessions') ?></span> <a class="float-right badge badge-secondary-soft"><?php echo count_mentee_booking($mentee->id, 3) ?></a>
              </li>
              <li class="list-group-item pl-3 pr-3 text-dark">
                <span class="font-weight-bold fs-12"><?php echo trans('recurring-sessions') ?></span> <a class="float-right badge badge-secondary-soft"><?php echo count_mentee_recurring_booking($mentee->id, 2) ?></a>
              </li>
            </ul>
          </div>
        </div>

        <div class="col-md-9">
          <?php if (!empty($sessions)): ?>
            <div class="card pl-3">
              <div class="card-header">
                <h5 class="card-title mb-0"><?php echo trans('bookings') ?> </h5>

                <div class="card-tools pull-right"><a class="pull-right btn btn-secondary btn-sm" href="<?php echo base_url('admin/sessions/booking') ?>"><i class="fas fa-angle-left"></i> <?php echo trans('back') ?></a></div>
              </div>
              
              <div class="card-body table-responsive p-0">
                <table class="table table-hover table-valign-middle <?php if(count($sessions) > 10){echo "datatable";} ?>">
                  <thead>
                  <tr>
                    <th>#</th>
                    <th><?php echo trans('session') ?></th>
                    <th><?php echo trans('recurring-info') ?></th>
                    <th><?php echo trans('date') ?></th>
                    <th><?php echo trans('payment') ?></th>
                    <th><?php echo trans('created') ?></th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php $i=1; foreach ($sessions as $session): ?>
                      <tr>
                        <td><?= $i; ?></td>
                        <td>
                          <!-- <?php $rating = check_session_rating($session->id); ?>
                            <?php if(!empty($rating)): ?>
                              <?php for($i = 1; $i <= 5; $i++):?>
                                <?php 
                                if($i > $rating->rating){
                                  $star = 'far fa-star';
                                }else{
                                  $star = 'fas fa-star';
                                }
                                ?>
                                <i class="<?php echo html_escape($star);?> text-warning-alt fs-13"></i> 
                              <?php endfor;?>
                            <?php endif; ?> -->
                          <p class="mt-0 mb-0">
                            <b><?php echo get_by_id($session->session_id, 'sessions')->name ?></b>
                          </p>
                          
                          <?php 

                            if(get_by_id($session->session_id,'sessions')->session_repeat == 7){
                              $repeat = 'weekly';
                            }else{
                              $repeat = 'monthly';
                            }

                          ?>
                    
                          <p class="mb-0 mt-0">
                            <span><i class="bi bi-clock text-muted"></i> <?php echo html_escape($session->duration) ?> <?php echo trans('minutes') ?></span>
                          </p>

                          <?php if(get_by_id($session->session_id,'sessions')->type == 2): ?>
                            <p class="mb-0 font-weight-normal"><?php echo trans('repeated-in') ?> <b><?php echo html_escape($repeat) ?></b></p>
                            <p class="mb-0 font-weight-normal"><?php echo trans('total-sessions') ?> : <b><?php echo get_by_id($session->session_id,'sessions')->session_number ?></b></p>
                          <?php endif; ?>  
                        </td>

                        <?php if(get_by_id($session->session_id,'sessions')->session_number != $session->recurring_count): ?>
                          <td>
                            <?php if(get_by_id($session->session_id,'sessions')->type == 2): ?>
                              <p class="mb-0"><b><?php echo trans('next-session') ?> : </b>  <?php echo my_date_show($session->next_recur_date) ?></p>
                              <p class="mb-0 mt-0"><b><?php echo trans('recurring-count') ?> : </b><?php echo html_escape($session->recurring_count) ?></p>
                            <?php endif; ?>
                          </td>
                        <?php   endif; ?>


                          <?php if(get_by_id($session->session_id,'sessions')->session_number == $session->recurring_count): ?>
                            <td>
                                <p class="mb-0 mt-0 badg badge-success"><b><i class="bi bi-check-circle"></i><?php echo trans('recurring-session-complete') ?></b></p>
                            </td>
                          <?php endif; ?>

                        <td>
                          <p class="mb-0 mt0"><?php echo my_date_show($session->date) ?></p>


                          <?php 
                              $user = get_by_id($session->user_id,'users');
                              $mentee = get_by_id($session->mentee_id,'users');
                              $booking_time= explode('-', $session->time);
                              $time=trim($booking_time[0]);
                              $time1=trim($booking_time[1]);
                              $from_date_time = $session->date.' '.$time.':00';
                              $from_date_time1 = $session->date.' '.$time1.':00';

                              $from_time_zone = get_by_id($user->time_zone, 'time_zone')->name;
                              $to_time_zone = get_by_id($mentee->time_zone, 'time_zone')->name;
                              if(!empty($to_time_zone)){
                                $convert_time =  convert_timezone($from_date_time,$from_time_zone,$to_time_zone). '-'.convert_timezone($from_date_time1,$from_time_zone,$to_time_zone); 
                              }
                            ?>
                            <i class="bi bi-clock"></i> <?php echo html_escape($convert_time) ?>
                          
                        </td>

                        <td>
                          <p class="mb-0 mt-0">
                            <?php 
                              $coupon = check_coupon_mentee($session->session_id, $session->mentee_id);

                              if(empty($coupon)){
                                $price = $session->price;
                                $text = '';
                              }else{
                                $discount = $coupon->discount;
                                $discount_amount = ($session->price * $discount)/ 100 ;
                                $price = $session->price - $discount_amount;
                                $text = trans('coupon-applied');
                              }
                           ?>

                            <span class="mr-2">
                              <strong><?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?>
                                <?php echo number_format($price, settings()->num_format) ?>
                              <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?> </strong>
                              <?php if (!empty($text)): ?>(<?php echo html_escape($text); ?>)<?php endif ?>
                            </span>
                          </p>
                          <p class="mb-0 mt-0">
                            <?php $check_payment = check_booking_payment($booking->id) ?>
                            <?php if ($check_payment == true): ?>
                              <span class="badge badge-success-soft"><i class="bi bi-check-circle"></i> <?php echo trans('paid') ?></span>
                            <?php else: ?>
                                <span class="badge badge-warning-soft"><i class="bi bi-clock"></i> <?php echo trans('pending') ?></span>

                                <?php if (is_mentee()): ?>
                                  <a href="<?php echo base_url('admin/sessions/booking_details/'.html_escape($booking->booking_number));?>" class="badge badge-primary"><i class="fas fa-clock"></i> <?php echo trans('pay-now') ?></a>
                                <?php endif; ?>
                            <?php endif; ?>
                          </p>
                        </td>
                        <td>
                          <?php echo my_date_show($session->created_at) ?>
                        </td>
                        
                      </tr>
                    <?php $i++; endforeach ?>
                  </tbody>
                </table>
              </div>
            </div>
          <?php else: ?>
            <div class="card mt-5">
              <div class="card-body mt-2 text-center p-5 pt-4">
                  <p><?php echo trans('no-data-found') ?></p>
              </div>
            </div>
          <?php endif ?>
        </div>

      </div>
    </div>
  </div>
</div>