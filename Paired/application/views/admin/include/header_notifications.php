

<!-- 
* Notification Types

1.Welcome notification
2.Booking success notificatio
3.Booking status notification
4.Meeting reminder notification
5.Session review notification
6.New message  notification
7.Favourite notification
8.Payout withdrawl notification
9.Payment done notification 

-->


<div class="dropdown-menu mr-4 noti-card-position show_noti p-0 m-0" >
  <?php foreach ($notifications as $notification): ?>

    <?php 

      if($notification->noti_type == 1){
        $noty_link = 'https://kb.pairedbybpu.uk/welcome/'; 

      }elseif($notification->noti_type == 2 ){
        $noty_link = base_url('admin/sessions/booking_details/'.$notification->content_id) ;

      }elseif($notification->noti_type == 3 ){
        $noty_link = '#';

      }elseif($notification->noti_type == 5 ){
        $noty_link = '#';

      }elseif($notification->noti_type == 6 ){
        $noty_link = base_url('admin/message') ;

      }elseif($notification->noti_type == 7 ){
        $noty_link = '#';

      }elseif($notification->noti_type == 8 ){

        if($notification->user_id == 0){
          $noty_link = base_url('admin/payouts/requests');
        }else{
          $noty_link = base_url('admin/payouts/user');
        }
      }else{
        $noty_link = base_url('admin/payment/transactions');
      }

      if(!empty($notification->thumb)){
        $thumb = $notification->thumb; 
      }else{
        $thumb = ('assets/images/no-photo.png');
      }

      $message = $notification->text;
      
    ?>

  <a href="<?php echo html_escape($noty_link) ?>" class="dropdown-item <?php if($notification->seen == 0){echo 'bg-light';} ?>">
    <div class=" py-2 d-flex justify-content-start">
      <div>
        <div class="mt-1 avatar-sm" style="background-image: url(<?php echo base_url($thumb) ?>);">
        
      </div>
      </div>
      <div class="ml-4">
        <p class="text-sm mt-1 mb-1 noti_msg break_word fw-500"><?php echo html_escape($message) ?></p>
        <p class="fs-12 text-muted mt-0 mb-1"><i class="far fa-clock mr-1"></i><?php echo get_time_ago($notification->noti_time) ?></p>
      </div>
    </div>
  </a>
  <div class="dropdown-divider mt-0 mb-0"></div>

  <?php endforeach ?>

  <a href="<?php echo base_url('admin/notifications/all') ?>" class="py-3 dropdown-item dropdown-footer"><?php echo trans('see-all') ?></a>
</div>

