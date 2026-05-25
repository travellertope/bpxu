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
                  <h3 class="card-title"><?php echo trans('mentors') ?></h3>
                    <div class="card-tools">
                      <div class="filter-bars pull-right">
                        <a class="filter-action btn btn-outline-primary text-primary"> <i class="fas fa-filter"></i></a>
                      </div>
                    </div>
                </div>

                <div class="filter_popup showFilter">
                    <div class="d-flex justify-content-between bbm-1">
                      <div><p><?php echo trans('filters') ?></p></div>
                      <div><a href="<?php echo base_url('admin/users') ?>" class="btn btn-light btn-xs"><i class="bi bi-arrow-repeat"></i> <?php echo trans('reset') ?></a></div>
                    </div>

                    <form action="<?php echo base_url('admin/users/all_users/all') ?>" class="sort_form" method="get">
                      <div class="row">

                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo trans('name') ?></label>
                                <input type="text" name="search" class="form-control form-control-sm" placeholder="<?php echo trans('search-by-name') ?>">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo trans('countries') ?></label>
                                <select name="country" class="form-control form-control-sm">
                                    <option <?php if(isset($_GET['country']) && $_GET['country'] == 'all'){echo "selected";} ?> value="all"><?php echo trans('select') ?></option>
                                    <?php foreach ($countries as $country): ?>
                                        <option <?php if(isset($_GET['country']) && $_GET['country'] == $country->id){echo "selected";} ?> value="<?php echo html_escape($country->id) ?>"><?php echo html_escape($country->name) ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-12">
                            <div class="form-group">
                                <label><?php echo trans('categories') ?></label>
                                <select name="category" class="form-control form-control-sm">
                                    <option <?php if(isset($_GET['category']) && $_GET['category'] == 'all'){echo "selected";} ?> value="all"><?php echo trans('select') ?></option>
                                    <?php foreach ($categories as $category): ?>
                                        <option <?php if(isset($_GET['category']) && $_GET['category'] == $category->id){echo "selected";} ?> value="<?php echo html_escape($category->id) ?>"><?php echo html_escape($category->name) ?></option>
                                    <?php endforeach ?>
                                </select>
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
                          <th></th>
                          <th><?php echo trans('info') ?></th>
                          <th><?php echo trans('profile') ?></th>
                          <th><?php echo trans('kyc') ?></th>
                          <th><?php echo trans('earning-info') ?></th>
                          <th>Hear about us</th>
                          <th><?php echo trans('status') ?></th>
                          <th><?php echo trans('action') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php $i=1; foreach ($users as $user): ?>
                              <tr id="row_<?php echo html_escape($user->id) ?>">
                                  <td scope="row"><?php echo html_escape($i) ?></td>
                                  
                                  <?php if ($user->thumb == ''): ?>
                                      <?php $avatar = 'assets/images/no-photo-sm.png'; ?> 
                                  <?php else: ?>
                                      <?php $avatar = $user->thumb; ?>
                                  <?php endif ?>

                                  <td class="pl-2">
                                    
                                      <div class="d-flex align-items-center">
                                        <div class="mr-2">
                                          <a data-tooltip="<?php echo trans('view-details') ?>" href="<?php echo base_url('admin/users/mentor_details/'.($user->id));?>" class="text-dark">
                                            <div class="avatar-sm" style="background-image: url(<?php echo base_url($avatar) ?>);"></div>
                                          </a>
                                        </div>
                                        
                                        <div class="d-flexs flex-columns">

                                          <span class="leads font-weight-bold mb-0 mr-1"><?php echo ucfirst($user->name); ?></span>
                                          
                                          <?php $code = get_by_id($user->country, 'country')->code; ?>
                                            <span data-tooltip="<?php echo get_by_id($user->country, 'country')->name; ?>" class=""><img class="flag-img-booking ml-1" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>"></span>
                                          
                                          <p class="text-muted mb-0">
                                            <?php echo html_escape($user->email); ?>
                                            <?php if ($user->email_verified == 1): ?>
                                              <span class="ml-1 text-success" data-toggle="tooltip" data-title="Email Verified" data-placement="top"><i class="fas fa-check-circle"></i></span>
                                            <?php endif ?>
                                          </p>
                                        </div>
                                      </div>
                                    
                                  </td>

                                  <td>
                                    <?php if(!empty($user->bp_network)): ?>
                                      <p class="mt-0 mb-1"><b>Bp Network:</b> <?php echo html_escape($user->bp_network) ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if(!empty($user->employment_status)): ?>
                                      <p class="mt-0 mb-1"><b>Employment Status:</b> <?php echo html_escape($user->employment_status) ?></p>
                                    <?php endif; ?>

                                    <?php if(!empty($user->expertise_industry) && $user->expertise_industry != 'Other'): ?>
                                      <p class="mt-0 mb-1"><b>Expertise Industry:</b> <?php echo html_escape($user->expertise_industry) ?></p>
                                    <?php endif; ?>

                                    <?php if(!empty($user->other_industry) && $user->expertise_industry == 'Other'): ?>
                                      <p class="mt-0 mb-1"><b>Expertise Industry:</b> <?php echo html_escape($user->other_industry) ?></p>
                                    <?php endif; ?>

                                    <?php if(!empty($user->company)): ?>
                                      <p class="mt-0 mb-1"><b>Company:</b> <?php echo html_escape($user->company) ?></p>
                                    <?php endif; ?>
                                  </td>

                                  <td>
                                    <?php $skills = explode(',', $user->skills); ?>
