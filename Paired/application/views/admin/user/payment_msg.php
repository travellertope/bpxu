
<div class="content-wrapper">
  <section class="content">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-md-10 mx-200">
                <div class="pay_status p-20">
                    <?php if (isset($pay_status) && $pay_status=='success'): ?>
                        <h1 class="text-success"><i class="bi bi-check-circle"></i><br> <?php echo trans('success') ?></h1>
                        <h5 class="text-success"><?php echo trans('payment-completed-successfully') ?> !</h5><br>
                        <a href="<?php echo base_url('admin/sessions/booking') ?>" class="btn btn-md btn-success"><?php echo trans('continue') ?> <i class="fa fa-long-arrow-right"></i></a>
                    <?php endif; ?>
                    <?php if (isset($pay_status) && $pay_status=='error'): ?>
                        <h1 class="text-danger"><i class="bi bi-x-circle-fill"></i><br> <?php echo trans('failed') ?>!</h1>
                        <h5 class="text-danger"><?php echo trans('your-payment-has-been-failed') ?> !</h5><br>
                        <a href="<?php echo base_url('admin/sessions/booking_payment/'.md5($booking_id)) ?>" class="btn btn-md btn-danger"><?php echo trans('try-again') ?> <i class="fa fa-long-arrow-right"></i></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
  </section>
</div>