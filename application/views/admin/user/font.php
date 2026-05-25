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
                  <a href="<?php echo base_url('admin/font') ?>" class="pull-right btn btn-secondary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a>
                <?php else: ?>
                  <a href="#" class="text-right btn btn-secondary cancel_btn btn-sm"><?php echo trans('fonts') ?></a>
                <?php endif; ?>
              </div>
            </div>

            <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/font/add')?>" role="form" novalidate>
              <div class="card-body">
                  <div class="form-group">
                    <label><?php echo trans('font-name') ?><span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="<?php if(isset($font[0]['name'])){echo html_escape($font[0]['name']);} ?>" required>
                  </div>
              </div>

              <div class="card-footer">
                <input type="hidden" name="id" value="<?php if(isset($font[0]['id'])){echo html_escape($font[0]['id']);} ?>">
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
                  <h3 class="card-title pt-2"><?php echo trans('edit') ?> <a href="<?php echo base_url('admin/font') ?>" class="pull-right btn btn-sm btn-primary btn-sm"><i class="fa fa-angle-left"></i> <?php echo trans('back') ?></a></h3>
                <?php else: ?>
                  <h3 class="card-title pt-2"><?php echo trans('google-fonts') ?></h3>
                <?php endif; ?>

                <div class="card-tools pull-right">
                 <a target="_blank" href="https://fonts.google.com" class="pull-right btn btn-sm btn-outline-danger mr-2"> <?php echo trans('get-new-font') ?> <i class="bi bi-arrow-right"></i></a>

                 <a href="#" class="pull-right btn btn-sm btn-secondary add_btn"><i class="fa fa-plus"></i> <?php echo trans('create-new') ?> </a>
                </div>
              </div>

              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap <?php if(count($fonts) > 5){echo "datatable";} ?>">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th><?php echo trans('font-name') ?></th>
                      <th><?php echo trans('action') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php $i=1; foreach ($fonts as $row): ?>
                      <tr id="row_<?php echo html_escape($row->id); ?>">
                        <td><?= $i; ?></td>
                        
                        <td><p class="mb-0"><?php echo character_limiter($row->name, 20); ?></p></td> 
               
                        <td class="actions">
                          <div class="btn-group">
                            <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                              <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu" >
                              <a href="<?php echo base_url('admin/font/edit/'.html_escape($row->id));?>" class="dropdown-item"><?php echo trans('edit') ?></a>
                              <a data-val="Category" data-id="<?php echo html_escape($row->id); ?>" href="<?php echo base_url('admin/font/delete/'.html_escape($row->id));?>" class="dropdown-item delete_item"><?php echo trans('delete') ?></a>
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
