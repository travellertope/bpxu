<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-3 pt-2">
            <div class="card-body box-profile pt-4 mt-5 p-0">

              <?php if(!empty($mentor->thumb)): ?>
                <div class="avatar-md  mx-auto" style="background-image: url(<?php echo base_url($mentor->thumb) ?>);"></div>
              <?php else: ?>
                <div class="avatar-md  mx-auto" style="background-image: url(<?php echo base_url('assets/images/no-photo.png') ?>);"></div>
              <?php endif; ?>

              <div class="text-center">
                <span class="profile-username font-weight-bold mb-1"><?php echo html_escape($mentor->name) ?></span>

                <?php $code = get_by_id($mentor->country, 'country')->code; ?>
                <span data-tooltip="<?php echo get_by_id($mentor->country, 'country')->name; ?>" class="text-dark mb-0 font-weight-bold"><img class="flag-img-mentee ml-1" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>"></span>
              </div>

              <p class="text-muted text-center mb-1"><?php echo html_escape($mentor->email) ?></p>
              <p class="text-muted text-center mb-1"><?php echo html_escape($mentor->phone) ?></p>
              <?php if(!empty($mentor->created_at)): ?>
              <p class="text-muted text-center strong"><?php echo trans('joined') ?>: <?php echo get_time_ago($mentor->created_at) ?></p>
              <?php endif; ?>

              <ul class="list-group list-group-unbordered pt-3">
                <?php if(!empty($sessions)): ?>
                  <li class="list-group-item pl-3 pr-3 text-dark">
                    <span class="font-weight-bold fs-12"><?php echo trans('total-sessions') ?></span> <span class="float-right badge badge-secondary-soft"><?php echo count($sessions); ?></span>
                  </li>
                <?php endif; ?>

                <?php if(!empty($mentor->company)): ?>
                <li class="list-group-item pl-3 pr-3 text-dark">
                  <span class="font-weight-bold fs-12"><?php echo trans('company') ?></span> <span class="float-right badge badge-info"><?php echo html_escape($mentor->company); ?></span>
                </li>
                <?php endif; ?>

                <?php if(!empty($mentor->level)): ?>
                  <li class="list-group-item pl-3 pr-3 text-dark">
                    <span class="font-weight-bold fs-12"><?php echo trans('mentorship-level') ?></span> <span class="float-right badge badge-success"><?php echo html_escape($mentor->level); ?></span>
                  </li>
                <?php endif; ?>

                <?php if(!empty($mentor->language)): ?>
                  <li class="list-group-item pl-3 pr-3 text-dark">
                    <?php $languages = explode(",", $mentor->language);  ?>
                    <span class="font-weight-bold fs-12"><?php echo trans('language') ?></span>
                    <?php foreach ($languages as $language): ?> 
                      <span class="float-right badge badge-warning-soft mr-1"><?php    echo html_escape($language) ?>
                        
                      </span>
                    <?php endforeach ?>
                  </li>
                <?php endif; ?>
              </ul>
            </div>
          </div>

          <div class="col-md-9">
            <?php if (!empty($sessions)): ?>
              <div class="card pl-3">
                <div class="card-header">
                  <h5 class="card-title mb-0"><?php echo trans('mentor-sessions') ?></h5>

                  <div class="card-tools pull-right"><a class="pull-right btn btn-secondary btn-sm" href="<?php echo base_url('admin/users') ?>"><i class="fas fa-angle-left"></i> Back</a></div>
                </div>
                
                <div class="card-body table-responsive p-0">
                  <table class="table table-hover table-valign-middle <?php if(count($sessions) > 10){echo "datatable";} ?>">
                    <thead>
                    <tr>
                      <th>#</th>
                      <th><?php echo trans('session') ?></th>
                      <th><?php echo trans('total-booking') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php $i=1; foreach ($sessions as $session): ?>
                        <tr>
                          <td><?= $i; ?></td>
                          <td>
                            <?php $rating = check_session_rating($session->id); ?>
                              <?php if(!empty($rating)): ?>
                                <?php for($i = 1; $i <= 5; $i++):?>
                                  <?php 
                                  if($i > $rating->rating){
                                    $star = 'far fa-star';
                                  }else{
                                    $star = 'fas fa-star';
                                  }
                                  ?>
                                  <i class="<?php echo html_escape($star);?> text-warning-alt fs-13"></i> 
                                <?php endfor;?>
                              <?php endif; ?>
                            <p class="mt-0 mb-0">
                              <b><?php echo html_escape($session->name )?></b>
                            </p>
                      
                            <p class="mb-0 mt-0">
                              <span><i class="bi bi-clock text-muted"></i> <?php echo html_escape($session->duration) ?> <?php echo trans('minutes') ?></span>
                              <span class="ml-2"><?php echo settings()->currency_symbol ?> <?php echo html_escape($session->price) ?></span>
                            </p> 
                          </td>
                          <td>
                            <p class="badge badge-primary pl-3 pr-3"><b><?php echo  count_session_booking($session->id) ?></b></p>
                          </td>
                          
                        </tr>
                      <?php $i++; endforeach ?>
                    </tbody>
                  </table>
                </div>
              </div>
            <?php else: ?>
              <div class="card mt-5">
                <div class="card-body mt-2 text-center p-5 pt-4">
                    <p><?php echo trans('no-data-found') ?></p>
                </div>
              </div>
            <?php endif ?>
          </div>

        </div>
    </div>
  </div>
</div>