
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
                  <a href="<?php echo base_url('admin/country') ?>" class="pull-right btn btn-secondary btn-sm"><i class="lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php else: ?>
                  <?php $required = 'required'; ?>
                  <a href="#" class="text-right btn btn-secondary btn-sm cancel_btn"><i class="fa lni lni-arrow-left"></i> <?php echo trans('back') ?></a>
                <?php endif; ?>
              </div>
            </div>

            <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/country/add')?>" role="form" novalidate>
              <div class="card-body">

                <div class="form-group">
                  <label><?php echo trans('name') ?> <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" name="name" placeholder="United States" value="<?php if(isset($country->name)) {echo html_escape($country->name);} ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('country-code') ?><span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" name="country_code" placeholder="Us" value="<?php if(isset($country->code)) {echo html_escape($country->code);} ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('dial-code') ?><span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" name="dial_code" placeholder="+376" value="<?php if(isset($country->dial_code)) {echo html_escape($country->dial_code);} ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('currency-name') ?><span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" name="currency_name" placeholder="United States Dollar" value="<?php if(isset($country->currency_name)) {echo html_escape($country->currency_name);} ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('currency-symbol') ?><span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" name="currency_symbol" placeholder="$" value="<?php if(isset($country->currency_symbol)) {echo html_escape($country->currency_symbol);} ?>">
                </div>

                <div class="form-group">
                  <label><?php echo trans('currency-code') ?><span class="text-danger">*</span></label>
                  <input type="text" class="form-control" required="required" name="currency_code" placeholder="USD" value="<?php if(isset($country->currency_code)) {echo html_escape($country->currency_code);} ?>">
                </div>
              </div>

              <div class="card-footer">
                
                <input type="hidden" name="id" value="<?php if(isset($country->id)){echo html_escape($country->id);} ?>">
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
                <h3 class="card-title pt-2"><?php echo trans('countries') ?></h3>
              <?php endif; ?>

              <div class="card-tools pull-right">
                 <a href="#" class="pull-right btn btn-secondary btn-sm add_btn"><i class="fas fa-plus"></i> <?php echo trans('create-new') ?></a>
                </div>
            </div>
            <?php if(!empty($countries)): ?>
            <div class="card-body table-responsive p-0">
              <table class="table table-hover text-nowrap <?php if(count($countries) > 10){echo "datatable";} ?>">
                <thead>
                  <tr>
                    <th>#</th>
                    <th><?php echo trans('flag') ?></th>
                    <th><?php echo trans('name') ?></th>
                    <th><?php echo trans('currency') ?></th>
                    <th class="text-right"><?php echo trans('action') ?></th>
                  </tr>
                </thead>

                <tbody>
                  <?php $i=1; foreach ($countries as $country): ?>
                    <tr id="row_<?php echo ($country->id); ?>">
                        
                      <td><?= $i; ?></td>

                      <td><img width="25px" src="<?php echo base_url('assets/images/flags/'.strtolower($country->code).'.png') ?>"></td>

                      <td><?php echo html_escape($country->name); ?> - <?php echo html_escape($country->code) ?></td>
                      
                      <td><?php echo html_escape($country->currency_name); ?> - <?php echo html_escape($country->currency_code) ?></td>

                      <td class="actions text-right">
                        <div class="btn-group">
                          <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-ellipsis-h"></i>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right" role="menu" >
                            <a href="<?php echo base_url('admin/country/edit/'.html_escape($country->id))?>" class="dropdown-item"><?php echo trans('edit') ?></a>
                            <a data-val="Category" data-id="<?php echo html_escape($country->id); ?>" href="<?php echo base_url('admin/country/delete/'.html_escape($country->id));?>" class="dropdown-item delete_item"><?php echo trans('delete') ?></a>
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
