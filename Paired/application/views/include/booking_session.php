<link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/select2/css/select2.css">
<link rel="stylesheet" href="<?php echo base_url() ?>assets/admin/plugins/select2/css/select2.min.css">

<div class="shedule_area">
    <?php if(settings()->booking_date_type == 'slot'): ?>
        <div class="d-flex justify-content-between align-items-center mb-2 px-2">
            <div class="pl-0">
                <p class="fs-16 mb-3 fw-600"><?php echo trans('when-should-we-meet') ?></p>
            </div>
            <div class="text-left">
                <span class="btn-prev"><i class="bi bi-arrow-left"></i></span>
                <span class="btn-next"><i class="bi bi-arrow-right"></i></span>
            </div>
        </div>
    <?php endif; ?>
    
    <form action="<?php echo base_url('booking/'.$session->slug.'/'. $session->uid.'?type=login') ?>" method="post">
        <div class="row">
            <div class="col-md-12 p-0">

                <?php if(check_auth() == false): ?>
                    <div class="form-group w-50 pl-4">
                        <label><?php echo trans('time-zone') ?> <span class="text-danger">*</span></label>
                        <select class="form-control select2 visitor_time_zone" name="time_zone" required>
                            <option value=""><?php echo trans('select') ?></option>
                            <?php foreach ($time_zones as $time): ?>
                              <option value="<?php echo html_escape($time->id) ?>"><?php echo html_escape($time->name) ?></option>
                            <?php endforeach ?>                 
                        </select>
                    </div>
                <?php endif; ?>
                
                <div class="booking_calendars">
                    
                        <?php if(settings()->booking_date_type == 'slot'): ?>
                            <div class="booking_calendar px-3">
                                <div class="scrollers scroll-content days-container rows carousel-4g owl-carouselg owl-themeg navTopRight">
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
                                            <div class="item col-6 p-2" style="width: 0px;">
                                                <div class="day" data-date="<?php echo $next_day ?>">
                                                    <p class="days mb-1 fs-14 text-muted"> <?php echo trans(strtolower(date("l", strtotime($next_day)))); ?></p>
                                                    <p class="mb-0 fs-15 fw-500"><?php echo my_date_month_show($next_day) ?></p>
                                                </div>
                                            </div>
                                        <?php endif; ?>


                                        
                                    <?php $i++; endforeach; ?>

                                </div>
                            </div>
                        <?php else: ?>
                            <?php $this->load->view('include/datepicker-js') ?>
                            <div id="datepickers" class="p-5"></div>
                        <?php endif; ?>
                


                    <input type="hidden" class="booking_date" name="date" value="">
                    <input type="hidden" class="session_id" name="session_id" value="<?php echo $session->id ?>">

                    <?php if(check_auth() == false): ?>
                        <input type="hidden" class="booking_time_zone" name="booking_time_zone" value="">
                    <?php else: ?>
                        <input type="hidden" class="booking_time_zone" name="booking_time_zone" value="<?php echo html_escape(user()->time_zone) ?>">
                    <?php endif; ?>

                </div>
            <div class="col-12">
                <div id="load_data"></div>


                <div class="book_now_btn hide mt-5 <?php if(user()->id == $mentor->id){echo 'd-none';} ?>">
                    <button type="submit" class="btn btn-primary btn-block btn-md fs-14"> <?php echo trans('go-to-checkout') ?> <i class="bi bi-arrow-right pl-2"></i></button>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                </div>
            </div>
        </div>
    </form>
</div>


<script>
    $(document).ready(function() {
        var scrollAmount = 500; // Adjust this value to set the scroll amount

        $('.btn-next').click(function() {
            var scrollLeft = $('.scroll-content').scrollLeft();
            $('.scroll-content').animate({
                scrollLeft: scrollLeft + scrollAmount
            }, 400); // Adjust the duration as needed
        });

        $('.btn-prev').click(function() {
            var scrollLeft = $('.scroll-content').scrollLeft();
            $('.scroll-content').animate({
                scrollLeft: scrollLeft - scrollAmount
            }, 400); // Adjust the duration as needed
        });
    });
</script>

<script src="<?php echo base_url()?>assets/admin/plugins/select2/js/select2.full.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('.select2').select2();
    });
</script>
