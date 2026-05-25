
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
                  <a href="<?php echo base_url('admin/education') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php else: ?>
                  <?php $required = 'required'; ?>
                  <a href="#" class="text-right btn btn-secondary btn-sm cancel_btn"><i class="fa lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php endif; ?>
              </div>
            </div>

            <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/education/add')?>" role="form" novalidate>
              <div class="card-body">
                <div class="form-group">
                  <label><?php echo trans('institute') ?><span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" placeholder="<?php echo  trans('eg-university-of-dalas') ?>" name="institute" value="<?php if(isset($education->institute)) {echo html_escape($education->institute);} ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('degree') ?><span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" placeholder="<?php echo trans('eg-bachelors-in-rchitect') ?>" name="degree" value="<?php if(isset($education->degree)) {echo html_escape($education->degree);} ?>">
                </div>

                <div class="mb-2"><?php echo trans('duration') ?></div>
                
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="text" class="form-control" placeholder="Eg: 2019" name="start_year" value="<?php if(isset($education->start_year)) {echo html_escape($education->start_year);} ?>">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="text" class="form-control"  name="end_year" placeholder="Eg: 2021" value="<?php if(isset($education->end_year)) {echo html_escape($education->end_year);} ?>">
                    </div>
                  </div>
                </div>

                <div class="form-group clearfix">
                  <label><?php echo trans('status') ?></label><br>
                  <div class="icheck-primary radio radio-inline d-inline mr-4 mt-2">
                    <input type="radio" id="radioPrimary1" value="1" name="status" <?php if(isset($education->status) && $education->status == 1){echo "checked";} ?> <?php if (isset($page_title) && $page_title != "Edit"){echo "checked";} ?>>
                    <label for="radioPrimary1"> <?php echo trans('show') ?>
                    </label>
                  </div>

                  <div class="icheck-primary radio radio-inline d-inline">
                    <input type="radio" id="radioPrimary2" value="2" name="status" <?php if(isset($education->status) && $education->status == 2){echo "checked";} ?>>
                    <label for="radioPrimary2"> <?php echo trans('hide') ?>
                    </label>
                  </div>
                </div>
              </div>

              <div class="card-footer">
                <input type="hidden" name="id" value="<?php if(isset($education->id)){echo html_escape($education->id);} ?>">
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
                <h3 class="card-title pt-2"><?php echo trans('edit') ?> <a href="<?php echo base_url('admin/education') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a></h3>
              <?php else: ?>
                <h3 class="card-title pt-2">Education</h3>
              <?php endif; ?>

              <div class="card-tools pull-right">
                 <a href="#" class="pull-right btn btn-secondary btn-sm add_btn"><i class="fas fa-plus"></i> <?php echo trans('create-new') ?></a>
              </div>
            </div>
            <?php if (!empty($educations)): ?>
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap <?php if(count($educations) > 10){echo "datatable";} ?>">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th><?php echo trans('degree') ?></th>
                      <th><?php echo trans('institution') ?></th>
                      <th><?php echo trans('duration') ?></th>
                      <th><?php echo trans('status') ?></th>
                      <th class="text-right"><?php echo trans('action') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i=1; foreach ($educations as $education): ?>
                      <tr id="row_<?php echo ($education->id); ?>">
                        <td><?= $i; ?></td>
                        <td><?php echo html_escape($education->degree); ?></td>

                        <td><?php echo html_escape($education->institute); ?></td>

                        <td>
                           <?php echo ($education->start_year); ?> To <?php echo ($education->end_year); ?> 
                        </td>

                        <td>
                          <?php if ($education->status == 1): ?>
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
                              <a href="<?php echo base_url('admin/education/edit/'.html_escape($education->id))?>" class="dropdown-item"><i class="bi bi-pencil-square mr-1"></i><?php echo trans('edit') ?></a>
                              <a data-val="Category" data-id="<?php echo html_escape($education->id); ?>" href="<?php echo base_url('admin/education/delete/'.html_escape($education->id));?>" class="dropdown-item delete_item"><i class="bi bi-trash3 mr-1"></i><?php echo trans('delete') ?></a>
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
