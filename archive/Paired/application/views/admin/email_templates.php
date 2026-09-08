
<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <?php include"include/breadcrumb.php"; ?>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- /.col-md-6 -->
          <div class="col-lg-12">
            <div class="card">

              <div class="">
                <div class="row">
                  <div class="col-md-4">
                    <div class="card-body">
                      <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
                       <?php $i=1; foreach ($templates as $template): ?>
                          <li class="nav-item mt-2">
                            <a class="nav-link <?php if($i==1){echo 'active';} ?> template_email email-template-<?php echo html_escape($template->slug) ?>" data-id="<?php echo html_escape($template->slug) ?>" href="#" ><i class="fa fa-arrow-circle-right mr-2"></i><?php echo html_escape($template->title) ?></a>
                          </li>
                        <?php $i++; endforeach ?>
                        
                      </ul>
                    </div>
                  </div>

                  <!-- col-md-4 -->
                  <div class="col-md-8 pl-5 card">
                    

                    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/email_templates/add') ?>" role="form" class="form-horizontal pl-20 card-body">

                      <div class="email_template_area pt-20">
                        <?php $this->load->view('admin/include/email_template') ?>
                      </div>
                    

                      <!-- csrf token -->
                      <input type="hidden" name="slug" value="verification" class="template-slug">
                      <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                      <button type="submit" class="btn btn-primary btn-lg btn-block mt-3 fs-15"> <?php echo trans('save-changes') ?></button>
                          
                    </form>



                  </div>
                </div>
                
              </div>
            </div>

          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>








