<div class="content-wrapper">
<!-- Content Header (Page header) -->
<?php $this->load->view('admin/include/breadcrumb'); ?>
<!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <?php if (settings()->enable_kyc == 1 && user()->kyc_verified == 0): ?>
        <div class="row mt-5">
          <div class="col-lg-12">
            <div class="alert alert-warning-soft" data-aos="fade-up">
                <i class="bi bi-info-circle-fill"></i> <?php echo trans('kyc-verify-alert-user') ?> <a class="text-muted" href="<?php echo base_url('admin/verification'); ?>">Submit Documents <i class="bi bi-arrow-right"></i></a>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="row">
          <div class="col-lg-12">
            <div class="card add_area <?php if(isset($page_title) && $page_title == "Edit"){echo "d-block";}else{echo "hide";} ?>">
              <div class="card-header">
                <?php if (isset($page_title) && $page_title == "Edit"): ?>
                  <h3 class="card-title pt-2"><?php echo trans('edit') ?></h3>
                <?php else: ?>
                  <h3 class="card-title pt-2"><?php echo trans('create-new') ?> </h3>
                <?php endif; ?>

                <div class="card-tools pull-right">
                  <?php if (isset($page_title) && $page_title == "Edit"): ?>
                    <?php $required = ''; ?>
                    <a href="<?php echo base_url('admin/sessions') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                  <?php else: ?>
                    <?php $required = 'required'; ?>
                    <a href="#" class="text-right btn btn-secondary btn-sm cancel_btn"><i class="fa lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                  <?php endif; ?>
                </div>
              </div>

              <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/sessions/add')?>" role="form" novalidate>
                <div class="row">
                  
                  <div class="col-md-6">
                    <div class="card-body">

                      <div class="form-group">

                        <?php if($page_title == 'Edit'): ?>
                          <div class="mih-100">
                            <img width="100px" src="<?php echo base_url($session->thumb); ?>">
                          </div>
                        <?php endif; ?>

                        <div class="form-group">
                          <div class="custom-file">
                          <input type="file" class="custom-file-input" name="photo" id="customFile">
                          <label class="custom-file-label" for="customFile"><?php echo trans('upload-image') ?></label>
                          </div>
                        </div>
                      </div>

                      
                      <div class="form-group">
                        <label><?php echo trans('session-name') ?><span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" required="required" name="name" value="<?php if(isset($session->name)){echo html_escape($session->name);} ?>">
                      </div>
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="form-group">
                            <label><?php echo trans('duration') ?><span class="text-danger"> *</span></label>
                            <div class="input-group mb-3">
                              <input type="number" class="form-control" name="duration" value="<?php if(isset($session->duration)){echo html_escape($session->duration);} ?>" required="required">
                              <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2"><?php echo trans('minutes') ?></span>
                              </div>
                            </div>
                          </div>
                        </div>
                        <!-- Comment to hide price field from Booking Sessions
                        <div class="col-md-6">
                          <div class="form-group">
                            <label><?php echo trans('price') ?><span class="text-danger"> *</span></label>
                            <div class="input-group mb-0">
                              <input type="text" class="form-control" name="price" value="<?php if(isset($session->price)){echo html_escape($session->price);} ?>" required="required">
                              <div class="input-group-append">
                                <span class="input-group-text" id="basic-addon2"><?php echo settings()->currency_symbol ?></span>
                              </div>
                            </div>
                            <p class="mt-1 mb-3 small text-danger">* <?php echo trans('set-price-0-for-free-session') ?></p>
                          </div>
                        </div> -->
                      </div>
                      
                      <div class="row d-none">
                        <div class="col-md-6">
                          <div class="form-group">
                            <label><?php echo trans('number-of-slot') ?><span class="text-danger"> *</span></label>
                            <input type="number" class="form-control" name="total_slot" value="<?php if(isset($session->total_slot)){echo html_escape($session->total_slot);} ?>">
                          </div>
                        </div>

                        <div class="col-md-6 ">
                          <div class="form-group">
                            <label><?php echo trans('slot-for') ?></label>
                            <select class="form-control" name="slot_for" >
                              <option value=""><?php echo trans('select') ?></option>
                              <option value="1" <?php if(isset($session->slot_for) && $session->slot_for == 1) {echo 'selected';} ?>><?php echo trans('daily') ?></option>
                              <option value="2" <?php if(isset($session->slot_for) && $session->slot_for == 2) {echo 'selected';} ?>><?php echo trans('weekly') ?></option>
                              <option value="3" <?php if(isset($session->slot_for) && $session->slot_for == 3) {echo 'selected';} ?>><?php echo trans('monthly') ?></option>
                            </select>
                          </div>
                        </div>
                      </div>

                      <div class="form-group">
                        <label><?php echo trans('details') ?><span class="text-danger"> *</span></label>
                        <textarea class="form-control" rows="6" name="details" required="required"><?php if(isset($session->details)) {echo html_escape($session->details);} ?></textarea>
                      </div>

                      <div class="form-group">
                        <label><?php echo trans('session-type') ?> <span class="text-danger"> *</span></label>
                        <select class="form-control is_reccuring" name="type" required>
                          <option value=""><?php echo trans('select') ?></option>
                          <option value="1" <?php if(isset($session->type) && $session->type == 1) {echo 'selected';} ?>>One-Off Session</option>
                          <option value="2" <?php if(isset($session->type) && $session->type == 2) {echo 'selected';} ?>><?php echo trans('recurring-sessions') ?></option>
                        </select>
                      </div>

                      <div class="recurring_session <?php if(isset($page_title) && $page_title != 'Edit'){echo 'hide';} ?> <?php if(isset($session->type) && $session->type == 1){echo 'hide';} ?>">
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group">
                              <label><?php echo trans('number-of-sessions') ?></label>
                              <select class="form-control" name="session_number" >
                                <option value=""><?php echo trans('select') ?></option>
                                <?php for ($i=1; $i <=20; $i++): ?> 
                                  <option value="<?php echo html_escape($i);  ?>" <?php if(isset($session->session_number) && $session->session_number == $i) {echo 'selected';} ?>><?php echo html_escape($i); ?></option>
                                <?php endfor; ?>
                              </select>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <label><?php echo trans('repeat-in') ?></label>
                              <select class="form-control" name="session_repeat" >
                                <option value=""><?php echo trans('select') ?></option>
                                <option value="7" <?php if(isset($session->session_repeat) && $session->session_repeat == 7) {echo 'selected';} ?>><?php echo trans('repeats-weekly') ?></option>
                                <option value="30" <?php if(isset($session->session_repeat) && $session->session_repeat == 30) {echo 'selected';} ?>><?php echo trans('repeats-monthly') ?></option>
                              </select>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- Comment to hide session topic field from Booking Sessions
                      <div class="form-group session_type <?php if($session->allow_session == 1){echo 'hide';} ?>">
                        <label><?php echo trans('session-topic') ?></label>
                        <select class="form-control" name="skill" >
                          <option value=""><?php echo trans('select') ?></option>
                          <?php foreach($skills as $skill): ?>
                            <option value="<?php echo html_escape($skill); ?>" <?php if(isset($session->skill)&& $session->skill==$skill){echo 'selected';} ?>>
                              <?php echo html_escape($skill); ?>
                            </option>
                          <?php endforeach; ?>
                        </select>
                      </div> -->

                      <div class="form-group hide">
                        <input type="checkbox" id="allow_session" name="allow_session" value="1" <?php if(isset($session->allow_session) && $session->allow_session == 1) {echo 'checked';} ?>>
                        <label><?php echo trans('allow-mentee-to-chose-topic') ?></label>
                      </div>

                      <div class="form-group hide">
                        <div class="custom-control custom-switch prefrence-item ml-10">
                          <input type="checkbox" name="is_public" class="custom-control-input" value="1" id="switch-88" <?php if($session->is_public == 1){echo "checked";} ?>>
                          <label class="custom-control-label" for="switch-88"><?php echo trans('show-session-on-your-public-profile') ?></label>
                          <p class="text-muted"><small><?php echo trans('allow-session-text') ?></small></p>
                        </div>
                      </div>

                      
                      <div class="form-group">
                        <label><?php echo trans('intro-video-url') ?></label>
                        <input type="text" class="form-control" name="intro_video" value="<?php if(isset($session->intro_video)){echo html_escape($session->intro_video);} ?>">
                      </div>


                      <div class="form-group clearfix">
                        <label><?php echo trans('status') ?></label><br>
                        <div class="icheck-primary radio radio-inline d-inline mr-4 mt-2">
                          <input type="radio" id="radioPrimary1" value="1" name="status" <?php if(isset($session->status) && $session->status == 1){echo "checked";} ?> <?php if (isset($page_title) && $page_title != "Edit"){echo "checked";} ?>>
                          <label for="radioPrimary1"> <?php echo trans('show') ?>
                          </label>
                        </div>

                        <div class="icheck-primary radio radio-inline d-inline">
                          <input type="radio" id="radioPrimary2" value="2" name="status" <?php if(isset($session->status) && $session->status == 2){echo "checked";} ?>>
                          <label for="radioPrimary2"> <?php echo trans('hide') ?>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-6 pl-4">
                    <h6><?php echo trans('group-booking') ?></h6>
                    <div class="card-body mb-4 py-0">
                      <div class="form-group">
                        <div class="custom-control custom-switch prefrence-item ml-10">
                          <input type="checkbox" name="enable_group_booking" class="custom-control-input enable_group_booking" value="1" id="switch-89" <?php if($session->enable_group_booking == 1){echo "checked";} ?>>
                          <label class="custom-control-label  mb-3" for="switch-89"><?php echo trans('enable-group-booking') ?></label>
                        </div>
                      </div>

                      <div class="form-group <?php if(!empty($page_title) && $page_title != 'Edit'){echo 'hide';} ?> group_booking_slot">
                        <label><?php echo trans('slot-for-group-booking') ?></label>
                        <input type="number" class="form-control" name="group_booking_slot" value="<?php if(isset($session->group_booking_slot)){echo html_escape($session->group_booking_slot);} ?>">
                        <p class="small mt-1"><i class="bi bi-info-circle text-danger"></i> <?php echo trans('individuals-per-slot-booking') ?></p>
                      </div>
                    </div>


                    <h6 class="mt-3"><?php echo trans('set-schedule') ?></h6>
                    <div  class="card-body">

                      <p><?php echo trans('define-your-availability') ?> <b><?php echo get_by_id(user()->time_zone , 'time_zone')->name  ?></b></p>

                      <div class="form-group">
                        <select class="form-control is_default" name="is_default" >
                          <option><?php echo trans('select') ?></option>
                          <option class="default_hour_option" value="1" <?php if(isset($session->is_default) && $session->is_default == 1){echo 'selected';} ?> <?php if(isset($session->enable_group_booking) && $session->enable_group_booking == 1){echo 'disabled';} ?> ><?php echo trans('defult-hours') ?></option>
                          <option value="2" <?php if(isset($session->is_default) && $session->is_default == 2){echo 'selected';} ?>><?php echo trans('set-custom-hours') ?></option>
                        </select>
                      </div>

                      <?php foreach ($working_days as $working_day): ?>
                        <div class="default_hour_area <?php if(!empty($session->is_default) && $session->is_default != 1){echo 'hide';}else{'show';} ?> <?php if($page_title!='Edit'){echo 'hide';} ?>">
                          <div class="row">
                            <div class="col-md-3 mt-3">
                              <p class="text-left"><?php echo get_days($working_day->day); ?></p>
                            </div>
                            
                            <?php 
                                $times = get_time_by_default_days($working_day->day, user()->id);
                             ?>

                            <div class="col-md-9 mt-3">
                              <?php foreach ($times as $time):?>
                                <span class="default_work_hour">
                                  <i class="bi bi-clock"></i> <?php echo html_escape($time->start); ?> - <?php echo html_escape($time->end); ?>
                                </span>
                              <?php endforeach ?>
                            </div>
                          </div>
                        </div>
                      <?php endforeach ?>



                      <?php $days = get_days();?>
                      <div class="row main_item ml-1 custom_hour_area <?php if(!empty($session->is_default) && $session->is_default != 2){echo 'hide';}else{'show';} ?> <?php if($page_title!='Edit'){echo 'hide';} ?>">
                        <?php $i=1; foreach ($days as $day): ?>
                          
                          <?php $checks=0; ?>
                          <?php foreach ($my_days as $asnday): ?>
                            <?php if ($asnday['day'] == $i) {
                              $check = 'checked';
                              $checks = $asnday['day'];
                              break;
                            } else {
                              $check = '';
                              $checks = 0;
                            }
                            ?>
                          <?php endforeach ?>

                          <div class="item-rows w-100 mb-20">
                            <div class="form-group col-md-12 mb-3">
                              <div class="custom-control custom-switch pt-10">
                                <input type="checkbox" value="<?= $i; ?>" name="day_<?= $i-1; ?>" class="custom-control-input day_option" id="switch-<?= $i;?>" <?php if(!empty($check)){echo html_escape($check);} ?>>
                                <label class="custom-control-label" for="switch-<?= $i;?>"><?php echo html_escape($day) ?></label>
                              </div>
                            </div>

                            <?php $user_id = user()->id; ?>

                            <?php foreach (get_time_by_days($i, $session->id, $user_id) as $time): ?>
                            <div class="hour-item col-md-12 mb-3 hideable_<?= $i; ?>" id="row_<?= $time->id ?>">
                              <div class="row <?php if($page_title!='Edit'){echo 'hide';} ?>">
                                <div class="col-sm-5 pr-0">
                                    <div class="input-group">
                                      <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                      </div>
                                      <input type="text" class="form-control timepicker" name="start_time_<?= $i-1; ?>[]" value="<?php echo html_escape($time->start); ?>" placeholder="<?php echo trans('start-time') ?>" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-sm-5 mb-2">
                                  <div class="input-group">
                                      <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                      </div>
                                      <input type="text" class="form-control timepicker" name="end_time_<?= $i-1; ?>[]" value="<?php echo html_escape($time->end); ?>" placeholder="<?php echo trans('end-time') ?>" autocomplete="off">
                                  </div>
                                </div>

                                <div class="col-sm-2 mb-2">
                                  <a data-id="<?= $time->id ?>" href="<?php echo base_url('admin/settings/delete_time/'.$time->id) ?>" class="del_time_row delete_item text-danger"><i class="bi bi-trash3"></i></a>
                                </div>
                              </div>
                            </div>
                            <?php endforeach ?>

                            <div class="houritem_<?= $i-1; ?> col-md-12"></div>

                            <div class="form-group col-sm-12 mt-2 hideable_<?= $i; ?> <?php if($check == 'checked'){echo 'show';}else{echo "hide";} ?>">
                              <a href="#" data-id="<?= $i-1; ?>" class="add_time_row"><i class="fa fa-plus-circle"></i> <?php echo trans('add-new-time') ?></a>
                            </div>

                            <div class="day_highliter"></div>
                            <div class="day_divider"></div>
                          </div>
                        <?php $i++; endforeach ?>
                      </div>
                    </div>


                    <div class="card-footer">
                      <input type="hidden" name="id" value="<?php if(isset($session->id)){echo html_escape($session->id);} ?>">
                      <!-- csrf token -->
                      <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">

                      <?php if (isset($page_title) && $page_title == "Edit"): ?>
                        <button type="submit" class="btn btn-primary pull-left btn-block"><?php echo trans('save-changes') ?></button>
                      <?php else: ?>
                        <button type="submit" class="btn btn-primary pull-left btn-block"> <?php echo trans('save') ?></button>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>

                
              </form>
            </div>

            <?php if (isset($page_title) && $page_title != "Edit"): ?>
            <div class="card list_area">
              <div class="card-header">
                <?php if (isset($page_title) && $page_title == "Edit"): ?>
                  <h3 class="card-title pt-2"><?php echo trans('edit') ?> <a href="<?php echo base_url('admin/sessions') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a></h3>
                <?php else: ?>
                  <h3 class="card-title pt-2"><?php echo trans('sessions') ?></h3>
                <?php endif; ?>

                <div class="card-tools pull-right">
                  <a href="#" class="pull-right btn btn-secondary btn-sm add_btn"><i class="fas fa-plus"></i> <?php echo trans('create-new') ?></a>
                </div>
              </div>

              <?php if (!empty($sessions)): ?>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap <?php if(count($sessions) > 10){echo "datatable";} ?>">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th><?php echo trans('image') ?></th>
                      <th><?php echo trans('name') ?></th>
                      <th><?php echo trans('session-topic') ?></th>
                      <th><?php echo trans('type') ?></th>
                      <th><?php echo trans('') ?></th>
                      <th><?php echo trans('duration') ?></th>
                      <th><?php echo trans('price') ?></th>
                      <th><?php echo trans('status') ?></th>
                      <th class="text-right"><?php echo trans('action') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i=1; foreach ($sessions as $session): ?>
                      <?php $mentor = $this->admin_model->get_by_id($session->user_id, 'users'); ?>
                      <tr id="row_<?php echo ($session->id); ?>">
                        <td><?= $i; ?></td>
                        <td>
                          <?php if (!empty($session->thumb)): ?>
                            <img class="feature-img" src="<?php echo base_url($session->thumb) ?>">
                          <?php endif ?>
                        </td>
                        <td>
                         <p class="mb-0 fw-500"> <?php echo html_escape($session->name); ?></p>
                          
                          <!-- <?php $rating = get_ratings_info($session->id);?>
                            <?php if (isset($rating->total_point) && $rating->total_point != 0): ?>
                            <?php $average = number_format($rating->total_point/$rating->total_user, 1) ?>
                            <?php endif ?>

                            <?php if (!empty($rating->total_point)): ?>
                              <?php for($u = 1; $u <= 5; $u++):?>
                                <?php 
                                  if ( round($average - .25) >= $u) {
                                        $star = "fas fa-star";
                                    } elseif (round($average + .25) >= $u) {
                                        $star = "fas fa-star-half-alt";
                                    } else {
                                        $star = "far fa-star";
                                    }
                                ?>
                                <i class="<?php echo html_escape($star);?> text-warning-alt fs-12"></i> 
                              <?php endfor;?>
                              <br><?php echo html_escape($average); ?> <small>(<?php echo get_total_rating_user($session->id) ?> <?php echo trans('reviews') ?>)</small>
                          <?php endif ?>   -->
                        </td>
                        <td>
                          <?php if($session->allow_session != 1): ?>
                            <?php echo html_escape($session->skill); ?>
                          <?php else: ?>
                            <p class="mt-3"><?php echo trans('not-available') ?></p>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if($session->type==2): ?>
                            <p class="mb-0 text-left"><b><?php echo trans('recurring') ?></b></p>
                            <p class="mb-0 text-left"><?php echo trans('number-of-session') ?> : <?php echo html_escape($session->session_number) ?></p>
                            <?php if($session->session_repeat==7): ?>
                              <p class="mb-0 text-left"><?php echo trans('session-repeats-weekly') ?></p>
                            <?php else: ?>
                              <p class="mb-0 text-left"><?php echo trans('session-repeats-monthly') ?></p>
                            <?php endif; ?>
                          <?php else: ?>
                            <p class="mt-3 text-left"><?php echo trans('one-time-session') ?></p>
                          <?php endif; ?>
                        </td>

                        <td>
                          <?php if($session->enable_group_booking == 1): ?>
                            <span class="badge badge-primary"><i class="bi bi-people"></i> <?php echo trans('group-booking') ?></span>
                          <?php else: ?>
                            <span class="badge badge-primary"><i class="bi bi-person"></i> <?php echo trans('individual-booking') ?></span>
                          <?php endif; ?>

                        </td>
                        
                        <td><i class="bi bi-clock"></i> <?php echo html_escape($session->duration); ?> <?php echo trans('minutes') ?></td>
                        <td>
                          <?php if($session->price !=0): ?>
                            <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?>
                            <?php echo number_format($session->price, settings()->num_format) ?>
                            <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                          <?php else: ?>
                            <p class="badge badge-info"><?php echo trans('free') ?></p>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($session->status == 1): ?>
                            <span class="badge badge-success"><i class="lnib lni-checkmark"></i> <?php echo trans('active') ?></span>
                          <?php else: ?>
                            <span class="badge badge-secondary"><i class="fas fa-eye-slash"></i> <?php echo trans('hidden') ?></span>
                          <?php endif ?>
                        </td>
                        <td class="actions text-right">
                          <div class="btn-group">
                            <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                              <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu" >
                              <a data-toggle="modal" href="#reviewModal_<?php echo html_escape($i) ?>" class="dropdown-item"><i class="bi bi-star mr-1"></i><?php echo trans('reviews') ?></a>
                              <a href="<?php echo base_url('admin/sessions/edit/'.html_escape($session->id))?>" class="dropdown-item"><i class="bi bi-pencil-square mr-1"></i><?php echo trans('edit') ?></a>
                              <a data-val="Category" data-id="<?php echo html_escape($session->id); ?>" href="<?php echo base_url('admin/sessions/delete/'.html_escape($session->id));?>" class="dropdown-item delete_item"><i class="bi bi-trash3 mr-1"></i><?php echo trans('delete') ?></a>
                            </div>
                          </div>
                        </td>
                      </tr>
                    <?php $i++; endforeach; ?>
                  </tbody>
                </table>
              </div>
              <?php else: ?>
                <?php $this->load->view('admin/include/not-found'); ?>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>



