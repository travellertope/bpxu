<?php if (settings()->site_mode == 'dark'): ?>
  <style type="text/css">
    .ui-datepicker-prev:after, .ui-datepicker-next:after {
font-family: "Font Awesome 5 Free";
font-weight: 500;
content: "\f008";
position: absolute;
display: block;
width: 10px;
height: 10px;
border-left: 2px solid #fff;
border-bottom: 2px solid #fff;
color: transparent !important;
top: 157px;
}
  </style>
<?php endif ?>

<div class="content-wrapper">
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12">

          <?php if(is_user()): ?>
            <div class="card add_area <?php if(isset($page_title) && $page_title == "Edit"){echo "d-block";}else{echo "hide";} ?>">
              <div class="card-header">
                <?php if (isset($page_title) && $page_title == "Edit"): ?>
                  <h3 class="card-title pt-2"><?php echo trans('edit') ?></h3>
                <?php else: ?>
                  <h3 class="card-title pt-2"><?php echo trans('create-new') ?> </h3>
                <?php endif; ?>

                <div class="card-tools pull-right">
                  <?php if (isset($page_title) && $page_title == "Edit"): ?>
                    <?php $required = ''; ?>
                    <a href="<?php echo base_url('admin/sessions/booking') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                  <?php else: ?>
                    <?php $required = 'required'; ?>
                    <a href="#" class="text-right btn btn-secondary btn-sm cancel_btn"><i class="fa lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                  <?php endif; ?>
                </div>
              </div>

              <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/sessions/booking_add')?>" role="form" novalidate>
                <div class="row">
                  <div class="col-md-6">
                    <div class="card-body">
                      <div class="form-group">
                        <label><?php echo trans('session') ?></label>
                        <select class="form-control book_session" name="session_id" required>
                          <option value=""><?php echo trans('select') ?></option>
                          <?php foreach ($sessions as $session): ?>
                            <option value="<?php echo html_escape($session->id) ?>" <?php if(isset($booking->session_id) && $booking->session_id == $session->id) {echo 'selected';} ?>> <?php echo html_escape($session->name) ?> </option>
                          <?php endforeach ?>
                        </select>
                      </div>

                      <!-- <div class="form-group">
                        <label><?php echo trans('mentee') ?></label>
                        <select class="form-control" name="mentee_id" required>
                          <option value=""><?php echo trans('select') ?></option>
                          <?php foreach ($mentees as $mentee): ?>
                            <option value="<?php echo html_escape($mentee->id) ?>" <?php if(isset($booking->mentee_id) && $booking->mentee_id == $mentee->id) {echo 'selected';} ?>> <?php echo html_escape($mentee->name) ?> </option>
                          <?php endforeach ?>
                        </select>
                      </div> -->


                      <div class="form-group">
                        <label><?php echo trans('mentee') ?></label>
                        <select class="form-control" name="mentee_id" required>
                          <option value=""><?php echo trans('select') ?></option>
                          <?php foreach ($user_mentees as $mentee): ?>
                            <option value="<?php echo html_escape($mentee->mentee_id) ?>" <?php if(isset($booking->mentee_id) && $booking->mentee_id == $mentee->id) {echo 'selected';} ?>> <?php echo html_escape($mentee->mentee_name) ?> </option>
                          <?php endforeach ?>
                        </select>
                      </div>

                      <div class="form-group">
                        <label><?php echo trans('notes') ?></label>
                        <textarea class="form-control" name="notes"><?php if(isset($booking->note)) {echo html_escape($booking->note);} ?></textarea>
                      </div>
                    </div>

                    <div class="card-footer">
                      <input type="hidden" name="id" value="<?php if(isset($booking->id)){echo html_escape($booking->id);} ?>">
                      <!-- csrf token -->
                      <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">

                      <?php if (isset($page_title) && $page_title == "Edit"): ?>
                        <button type="submit" class="btn btn-primary pull-left btn-block"><?php echo trans('save-changes') ?></button>
                      <?php else: ?>
                        <button type="submit" class="btn btn-primary pull-left btn-block"> <?php echo trans('save') ?></button>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="col-md-6 pl-5">
                    <div class="booking_calender">
                      <?php if (isset($page_title) && $page_title == "Edit"): ?>
                        <div class="card-body">
                          <div class="">
                            <p class="mb-1"><b><?php echo trans('booking-date') ?></b></p>
                            <span class="badge badge-secondary"><?php echo my_date_show($booking->date) ?></span>
                          </div>

                          <div>
                            <p class="mt-3 mb-1"><b><?php echo trans('booking-time') ?></b></p>
                            <span class="badge badge-secondary"><?php echo html_escape($booking->time) ?></span>
                          </div>
                        </div>
                        
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          <?php endif; ?>


          <!-- booking table area-->
          <?php if (isset($page_title) && $page_title != "Edit"): ?>
          <div class="card list_area">
            <div class="card-header">
              <?php if (isset($page_title) && $page_title == "Edit"): ?>
                <h3 class="card-title pt-2"><?php echo trans('edit') ?> <a href="<?php echo base_url('admin/sessions/booking') ?>" class="pull-right btn btn-secondary-soft btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a></h3>
              <?php else: ?>

                <div class="row">
                  <div class="col-lg-10 col-md-9 col-sm-12">
                    <nav id="btab" class="mb-4 nav nav-tabs over-scroll" role="tablist">
                      
                      <a href="<?php echo base_url('admin/sessions/booking/?search=upcoming') ?>" role="tab" data-rb-event-key="upcoming" aria-selected="true" class="nav-item nav-link <?php if(empty($_GET['search'])){ echo 'active';} ?> <?php if($_GET['search'] == 'upcoming'){ echo 'active';} ?>">
                        <span class="badge fs-12 badge-primary mr-1"><?php echo count_booking('upcoming') ?></span>
                        <span class="text-dark fw-500"><?php echo trans('upcoming') ?></span>
                      </a>

                      <a href="<?php echo base_url('admin/sessions/booking/?search=all') ?>" role="tab" data-rb-event-key="all" aria-selected="true" class="nav-item nav-link <?php if($_GET['search'] == 'all'){ echo 'active';} ?>">
                        <span class="badge fs-12 badge-success mr-1"><?php echo count_booking('all') ?></span>
                        <span class="text-dark fw-500"><?php echo trans('all') ?></span>
                      </a>

                      <a href="<?php echo base_url('admin/sessions/booking/?search=pending') ?>" role="tab" data-rb-event-key="pending" aria-selected="false" class="nav-item nav-link <?php if($_GET['search'] == 'pending'){ echo 'active';} ?>">
                        <span class="badge fs-12 badge-warning mr-1"><?php echo count_booking('0') ?></span>
                        <span class="text-dark fw-500"><?php echo trans('pending') ?></span>
                      </a>

                      <a href="<?php echo base_url('admin/sessions/booking/?search=completed') ?>" role="tab" data-rb-event-key="completed" aria-selected="false" class="nav-item nav-link <?php if($_GET['search'] == 'completed'){ echo 'active';} ?>">
                        <span class="badge fs-12 badge-danger mr-1"><?php echo count_booking(3) ?></span>
                        <span class="text-dark fw-500"><?php echo trans('completed') ?></span>
                      </a>

                      <a href="<?php echo base_url('admin/sessions/booking/?search=recurring') ?>" role="tab" data-rb-event-key="recurring" aria-selected="false" class="nav-item nav-link <?php if($_GET['search'] == 'recurring'){ echo 'active';} ?>">
                        <span class="badge fs-12 badge-info mr-1"><?php echo count_booking('recurring') ?></span>
                        <span class="text-dark fw-500"><?php echo trans('recurring') ?></span>
                      </a>
                    </nav>
                  </div>

                  <div class="col-lg-2 col-md-3 col-sm-12 text-md-right">
                    <?php if(is_user()): ?>
                      <a href="#" class="btn btn-secondary btn-sm add_btn pull-left"><i class="bi bi-plus-circle"></i> <?php echo trans('create-new') ?></a>
                    <?php endif; ?>
                    <a href="#" class="filter-action pull-right btn btn-primary btn-sm ml-2"><i class="bi bi-funnel"></i></a>
                  </div>
                </div>
             
              <?php endif; ?>
            </div>


            <div class="filter_popup showFilter">
                <div class="d-flex justify-content-between bbm-1">
                  <div><p><?php echo trans('filters') ?></p></div>
                  <div><a href="<?php echo base_url('admin/sessions/booking') ?>" class="btn btn-light btn-xs"><i class="bi bi-arrow-repeat"></i> <?php echo trans('reset') ?></a></div>
                </div>

                

                <form action="<?php echo base_url('admin/sessions/booking') ?>" class="sort_form" method="get">
                  <div class="row">
                    <div class="col-md-12">

                        <?php if(is_user()): ?>
                          <div class="form-group">
                            <select name="session" class="form-control">
                              <option value="all" <?php if(isset($_GET['session']) && $_GET['session']=='all'){echo "selected";} ?>><?php echo trans('select-session') ?></option>
                              <?php foreach ($sessions as $session): ?>
                              <option <?php if(isset($_GET['session']) && $_GET['session']==$session->id){echo "selected";} ?> value="<?php if(!empty($session)){echo html_escape($session->id);}?>"><?php echo html_escape($session->name) ?></option>
                              <?php endforeach ?>
                            </select>
                          </div>
                        <?php endif; ?>

                        <?php if(is_user()): ?>
                          <div class="form-group">
                            <select name="mentee" class="form-control">
                              <option value="all" <?php if(isset($_GET['mentee']) && $_GET['mentee']=='all'){echo "selected";} ?>><?php echo trans('select-mentee') ?></option>
                              <?php foreach ($user_mentees as $mentee): ?>
                              <option <?php if(isset($_GET['mentee']) && $_GET['mentee']==$mentee->mentee_id){echo "selected";} ?> value="<?php if(!empty($mentee)){echo html_escape($mentee->mentee_id);}?>"><?php echo html_escape($mentee->mentee_name) ?></option>
                              <?php endforeach ?>
                            </select>
                          </div>
                        <?php endif; ?>

                        <?php if(is_mentee()): ?>
                          <div class="form-group">
                            <select name="mentor" class="form-control">
                              <option value="all" <?php if(isset($_GET['mentor']) && $_GET['mentor']=='all'){echo "selected";} ?>><?php echo trans('select-mentor') ?></option>
                              <?php foreach ($mentee_mentors as $mentor): ?>
                              <option <?php if(isset($_GET['mentor']) && $_GET['mentor']==$mentor->user_id){echo "selected";} ?> value="<?php if(!empty($mentor)){echo html_escape($mentor->user_id);}?>"><?php echo html_escape($mentor->mentor_name) ?></option>
                              <?php endforeach ?>
                            </select>
                          </div>
                        <?php endif; ?>


                        <div class="form-group">
                          <select name="status" class="form-control">
                            <option value="all" <?php if(isset($_GET['status']) && $_GET['status']=='all'){echo "selected";} ?>><?php echo trans('select-status') ?></option>
                            <option <?php if(isset($_GET['status']) && $_GET['status'] == '0'){echo "selected";} ?> value="0"><?php echo trans('pending') ?></option>
                            <option <?php if(isset($_GET['status']) && $_GET['status'] == 1){echo "selected";} ?> value="1"><?php echo trans('approved') ?></option>
                            <option <?php if(isset($_GET['status']) && $_GET['status'] == 2){echo "selected";} ?> value="2"><?php echo trans('rejected') ?></option>
                            <option <?php if(isset($_GET['status']) && $_GET['status'] == 3){echo "selected";} ?> value="3"><?php echo trans('completed') ?></option>
                          </select>
                        </div>

                        <?php if(is_user()): ?>
                          <div class="form-group">
                            <input placeholder="<?php echo trans('search-by-session-mentee') ?>" class="form-control form-control-sm" type="text" name="search_booking" value="<?php if(!empty($_GET['search_booking'])){echo html_escape($_GET['search_booking']);} ?>">
                          </div>
                        <?php endif; ?>
                        
                    </div>

                    <div class="col-md-12 mt-3">
                      <button type="submit" class="btn btn-primary btn-sm btn-block"><?php echo trans('submit') ?></button>
                    </div>

                  </div>
                </form>
            </div>

            <?php if(!empty($bookings)): ?>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap <?php if(is_countable($bookings) && count($bookings)  > 10){echo "datatable";} ?>">
                <thead>
                  <tr>
                    <th>#</th>
                    <th><?php echo trans('booking-id') ?></th>
                    <?php if(!is_mentee()): ?>
                      <th><?php echo trans('mentee') ?></th>
                    <?php endif; ?>

                    <?php if(is_mentee()): ?>
                      <th><?php echo trans('mentor') ?></th>
                    <?php endif; ?>
                    <th><?php echo trans('session') ?></th>

                    <?php if(is_user()): ?>
                      <th><?php echo trans('group-booking') ?></th>
                    <?php endif; ?>
                    <th><?php echo trans('date-time') ?></th>
                    <?php if($_GET['search'] == 'recurring' || $_GET['search'] == 'upcoming'): ?>
                      <th><?php echo trans('recurring-info') ?></th> 
                    <?php endif; ?>
                    <th><?php echo trans('online-meeting') ?></th>
                    <th><?php echo trans('status') ?></th>
                    <th><?php echo trans('payment-status') ?></th>
                    <th><?php echo trans('action') ?></th>
                  </tr>
                </thead>

                <tbody>
                  <?php $s=1; foreach ($bookings as $booking): ?>

                    <tr class="<?php if($booking->status == 4){echo 'bg-danger-soft';} ?> " id="row_<?php echo html_escape($booking->id); ?>">
                      <td><?= $s; ?></td>
                      <td><b>#<?php echo html_escape($booking->booking_number) ?></b></td>

                      <?php if(is_user()): ?>
                        <td>
                          <a data-tooltip="<?php echo trans('view-details') ?>" href="<?php echo base_url('admin/sessions/mentee_details/'.$booking->mentee_id);?>" class="text-dark">
                            <div class="d-flex">
                              <div class="mr-3">
                                <?php $thumb = get_by_id($booking->mentee_id, 'users')->thumb ?>
                                <?php if(!empty($thumb)): ?>
                                  <img class="img-circle mt-1" width="30px" src="<?php echo base_url($thumb) ?>">
                                <?php else: ?>
                                  <img class="img-circle mt-1" width="30px" src="<?php echo base_url('assets/images/no-photo.png') ?>">
                                <?php endif; ?>
                                  
                              </div>
                              <div>
                                <b><span class="mt-0 mb-0"><?php echo get_by_id($booking->mentee_id, 'users')->name ?></span></b>

                                <?php $mentee = get_by_id($booking->mentee_id, 'users'); ?>
                                <?php $code = get_by_id($mentee->country, 'country')->code; ?>
                                  <span data-tooltip="<?php echo get_by_id($mentee->country, 'country')->name; ?>" class=""><img class="flag-img-booking ml-1" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>"></span>



                                <p class="mt-0 mb-0 text-muted"><?php echo get_by_id($booking->mentee_id, 'users')->email ?></p>
                              </div>
                            </div>
                          </a>

                          <a href="<?php echo base_url('admin/sessions/mentee_profile/'.$booking->mentee_id) ?>" class="btn btn-primary btn-sm mt-2 ml-5">Mentee profile <i class="bi bi-arrow-right"></i></a>
                        </td>
                      <?php endif; ?>


                      <?php if(is_mentee()): ?>
                        <td>
                          <div class="d-flex">
                              <div class="mr-3">
                                <?php $thumb = get_by_id($booking->user_id, 'users')->thumb ?>
                                <img class="img-circle mt-1" width="30px" src="<?php echo base_url($thumb) ?>">
                              </div>
                              <div>
                                <b><span class="mt-0 mb-0"><?php echo get_by_id($booking->user_id, 'users')->name ?></span></b>


                                <?php $mentor = get_by_id($booking->user_id, 'users'); ?>
                                <?php $code = get_by_id($mentor->country, 'country')->code; ?>
                                  <span data-tooltip="<?php echo get_by_id($mentor->country, 'country')->name; ?>" class=""><img class="flag-img-booking ml-1" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>"></span>


                                  <?php if ($mentor->visible_profile == 2): ?>
                                      <span class="ml-3"><a target="_blank" href="<?php echo base_url('mentor/'. $mentor->slug) ?>" class="badge badge-primary">Visit mentor profile <i class="bi bi-arrow-right"></i></a></span>
                                  <?php endif; ?>
                                  



                                <p class="mt-0 mb-0 text-muted"><?php echo get_by_id($booking->user_id, 'users')->email ?></p>
                              </div>
                            </div>
                        </td>
                      <?php endif; ?>

                      <td>
                        <!-- <?php $rating = check_session_rating($booking->id); ?>
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

                        <p class="mb-0 font-weight-bold"><?php echo get_by_id($booking->session_id,'sessions')->name ?></p>

                        <?php 

                          if(get_by_id($booking->session_id,'sessions')->session_repeat == 7){
                            $repeat = 'weekly';
                          }else{
                            $repeat = 'monthly';
                          }
                        ?>
                        
                        
                          <p class="mb-0 ">
                            <?php if($booking->price != 0): ?>
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
                              <strong>
                                <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?>
                                <?php echo number_format($price, settings()->num_format) ?>
                                <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                              </strong>
                              <?php if (!empty($text)): ?>(<?php echo html_escape($text); ?>)<?php endif ?>
                            </span>
                            <?php else: ?>
                              <span> <?php echo trans('free') ?> </span>
                            <?php endif; ?>

                            <span><i class="bi bi-clock text-muted"></i> <?php echo html_escape($booking->duration) ?> <?php echo trans('minutes') ?></span>
                          </p>
                        
                          

                        <?php if(get_by_id($booking->session_id,'sessions')->type == 2): ?>
                          <p class="mb-0 font-weight-normal">Repeated in <b><?php echo html_escape($repeat) ?></b></p>
                          <p class="mb-0 font-weight-normal">Total session : <b><?php echo get_by_id($booking->session_id,'sessions')->session_number ?></b></p>
                        <?php endif; ?>

                      </td>

                      <?php if(is_user()): ?>
                        <td>
                          <?php $enable_group_booking = get_by_id($booking->session_id,'sessions')->enable_group_booking ?> 
                          <?php if($enable_group_booking == 1): ?>
                            <span  class="badge badge-success"><i class="bi bi-people"></i> <?php echo trans('yes') ?></span>
                          <?php else: ?>
                            <span  class="badge badge-danger-soft"><?php echo trans('no') ?></span>
                          <?php endif; ?>
                        </td>
                      <?php endif; ?>
                      
                      <td>
                        <b><p class="mb-0"><i class="bi bi-calendar2-check"></i>  <?php echo my_date_show($booking->date) ?></p></b>
                        <?php if (is_user()): ?>
                          <p class="mb-0 text-muted fs-13"><i class="bi bi-clock"></i>  <?php echo html_escape($booking->time) ?></p>
                        <?php endif; ?>

                        <?php if (is_mentee()): ?>
                          <p>
                            <?php 
                              $user = get_by_id($booking->user_id,'users');
                              $mentee = get_by_id($booking->mentee_id,'users');
                              $booking_time= explode('-', $booking->time);
                              $time=trim($booking_time[0]);
                              $time1=trim($booking_time[1]);
                              $from_date_time = $booking->date.' '.$time.':00';
                              $from_date_time1 = $booking->date.' '.$time1.':00';

                              $from_time_zone = get_by_id($user->time_zone, 'time_zone')->name;
                              $to_time_zone = get_by_id($mentee->time_zone, 'time_zone')->name;
                              if(!empty($to_time_zone)){
                                $convert_time =  convert_timezone($from_date_time,$from_time_zone,$to_time_zone). '-'.convert_timezone($from_date_time1,$from_time_zone,$to_time_zone); 
                              }
                            ?>
                            <i class="bi bi-clock"></i> <?php echo html_escape($convert_time) ?>

                          </p>
                        <?php endif ?>
                      </td>

                      <?php if($_GET['search'] == 'recurring' || $_GET['search'] == 'upcoming'): ?>
                        <?php if(get_by_id($booking->session_id,'sessions')->session_number != $booking->recurring_count): ?>
                          <td>
                            <?php if(get_by_id($booking->session_id,'sessions')->type == 2): ?>
                              <p class="mb-0"><b><?php echo trans('next-session') ?> : </b>  <?php echo my_date_show($booking->next_recur_date) ?></p>
                              <p class="mb-0 mt-0"><b><?php echo trans('recurring-count') ?> : </b><?php echo html_escape($booking->recurring_count) ?></p>
                            <?php endif; ?>
                          </td>
                        <?php endif; ?>
                      <?php endif; ?>


                      <?php if(get_by_id($booking->session_id,'sessions')->session_number == $booking->recurring_count): ?>
                        <td>
                            <p class="mb-0 mt-0 badg badge-success"><b><i class="bi bi-check-circle"></i><?php echo trans('recurring-session-complete') ?></b></p>
                        </td>
                      <?php endif; ?>

                      <?php $check_payment = check_booking_payment($booking->id) ?>


                      <?php if($booking->price == 0): ?>
                        <?php $check_payment = true; ?>
                      <?php endif ?>
                      

                      <!-- meeting options for mentor -->
                      <?php if (is_user()): ?>
                        
                        <td>
                          <a href="<?php echo base_url('auth/send_notify_mail/'.$booking->id);?>" class="btn btn-light-info btn-sm btn-block mb-1" data-toggle="tooltip" data-placement="top" title="<?php echo trans('send-notify-mail-to-user-for-joining-meeting') ?>"><i class="bi bi-send"></i></a>

                          <!-- zoom meeting start -->
                          <?php if (user()->meet_type == 'zoom'): ?>
                            <?php if(!empty($booking->host_url) && $booking->is_start == 0): ?>
                              <a href="<?php echo base_url('admin/sessions/zoom/'.html_escape($booking->id));?>" class="btn btn-light-success btn-sm btn-block start_meeting"><i class="bi bi-play-circle"></i> <?php echo trans('start-meeting') ?></a>
                            <?php endif; ?>

                            <?php if(!empty($booking->host_url) && $booking->is_start == 1): ?>
                              <a target="_blank" href="<?= html_escape($booking->host_url) ?>" class="btn btn-success btn-sm btn-block mt-2"><i class="bi bi-person-video"></i> <?php echo trans('join-meeting') ?></a>

                              <a href="<?php echo base_url('admin/sessions/cancel_meeting/'.html_escape($booking->id));?>" class="btn btn-light-danger btn-block btn-sm mt-2"><i class="bi bi-x-circle-fill"></i> <?php echo trans('cancel-meeting') ?></a>
                            <?php endif; ?>

                            <?php if(empty($booking->host_url)): ?>                        
                                <?php if ($booking->date > date('Y-m-d') && !empty(settings()->zoom_account_id) && !empty(settings()->zoom_client_id) && !empty(settings()->zoom_client_secret)): ?>                        
                                <a href="<?php echo base_url('admin/sessions/add_meeting/'.html_escape($booking->id));?>" class="btn btn-primary btn-sm btn-block mt-2 create_meeting" ><i class="bi bi-plus-circle-fill"></i> <?php echo trans('create-meeting') ?></a>
                              <?php endif; ?>
                            <?php endif; ?>

                          <?php endif; ?>
                          <!-- zoom meeting end -->


                          <!-- google meet start -->
                          <?php if (user()->meet_type == 'meet'): ?>
                            <?php if($booking->is_start == 0): ?>
                              <a href="<?php echo base_url('admin/sessions/meet/'.html_escape($booking->id));?>" class="btn btn-light-success btn-block btn-sm start_meeting"><i class="bi bi-play-circle"></i> <?php echo trans('start-meeting') ?></a>
                            <?php else: ?>
                              <a target="_blank" href="<?= html_escape(user()->gmeet_url) ?>" class="btn btn-success btn-sm btn-block mt-2"><i class="bi bi-person-video"></i> <?php echo trans('join-meeting') ?></a>

                              <a href="<?php echo base_url('admin/sessions/cancel_meeting/'.html_escape($booking->id));?>" class="btn btn-light-danger btn-block btn-sm mt-2"><i class="bi bi-x-circle-fill"></i> <?php echo trans('cancel-meeting') ?></a>
                            <?php endif; ?>
                          <?php endif; ?>
                          <!-- google meet end -->
                          

                        </td>

                      <?php endif ?>
                      <!-- end meeting options for mentor -->


                      <!-- meeting options for mentee -->
                      <?php if (is_mentee()): ?>
                        <td>
                          <?php if ($booking->date >= date('Y-m-d')): ?>
                                <?php if($booking->type == 'online' && $booking->status == 0): ?>
                                      <a href="<?php echo base_url('admin/sessions/booking_details/'.html_escape($booking->booking_number));?>" class="btn btn-primary btn-sm hide">
                                      <?php echo trans('pay-now') ?></a>
                                <?php endif ?>

                                <?php if($booking->type == 'online' && $check_payment == true && $booking->is_start == 1): ?>
                                    
                                    <?php $user = get_by_id($booking->user_id, 'users')  ?>

                                    <?php if ($user->meet_type == 'zoom'): ?>
                                     
                                      <?php if(!empty($booking->join_url)):?>                         
                                        <a target="_blank" href="<?= $booking->join_url ?>" class="btn btn-primary btn-sm position-relative"><i class="bi bi-person-video"></i> <?php echo trans('join-meeting') ?>

                                        <div class="pulse" data-toggle="tooltip" data-title="Doctor started the meeting click the join button"></div>
                                      </a>
                                      <?php endif;?>
                                      <p><?php echo trans('meeting-password') ?>: <b><?php echo html_escape($booking->zoom_password); ?></b></p>
                                    <?php endif; ?>

                                    <?php if ($user->meet_type == 'meet'): ?>
                                      <a target="_blank" href="<?php echo html_escape($user->gmeet_url); ?>" class="btn btn-primary position-relative"><i class="bi bi-person-video"></i> <?php echo trans('join-meeting') ?>

                                        <div class="pulse" data-toggle="tooltip" data-title="Mentor started the meeting click the join button"></div>
                                      </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                  <label class="badge badge-secondary-soft brd-20"><i class="bi bi-camera-video-off-fill"></i> <?php echo trans('not-started-yet') ?> </label>
                                <?php endif ?>

                              <?php else: ?>
                                <label class="badge badge-danger-soft brd-20"><i class="bi bi-clock"></i> <?php echo trans('expired') ?></label>
                              <?php endif ?>
                        </td>
                      <?php endif; ?>
                      <!-- end meeting options for mentee -->


                      <td>
                        <?php if(!is_mentee()): ?>
                          <select data-id="<?php echo html_escape($booking->id) ?>" name="" class="nice_select nice-select p-2 small custom active_status <?php if ($booking->status == 0){echo "br-warning";}elseif($booking->status == 1){echo "br-success";}elseif($booking->status == 2){echo "br-danger";}else{echo "br-primary";} ?>">
                            <option value="0" <?php if ($booking->status == 0){echo "selected";} ?>>🕒 <?php echo trans('pending') ?></option>
                            <option value="1" <?php if ($booking->status == 1){echo "selected";} ?>>✅ <?php echo trans('approved') ?></option>
                            <option value="2" <?php if ($booking->status == 2){echo "selected";} ?>>❌ <?php echo trans('rejected') ?></option>
                            <option value="3" <?php if ($booking->status == 3){echo "selected";} ?>>☑️ <?php echo trans('completed') ?></option>
                          </select>
                        <?php endif; ?>

                        <?php if(is_mentee()): ?>
                          <?php if ($booking->status == 0): ?>
                            <span class="badge badge-warning-soft"><i class="fas fa-clock"></i> <?php echo trans('pending') ?></span>
                          <?php elseif($booking->status == 1): ?>
                            <span class="badge badge-info-soft"><i class="fas fa-check-circle"></i> <?php echo trans('approved') ?></span>
                          <?php elseif($booking->status == 2): ?>
                            <span class="badge badge-danger"><i class="fas fa-check-circle"></i> <?php echo trans('rejected') ?></span>>
                          <?php elseif($booking->status == 3): ?>
                            <span class="badge badge-success-soft"><i class="fas fa-check-circle"></i> <?php echo trans('completed') ?></span>
                          <?php else: ?>
                            <span class="badge badge-danger-soft"><i class="fas fa-check-circle"></i> <?php echo trans('cancelled') ?></span>
                          <?php endif; ?>
                        <?php endif; ?>
                      </td>

                      <td>

                        <?php if($booking->price != 0): ?>
                          <?php if ($check_payment == true): ?>
                            <span class="badge badge-success-soft"><i class="bi bi-check-circle"></i> <?php echo trans('paid') ?></span>
                          <?php else: ?>
                              <span class="badge badge-warning-soft"><i class="bi bi-clock"></i> <?php echo trans('pending') ?></span>

                              <?php if (is_mentee()): ?>
                                <a href="<?php echo base_url('admin/sessions/booking_details/'.html_escape($booking->booking_number));?>" class="badge badge-danger"><i class="bi bi-coin"></i> <?php echo trans('pay-now') ?></a>
                              <?php endif; ?>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="badge badge-info-soft"><i class="bi bi-clock"></i> <?php echo trans('free') ?></span>
                        <?php endif; ?>
                      </td>

                      <td class="actions">
                          <div class="btn-group">
                            <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                              <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu" >

                              <!-- User actions -->
                              <?php if(is_user()): ?>
                                <a data-val="Category" data-id="<?php echo html_escape($booking->id); ?>" href="<?php echo base_url('admin/sessions/booking_edit/' . html_escape($booking->id)) ?>" class="dropdown-item"><i class="bi bi-pencil-square mr-2"></i><?php echo trans('edit') ?></a>

                                <a data-val="Category" data-id="<?php echo html_escape($booking->id); ?>" href="<?php echo base_url('admin/sessions/booking_delete/' . html_escape($booking->id)) ?>" class="dropdown-item delete_item"><i class="bi bi-trash3 mr-2"></i><?php echo trans('delete') ?></a>

                                <?php if ($check_payment != true): ?>
                                  <a href="#paymentModal_<?= $s; ?>" data-toggle="modal" class="dropdown-item d-none"><i class="bi bi-coin mr-2"></i><?php echo trans('record-payment') ?></a>
                                <?php endif; ?>

                                <?php if($booking->status == 0): ?>
                                  <a data-val="Category" data-id="<?php echo html_escape($booking->id); ?>" href="<?php echo base_url('admin/sessions/booking_cancell/' . html_escape($booking->id)) ?>" class="dropdown-item"><i class="bi bi-x-lg mr-2"></i><?php echo trans('cancel') ?></a>
                                <?php endif; ?>

                                <?php if ($booking->sync_calendar_user == 0): ?>
                                  <a class="dropdown-item" href="<?php echo base_url('admin/sessions/sync/'.md5($booking->id)) ?>"><i class="bi bi-arrow-repeat"></i> <?php echo trans('sync-google-calednder') ?></a>
                                <?php endif; ?>
                              <?php endif; ?>



                              

                              <!-- Mentee actions -->
                              <?php if(is_mentee()): ?>
                                <?php if ($booking->sync_calendar == 0): ?>
                                  <a class="dropdown-item" href="<?php echo base_url('admin/sessions/sync/'.md5($booking->id)) ?>"><i class="bi bi-arrow-repeat"></i> <?php echo trans('sync-google-calednder') ?></a>
                                <?php endif; ?>


                               <!--  <?php if($booking->status==3): ?>
                                    <a href="#reviewModal_<?= $s; ?>" data-toggle="modal" class="dropdown-item"><i class="bi bi-coin mr-2"></i>Review</a>
                                <?php endif; ?> -->
                              <?php endif; ?>


                              <a data-val="Category" data-id="<?php echo html_escape($booking->id); ?>"  href="<?php echo base_url('admin/sessions/booking_details/'.html_escape($booking->booking_number));?>" class="dropdown-item"><i class="bi bi-eye mr-2"></i><?php echo trans('view-details') ?></a>

                            </div>
                          </div>
                        </td>
                    </tr>
                  <?php $s++; endforeach; ?>
                </tbody>
              </table>
            </div>
            <?php else: ?>
              <?php $this->load->view('admin/include/not-found'); ?>
            <?php endif; ?>
          </div>
          <?php endif; ?>
          <!-- booking table area end-->



        </div>
      </div>

      <div class="mt-4 ">
        <?php echo $this->pagination->create_links(); ?>
      </div>
      
    </div>
  </div>
