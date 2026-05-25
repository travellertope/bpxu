<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content pt-4">
      <div class="container">

        <div class="row ">
          <div class="col-md-12">

            <div class="card add_area <?php if(isset($_GET['error']) && $_GET['error'] == "Invalid"){echo "d-block";}else{echo "hide";} ?>">
              <div class="card-header with-border">
                <h3 class="card-title"><?php echo trans('send-payout-request') ?></h3>

                <div class="card-tools pull-right">
                    <a href="#" class="text-right btn btn-secondary cancel_btn btn-sm"><?php echo trans('payouts') ?></a>
                </div>
              </div>


              <form method="post" enctype="multipart/form-data" class="validate-form" action="<?php echo base_url('admin/referral/add_payouts')?>" role="form" novalidate>
                <div class="card-body">
                   
                  <div class="form-group">
                    <label><?php echo trans('amount') ?> (<?php echo settings()->currency_code?>)</label>
                    <div class="input-group">
                      <div class="input-group-append">
                        <span class="input-group-text"><?php echo settings()->currency_symbol ?></span>
                      </div>
                      <input type="number" class="form-control" name="amount" value="" placeholder="<?php echo trans('minimum') ?>:<?php echo  html_escape($settings->minimum_payout) ?>" required aria-invalid="false">
                    </div>
                  </div>

                  <div class="form-group mt-4">
                    <label> <?php echo trans('withdrawal-method') ?><span class="text-danger">*</span></label>
                    <select class="form-control show_method" required name="payment_method">
                          <option class="" value=""><?php echo trans('select-your-payment-method') ?></option>
                          <option class="" value="Paypal"><?php echo trans('paypal') ?></option>
                          <option class="" value="Bank"><?php echo trans('bank') ?></option>
                    </select>
                  </div>

                  <div class="form-group purple-border method_details hide">
                    <label for="exampleFormControlTextarea4"><?php echo trans('method-details') ?></label>
                    <textarea class="form-control" id="exampleFormControlTextarea4" name="method_details" rows="3"></textarea>
                  </div>
                </div>

                <div class="card-footer">
                  <!-- csrf token -->
                  <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                  <input type="hidden" name="id" value="<?php echo user()->referral_id?>">
                  <button type="submit" class="btn btn-primary pull-left"><?php echo trans('submit') ?></button>
                </div>

              </form>

            </div>

            <div class="card list_area <?php if(isset($_GET['error']) && $_GET['error'] == "Invalid"){echo "hide";} ?>">
              <div class="card-header with-border">
                <h3 class="card-title pt-2"><?php echo trans('payouts') ?></h3>
                <div class="card-tools pull-right">
                  <?php //if ($balance >= settings()->min_payout_amount): ?>
                    <a href="#" class="pull-right btn btn-sm btn-success add_btn"><?php echo trans('send-payout-request') ?></a>
                  <?php //endif ?>
                </div>
              </div>

              <div class="card-body table-responsive p-0">
                <?php if (!empty($payouts)): ?>
                  <table class="table table-hover text-nowrap">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th><?php echo trans('transaction-id') ?></th>
                        <th><?php echo trans('amount') ?></th>
                        <th><?php echo trans('payment-method') ?></th>
                        <th><?php echo trans('status') ?></th>
                        <th><?php echo trans('date') ?></th>
                        <th><?php echo trans('action') ?></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i=1; foreach ($payouts as $payout): ?>
                        <tr id="row_<?php echo html_escape($payout->id); ?>">
                          <td><?= $i; ?></td>
                          <td>
                            <?php echo html_escape($payout->transaction_id); ?>
                          </td>
                          <td>
                            <p class="mb-0"><?php echo settings()->currency_symbol ?><?php echo html_escape($payout->amount); ?></p>
                          </td>
                          <td>
                            <span class="badge badge-secondary"><?php echo html_escape($payout->payout_method); ?></span>
                          </td>
                          <td>
                            <?php if ($payout->status == 1): ?>
                              <span class="badge badge-success"><i class="fas fa-check-circle"></i> <?php echo trans('completed') ?></span>
                            <?php else: ?>
                              <span class="badge badge-warning-soft"><i class="fas fa-clock"></i> <?php echo trans('pending') ?></span>
                            <?php endif ?>
                          </td>

                          <td>
                            <p class="mb-0 fs-14"><?php echo my_date_show_time($payout->created_at) ?></p>
                          </td>

                          <td class="actions">
                              <div class="btn-group">
                                <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                                  <i class="fas fa-ellipsis-h"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" role="menu" >
                                  
                                  <a data-val="Category" data-id="<?php echo html_escape($payout->id); ?>" href="<?php echo base_url('admin/referral/payout_delete/'.html_escape($payout->id));?>" class="dropdown-item delete_item"><?php echo trans('delete') ?></a>
                                </div>
                            </div>

                          </td>
                        </tr>
                        
                      <?php $i++; endforeach; ?>
                    </tbody>
                  </table>
                <?php else: ?>
                  <?php $this->load->view('admin/include/not-found'); ?>
                <?php endif; ?>
              </div>

              <div class="mt-4">
                  <?php echo $this->pagination->create_links(); ?>
              </div>

            </div>
          </div>
        </div>

      </div>
    </div>

</div>
