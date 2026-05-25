
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
                  <a href="<?php echo base_url('admin/time_zone') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php else: ?>
                  <?php $required = 'required'; ?>
                  <a href="#" class="text-right btn btn-secondary btn-sm cancel_btn"><i class="fa lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php endif; ?>
              </div>
            </div>

            <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/time_zone/add')?>" role="form" novalidate>
              <div class="card-body">

                  <div class="form-group">
                      <label><?php echo trans('name') ?> <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" required="required" name="name" placeholder="United States" value="<?php if(isset($time_zone->name)) {echo html_escape($time_zone->name);} ?>">
                  </div>
              </div>

              <div class="card-footer">
                
                <input type="hidden" name="id" value="<?php if(isset($time_zone->id)){echo html_escape($time_zone->id);} ?>">
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
                <h3 class="card-title pt-2">Time Zone</h3>
              <?php endif; ?>

              <div class="card-tools pull-right">
                 <a href="#" class="pull-right btn btn-secondary btn-sm add_btn"><i class="fas fa-plus"></i> <?php echo trans('create-new') ?></a>
                </div>
            </div>

            <?php if($time_zones): ?>
              <div class="card-body table-responsive p-0">
                  <table class="table table-hover text-nowrap <?php if(count($time_zones) > 10){echo "datatable";} ?>">
                      <thead>
                          <tr>
                              <th>#</th>
                              <th><?php echo trans('name') ?></th>
                              <th class="text-right"><?php echo trans('action') ?></th>
                          </tr>
                      </thead>
                      <tbody>
                        <?php $i=1; foreach ($time_zones as $time_zone): ?>
                          <tr id="row_<?php echo ($time_zone->id); ?>">
                              
                              <td><?= $i; ?></td>
                              <td><?php echo html_escape($time_zone->name); ?></td>

                              <td class="actions text-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                                      <i class="fas fa-ellipsis-h"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" role="menu" >
                                      <a href="<?php echo base_url('admin/time_zone/edit/'.html_escape($time_zone->id))?>" class="dropdown-item"><?php echo trans('edit') ?></a>
                                      <a data-val="Category" data-id="<?php echo html_escape($time_zone->id); ?>" href="<?php echo base_url('admin/time_zone/delete/'.html_escape($time_zone->id));?>" class="dropdown-item delete_item"><?php echo trans('delete') ?></a>
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
