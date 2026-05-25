<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <?php $this->load->view('admin/include/breadcrumb'); ?>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-6 pl-3">
            <div class="card">
              <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/referral/update_settings') ?>" role="form" class="form-horizontal pl-20">
                  
                
                <div class="card-body">

                  <div class="custom-control custom-switch prefrence-item ml-10">
                    <input type="checkbox" name="enable_referral" class="custom-control-input" value="1" id="switch-88" <?php if($settings->is_enable == 1){echo "checked";} ?>>
                    <label class="custom-control-label" for="switch-88"><?php echo trans('enable-referral') ?></label>
                    <p></p>
                  </div>
                 
                  <div class="form-group mb-4 d-none">
                    <label><?php echo trans('referral-policy') ?></label>
                    <select class="form-control" name="referral_policy" >
                        <option value=""><?php echo trans('choose-referral-policy') ?></option>
                        <option value="1" selected <?php if($settings->referral_policy == 1){echo "selected";} ?>><?php echo trans('commission-only-on-first-purchase') ?></option>
                        <option value="2"<?php if($settings->referral_policy == 2){echo "selected";} ?>><?php echo trans('commission-on-every-purchase') ?></option>
                    </select>
                  </div>

                  <div class="form-group mb-4 ">
                    <label><?php echo trans('commision-rate') ?>(%)</label>
                    <input class="form-control" type="number" name="commision_rate" value="<?php echo html_escape($settings->commision_rate) ?>">
                  </div>

                  <div class="form-group mb-4">
                    <label><?php echo trans('minimum-payout') ?></label>
                    <div class="input-group mb-0">
                      <div class="input-group-append">
                        <span class="input-group-text" id="basic-addon2"><?php echo settings()->currency_symbol ?></span>
                      </div>
                      <input class="form-control" type="number" name="minimum_payout" value="<?php echo html_escape($settings->minimum_payout) ?>">
                    </div>
                  </div>

             

                  <div class="form-group mb-4 ">
                    <label><?php echo trans('payment-method') ?></label>
                    <input class="form-control" type="text" name="payment_method" value="<?php echo html_escape($settings->payment_method) ?>">
                  </div>

                  <div class="form-group mb-4 ">
                    <label ><?php echo trans('refferal-guidelines') ?></label>
                    <textarea class="form-control summernote" id="" rows="3" name="referral_guideline"><?php echo html_escape($settings->referral_guideline) ?></textarea>
                  </div>
                </div>

                <div class="card-footer">
                  <input type="hidden" name="id" value="<?php echo html_escape($settings->id); ?>">
                  <!-- csrf token -->
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                  <button type="submit" class="btn btn-primary mt-2"><?php echo trans('save-changes') ?></button>
                </div>
              </form>
            </div>
          </div>

        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
