<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <?php include"include/breadcrumb.php"; ?>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

        <div class="row">
          <div class="col-lg-12">
            <div class="card">
                <div class="card-header mt-5 mb-3">
                  <h3 class="card-title"><?php echo trans('kyc') ?></h3>
                </div>



                <div class="row">
                  <div class="col-md-9">
                    <nav id="btab" class="mb-4 nav nav-tabs over-scroll" role="tablist">

                      <a href="<?php echo base_url('admin/verification/kyc/?search=pending') ?>" role="tab" data-rb-event-key="pending" aria-selected="false" class="nav-item nav-link <?php if($_GET['search'] == 'pending'){ echo 'active';} ?>">
                        <span class="badge fs-12 badge-warning mr-1"><?php echo count_kyc('0') ?></span>
                        <span class="text-dark fw-500"><?php echo trans('pending') ?></span>
                      </a>
                          
                      <a href="<?php echo base_url('admin/verification/kyc/?search=reject') ?>" role="tab" data-rb-event-key="reject" aria-selected="false" class="nav-item nav-link <?php if($_GET['search'] == 'reject'){ echo 'active';} ?>">
                        <span class="badge fs-12 badge-danger mr-1"><?php echo count_kyc(2) ?></span>
                        <span class="text-dark fw-500"><?php echo trans('rejected') ?></span>
                      </a>

                      <a href="<?php echo base_url('admin/verification/kyc/?search=approve') ?>" role="tab" data-rb-event-key="approve" aria-selected="false" class="nav-item nav-link <?php if($_GET['search'] == 'approve'){ echo 'active';} ?>">
                        <span class="badge fs-12 badge-success mr-1"><?php echo count_kyc(1) ?></span>
                        <span class="text-dark fw-500"><?php echo trans('approved') ?></span>
                      </a>

                      <a href="<?php echo base_url('admin/verification/kyc/?search=all') ?>" role="tab" data-rb-event-key="all" aria-selected="true" class="nav-item nav-link <?php if($_GET['search'] == 'all'){ echo 'active';} ?>">
                        <span class="badge fs-12 badge-info mr-1"><?php echo count_kyc('all') ?></span>
                        <span class="text-dark fw-500"><?php echo trans('all') ?></span>
                      </a>
                    </nav>
                  </div>

                  <div class="col-md-3">
                    <form method="get" class="validate-form" action="<?php echo base_url('admin/verification/kyc')?>" role="form" novalidate>
                        <div>
                          <div class="input-group">
                            <input type="text" class="form-control" placeholder="search by mentors name or email" aria-label="search by mentors name or email" aria-describedby="basic-addon2" name="name" value="<?php if(!empty($_GET['name'])){echo html_escape($_GET['name']);} ?>">
                            <div class="input-group-append">
                              <button class="btn btn-primary" type="submit"><?php echo trans('search') ?></button>
                            </div>
                          </div>
                        </div>
                    </form>
                  </div>
                </div>

                








                
                <div class="card-body table-responsive p-0">
                  <?php if (empty($kycs)): ?>
                    <?php $this->load->view('admin/include/not-found') ?>
                  <?php else: ?>
                    <table class="table table-hover m-0">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th><?php echo trans('mentor') ?></th>
                          <th><?php echo trans('document') ?></th>
                          <th><?php echo trans('document-photo') ?></th>
                          <th><?php echo trans('status') ?></th>
                          <th><?php echo trans('action') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                          <?php $i=1; foreach ($kycs as $kyc): ?>

                              <?php 
                                $user = get_by_id($kyc->user_id, 'users');
                                if ($kyc->document_type == 'nid') {
                                  $text = trans('national-id');
                                }elseif($kyc->document_type == 'dlicense'){
                                  $text = trans('driving-license');
                                }elseif($kyc->document_type == 'passport'){
                                  $text = trans('passport');
                                }
                              ?>

                              <tr id="row_<?php echo html_escape($kyc->id) ?>">
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
                                    
                                    <p class="mb-1"><b><?php echo trans('type') ?></b> : <?php echo html_escape($text) ?></p>
                                    
                                    <p class="mb-1 mt-1"><b><?php echo trans('document-number') ?></b> : <?php echo html_escape($kyc->doc_id_number) ?></p>
                                    
                                  </td>

                                  <td>

                                    <div class="d-flex justify-content-start">

                                      <?php if($kyc->document_type == 'nid' || $kyc->document_type == 'dlicense'): ?>
                                        <div class="mr-3">
                                          <a href="<?php echo base_url($kyc->front_side_doc) ?>" data-lightbox="roadtrip" data-title="Front side of <?php echo html_escape($text) ?>">
                                            <div class="kyc_img" style="background-image: url(<?php echo base_url($kyc->front_side_doc) ?>);"></div>
                                          </a>
                                        </div>

                                        <div class="mr-3">
                                          <a href="<?php echo base_url($kyc->back_side_doc) ?>" data-lightbox="roadtrip" data-title="Back side of <?php echo html_escape($text) ?>">
                                            <div class="kyc_img" style="background-image: url(<?php echo base_url($kyc->back_side_doc) ?>);"></div>
                                          </a>
                                        </div>
                                      <?php endif; ?>

                                      <div class="d-flex justify-content-start">

                                      <?php if($kyc->document_type == 'passport'): ?>
                                        <div class="mr-3">
                                          <a href="<?php echo base_url($kyc->passport) ?>" data-lightbox="roadtrip" data-title="Image of <?php echo html_escape($text) ?>">
                                            <div class="kyc_img" style="background-image: url(<?php echo base_url($kyc->passport) ?>);"></div>
                                          </a>
                                        </div>
                                      <?php endif; ?>

                                      <div class="mr-3">
                                        <a href="<?php echo base_url($kyc->selfiee_with_doc) ?>" data-lightbox="roadtrip" data-title="Selfie With <?php echo html_escape($text) ?>">
                                          <div class="kyc_img" style="background-image: url(<?php echo base_url($kyc->selfiee_with_doc) ?>);"></div>
                                        </a>
                                      </div>
                                    </div>
                                  </td>

                                   <td>
                                    <?php if ($kyc->status == 0): ?>
                                        <span class="badge-custom badge-warning-soft"><i class="fas fa-check-circle"></i> <?php echo trans('pending') ?></span>
                                    <?php elseif($kyc->status == 1): ?>
                                      <span class="badge-custom badge-success-soft"><i class="fas fa-times-circle"></i> <?php echo trans('approved') ?></span>
                                    <?php else: ?>
                                      <span class="badge-custom badge-danger-soft"><i class="fas fa-times-circle"></i> <?php echo trans('rejected') ?></span>
                                      <?php if($kyc->is_preview != 0): ?>
                                        <p class="badge-custom badge-info-soft mt-2"><?php echo trans('resubmitted-at') ?> <?php echo my_date_show_time($kyc->resub_date) ?></p>
                                      <?php endif; ?>
                                    <?php endif ?>
                                  </td>

                                  <td class="actions" width="12%">
                                      <div class="btn-group">
                                          <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                          </button>
                                          <div class="dropdown-menu dropdown-menu-right" role="menu" >

                                            
                                            <a href="<?php echo base_url('admin/verification/status_action/1/'.html_escape($kyc->id));?>" class="dropdown-item"><i class="bi bi-check-circle-fill mr-1"></i>  <?php echo trans('approve') ?></a>
                                            
                                            <?php if ($kyc->status == 0): ?>
                                              <a data-toggle="modal" href="#rejectModal_<?php echo html_escape($i) ?>" class="dropdown-item"><i class="bi bi-x-circle mr-1"></i>  <?php echo trans('reject') ?></a>
                                            <?php endif ?>
                                            
                                            <a data-val="User" data-id="<?php echo html_escape($kyc->id); ?>" href="<?php echo base_url('admin/verification/delete/'.html_escape($kyc->id));?>" class="dropdown-item delete_item"><i class="lni lni-trash-can mr-1"></i>  <?php echo trans('delete') ?></a>
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







<?php $b=1; foreach ($kycs as $kyc): ?>
<div class="modal fade" id="rejectModal_<?php echo html_escape($b) ?>">
  <div class="modal-dialog">
    <div class="modal-content">

      <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/verification/reject_reason')?>" role="form" novalidate>
            
            <div class="modal-body">
              <div class="form-group">
                <label><?php echo trans('reject-reason') ?></label>
                <textarea class="form-control" name="reject_reason" rows="4"></textarea>
              </div>
            </div>

            <div class="modal-footer justify-content-start">
              <input type="hidden" name="kyc" value="<?php echo html_escape($kyc->id) ?>">
              <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">
              <button type="submit" class="btn btn-primary"><?php echo trans('submit') ?></button>
            </div>
      </form>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<?php $b++; endforeach; ?>





