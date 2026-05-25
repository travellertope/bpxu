

<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-lg-10">
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
                    <a href="<?php echo base_url('admin/pages') ?>" class="pull-right btn btn-secondary btn-sm mt-15"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                  <?php else: ?>
                    <?php $required = 'required'; ?>
                    <a href="#" class="text-right btn btn-secondary btn-sm cancel_btn"> <?php echo trans('pages') ?></a>
                  <?php endif; ?>
                </div>
              </div>

              <form id="cat-form" method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/pages/add')?>" role="form" novalidate>
                <div class="card-body">
                  
                    <div class="form-group">
                        <label><?php echo trans('title') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" value="<?php if(isset($page[0]['title'])) {echo html_escape($page[0]['title']);} ?>" <?php echo html_escape($required); ?>>
                    </div>

                    <div class="form-group">
                        <label><?php echo trans('slug') ?></label>
                        <input type="text" class="form-control" name="slug" <?php if(isset($page[0]['id']) && $page[0]['id'] == 1 || isset($page[0]['id']) && $page[0]['id'] == 2){echo "readonly";} ?> value="<?php if(isset($page[0]['slug'])) {echo html_escape($page[0]['slug']);} ?>" <?php echo html_escape($required); ?>>
                    </div>
                   
                    <div class="form-group">
                        <label><?php echo trans('description') ?></label>
                        <textarea class="form-control summernote" name="details"><?php if(isset($page[0]['details'])) {echo html_escape($page[0]['details']);} ?></textarea>
                    </div>
                </div>

                <div class="card-footer">
                  <input type="hidden" name="id" value="<?php if(isset($page[0]['id'])) {echo html_escape($page[0]['id']);} ?>">
                  <!-- csrf token -->
                  <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">

                  <?php if (isset($page_title) && $page_title == "Edit"): ?>
                    <button type="submit" class="btn btn-primary pull-left"> <?php echo trans('save-changes') ?></button>
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
                    <h3 class="card-title pt-2">Edit page <a href="<?php echo base_url('admin/pages') ?>" class="pull-right btn btn-secondary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h3>
                  <?php else: ?>
                    <h3 class="card-title pt-2"><?php echo trans('pages') ?> </h3>
                  <?php endif; ?>

                  <div class="card-tools pull-right">
                   <a href="#" class="pull-right btn btn-secondary btn-sm add_btn"><i class="fa fa-plus"></i> <?php echo trans('create-new') ?></a>
                  </div>
                </div>

                <?php if(!empty($pages)): ?>
                  <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap <?php if(is_countable($pages) && count($pages)  > 10){echo "datatable";} ?>">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th><?php echo trans('title') ?></th>
                          <th><?php echo trans('details') ?></th>
                          <th><?php echo trans('action') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $i=1; foreach ($pages as $row): ?>
                          <tr id="row_<?php echo ($row->id); ?>">
                              
                            <td><?= $i; ?></td>
                            <td><?php echo html_escape($row->title); ?></td>
                            <td><?php echo strip_tags(character_limiter($row->details, 60)); ?></td>

                            <td class="actions">
                              <div class="btn-group">
                                <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                                  <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu" >
                                  <a href="<?php echo base_url('admin/pages/edit/'.html_escape($row->id));?>" class="dropdown-item"><?php echo trans('edit') ?></a>
                                
                                  <a data-val="Category" data-id="<?php echo html_escape($row->id); ?>" href="<?php echo base_url('admin/pages/delete/'.html_escape($row->id));?>" class="dropdown-item delete_item"><?php echo trans('delete') ?></a>
                               
                                  <?php if($row->status == 0): ?>
                                    <a href="<?php echo base_url('admin/pages/update_status/1/'.html_escape($row->id));?>" class="dropdown-item"><?php echo trans('activate') ?></a>
                                  <?php else: ?>
                                    <a href="<?php echo base_url('admin/pages/update_status/0/'.html_escape($row->id));?>" class="dropdown-item"><?php echo trans('deactivate') ?></a>
                                  <?php endif ?>
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