</div>


<?php $a=1; foreach ($bookings as $booking): ?>

<div class="modal fade d-hide" id="paymentModal_<?= $a; ?>" aria-hidden="true">
  <div class="modal-dialog">
  
    <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/payment/record_payment/'.$booking->id)?>" role="form" novalidate>
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"><?php echo trans('record-payment') ?> - <?php echo get_by_id($booking->session_id, 'sessions')->name; ?></h4>
          <div class="mclose" data-dismiss="modal"><i class="lnib lni-close"></i></div>
        </div>

        <?php 
            $coupon = check_coupon_mentee($booking->session_id, $booking->mentee_id);
            if(empty($coupon)){
              $price = $booking->price;
              $text = '';
            }else{
              $discount = $coupon->discount;
              $discount_amount = ($booking->price * $discount)/ 100 ;
              $price = $booking->price - $discount_amount;
            }
        ?>

        <div class="modal-body">
          <div class="form-group">
            <label><?php echo trans('price') ?> <span class="text-danger">*</span></label>
            <input type="text" class="form-control" required name="price" value="<?php echo html_escape($price) ?>" disabled>
          </div>
        </div>

        <div class="mb-4 pl-3">
          <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
          <button type="submit" class="btn btn-primary btn-sm"><?php echo trans('submit') ?></button>
        </div>
      </div>
    </form>
  </div>
