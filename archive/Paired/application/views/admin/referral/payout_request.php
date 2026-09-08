<div class="content-wrapper">


  <!-- Main content -->
  <div class="content pt-4 mb-4">
    <div class="container-fluid">
      
      <div class="row mt-5">
        <div class="col-md-12">

          <div class="card list_area">
            <div class="card-header with-border">
              <h3 class="card-title pt-2"><?php echo html_escape($page_title) ?> </h3>

              <div class="card-tools">
                  <div class="filter-bars pull-right">
                    <a class="filter-action btn btn-outline-primary text-primary"> <i class="fas fa-filter"></i></a>
                  </div>
              </div>
            </div>

            <div class="filter_popup showFilter">
              <p class="leads mb-3"><?php echo trans('filters') ?></p>
              
              <form action="<?php echo base_url('admin/referral/payout_request') ?>" class="sort_form" method="get">
                <div class="row">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label><?php echo trans('transaction-id') ?></label>
                      <input type="text" name="transaction_id" class="form-control form-control-sm">
                    </div>
                  </div>

                  <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary btn-sm btn-block"><?php echo trans('submit') ?></button>
                  </div>

                </div>
              </form>
            </div>

            <div class="card-body table-responsive p-0">
              <?php if (!empty($payouts)): ?>
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th><?php echo trans('user') ?></th>
                      <th><?php echo trans('withdrawal-method') ?></th>
                      <th><?php echo trans('withdrawal-amount') ?></th>
                      <th><?php echo trans('balance') ?></th>
                      <th><?php echo trans('transaction-id') ?></th>
                      <th><?php echo trans('status') ?></th>
                      <th><?php echo trans('request-sent') ?></th>
                      <th><?php echo trans('action') ?></th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    <?php $i=1; foreach ($payouts as $row): ?>
                      <?php $user = get_by_id($row->user_id, 'users'); ?>

                      <tr id="row_<?php echo html_escape($row->id); ?>">
                        <td><?= $i; ?></td>
                        
                        <td class="pl-2">
                          <div class="d-flex align-items-center">
                            <div>
                              <?php if ($row->thumb == ''): ?>
                                  <?php $avatar = 'assets/images/no-photo-sm.png'; ?> 
                              <?php else: ?>
                                  <?php $avatar = $row->thumb; ?>
                              <?php endif ?>
                              <img width="50px" class="img-circle mr-3" src="<?php echo base_url($avatar) ?>"> 
                            </div>
                            
                            <div class="d-flexs flex-columns">
                              <div>
                                  <p class="leads font-weight-bold mb-0"><?php echo html_escape($user->name); ?></p>
                              </div>
                            </div>
                          </div>
                        </td>

                        <td>
                          <span class="badge badge-primary"><?php echo ucfirst($row->payout_method); ?></span>
                          <a data-toggle="modal" href="#payoutModal_<?php echo html_escape($i) ?>" >
                            <span class="badge badge-secondary"><i class="far fa-eye"></i> <?php echo trans('view-details') ?></span>
                          </a>
                        </td>

                        <td>
                          <p class="mb-0 text-success"><?php echo settings()->currency_symbol.''.(html_escape($row->amount)) ?></p>
                        </td>

                        <td><p class="mb-0 text-success"><?php echo settings()->currency_symbol.''.(html_escape($user->referral_earn)) ?></p></td>
                        <td>
                          <p class="mb-0"><?php echo html_escape($row->transaction_id); ?></p>
                        </td>

                        <td>
                          <?php if ($row->status == 1): ?>
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> <?php echo trans('completed') ?></span>
                          <?php else: ?>
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> <?php echo trans('pending') ?></span>
                          <?php endif ?>
                        </td>

                        <td>
                          <p class="mb-0 fs-14"><?php echo get_time_ago($row->created_at) ?></p>
                        </td>

                        <td class="actions">
                          <div class="btn-group">
                            <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                              <i class="fas fa-ellipsis-h"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" role="menu" >
                              <?php if ($row->status == 0): ?>
                                <a href="<?php echo base_url('admin/referral/payout_complete/'.md5($row->id));?>" class="dropdown-item <?php if($user_balance < $withdraw_amount){echo "hide";} ?>"><i class="far fa-check-circle"></i><?php echo trans('completed') ?></a>
                              <?php endif ?>
                              <a data-val="Category" data-id="<?php echo html_escape($row->id); ?>" href="<?php echo base_url('admin/payouts/delete/'.html_escape($row->id));?>" class="dropdown-item delete_item"><i class="lni lni-trash-can"></i><?php echo trans('delete') ?></a>
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



<?php $b=1; foreach ($payouts as $payout): ?>

<div class="modal fade" id="payoutModal_<?php echo html_escape($b) ?>">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header justify-content-between">
        <h6 class="modal-title text-muted"><?php echo trans('payout-method') ?> (<?php echo ucfirst($payout->payout_method) ?>)</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true" class="fs-14"><i class="lnib lni-close"></i></span>
        </button>
      </div>

      <div class="modal-body p-0">

        <?php if ($payout->payout_method == 'Paypal'): ?>
        <ul class="list-group">
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <?php echo trans('paypal-email') ?>
            <span class="badge badge-primary-soft badge-pill fs-14 font-weight-normal"><?php echo html_escape($payout->method_details) ?></span>
          </li>
        </ul>
        <?php endif ?>

        <?php if ($payout->payout_method == 'Bank'): ?>
        <ul class="list-group">
          <li class="list-group-item d-flex justify-content-between align-items-center">Bank <?php echo trans('details') ?>
            <span class="badge badge-primary-soft badge-pill fs-14 font-weight-normal"><?php echo html_escape($payout->method_details) ?></span>
          </li>
        </ul>
        <?php endif ?>

      </div>

    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<?php $b++; endforeach; ?>
