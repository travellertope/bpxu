<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
      <div class="container">
         <div class="row container d-flex justify-content-center">

            <div class="col-md-4 card-body px-0">
               <div class="list-group mb-3">

                  <div class="input-group px-3 mb-3 mt-2 rounded">
                      <input type="text" class="form-control border-right-0 search_contact" name="search_contact" placeholder="<?php echo trans('search-contacts') ?>" aria-describedby="basic-addon2">
                      <div class="input-group-append rounded">
                        <span class="input-group-text bg-white rounded ml--2" id="basic-addon2"><i class="bi bi-search"></i></span>
                      </div>
                   </div>
                 
                  <div class="load_contact">
                     <?php $this->load->view('admin/user/include/contact'); ?>
                  </div>
                  <input type="hidden" class="msg_load_url" name="msg_load_url" value="">
               </div>
            </div>

            <div class="col-md-8 card-body px-0 pt-0">
               <div class="box shadow-none box-warning direct-chat direct-chat-warning mb-0">

                  <div class="box-body load_message_content">
                     <div class="without_message py-5 px-8">
                        <div>
                           <p class="text-center mt-2 mb-0"><i class="bi bi-chat-text empty_message_icon"></i></p>
                           <h6 class="text-center"><?php echo trans('start-new-message') ?></h6>
                           <p class="text-center text-dark"><?php echo trans('messages-sent-after-connecting') ?></p>
                        </div>
                     </div>

                     <?php $this->load->view('admin/user/include/message_content'); ?>
                  </div>

                  <div class="box-footer mt-4 pt-4 px-3 message_input hide">
                     <form method="post" enctype="multipart/form-data" class="validate-form message_btn" action="<?php echo base_url('admin/message/send_message')?>" role="form" novalidate>
                        <div class="input-group">
                           <input type="text" name="message" value="" placeholder="Type Message" class="form-control message_value" required>
                           <span class="input-group-btn">
                           <button type="submit" class="btn btn-primary btn-flat btnchat"><i class="fa fa-paper-plane"></i></button>
                           </span>
                           <input type="hidden" class="msg_with" name="mgs_to" value="<?php echo html_escape($mgs_with_id);?>">
                           <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>