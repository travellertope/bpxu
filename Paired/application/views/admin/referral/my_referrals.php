<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
          <div class="col-md-12">

            <?php if (isset($page_title) && $page_title != "Edit"): ?>
              <div class="card list_area">
                    <h3 class="card-title pt-2"><?php echo trans('referrals') ?></h3>
              </div>

              <?php if(!empty($referrals)): ?>
                <div class="card-body table-responsive p-0">

                  <table class="table table-hover text-nowrap <?php if(count($referrals) > 5){echo "datatable";} ?>">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th><?php echo trans('referrar-id') ?></th>
                        <th><?php echo trans('order-id') ?></th>
                        <th><?php echo trans('amount') ?></th>
                        <th><?php echo trans('commision') ?>(%)</th>
                        <th><?php echo trans('commision-amount') ?></th>
                        <th><?php echo trans('date') ?></th>
                        <th><?php echo trans('action') ?></th>
                      </tr>
                    </thead>
                    
                    <tbody>
                      <?php $i=1; foreach ($referrals as $referral): ?>
                        <tr id="row_<?php echo html_escape($referral->id); ?>">
                            
                          <td><?= $i; ?></td>
                          <td><?php echo html_escape($referral->referrar_id) ?></td>
                          <td><?php echo html_escape($referral->order_id) ?></td>
                          <td>
                            <?php if($this->business->curr_locate == 0){echo html_escape($this->business->currency_symbol);} ?>
                            <?php echo number_format($referral->amount, $this->business->num_format) ?>
                            <?php if($this->business->curr_locate == 1){echo html_escape($this->business->currency_symbol);} ?>  
                          </td>
                          <td><?php echo html_escape($referral->commision) ?></td>
                          <td>
                            <?php if($this->business->curr_locate == 0){echo html_escape($this->business->currency_symbol);} ?>
                            <?php echo html_escape($referral->commision_amount, $this->business->num_format) ?>
                            <?php if($this->business->curr_locate == 1){echo html_escape($this->business->currency_symbol);} ?>  
                          </td> 
                          <td><?php echo html_escape($referral->created_at) ?></td> 
                          <td class="actions">
                            <div class="btn-group">
                              <button type="button" class="btn btn-tool" data-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-h"></i>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right" role="menu" >
                                
                                <a data-val="Category" data-id="<?php echo html_escape($referral->id); ?>" href="<?php echo base_url('admin/referral/referral_delete/'.html_escape($referral->id));?>" class="dropdown-item delete_item"><?php echo trans('delete') ?></a>
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
            <?php endif; ?>

          </div>

      </div>
    </div>
  </div>
</div>
