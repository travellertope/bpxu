<?php if(!empty($user)): ?>
   <div class="box-header with-border p-3">
      <div class="d-flex justify-content-start align-item-center">
         <?php if(!empty($user->thumb)): ?>
            <div class="avatar-sm border-0 position-relative mr-3" style="background-image: url(<?php echo base_url($user->thumb) ?>);"></div>
         <?php else: ?>
            <div class="avatar-sm border-0 position-relative mr-3" style="background-image: url(<?php echo base_url('assets/images/no-photo-sm.png') ?>);"></div>
         <?php endif; ?>

         <h5 class="mb-0 pt-2"><?php echo html_escape($user->name) ?></h5>
      </div>
   </div>
<?php endif; ?>


<?php if (!empty($messages)): ?>
<div class="message-scroll">
   <?php foreach ($messages as $message): ?>
      <div class="direct-chat-messages">
         <?php if($message->mgs_from == $this->session->userdata('id')):?>

            <div class="direct-chat-msg right">
               <div class="direct-chat-text text-left px-3 py-2">
                  <?php 
                     if (filter_var($message->message, FILTER_VALIDATE_URL) === FALSE) {
                        echo ($message->message);
                     }else{
                        echo "<a target='_blank' class='text-primary' href='".$message->message."'>".$message->message.'</a>';
                     }
                   ?>
               </div>
               <div class="direct-chat-info clearfix pt-1">
                  <span class="direct-chat-timestamp pull-left"> <?php echo my_date_show_time($message->mgs_time) ?></span>
               </div>
            </div>
         <?php else:?>
            <div class="direct-chat-msg text-right px-3 py-2">

               <?php if(!empty(get_by_id($message->mgs_from, 'users')->thumb)): ?>
                  <img class="direct-chat-img" src="<?php echo base_url(get_by_id($message->mgs_from, 'users')->thumb) ?>">
               <?php else: ?>
                  <img class="direct-chat-img" src="<?php echo base_url('assets/images/no-photo-sm.png') ?>" alt="">
               <?php endif; ?>

               <div class="direct-chat-text text-left">
                  <?php 
                     if (filter_var($message->message, FILTER_VALIDATE_URL) === FALSE) {
                        echo ($message->message);
                     }else{
                        echo "<a target='_blank' class='text-light' href='".prep_url($message->message)."'>".$message->message.'</a>';
                     }
                   ?>
               </div>
               <div class="direct-chat-info clearfix pt-1">
                  <span class="direct-chat-timestamp pull-right"><?php echo get_time_ago($message->mgs_time) ?></span>
               </div>
            </div>
         <?php endif;?>
      </div>
   <?php endforeach ?>
</div>
<?php endif ?>