</div>
<?php $a++; endforeach; ?>



<?php //$b=1; foreach ($bookings as $booking): ?>
<!-- <div class="modal fade" id="reviewModal_<?= $b; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/sessions/add_review')?>" role="form" novalidate>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">
            <?php if (check_session_rating($booking->id) == 0): ?>
              <?php echo trans('rate-this-session') ?>
            <?php else: ?>
              <?php echo trans('your-feedback') ?>
            <?php endif; ?>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"><i class="bi bi-x"></i></span>
          </button>
        </div>
        <div class="modal-body">
          <?php if (check_session_rating($booking->id) == 0): ?>
          <?php 
            $id1 = $b+rand(); $id2 = $b+rand(); $id3 = $b+rand(); $id4 = $b+rand(); $id5 = $b+rand();
          ?>
          <div class="form-group mt-2">
            <fieldset class="rating one mb-4">
                <input type="radio" id="star<?= $id1 ?>" name="rating" value="5" /><label for="star<?= $id1 ?>"><span><i class="fas fa-star"></i></span></label>
                <input type="radio" id="star<?= $id2 ?>" name="rating" value="4" /><label for="star<?= $id2 ?>"><span><i class="fas fa-star"></i></span></label>
                <input type="radio" id="star<?= $id3 ?>" name="rating" value="3" /><label for="star<?= $id3 ?>"><span><i class="fas fa-star"></i></span></label>
                <input type="radio" id="star<?= $id4 ?>" name="rating" value="2" /><label for="star<?= $id4 ?>"><span><i class="fas fa-star"></i></span></label>
                <input type="radio" id="star<?= $id5 ?>" name="rating" value="1" /><label for="star<?= $id5 ?>"><span><i class="fas fa-star"></i></span></label>
            </fieldset>
          </div>

          <div class="form-group">
            <textarea class="form-control" name="feedback" rows="4"></textarea>
          </div>
          <?php else: ?>
            <?php $rating = check_session_rating($booking->id); ?>
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

            <p class="mt-2 lead"><?php echo html_escape($rating->feedback) ?></p>
          <?php endif; ?>
        </div>
        <?php if (check_session_rating($booking->id) == 0): ?>
            <div class="modal-footer justify-content-start">
              <div class="mb-2">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                <input type="hidden" name="booking_id" value="<?php echo html_escape($booking->id); ?>">
                <button type="submit" class="btn btn-primary btn-sm"><?php echo trans('submit') ?></button>
              </div>
            </div>
          <?php endif; ?>
      </div>
    </form>
  </div>
</div> -->
<?php //$b++; endforeach; ?>



