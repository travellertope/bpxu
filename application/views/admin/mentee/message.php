

<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
      <div class="container">
         <div class="row container d-flex justify-content-center">

            <div class="col-md-9">
               <div class="box box-warning direct-chat direct-chat-warning">
                  <div class="box-header with-border p-3">
                     <h3 class="box-title"><img width="18px" src="" ><?php echo trans('message') ?></h3>
                  </div>

                  <div class="box-body load_message_content">
                     <?php $this->load->view('admin/user/include/message_content'); ?>
                  </div>

                  <div class="box-footer p-4">
                     <form method="post" enctype="multipart/form-data" class="validate-form message_btn" action="<?php echo base_url('admin/message/send_message')?>" role="form" novalidate>
                        <div class="input-group">
                           <input type="text" name="message" placeholder="Type Message" class="form-control prompt" required>
                           <span class="input-group-btn">
                           <button type="submit" class="btn btn-primary btn-flat btnchat"><i class="fa fa-paper-plane"></i></button>
                           </span>
                           <input class="uid" type="hidden" name="uid" value="">
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