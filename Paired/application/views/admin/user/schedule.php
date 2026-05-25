<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <?php $this->load->view('admin/include/breadcrumb'); ?>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          
          <?php $this->load->view('admin/user/include/settings_menu.php'); ?>

          <div class="col-lg-9 pl-3">
            <div class="card">
              <div class="box-header with-border">
                  <h3 class="box-title"><?php echo trans('set-schedule') ?></h3>
              </div>

              <?php $days = get_days();?>
              <form method="post" class="validate-form" action="<?php echo base_url('admin/settings/set')?>" role="form" enctype="multipart/form-data">
                <div class="card-body mt-0">
                  <div class="row main_item ml-3">
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
                          <div class="custom-control custom-switch  pt-10">
                            <input type="checkbox" value="<?= $i; ?>" name="day_<?= $i-1; ?>" class="custom-control-input day_option" id="switch-<?= $i;?>" <?php if(!empty($check)){echo html_escape($check);} ?>>
                            <label class="custom-control-label" for="switch-<?= $i;?>"><?php echo trans(strtolower($day)) ?></label>
                          </div>
                        </div>

                        <?php $user_id = $user->id; ?>

                        <?php foreach (get_time_by_default_days($i, $user_id) as $time): ?>
                        <div class="hour-item col-md-12 mb-3 hideable_<?= $i; ?>" id="row_<?= $time->id ?>">
                          <div class="row">
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
                  <!-- csrf token -->
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block pull-right mt-3"><i class="ficon flaticon-check"></i> <?php echo trans('update') ?></button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

