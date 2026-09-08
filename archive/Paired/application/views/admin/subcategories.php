
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
                  <a href="<?php echo base_url('admin/category/sub') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php else: ?>
                  <?php $required = 'required'; ?>
                  <a href="#" class="text-right btn btn-secondary btn-sm cancel_btn"><i class="fa lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php endif; ?>
              </div>
            </div>

            <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/category/sub_add')?>" role="form" novalidate>
              <div class="card-body">

                  <div class="form-group">
                    <label class="col-sm-2 control-label p-0" for="example-input-normal"><?php echo trans('categories') ?> <span class="text-danger">*</span></label>
                    <select class="form-control" name="category_id" required>
                      <option value=""><?php echo trans('select') ?></option>
                        <?php foreach ($categories as $category): ?>
                          <option value="<?php echo html_escape($category->id); ?>" 
                            <?php if(isset($subcategory[0]['parent_id']) && $subcategory[0]['parent_id'] == $category->id){echo 'selected';} ?>> 
                            <?php echo html_escape($category->name); ?>
                          </option>
                        <?php endforeach ?>
                    </select>
                  </div>
                  
                  <div class="form-group">
                    <label><?php echo trans('name') ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" required="required" name="name" value="<?php if(isset($subcategory[0]['name'])) {echo html_escape($subcategory[0]['name']);} ?>">
                  </div>

                  <div class="form-group">
                    <label><?php echo trans('details') ?></label>
                    <textarea class="form-control" name="details"><?php if(isset($subcategory[0]['details'])){echo html_escape($subcategory[0]['details']);} ?></textarea>
                  </div>

                  <div class="form-group clearfix">
                    <label><?php echo trans('status') ?></label><br>

                    <div class="icheck-primary radio radio-inline d-inline mr-4 mt-2">
                      <input type="radio" id="radioPrimary1" value="1" name="status" <?php if(isset($subcategory[0]['status']) && $subcategory[0]['status'] == 1){echo "checked";} ?> <?php if (isset($page_title) && $page_title != "Edit"){echo "checked";} ?>>
                      <label for="radioPrimary1"> <?php echo trans('show') ?>
                      </label>
                    </div>

                    <div class="icheck-primary radio radio-inline d-inline">
                      <input type="radio" id="radioPrimary2" value="2" name="status" <?php if(isset($subcategory[0]['status']) && $subcategory[0]['status'] == 2){echo "checked";} ?>>
                      <label for="radioPrimary2"> <?php echo trans('hide') ?>
                      </label>
                    </div>
                   </div>
              </div>

              <div class="card-footer">
                <input type="hidden" name="id" value="<?php if(isset($subcategory[0]['id'])){echo html_escape($subcategory[0]['id']);} ?>">
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
                <h3 class="card-title pt-2"><?php echo trans('edit') ?> <a href="<?php echo base_url('admin/pages') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a></h3>
              <?php else: ?>
                <h3 class="card-title pt-2"><?php echo trans('subcategories') ?></h3>
              <?php endif; ?>

              <div class="card-tools pull-right">
                 <a href="#" class="pull-right btn btn-secondary btn-sm add_btn"><i class="fas fa-plus"></i> <?php echo trans('create-new') ?></a>
                </div>
            </div>

            <?php if($subcategories): ?>
              <div class="card-body table-responsive p-0">
                  <table class="table table-hover text-nowrap <?php if(count($subcategories) > 10){echo "datatable";} ?>">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th><?php echo trans('name') ?></th>
                          <th><?php echo trans('category') ?></th>
                          <th><?php echo trans('status') ?></th>
                          <th class="text-right"><?php echo trans('action') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $i=1; foreach ($subcategories as $subcategory): ?>
                          <tr id="subcategory_<?php echo ($subcategory->id); ?>">
                            <td><?= $i; ?></td>
                            <td><?php echo html_escape($subcategory->name); ?></td>
                            <td><?php echo get_by_id($subcategory->parent_id,'categories')->name; ?></td>
                            <td>
                              <?php if ($subcategory->status == 1): ?>
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
                                    <a href="<?php echo base_url('admin/category/sub_edit/'.html_escape($subcategory->id));?>" class="dropdown-item"><?php echo trans('edit') ?></a>
                                    <a data-val="Category" data-id="<?php echo html_escape($subcategory->id); ?>" href="<?php echo base_url('admin/category/sub_delete/'.html_escape($subcategory->id));?>" class="dropdown-item delete_item"><?php echo trans('delete') ?></a>
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
