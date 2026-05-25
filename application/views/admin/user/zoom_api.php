<div class="content-wrapper">
    <?php $this->load->view('admin/include/breadcrumb'); ?>
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                <?php $this->load->view('admin/user/include/settings_menu.php'); ?>

                <div class="col-lg-9 pl-3">
                    <div class="card">
                        <div class="box-header with-border">
                          <h3 class="box-title"><?php echo trans('online-meeting') ?></h3>
                        </div>

                        <div class="alert bg-success-soft conn_info hide brd-2 ml-1" role="alert">
                       
                          </div>

             
                          <div class="alert bg-danger-soft conn_error hide brd-2 ml-1" role="alert">
                    
                          </div>

                        <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/settings/update_zoom_api') ?>" role="form" class="form-horizontal pl-20">
                            <div class="card-body">
                               <div class="form-group row">
                                  <div class="col-6">
                                    <a class="pull-left badge badge-danger-soft brd-20 mr-3" target="_blank" href="https://marketplace.zoom.us/"><i class="fa fa-plus-circle"></i> <?php echo trans('create-zoom-app') ?></a>
                                  </div>

                                  <div class="col-6 text-right">
                                    <a class="pull-right badge badge-success-soft brd-20" target="_blank" href="http://mentorship.originlabsoft.com/docs/#idocs_zoom"><i class="fa fa-question-circle"></i> <?php echo trans('zoom-integration-doc') ?></a>
                                  </div>
                              </div>

                              <div class="form-group mt-4">
                                <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('zoom-account-id') ?></label>
                                <div class="col-sm-12">
                                  <input type="text" name="zoom_account_id" value="<?php echo html_escape(user()->zoom_account_id); ?>" class="form-control">
                                </div>
                              </div>

                              <div class="form-group m-t-20">
                                <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('zoom-client-id') ?></label>
                                <div class="col-sm-12">
                                  <input type="text" name="zoom_client_id" value="<?php echo html_escape(user()->zoom_client_id); ?>" class="form-control">
                                </div>
                              </div>

                              <div class="form-group m-t-20">
                                <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('zoom-client-secret') ?></label>
                                <div class="col-sm-12">
                                  <input type="password" name="zoom_client_secret" value="<?php echo html_escape(user()->zoom_client_secret); ?>" class="form-control">
                                </div>
                              </div>


                              <div class="form-group mb-5 d-hidesdf">
                                <div class="col-sm-12">
                                  <?php if (!empty(user()->zoom_account_id) && !empty(user()->zoom_client_id) && !empty(user()->zoom_client_secret)): ?>    
                                    <a class="btn btn-danger pull-right mb-5 check_zoom_api" href="#"><i class="bi bi-check-circle"></i> <?php echo trans('check-api-connection') ?></a><br><br>
                                  <?php endif; ?>
                                </div>
                              </div>
                            </div>

                            <div class="card-footer">
                                <input type="hidden" name="id" value="<?php echo html_escape(user()->id); ?>">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                                <button type="submit" class="btn btn-primary mt-2"><?php echo trans('save-changes') ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
