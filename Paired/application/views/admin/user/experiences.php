<div class="content-wrapper">

<!-- Content Header (Page header) -->
<?php $this->load->view('admin/include/breadcrumb'); ?>

<!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-8">
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
                  <a href="<?php echo base_url('admin/experience') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php else: ?>
                  <?php $required = 'required'; ?>
                  <a href="#" class="text-right btn btn-secondary btn-sm cancel_btn"><i class="fa lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php endif; ?>
              </div>
            </div>

            <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/experience/add')?>" role="form" novalidate>
              <div class="card-body">
                <div class="form-group">
                  <label><?php echo trans('icon') ?></label>
                  <input type="text" class="form-control iconpicker" name="icon" value="<?php if(isset($experience->icon)) {echo html_escape($experience->icon);} ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('title') ?> <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" name="title" value="<?php if(isset($experience->title)) {echo html_escape($experience->title);} ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('company') ?> <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" name="company" value="<?php if(isset($experience->company)) {echo html_escape($experience->company);} ?>">
                </div>

                <div class="row">
                  <div class="col-md-8">
                    <div class="form-group">
                      <label><?php echo trans('start-date') ?></label>
                      <input type="text" class="form-control datepicker" name="start_date" value="<?php if(isset($experience->start_date)) {echo html_escape($experience->start_date);} ?>">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group mt-5">
                      <span><?php echo trans('to') ?> </span>
                        <input type="checkbox" id="is_present" name="is_present" value="1" <?php if(isset($experience->is_present) && $experience->is_present == 1) {echo 'checked';} ?>>
                        <label for=""><?php echo trans('present') ?></label><br>
                    </div>
                  </div>
                </div>

                <div class="form-group end_date <?php if(isset($experience->is_present) && $experience->is_present == 1) {echo 'hide';}else{echo 'show';} ?>">
                    <label><?php echo trans('end-date') ?></label>
                    <input type="text" class="form-control datepicker"  name="end_date" value="<?php if(isset($experience->end_date)) {echo html_escape($experience->end_date);} ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('contribution') ?></label>
                  <textarea class="form-control" name="contribution"><?php if(isset($experience->contribution)) {echo html_escape($experience->contribution);} ?></textarea>
                </div>

                <div class="form-group clearfix">
                  <label><?php echo trans('status') ?></label><br>

                  <div class="icheck-primary radio radio-inline d-inline mr-4 mt-2">
                    <input type="radio" id="radioPrimary1" value="1" name="status" <?php if(isset($experience->status) && $experience->status == 1){echo "checked";} ?> <?php if (isset($page_title) && $page_title != "Edit"){echo "checked";} ?>>
                    <label for="radioPrimary1"> <?php echo trans('show') ?>
                    </label>
                  </div>

                  <div class="icheck-primary radio radio-inline d-inline">
                    <input type="radio" id="radioPrimary2" value="2" name="status" <?php if(isset($experience->status) && $experience->status == 2){echo "checked";} ?>>
                    <label for="radioPrimary2"> <?php echo trans('hide') ?>
                    </label>
                  </div>
                </div>
              </div>

              <div class="card-footer">
                <input type="hidden" name="id" value="<?php if(isset($experience->id)){echo html_escape($experience->id);} ?>">
                <!-- csrf token -->
                <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">

                <?php if (isset($page_title) && $page_title == "Edit"): ?>
                  <button type="submit" class="btn btn-primary pull-left"><?php echo trans('save-changes') ?></button>
                <?php else: ?>
                  <button type="submit" class="btn btn-primary pull-left"> <?php echo trans('save') ?></button>
                <?php endif; ?>
              </div>
            </form>
          </div>

          <?php if (isset($page_title) && $page_title != "Edit"): ?>
            <div class="card list_area">
              <div class="card-header">
                <?php if (isset($page_title) && $page_title == "Edit"): ?>
                  <h3 class="card-title pt-2"><?php echo trans('edit') ?> <a href="<?php echo base_url('admin/experience') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a></h3>
                <?php else: ?>
                  <h3 class="card-title pt-2"><?php echo trans('experiences') ?></h3>
                <?php endif; ?>

                <div class="card-tools pull-right">
                   <a href="#" class="pull-right btn btn-secondary btn-sm add_btn"><i class="fas fa-plus"></i> <?php echo trans('create-new') ?></a>
                  </div>
              </div>

              <?php if(!empty($experiences)): ?>
                <div class="card-body table-responsive p-0">
                  <table class="table table-hover text-nowrap <?php if(count($experiences) > 10){echo "datatable";} ?>">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th><?php echo trans('title') ?></th>
                        <th><?php echo trans('company') ?></th>
                        <th><?php echo trans('duration') ?></th>
                        <th><?php echo trans('status') ?></th>
                        <th class="text-right"><?php echo trans('action') ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i=1; foreach ($experiences as $experience): ?>
                        <tr id="row_<?php echo ($experience->id); ?>">
                          <td><?= $i; ?></td>

                          <td><?php echo html_escape($experience->title); ?></td>

                          <td><?php echo html_escape($experience->company); ?></td>
                          
                          <td>
                            <?php 
                              if($experience->is_present == 1){
                                $start = my_date_show($experience->start_date);
                                $end = 'Present';
                              }else{
                                $start = my_date_show($experience->start_date);
                                $end = my_date_show($experience->end_date);
                              }

                             ?>
                             
                             <?php echo html_escape($start); ?> To <?php echo html_escape($end); ?> 
                              
                          </td>
                           
                          <td>
                            <?php if ($experience->status == 1): ?>
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
                                <a href="<?php echo base_url('admin/experience/edit/'.html_escape($experience->id))?>" class="dropdown-item"><i class="bi bi-pencil-square mr-1"></i><?php echo trans('edit') ?></a>
                                <a data-val="Category" data-id="<?php echo html_escape($experience->id); ?>" href="<?php echo base_url('admin/experience/delete/'.html_escape($experience->id));?>" class="dropdown-item delete_item"><i class="bi bi-trash3 mr-1"></i><?php echo trans('delete') ?></a>
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
    </div>
  </div>
</div>
