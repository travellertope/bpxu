<section class="pt-6 bg-grey">
    <div class="container">

        <div class="row mt-3">

            <div class="col-md-6 pr-md-7">
                <div>
                    <span class="mr-3"><a class="booking_header" href="<?php echo base_url(); ?>"><i class="bi bi-house-door fs-18"></i></a></span>
                    <span><a class="booking_header" href="<?php echo base_url('mentor/'. $mentor->slug) ?>"><?php echo html_escape($mentor->name) ?></a> </span> 

                    <span class="ml-2 mr-2 text-muted"><i class="bi bi-arrow-right"></i></span>

                    <span><a class="booking_header" data-toggle="modal" data-target="#all_session"><?php echo trans('sessions') ?></a></span>

                    <span class="ml-2 mr-2 text-muted"><i class="bi bi-arrow-right"></i></span>

                    <?php if($session->enable_group_booking == 1): ?>
                        <span class="mr-5"><a class="booking_header" href="#">1:Group <?php echo trans('consultation') ?></a></span>
                    <?php else: ?>
                        <span class="mr-5"><a class="booking_header" href="#">1:1 <?php echo trans('consultation') ?></a></span>
                    <?php endif; ?>
                </div>


                <?php if(empty($session->image)): ?>
                    <div class="booking_bg" style="background-image: url(<?php echo base_url($mentor->image); ?>)">
                    </div>
                <?php else: ?>
                    <div class="booking_bg" style="background-image: url(<?php echo base_url($session->image); ?>)">
                    </div>
                <?php endif; ?>

                <div class="mb-0 mt-4">
                    <span class="mr-5">
                        <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?>
                        <?php echo number_format($session->price, settings()->num_format) ?>
                        <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                    </span>
                    <span class="mr-2 rating_icon">
                        <?php $rating = get_ratings_info($session->id);?>
                            <?php if (isset($rating->total_point) && $rating->total_point != 0): ?>
                            <?php $average = number_format($rating->total_point/$rating->total_user, 1) ?>
                            <?php endif ?>

                            <?php if (!empty($rating->total_point)): ?>
                              <?php for($u = 1; $u <= 5; $u++):?>
                                <?php 
                                  if ( round($average - .25) >= $u) {
                                        $star = "fas fa-star";
                                    } elseif (round($average + .25) >= $u) {
                                        $star = "fas fa-star-half-alt";
                                    } else {
                                        $star = "far fa-star";
                                    }
                                ?>
                                <i class="<?php echo html_escape($star);?> text-warning fs-12"></i> 
                              <?php endfor;?>
                              <small class="text-dark">( <?php echo get_total_rating_user($session->id) ?> <?php echo trans('reviews') ?>)</small>
                            <?php endif ?>
                    </span>

                    <span class="mb-0 "><?php echo html_escape($session->duration) ?> <?php echo trans('minutes') ?></span>
                    <!-- <span class="mr-5"><?php echo get_by_id($mentor->country, 'country')->name; ?></span> -->
                </div>


                <h2 class="mt-2 mb-1"><?php echo html_escape($session->name) ?></h2>
                
                <?php $character = str_word_count($session->details); ?>

                <?php if($character>70): ?>
                    <div class="mt-3 text-secondary">
                        <p><?php echo character_limiter($session->details, 300) ?> <a data-toggle="modal" href="#session_details"> <?php echo trans('read-more') ?></a></p>
                    </div>
                <?php else: ?>
                    <div class="mt-3 text-secondary">
                        <p><?php echo strip_tags($session->details) ?> </p>
                    </div>
                <?php endif; ?>

                <?php if($session->type == 2): ?>

                   <?php
                        if($session->session_repeat == 7){

                            $session_text = 'weekly';
                        }else{
                            $session_text = 'monthly';  
                        }
                    ?>
                    <div class="mt-5 mb-5">
                        <div class="d-flex justify-content-between">
                            <div class="border border-solid rounded p-3 mr-2">
                                
                                <p class="mt-0 mb-0 fs-13 text-muted"><?php echo trans('this-sessions-repeats') ?> <?php echo html_escape($session_text) ?></p>
                            </div>
                            <div class="border border-solid rounded p-3 ml-2">
                                
                                <p class="mt-0 mb-0 fs-13 text-muted"><?php echo trans('this-session-have-total') ?> <?php echo html_escape($session->session_number) ?> <?php echo trans('sessions') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(!empty($session->intro_video)): ?>
                    <iframe class="brd-10" width="100%" height="315"src="<?php echo prep_url($session->intro_video) ?>">
                    </iframe>
                <?php endif; ?>

            </div>

            <div class="col-md-6 pl-md-5">
                <p class="fs-18 mb-3 font-weight-bold">When should we meet?</p>
                <form action="<?php echo base_url('booking/'.$session->slug.'/'. $session->uid.'?type=login') ?>" method="post">
                    <div class="row">
                        <div class="col-12">

                            <?php if(check_auth() == false): ?>
                                <div class="form-group w-50">
                                    <label><?php echo trans('time-zone') ?> <span class="text-danger">*</span></label>
                                    <select class="form-control select2 visitor_time_zone" name="time_zone" required>
                                        <option value=""><?php echo trans('select') ?></option>
                                        <?php foreach ($time_zones as $time): ?>
                                          <option value="<?php echo html_escape($time->id) ?>"><?php echo html_escape($time->name) ?></option>
                                        <?php endforeach ?>                 
                                    </select>
                                </div>
                            <?php endif; ?>

                            <!-- <div class="<?php if(check_auth() == false){echo 'hide';}else{echo 'show';} ?> booking_calendarf" id="datepickers"></div> -->

                           
                            
                            <div class="booking_calendar  mt-5 <?php if(check_auth() == false){echo 'show';}else{echo 'show';} ?>">
                                <div class="days-container carousel-4 owl-carousel owl-theme navTopRight">
                                    <?php $i=0; foreach ($next_days as $next_day): ?>

                                       <?php 
                                        $date = date("l", strtotime($next_day));
                                        $day_id = get_day_id($date);
                                        ?>

                                       <?php 
                                            foreach ($assign_days as $assign_day) {
                                                if ($day_id == $assign_day) {
                                                    $show = 1;
                                                    break;
                                                }else{
                                                    $show = 0;
                                                }
                                            }
                                         ?>

                                        <?php if($show == 1): ?>
                                            <div class="day mt-0" data-date="<?php echo $next_day ?>">
                                                <p class="days mb-1 fs-14 text-muted"><?php echo date("l", strtotime($next_day)); ?></p>
                                                <p class="mb-0 fs-15 fw-500"><?php echo my_date_month_show($next_day) ?> </p>
                                            </div>
                                        <?php endif; ?>

                                    <?php $i++; endforeach; ?>
                                </div>
                            </div>
                            


                            <input type="hidden" class="booking_date" name="date" value="">

                            <?php if(check_auth() == false): ?>
                                <input type="hidden" class="booking_time_zone" name="booking_time_zone" value="">
                            <?php else: ?>
                                <input type="hidden" class="booking_time_zone" name="booking_time_zone" value="<?php echo html_escape(user()->time_zone) ?>">
                            <?php endif; ?>



                        </div>
                        <div class="col-12">
                            <div id="load_data"></div>


                            <div class="book_now_btn hide mt-5">
                                <button type="submit" class="btn btn-primary btn-block btn-md fs-14"> <?php echo trans('go-to-checkout') ?> <i class="bi bi-arrow-right pl-2"></i></button>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            
        </div>

        

    </div>
