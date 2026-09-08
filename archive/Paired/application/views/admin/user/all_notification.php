<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <?php $this->load->view('admin/include/breadcrumb'); ?>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                   
                    <div class="box-header">
                      <h3 class="box-title"><?php echo trans('notifications') ?></h3>
                    </div>

                    <div class="card-body border-0 p-0">
		            	<?php foreach ($notifications as $notification): ?>

		            		<?php 

						      if($notification->noti_type == 1){
						        $noty_link = '#'; 

						      }elseif($notification->noti_type == 2 ){
						        $noty_link = base_url('admin/sessions/booking_details/'.$notification->content_id) ;

						      }elseif($notification->noti_type == 3 ){
						        $noty_link = '#';

						      }elseif($notification->noti_type == 5 ){
						        $noty_link = '#';

						      }elseif($notification->noti_type == 6 ){
						        $noty_link = '#';

						      }elseif($notification->noti_type == 7 ){
						        $noty_link = '#';

						      }elseif($notification->noti_type == 8 ){

						        if($notification->user_id == 0){
						          $noty_link = base_url('admin/payouts/requests') ;
						        }else{
						          $noty_link = base_url('admin/payouts/user') ;
						        }
						        

						      }else{

						        $noty_link = '#';
						        $image = 'assets/images/avatar.png';
						      }

						      if(!empty($notification->thumb)){
						            $thumb = $notification->thumb; 
						      }else{
						            $thumb = ('assets/images/no-photo.png');
						      }

						      $message = $notification->text;
						      
						    ?>

						    <a href="<?php echo html_escape($noty_link) ?>" class="dropdown-item">
				            	<div class="d-flex justify-content-start p-2 mb-0 mt-0">
							      <div>
							        <img src="<?php echo base_url($thumb) ?>"  class="avatar-sm">
							      </div>
							      <div class="ml-4">
							        <p class="text-sm mt-0 mb-0"><?php echo strip_tags($message) ?></p>
							        <p class="text-sm text-muted mt-0 mb-0"><i class="far fa-clock mr-1"></i><?php echo get_time_ago($notification->noti_time) ?></p>
							      </div>
							    </div>
							</a>
						    <hr class="devider  mt-0 mb-0">
		            		
		            	<?php endforeach ?>
		            </div>

		            <div class="mt-4 mt-lg-10">
						<?php echo $this->pagination->create_links(); ?>
					</div>
                    
                </div>
            </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- end Main Wrapper -->