<div class="content-wrapper">
  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-md-8">
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title"><?php echo trans('upgrade-account') ?></h3>
          </div>
          <div class="box-body">
            <h3 class="text-warning"><?php echo trans('are-you-sure-want-to-upgrade-your-account-from-free-to-pro') ?></h3>
            <a class="btn btn-info btn-lg upgrade_btn" href="<?php echo base_url('admin/payment/upgrade_operation') ?>"><i class="fa fa-check"></i> <?php echo trans('yes-continue') ?></a>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>