<?php if (!empty($sessions)): ?>
  <?php $j=1; foreach ($sessions as $session): ?>
    <div class="modal fade" id="reviewModal_<?= $j; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel"><?php echo trans('reviews') ?></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"><i class="bi bi-x"></i></span>
            </button>
          </div>
          <div class="modal-body">
            <div class="row">
                <?php  
                  $ratings = get_all_ratings_by_session($session->id);
                  $rating = get_ratings_info($session->id);
                  $report = get_single_ratings($session->id);
                ?>

                <?php if (empty($ratings)): ?>
                  <?php $average = 0 ?>
                <?php else: ?>
                  <?php $average = number_format($rating->total_point/$rating->total_user, 1) ?>
                <?php endif ?>

                <?php if ($average != 0): ?>
                  <div class="col-sm-4">
                    <div class="rating-block">
                      <h6><?php echo trans('average-rating') ?></h6>
                       <?php for($i = 1; $i <= 5; $i++):?>
                        <?php 
                          if ( round($average - .25) >= $i) {
                                $star = "fas fa-star";
                            } elseif (round($average + .25) >= $i) {
                                $star = "fas fa-star-half-alt";
                            } else {
                                $star = "far fa-star";
                            }
                        ?>
                        <i class="<?php echo html_escape($star);?> text-warning-alt"></i> 
                      <?php endfor;?>
                      <h5 class="bold"><?php echo html_escape($average); ?> <small>(<?php echo get_total_rating_user($session->id) ?> <?php echo trans('ratings') ?>)</small></h5>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <h6><?php echo trans('ratings-summary') ?></h6>
                    
                    <div class="d-flex justify-content-between">
                      <div class="pull-lefts w_10">
                        <div class="height_9"> <span class="fa fa-star text-warning-alt"> </span> 5</div>
                      </div>
                      <div class="pull-lefts w_65">
                        <div class="progress h-9-m-8">
                          <div class="progress-bar bg-success" role="progressbar" aria-valuenow="5" aria-valuemin="0" aria-valuemax="5" style="width: <?php echo html_escape($report->five/$report->total_user*100) ; ?>%">
                          <span class="sr-only"></span>
                          </div>
                        </div>
                      </div>
                      <div class="pull-rights w_15"><?php echo html_escape($report->five) ?></div>
                    </div>

                    <div class="d-flex justify-content-between">
                      <div class="pull-lefts w_10">
                        <div class="height_9"> <span class="fa fa-star text-warning-alt"></span> 4</div>
                      </div>
                      <div class="pull-lefts w_65">
                        <div class="progress h-9-m-8">
                          <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="4" aria-valuemin="0" aria-valuemax="5" style="width: <?php echo html_escape($report->four/$report->total_user*100) ; ?>%">
                          <span class="sr-only"></span>
                          </div>
                        </div>
                      </div>
                      <div class="pull-rights w_15"><?php echo html_escape($report->four) ?></div>
                    </div>

                    <div class="d-flex justify-content-between">
                      <div class="pull-lefts w_10">
                        <div class="height_9"> <span class="fa fa-star text-warning-alt"></span> 3</div>
                      </div>
                      <div class="pull-lefts w_65">
                        <div class="progress h-9-m-8">
                          <div class="progress-bar bg-secondary" role="progressbar" aria-valuenow="3" aria-valuemin="0" aria-valuemax="5" style="width: <?php echo html_escape($report->three/$report->total_user*100) ; ?>%">
                          <span class="sr-only"></span>
                          </div>
                        </div>
                      </div>
                      <div class="pull-rights w_15"><?php echo html_escape($report->three) ?></div>
                    </div>

                    <div class="d-flex justify-content-between">
                      <div class="pull-lefts w_10">
                        <div class="height_9"> <span class="fa fa-star text-warning-alt"></span> 2</div>
                      </div>
                      <div class="pull-lefts w_65">
                        <div class="progress h-9-m-8">
                          <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="5" style="width: <?php echo html_escape($report->two/$report->total_user*100); ?>%">
                          <span class="sr-only"></span>
                          </div>
                        </div>
                      </div>
                      <div class="pull-rights w_15"><?php echo html_escape($report->two) ?></div>
                    </div>

                    <div class="d-flex justify-content-between">
                      <div class="pull-lefts w_10">
                        <div class="height_9"> <span class="fa fa-star text-warning-alt"></span> 1</div>
                      </div>
                      <div class="pull-lefts w_65">
                        <div class="progress h-9-m-8">
                          <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="5" style="width: <?php echo html_escape($report->one/$report->total_user*100); ?>%">
                          <span class="sr-only"></span>
                          </div>
                        </div>
                      </div>
                      <div class="pull-rights w_15"><?php echo html_escape($report->one) ?></div>
                    </div>
                  </div>  

                <?php else: ?>
                  <div class="col-sm-12 text-center">
                    <?php echo trans('no-data-found') ?>
                  </div>  
                <?php endif ?>

              </div>      
              
              <div class="row">
                <div class="col-sm-12">
                  <hr/>
                  <div class="review-block">
                    <?php foreach ($ratings as $rating): ?>
                      <div class="row">
                        <div class="col-sm-2 text-left">
                          <?php if (empty($rating->mentee_thumb)): ?>
                            <?php $avatar = 'assets/front/img/avatar.png'; ?>
                          <?php else: ?>
                            <?php $avatar = $rating->mentee_thumb; ?>
                          <?php endif ?>
                          <div class="user-thumb" style="background-image:url(<?php echo base_url($avatar) ?>)"></div>
                          <div class="review-block-name mt-2 ml-1"><?php echo html_escape($rating->mentee_name) ?></div>
                        </div>
                        <div class="col-sm-10 pl-0">
                          <?php for($i = 1; $i <= 5; $i++):?>
                            <?php 
                            if($i > $rating->rating){
                              $star = 'far fa-star';
                            }else{
                              $star = 'fas fa-star';
                            }
                            ?>
                            <i class="<?php echo html_escape($star);?> text-warning-alt"></i> 
                          <?php endfor;?>
                          <div class="review-block-date small mt-2 text-muted"><i class="bi bi-calendar2"></i> <?php echo my_date_show($rating->created_at) ?></div>

                          <div class="review-block-description mt-1">"<?php echo html_escape($rating->feedback) ?>"</div>
                          
                        </div>
                      </div><hr/>
                    <?php endforeach ?>
                  </div>
                </div>
              </div>
          </div>
        </div>
      </div>
    </div>
  <?php $j++; endforeach; ?>
<?php endif; ?>
