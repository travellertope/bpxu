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
                  <div class="filter-bars pull-right">
                    <a class="filter-action btn btn-outline-primary text-primary"> <i class="fas fa-filter"></i></a>
                  </div>
                </div>
            </div>

            <div class="filter_popup showFilter">
              <div class="d-flex justify-content-between bbm-1">
                <div><p><?php echo trans('filters') ?></p></div>
                <div><a href="<?php echo base_url('admin/mentee') ?>" class="btn btn-light btn-xs"><i class="bi bi-arrow-repeat"></i> <?php echo trans('reset') ?></a></div>
              </div>

              <form action="<?php echo base_url('admin/mentee') ?>" class="sort_form" method="get">
                <div class="row">
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
                      <label><?php echo trans('name') ?></label>
                      <input type="text" value="<?php if(isset($_GET['search'])){echo html_escape($_GET['search']);} ?>" name="search" class="form-control form-control-sm" placeholder="<?php echo trans('search-by-name') ?>">
                    </div>
                  </div>

                  <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm btn-block"><?php echo trans('submit') ?></button>
                  </div>

                </div>
              </form>
            </div>

            <?php if (empty($mentees)): ?>
              <?php $this->load->view('admin/include/not-found') ?>
            <?php else: ?>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover m-0">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th><?php echo trans('name') ?></th>
                      <th><?php echo trans('status') ?></th>
                      <th><?php echo trans('action') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i=1; foreach ($mentees as $mentee): ?>
                      <tr id="row_<?php echo html_escape($mentee->id) ?>">
                        <td scope="row"><?php echo html_escape($i) ?></td>
                        
                        <?php if ($mentee->thumb == ''): ?>
                            <?php $avatar = 'assets/images/no-photo-sm.png'; ?> 
                        <?php else: ?>
                            <?php $avatar = $mentee->thumb; ?>
                        <?php endif ?>

                        <td class="pl-2">
                          <a data-tooltip="<?php echo trans('view-details') ?>" href="<?php echo base_url('admin/sessions/mentee_details/'.($mentee->mentee_id));?>" class="text-dark">
                            <div class="d-flex align-items-center">
                              <div>
                                <img width="50px" class="img-circle mr-3" src="<?php echo base_url($avatar) ?>"> 
                              </div>
                              
                              <div class="d-flexs flex-columns">
                                  
                                <span class="leads font-weight-bold mb-0 mr-2"><?php echo ucfirst($mentee->name); ?></span>
                                <span data-tooltip="<?php if($mentee->is_active == 1){echo 'online';}else{ echo 'offline';} ?>"><i class="bi bi-circle-fill <?php if($mentee->is_active == 1){echo 'text-success';}else{'text-gray';} ?> active_icon_img"></i></span>

                                <?php $code = get_by_id($mentee->country, 'country')->code; ?>
                                  <span data-tooltip="<?php echo get_by_id($mentee->country, 'country')->name; ?>" class=""><img class="flag-img-booking ml-1" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>"></span>
                                
                                <p class="text-muted mb-0">
                                  <?php echo html_escape($mentee->email); ?>
                                  <?php if ($mentee->email_verified == 1): ?>
                                    <span class="ml-1 text-success" data-toggle="tooltip" data-title="Email Verified" data-placement="top"><i class="fas fa-check-circle"></i></span>
                                  <?php endif ?>
                                </p>
                              </div>
                            </div>
                          </a>
                        </td>

                        <td class="d-none">
                          <?php $skills = $this->common_model->get_mentor_skills($user->id); ?>
                          <?php //echo '<pre>'; print_r($skills); exit(); ?>
                          <p class="mb-1"><b><?php echo trans('category') ?></b> : <?php echo get_by_id($user->category,'categories')->name ?></p>
                          <b><?php echo trans('skill') ?></b> : 
                          <?php foreach ($skills as $skill): ?>
                            <p class="badge badge-info-soft mb-1"><?php echo get_by_id($skill->skill_id,'skills')->skill ?></p>
                          <?php endforeach ?>

                          <p class="mb-1 mt-0"><b><?php echo trans('total-mentoring') ?> :</b> <?php echo sprintf("%02d", get_count_minute_by_user($user->id)) ?>+ Minutes</p>

                          <p class="mb-1 mt-1"><b><?php echo trans('experience') ?></b> : <?php echo html_escape($user->experience_year) ?> <?php echo trans('years') ?></p>
                          
                        </td>

                        <td>
                          <?php if ($mentee->status == 1): ?>
                              <span class="badge-custom badge-success-soft"><i class="fas fa-check-circle"></i> <?php echo trans('active') ?></span>
                          <?php else: ?>
                            <span class="badge-custom badge-danger-soft"><i class="fas fa-times-circle"></i> <?php echo trans('disabled') ?></span>
                          <?php endif ?>
                        </td>


                        <td class="actions" width="12%">
                          <div class="btn-group">
                            <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                              <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu" >
                              
                              <a data-val="User" data-id="<?php echo html_escape($mentee->mentee_id); ?>" href="<?php echo base_url('admin/users/delete/'.html_escape($mentee->mentee_id));?>" class="dropdown-item delete_item"><i class="lni lni-trash-can mr-1"></i> <?php echo trans('delete') ?></a>
                            </div>
                          </div>
                        </td>
                      </tr>
                    <?php $i++; endforeach ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
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