</section>

<!-- Session Details modal -->
<div class="modal fade" id="session_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle"><?php echo trans('session-details') ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?php echo html_escape($session->details) ?>
      </div>
    </div>
  </div>
</div>

<!-- Session Details modal end -->

<!-- All Sessions modal -->

<div class="modal fade" id="all_session" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title pl-3" id="exampleModalLongTitle"><?php echo trans('sessions') ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="bi bi-x"></i></span>
        </button>
      </div>
      <div class="modal-body">
            <div class="row px-3 pb-3">
                <?php foreach ($sessions as $value): ?>
                    <div class="col-md-6 mt-4">
                        <div class="rounded session_block p-4 bg-light">

                            <div class="d-flex justify-content-between text-primary">
                                <div>
                                    <p><?php echo html_escape($value->duration) ?> <?php echo trans('minutes') ?></p>
                                </div>

                                <div>
                                    <?php if($value->price != 0): ?>
                                        <p>
                                            <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?>
                                            <?php echo number_format($value->price, settings()->num_format) ?>
                                            <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                                        </p>
                                    <?php else: ?>
                                        <p><?php echo trans('free') ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <a href="<?php echo base_url('session/'.$value->slug) ?>">
                                <h5 class="mb-2"><?php echo html_escape($value->name) ?></h5> 
                            </a>
                           
                            <p class="text-muted"><?php echo character_limiter($value->details, 180) ?></p>
                           
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
      </div>
    </div>
  </div>
</div>

