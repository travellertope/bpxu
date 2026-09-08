<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content pt-4">
      <div class="container">
        <div class="row box-payout-areas">

          <div class="col-md-3 col-sm-6 col-12 mb-1">
            <div class="info-box-pay border border-success">
              <div class="info-box-content-pay">
                <span class="info-box-number-pay text-success"><?php echo count($referrals) ?></span>
                <span class="info-box-text font-weight-bold text-muted"> <?php echo trans('total-referrals') ?></span>
              </div>
            </div>
          </div>
          
          <div class="col-md-3 col-sm-6 col-12 mb-1">
            <div class="info-box-pay border border-primary">
              <div class="info-box-content-pay">
                <span class="info-box-number-pay text-primary"><?php echo settings()->currency_symbol ?><?php echo html_escape($earns->commision_amount); ?></span>
                <span class="info-box-text font-weight-bold text-muted"><?php echo trans('total-earnings') ?>  (<?php echo html_escape($settings->commision_rate) ?>%)</span>
                <span class="small mt-1"></span>
              </div>
            </div>
          </div>

          <div class="col-md-3 col-sm-6 col-12 mb-1">
            <div class="info-box-pay border border-warning">
              <div class="info-box-content-pay">
                <span class="info-box-number-pay text-warning"><?php echo settings()->currency_symbol ?> <?php echo html_escape($withdraws->amount) ;  ?></span>
                <span class="info-box-text font-weight-bold text-muted"> <?php echo trans('total-withdraw') ?></span>
                <span class="small mt-1"></span>
              </div>
            </div>
            <span class="small mt-1"><i class="fas fa-info-circle text-muted"></i> <?php echo trans('minimum-payout-amounts') ?><?php echo settings()->currency_symbol.'<b>'.$settings->minimum_payout.'</b>' ?></span>
          </div>
          <!-- /.col -->

          <div class="col-md-3 col-sm-6 col-12 mb-1">
            <div class="info-box-pay border border-success">
              <div class="info-box-content-pay">
                <span class="info-box-number-pay text-success"><?php echo settings()->currency_symbol ?><?php echo user()->referral_earn; ?></span>
                <span class="info-box-text font-weight-bold text-muted"> <?php echo trans('balance') ?> </span>
              </div>
              <!-- /.info-box-content-pay -->
            </div>
          </div>
        </div>

        <div class="card-body mt-5">
          <div class="">
            <p class="font-weight-bold"><?php echo trans('my-referral-url') ?>:</p>
              <p class="font-weight-bold text-success text-right" id="successMsg"></p>
            
            <div class="input-group mb-3">
              <input type="text" class="form-control copy_url" placeholder="" name="url" value="<?php echo base_url() ?>?ref=<?php echo html_escape($user->referral_id) ?>" aria-label="Recipient's username" aria-describedby="basic-addon2">
              <div class="input-group-append">
                <a href="#" class="btn btn-primary btn-md copy_button" type="button"><i class="fas fa-link"></i></a>
              </div>
            </div>

          </div>

          <div class="mt-5">
            <p class="font-weight-bold mb-1"><?php echo trans('referral-policy') ?>:</p>
            <p class="">
              <?php if ($settings->referral_policy==1): ?>
                <?php echo trans('first-successful-payment-by-referred-person') ?>
              <?php else: ?>
                <?php echo trans('every-successful-payment-by-referred-person') ?>
              <?php endif ?>
            </p>
          </div>

          <div class="mt-5">
            <p class="font-weight-bold mb-1"><?php echo trans('referral-guidelines') ?>:</p>
            <?php echo ($settings->referral_guideline) ?> <br>
          </div>
        </div>

        <div class="card-body mt-5">
          <div class="mt-5">
            <h3 class="font-weight-bold mb-1 text-center"><?php echo trans('how-it-works') ?> ?</h3>
            <div class="row mt-5">
              <div class="col-4 text-center">
                <div class="">
                  <i class="fas fa-check-circle affiliate-icon"></i>
                </div>
                <p class="font-weight-bold mt-2 pl-3"><?php echo trans('send-invitation') ?></p>
                <p class=" mt-2"><?php echo trans('send-your-referral-link-to-your-friends-and-tell-them-how-cool-is-davinci') ?></p>
              </div>
              <div class="col-4 text-center">
                <div class="">
                  <i class="fas fa-check-circle affiliate-icon"></i>
                </div>
                <p class="font-weight-bold mt-2 "><?php echo trans('registration') ?></p>
                <p class=" mt-2"><?php echo trans('let-them-register-using-your-referral-link') ?></p>
              </div>
              <div class="col-4 text-center">
                <div class="">
                  <i class="fas fa-check-circle affiliate-icon"></i>
                </div>
                <p class="font-weight-bold mt-2">
                  <?php echo trans('get-commissions') ?>
                </p>
                <p class=" mt-2"><?php echo trans('earn-commission-for-their-first-subscription-plan-payments') ?></p>
              </div>
            </div>
          </div>

        </div>

      </div>
    </div>
</div>
