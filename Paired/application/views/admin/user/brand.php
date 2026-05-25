<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-8">

            <div class="card add_area <?php if(isset($page_title) && $page_title == "Edit"){echo "d-block";}else{echo "hide";} ?>">
              <div class="card-header with-border">
                <?php if (isset($page_title) && $page_title == "Edit"): ?>
                  <h3 class="card-title"><?php echo trans('edit') ?></h3>
                <?php else: ?>
                  <h3 class="card-title"><?php echo trans('create-new') ?> </h3>
                <?php endif; ?>

                <div class="card-tools pull-right">
                  <?php if (isset($page_title) && $page_title == "Edit"): ?>
                    <a href="<?php echo base_url('admin/brand') ?>" class="pull-right btn btn-secondary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                  <?php else: ?>
                    <a href="#" class="text-right btn btn-secondary cancel_btn btn-sm"><?php echo trans('brand') ?></a>
                  <?php endif; ?>
                </div>
              </div>


              <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/brand/add')?>" role="form" novalidate>
                <div class="card-body">
                    <div class="form-group">
                        <?php if (isset($page_title) && $page_title == "Edit"): ?>
                            <p><img src="<?php echo base_url($brand[0]['logo']) ?>"> <p>
                        <?php endif ?>
                        <div class="custom-file">
                          <input type="file" class="custom-file-input" name="photo" id="customFileUp">
                          <label class="custom-file-label" for="customFileUp"><?php echo trans('upload-logo') ?></label>
                        </div>
                    </div>

                    <div class="form-group">
                      <label><?php echo trans('name') ?><span class="text-danger">*</span></label>
                      <input type="text" class="form-control" name="name" value="<?php if(isset($brand[0]['name'])){echo html_escape($brand[0]['name']);} ?>" required>
                    </div>

                    <div class="form-group">
                      <label><?php echo trans('link') ?></label>
                      <input type="text" class="form-control" name="link" value="<?php if(isset($brand[0]['link'])){echo html_escape($brand[0]['link']);} ?>">
                    </div>
                    
                    <div class="form-group mt-4">
                      <div class="icheck-primary radio radio-inline d-inline mr-4 mt-2">
                        <input type="radio" id="radioPrimary1" value="1" name="status" <?php if(isset($brand[0]['status']) && $brand[0]['status'] == 1){echo "checked";} ?> <?php if (isset($page_title) && $page_title != "Edit"){echo "checked";} ?>>
                        <label for="radioPrimary1"> <?php echo trans('show') ?>
                        </label>
                      </div>

                      <div class="icheck-primary radio radio-inline d-inline">
                        <input type="radio" id="radioPrimary2" value="2" name="status" <?php if(isset($brand[0]['status']) && $brand[0]['status'] == 2){echo "checked";} ?>>
                        <label for="radioPrimary2"> <?php echo trans('hide') ?>
                        </label>
                      </div>
                    </div>
                </div>

                <div class="card-footer">
                    <input type="hidden" name="id" value="<?php if(isset($brand[0]['id'])){echo html_escape($brand[0]['id']);} ?>">
                    <!-- csrf token -->
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

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
                <div class="card-header with-border">
                  <?php if (isset($page_title) && $page_title == "Edit"): ?>
                    <h3 class="card-title pt-2"><?php echo trans('edit') ?> <a href="<?php echo base_url('admin/brand') ?>" class="pull-right btn btn-sm btn-primary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h3>
                  <?php else: ?>
                    <h3 class="card-title pt-2"><?php echo trans('brands') ?> </h3>
                  <?php endif; ?>

                  <div class="card-tools pull-right">
                   <a href="#" class="pull-right btn btn-sm btn-secondary add_btn"><i class="fa fa-plus"></i> <?php echo trans('create-new') ?></a>
                  </div>
                </div>

                <div class="card-body table-responsive p-0">

                    <table class="table table-hover text-nowrap <?php if(count($brands) > 5){echo "datatable";} ?>">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo trans('logo') ?></th>
                                <th><?php echo trans('name') ?></th>
                                <th><?php echo trans('link') ?></th>
                                <th><?php echo trans('status') ?></th>
                                <th><?php echo trans('action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php $i=1; foreach ($brands as $brand): ?>
                            <tr id="row_<?php echo html_escape($brand->id); ?>">
                                
                                <td><?= $i; ?></td>
                                <td><img class="img-thumbnail" width="100px" src="<?php echo base_url($brand->logo) ?>"></td>
                                <td>
                                  <p class="mb-0"><?php echo html_escape($brand->name); ?></p>
                                </td>
                                <td>
                                  <p class="mb-0"><?php echo html_escape($brand->link); ?></p>
                                </td>
                                <td>
                                  <?php if ($brand->status == 1): ?>
                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i> <?php echo trans('active') ?></span>
                                  <?php else: ?>
                                    <span class="badge badge-secondary"><i class="fas fa-eye-slash"></i> <?php echo trans('hidden') ?></span>
                                  <?php endif ?>
                                </td> 
                                <td class="actions">
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-h"></i>
                                      </button>
                                      <div class="dropdown-menu dropdown-menu-right" role="menu" >
                                        <a href="<?php echo base_url('admin/brand/edit/'.html_escape($brand->id));?>" class="dropdown-item"><?php echo trans('edit') ?></a>
                                        <a data-val="Category" data-id="<?php echo html_escape($brand->id); ?>" href="<?php echo base_url('admin/brand/delete/'.html_escape($brand->id));?>" class="dropdown-item delete_item"><?php echo trans('delete') ?></a>
                                      </div>
                                  </div>

                                </td>
                            </tr>
                            
                          <?php $i++; endforeach; ?>
                        </tbody>
                    </table>
                  
                </div>

              </div>
            <?php endif; ?>

          </div>
      </div>
    </div>
  </div>
</div>