<!--                                    
<p class="mb-1"><b><?php echo trans('category') ?></b> : <?php echo get_by_id($user->category,'categories')->name ?></p> 
-->

                                    <?php if (!empty($skills)): ?>
                                    <b><?php echo trans('skills') ?></b> : 
                                    <?php foreach ($skills as $skill): ?>
                                      <p class="badge badge-secondary-soft mb-1"><?php echo html_escape($skill) ?></p>
                                    <?php endforeach ?>
                                    <?php endif ?>

                                    <?php if (!empty(get_count_minute_by_user($user->id))): ?>
                                      <p class="mb-1 mt-0"><b><?php echo trans('total-mentoring') ?> :</b> <?php echo sprintf("%02d", get_count_minute_by_user($user->id)) ?>+ <?php echo trans('minutes') ?></p>
                                    <?php endif ?>
                                    
                                    <?php if (!empty($user->experience_year)): ?>
                                      <p class="mb-1 mt-1"><b><?php echo trans('experience') ?></b> : <?php echo html_escape($user->experience_year) ?> <?php echo trans('years') ?></p>
                                    <?php endif ?>

                                    <div>
                                      <?php if (!empty($user->linkedin_profile)): ?>
                                        <span class="mr-2"><a target="_blank" href="<?php echo prep_url($user->linkedin_profile) ?>"><i class="bi bi-linkedin"></i></a></span>
                                      <?php endif ?>
                                      <?php if (!empty($user->instagram_profile)): ?>
                                        <span class="mr-2"><a target="_blank" href="<?php echo prep_url($user->instagram_profile) ?>"><i class="bi bi-instagram"></i></a></span>
                                      <?php endif ?>
                                      <?php if (!empty($user->x_profile)): ?>
                                        <span class="mr-2"><a target="_blank" href="<?php echo prep_url($user->x_profile) ?>"><i class="bi bi-twitter"></i></a></span>
                                      <?php endif ?>
                                      <?php if (!empty($user->facebook_profile)): ?>
                                        <span class="mr-2"><a target="_blank" href="<?php echo prep_url($user->facebook_profile) ?>"><b><i class="bi bi-facebook"></i></b></a></span>
                                      <?php endif ?>
                                    </div>
                                    
                                  </td>

                                  <td>
                                    <?php if ($user->kyc_verified == 0): ?>
                                      <span class="badge badge-secondary-soft"><i class="bi bi-clock"></i> <?php echo trans('pending') ?></span>
                                    <?php else: ?>
                                      <span data-tooltip="<?php echo trans('kyc').' '.trans('verified') ?>"><img width="30px" src="<?php echo base_url('assets/images/approved.png') ?>"></span>
                                    <?php endif ?>
                                  </td>

                                  <td>
                                    <?php 
                                      $balance = number_format($user->balance/100, 2);
                                      $total_withdraw = total_earnings($user->id) - $balance;
                                    ?>

                                    <p class="mb-1"><b><?php echo trans('balance') ?> : </b><?php echo settings()->currency_symbol ?> <?php echo html_escape($balance) ?></p>
                                    <p class="mb-1"><b><?php echo trans('total-earnings') ?> : </b><?php echo settings()->currency_symbol ?> <?php echo total_earnings($user->id); ?></p>
                                    <p class="mb-1 mt-0"><b><?php echo trans('total-withdraw') ?> : </b><?php echo settings()->currency_symbol ?> <?php echo html_escape($total_withdraw) ?></p>
                                  </td>


                                  <td><?php echo html_escape($user->hear_about) ?></td>
                                   <td>
                                    <?php if ($user->status == 1): ?>
                                        <span class="badge-custom badge-success-soft"><i class="fas fa-check-circle"></i> <?php echo trans('approved') ?></span>
                                    <?php elseif($user->status == 2): ?>
                                      <span class="badge-custom badge-danger-soft"><i class="fas fa-times-circle"></i> <?php echo trans('disabled') ?></span>
                                    <?php else: ?>
                                      <span class="badge-custom badge-warning-soft"><i class="fas fa-times-circle"></i> <?php echo trans('pending') ?></span>
                                    <?php endif ?>
                                  </td>
                                
                                  <td class="d-none"> 
                                    <?php if ($user->user_type == 'registered'): ?>
                                      <?php echo html_escape(user_payment_details($user->id)->package); ?>
                                    <?php else: ?>
                                      <?php echo trans('trial') ?>
                                    <?php endif ?>
                                  </td>
                             
                                  
                                  <td class="d-none">
                                      <?php $label = ''; ?>
                                      <?php if (user_payment_details($user->id)->status == 'pending'){
                                        $label = 'warning-soft';
                                        $text = '<i class="fas fa-clock"></i> '.trans(user_payment_details($user->id)->status);
                                      }else if(user_payment_details($user->id)->status == 'verified'){ 
                                        $label = 'success';
                                        $text = '<i class="fas fa-check-circle"></i> '.trans('paid');
                                      }else{ 
                                        $label = 'danger';
                                        $text = '<i class="fas fa-times"></i>'. trans('expired');
                                      }?>
                                      <span class="badge badge-<?php echo html_escape($label) ?>">
                                          <b><?= $text; ?></b>
                                      </span>
                                  </td>
                                  

                                  <td class="d-none">
                                    <span class="mr-2 text-muted" data-tooltip="<?php echo trans('joined') ?>: <?php echo my_date_show($user->created_at) ?> " data-placement="top"><i class="fas fa-sign-in-alt"></i></span>

                                    <?php if ($user->user_type == 'registered'): ?>
                                      
                                    <?php if ($user->payment_status != 'expire'): ?>
                                        <span class="text-muted ml-1" data-tooltip="<?php echo trans('expire') ?>: <?php echo date_dif(date('Y-m-d'), $user->payment->expire_on) ?> Days left" data-placement="top"><i class="fas fa-user-clock"></i></span>
                                    <?php else: ?>
                                        <span class="text-muted ml-1" data-tooltip="<?php echo trans('expire') ?>: <?php echo get_time_ago($user->payment->expire_on) ?>" data-placement="top"><i class="fas fa-user-clock text-danger"></i></span>
                                    <?php endif ?>

                                  <?php else: ?>
                                    <span class="text-muted ml-1" data-tooltip="<?php echo trans('expire') ?>: <?php echo date_dif(date('Y-m-d'), $user->trial_expire) ?> Days left" data-placement="top"><i class="fas fa-user-clock"></i></span>
                                  <?php endif; ?>

                                  </td>


                                  <td class="actions" width="12%">
                                      <div class="btn-group">
                                          <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                          </button>
                                          <div class="dropdown-menu dropdown-menu-right" role="menu" >

                                            <?php if ($user->status == 1): ?>
                                              <a href="<?php echo base_url('admin/users/status_action/2/'.html_escape($user->id));?>" class="dropdown-item"><i class="lnib lni-cross-circle mr-1"></i>  <?php echo trans('deactivate') ?></a>
                                            <?php elseif ($user->status == 0): ?>
                                              <a href="<?php echo base_url('admin/users/status_action/1/'.html_escape($user->id));?>" class="dropdown-item"><i class="lnib lni-cross-circle mr-1"></i><?php echo trans('approve') ?></a>
                                            <?php else: ?>
                                                <a href="<?php echo base_url('admin/users/status_action/1/'.html_escape($user->id));?>" class="dropdown-item"><i class="lnib lni-checkmark-circle mr-1"></i>  <?php echo trans('activate') ?></a>
                                            <?php endif ?>
                                            
                                            <a data-val="User" data-id="<?php echo html_escape($user->id); ?>" href="<?php echo base_url('admin/users/delete/'.html_escape($user->id));?>" class="dropdown-item delete_item"><i class="lni lni-trash-can mr-1"></i> <?php echo trans('delete') ?></a>
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