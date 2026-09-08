<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <?php include"include/breadcrumb.php"; ?>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                  <h3 class="card-title"><?php echo trans('mentees') ?></h3>
                    <div class="card-tools">
                      <div class="filter-bars pull-right d-none">
                        <a class="filter-action btn btn-outline-primary text-primary"> <i class="fas fa-filter"></i></a>
                      </div>
                    </div>
                </div>

                <div class="filter_popup showFilter">
                    <p class="leads mb-3"><?php echo trans('filters') ?></p>

                    <form action="<?php echo base_url('admin/mentee/all') ?>" class="sort_form" method="get">
                      <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo trans('plans') ?></label>
                                <select name="package" class="form-control form-control-sm">
                                    <option <?php if(isset($_GET['package']) && $_GET['package'] == 'all'){echo "selected";} ?> value="all"><?php echo trans('all') ?></option>
                                    <?php foreach ($packages as $package): ?>
                                        <option <?php if(isset($_GET['package']) && $_GET['package'] == $package->id){echo "selected";} ?> value="<?php echo html_escape($package->id) ?>"><?php echo html_escape($package->name) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo trans('status') ?></label>
                                <select name="sort" class="form-control search form-control-sm">
                                    <option <?php if(isset($_GET['sort']) && $_GET['sort'] == 'all'){echo "selected";} ?> value="all"><?php echo trans('all') ?></option>
                                    <option <?php if(isset($_GET['sort']) && $_GET['sort'] == 'verified'){echo "selected";} ?> value="verified"><?php echo trans('paid') ?></option>
                                    <option <?php if(isset($_GET['sort']) && $_GET['sort'] == 'pending'){echo "selected";} ?> value="pending"><?php echo trans('pending') ?></option>
                                    <option <?php if(isset($_GET['sort']) && $_GET['sort'] == 'expired'){echo "selected";} ?> value="expired"><?php echo trans('expired') ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo trans('name') ?></label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="<?php echo trans('search-by-name') ?>">
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                          <button type="submit" class="btn btn-primary btn-sm btn-block"><?php echo trans('submit') ?></button>
                        </div>

                      </div>
                    </form>
                </div>

                <div class="card-body table-responsive p-0">
                  <?php if (empty($users)): ?>
                    <?php $this->load->view('admin/include/not-found') ?>
                  <?php else: ?>
                    <table class="table table-hover m-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo trans('name') ?></th>
                                <th><?php echo trans('info') ?></th>
                                <th>Hear about us</th>
                                <th><?php echo trans('status') ?></th>
                                <th><?php echo trans('informations') ?></th>
                                <th><?php echo trans('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach ($users as $user): ?>
                                <tr id="row_<?php echo html_escape($user->id) ?>">
                                    <th scope="row"><?php echo html_escape($i) ?></th>
                                    
                                    <?php if ($user->thumb == ''): ?>
                                        <?php $avatar = 'assets/images/no-photo-sm.png'; ?> 
                                    <?php else: ?>
                                        <?php $avatar = $user->thumb; ?>
                                    <?php endif ?>

                                    <td class="pl-2">
                                      <a  href="#" class="text-dark">
                                        <div class="d-flex align-items-center">
                                          <div>
                                            <img width="50px" class="img-circle mr-3" src="<?php echo base_url($avatar) ?>"> 
                                          </div>
                                          
                                          <div class="d-flexs flex-columns">
                                              
                                            <span class="leads font-weight-bold mb-0 mr-2"><?php echo ucfirst($user->name); ?></span>
                                            
                                            <?php $code = get_by_id($user->country, 'country')->code; ?>
                                            <span data-tooltip="<?php echo get_by_id($user->country, 'country')->name; ?>" class=""><img class="flag-img-booking ml-1" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>"></span>
                                              
                                              <p class="text-muted mb-0">
                                                <?php echo html_escape($user->email); ?>
                                                <?php if ($user->email_verified == 1): ?>
                                                  <span class="ml-1 text-success" data-toggle="tooltip" data-title="Email Verified" data-placement="top"><i class="fas fa-check-circle"></i></span>
                                                <?php endif ?>
                                              </p>

                                              <p class="mb-1">Active <?php echo get_time_ago($user->last_active) ?></p>
                                       
                                          </div>
                                        </div>
                                      </a>

                                      <a href="<?php echo base_url('admin/sessions/mentee_profile/'.$user->id);?>" class="btn btn-primary btn-sm mt-1 ml-5">Mentee Profile <i class="bi bi-arrow-right"></i></a>
                                    </td>

                                     <td><?php echo html_escape($user->hear_about) ?></td>

                                    <td>
                                      
                                      <?php if(!empty($user->employment_status)): ?>
                                        <p class="mt-0 mb-1"><b>Employment Status:</b> <?php echo html_escape($user->employment_status) ?></p>
                                      <?php endif; ?>

                                      <?php if(!empty($user->expertise_industry)): ?>
                                        <p class="mt-0 mb-1"><b>Expertise Industry:</b> <?php echo html_escape($user->expertise_industry) ?></p>
                                      <?php endif; ?>

                                     

                                      <?php if(!empty($user->experience_year)): ?>
                                        <p class="mt-0 mb-1"><b>Experience:</b> <?php echo html_escape($user->experience_year) ?></p>
                                      <?php endif; ?>

                                      <?php if(!empty($user->linkedin_profile)): ?>
                                          <span class="mt-0 mb-1"><b>Linkedin Url:</b></span>
                                          <span class="text-primary"><a target="_blank" href="<?php echo prep_url($user->linkedin_profile) ?>"><?php echo html_escape($user->linkedin_profile) ?></a></span>
                                      <?php endif; ?>

                                    </td>

                                     <td>
                                      <?php if ($user->status == 1): ?>
                                          <span class="badge-custom badge-success-soft"><i class="fas fa-check-circle"></i> <?php echo trans('active') ?></span>
                                      <?php else: ?>
                                        <span class="badge-custom badge-danger-soft"><i class="fas fa-times-circle"></i> <?php echo trans('disabled') ?></span>
                                      <?php endif ?>
                                    </td>
                                  
                                    <td>
                                      <span class="mr-2 text-muted"><b><?php echo trans('joined') ?>: <?php echo my_date_show($user->created_at) ?></b></span>
                                    </td>


                                    <td class="actions" width="12%">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                                              <i class="fas fa-ellipsis-h"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right" role="menu" >
                                              
                                              <?php if ($user->status == 1): ?>
                                                  <a href="<?php echo base_url('admin/mentee/status_action/2/'.html_escape($user->id));?>" class="dropdown-item"><i class="lnib lni-cross-circle mr-1"></i>  <?php echo trans('deactivate') ?></a>
                                              <?php else: ?>
                                                  <a href="<?php echo base_url('admin/mentee/status_action/1/'.html_escape($user->id));?>" class="dropdown-item"><i class="mentee lnib lni-checkmark-circle mr-1"></i>  <?php echo trans('activate') ?></a>
                                              <?php endif ?>
                                              
                                              <a data-val="User" data-id="<?php echo html_escape($user->id); ?>" href="<?php echo base_url('admin/mentee/delete/'.html_escape($user->id));?>" class="dropdown-item delete_item"><i class="lni lni-trash-can mr-1"></i> <?php echo trans('delete') ?></a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php $i++; endforeach ?>
                        </tbody>
                    </table>
                  <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
              <?php echo $this->pagination->create_links(); ?>
            </div>
          </div>
        </div>
          <!-- col-md-12 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>







<?php $b=1; foreach ($users as $user): ?>
<div class="modal fade" id="recordModal_<?php echo html_escape($b) ?>">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <div><h4 class="modal-title"><?php echo trans('record-payment') ?></h4></div>
        <div class="mclose" data-dismiss="modal"><i class="lnib lni-close"></i></div>
      </div>

      <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/payment/offline')?>" role="form" novalidate>
            
      <div class="modal-body">
          <div class="form-group mb-4">
              <label><?php echo trans('plans') ?></label>
              <select class="form-control single_select" name="package" required>
                  <option value=""><?php echo trans('select') ?></option>
                  <?php foreach ($packages as $package): ?>
                    <option value="<?php echo html_escape($package->id) ?>"><?php echo html_escape($package->name) ?> </option>
                  <?php endforeach ?>
              </select>
          </div>

          <div class="form-group mb-4">
              <label><?php echo trans('subscription-type') ?></label>
              <select class="form-control single_select" name="billing_type" required>
                  <option value=""><?php echo trans('select') ?></option>
                  <option value="monthly"><?php echo trans('monthly') ?></option>
                  <option value="yearly"><?php echo trans('yearly') ?></option>
                  <option value="yearly"><?php echo trans('yearly') ?></option>
              </select>
          </div>

          <div class="form-group mb-4">
              <label><?php echo trans('payment-status') ?></label>
              <select class="form-control single_select" name="status" required>
                  <option value=""><?php echo trans('select') ?></option>
                  <option value="verified"><?php echo trans('verified') ?></option>
                  <option value="pending"><?php echo trans('pending') ?></option>
              </select>
          </div>

      </div>

      <div class="modal-footer justify-content-between">
        <input type="hidden" name="user" value="<?php echo html_escape($user->id) ?>">
        <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">
        <button type="submit" class="btn btn-primary"><?php echo trans('add-payment') ?></button>
      </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<?php $b++; endforeach; ?>