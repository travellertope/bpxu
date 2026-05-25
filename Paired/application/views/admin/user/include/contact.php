<?php if(!empty($contacts)): ?>
   <?php foreach ($contacts as $contact): ?>
     <a data-id="<?php echo md5($contact->user_id) ?>" href="<?php echo base_url('admin/message/details/'.md5($contact->user_id)) ?>" class="list-group-itemc list-group-item-action d-flex justify-content-start align-items-start contact_details mb-0 bm-1 <?php if(get_unseen_messages(md5(user()->id), md5($contact->user_id)) == 1){echo "bg-primary-soft";} ?>">
      
         <?php if(!empty($contact->thumb)): ?>
            <div>
               <div class="avatar-sm border-0 position-relative mr-3" style="background-image: url(<?php echo base_url($contact->thumb) ?>);">
                  <span class="active-icon"><i class="bi bi-circle-fill fs-10 <?php if($contact->is_active == 1){echo 'text-success';}else{echo 'text-grey';} ?> mr-1 pb-1"></i></span>
               </div>
            </div>
         <?php else: ?>
          
            <div class="avatar-sm border-0 position-relative mr-3" style="background-image: url(<?php echo base_url('assets/images/no-photo-sm.png') ?>);">
               <span class="active-icon"><i class="bi bi-circle-fill fs-10 <?php if($contact->is_active == 1){echo 'text-success';}else{echo 'text-grey';} ?> mr-1 pb-1"></i></span>
            </div>
  
         <?php endif; ?>
         
         <span>
            <p class="mt-0 mb-0 fw-500"> <?php echo html_escape($contact->name) ?></p>
            <p class="mt-0 mb-0 fs-12 text-muted"><?php echo get_time_ago($contact->mgs_time) ?></p>
         </span>

         <?php if (get_unseen_messages(md5(user()->id), md5($contact->user_id)) != 0): ?>
            <div id="un_<?php echo md5($contact->user_id) ?>"></div>
         <?php endif ?>
         
     </a>
   <?php endforeach ?>
<?php else: ?>
   <div class="list-group-itemc text-center">
      <p class="empty_contact"><?php echo trans('no-contact-found') ?></p>
   </div>
<?php endif; ?>