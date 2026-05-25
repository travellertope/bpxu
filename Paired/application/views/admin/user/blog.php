<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
        <div class="row">
          <div class="col-md-12">
            <div class="card add_area <?php if(!empty($page_title) && $page_title == "Edit"){echo "d-block";}else{echo "hide";} ?>">
              <div class="card-header with-border">
                <?php if (!empty($page_title) && $page_title == "Edit"): ?>
                  <h3 class="card-title"><?php echo trans('edit') ?></h3>
                <?php else: ?>
                  <h3 class="card-title"><?php echo trans('create-new') ?> </h3>
                <?php endif; ?>

                <div class="card-tools pull-right">
                  <?php if (!empty($page_title) && $page_title == "Edit"): ?>
                    <a href="<?php echo base_url('admin/blog') ?>" class="pull-right btn btn-secondary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                  <?php else: ?>
                    <a href="#" class="text-right btn btn-secondary cancel_btn btn-sm"><?php echo trans('view') ?></a>
                  <?php endif; ?>
                </div>
              </div>

              <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/blog/add')?>" role="form" novalidate>
                <div class="card-body">
                
                  <div class="form-group">
                    <?php if (isset($page_title) && $page_title == "Edit"): ?>
                        <?php if (!empty($blog->image)): ?>
                          <img width="200px" src="<?php echo base_url($blog->image) ?>"> <br><br>
                        <?php endif ?>
                    <?php endif ?>

                    <div class="custom-file w-50 mt-2">
                      <input type="file" class="custom-file-input" name="photo" id="customFileUp">
                      <label class="custom-file-label" for="customFileUp"><?php echo trans('upload-image') ?></label>
                    </div>
                  </div>
                  
                  <input type="hidden" name="category" value="0">
              
                  <div class="form-group">
                    <label><?php echo trans('title') ?><span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" value="<?php if(!empty($blog)){echo html_escape($blog->title);} ?>" required>
                  </div>
              
                  <div class="form-group">
                    <label><?php echo trans('slug') ?><span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="slug" value="<?php if(!empty($blog)){echo html_escape($blog->slug);} ?>" required>
                  </div>
              
                  <div class="form-group">
                    <label><?php echo trans('details') ?></label>
                    <textarea class="form-control summernote"  name="details" rows="3"><?php  if(!empty($blog)){echo html_escape($blog->details);} ?></textarea>
                  </div>
              
                  <div class="form-group">
                    <label><?php echo trans('tags') ?><span class="text-danger">*</span></label>
                    <input type="text" class="form-control" data-role="tagsinput" name="tags" value="<?php if(!empty($blog)){echo html_escape($blog->tags);} ?>" required>
                  </div>
              
                  <div class="form-group">
                    <label><?php echo trans('meta_tags') ?></label>
                    <input type="text" class="form-control" data-role="tagsinput" name="meta_tags" value="<?php if(!empty($blog)){echo html_escape($blog->meta_tags);} ?>">
                  </div>
              
                  <div class="form-group">
                    <label><?php echo trans('meta_desc') ?></label>
                    <textarea class="form-control"  name="meta_desc" rows="3"><?php if(!empty($blog)){echo html_escape($blog->meta_desc);} ?></textarea>
                  </div>

                  <div class="form-group mt-4">
                    <div class="icheck-primary radio radio-inline d-inline mr-4 mt-2">
                      <input type="radio" id="radioPrimary1" value="1" name="status" <?php if(!empty($blog) && $blog->status == 1){echo "checked";} ?> <?php if (!empty($page_title) && $page_title == "Edit"){echo "checked";} ?>>
                      <label for="radioPrimary1"> <?php echo trans('show') ?>
                      </label>
                    </div>

                    <div class="icheck-primary radio radio-inline d-inline">
                      <input type="radio" id="radioPrimary2" value="0" name="status" <?php if(!empty($blog) && $blog->status == 0){echo "checked";} ?>>
                      <label for="radioPrimary2"> <?php echo trans('hide') ?>
                      </label>
                    </div>
                  </div>
                </div>

                <div class="card-footer">
                  <input type="hidden" name="id" value="<?php if(!empty($blog)){echo html_escape($blog->id);} ?>">
                  <!-- csrf token -->
                  <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">

                  <?php if (!empty($page_title) && $page_title == "Edit"): ?>
                    <button type="submit" class="btn btn-primary btn-block pull-left"><?php echo trans('save-changes') ?></button>
                  <?php else: ?>
                    <button type="submit" class="btn btn-primary btn-block pull-left"> <?php echo trans('save') ?></button>
                  <?php endif; ?>
                </div>
              </form>
            </div>

            <?php if (!empty($page_title) && $page_title != "Edit"): ?>
              <div class="card list_area">
                <div class="card-header with-border">
                  <?php if (!empty($page_title) && $page_title == "Edit"): ?>
                    <h3 class="card-title pt-2"><?php echo trans('edit') ?> <a href="<?php echo base_url('admin/gallery') ?>" class="pull-right btn btn-sm btn-primary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h3>
                  <?php else: ?>
                    <h3 class="card-title pt-2"><?php echo trans('blog') ?> </h3>
                  <?php endif; ?>

                  <div class="card-tools pull-right">
                   <a href="#" class="pull-right btn btn-sm btn-secondary add_btn"><i class="fa fa-plus"></i> <?php echo trans('create-new') ?></a>
                  </div>
                </div>

                <?php if(!empty($blogs)): ?>
                  <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap <?php if(is_countable($blogs) && count($blogs)  > 10){echo "datatable";} ?>">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th><?php echo trans('image') ?></th>
                          <th><?php echo trans('title') ?></th>
                          <th><?php echo trans('status') ?></th>
                          <th><?php echo trans('action') ?></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $i=1; foreach ($blogs as $blog): ?>
                          <tr id="row_<?php echo html_escape($blog->id); ?>">
                            <td><?= $i; ?></td>
                            <td>
                              <?php if (!empty($blog->thumb)): ?>
                                <img class="feature-img" src="<?php echo base_url($blog->thumb) ?>">
                              <?php else: ?>
                                <?php echo trans('no-image-found') ?>
                              <?php endif ?>
                            </td>
                            <td><?php echo html_escape($blog->title) ?></td>
                            
                            <td>
                              <?php if ($blog->status == 1): ?>
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
                                  <a href="<?php echo base_url('admin/blog/edit/'.html_escape($blog->id));?>" class="dropdown-item"><?php echo trans('edit') ?></a>

                                  <a data-val="Category" data-id="<?php echo html_escape($blog->id); ?>" href="<?php echo base_url('admin/blog/delete/'.html_escape($blog->id));?>" class="dropdown-item delete_item"><?php echo trans('delete') ?></a>
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
