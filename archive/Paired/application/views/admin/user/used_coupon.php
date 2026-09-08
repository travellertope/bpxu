<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <?php $this->load->view('admin/include/breadcrumb'); ?>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-lg-10">
            <?php if (isset($page_title) && $page_title != "Edit"): ?>
              <div class="card list_area">
                <div class="card-header with-border">
                    <h3 class="card-title pt-2"><?php echo trans('used-coupon') ?></h3>
                </div>

                <div class="card-body table-responsive p-0">

                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo trans('session') ?></th>
                                <th><?php echo trans('mentee') ?></th>
                                <th><?php echo trans('email') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php $i=1; foreach ($used_coupons as $used_coupon): ?>
                            <tr id="row_<?php echo html_escape($used_coupon->id); ?>">
                                
                                <td><?= $i; ?></td>
                                <td><?php echo get_by_id($used_coupon->session_id, 'sessions')->name ?></td>
                                <td><?php echo get_by_id($used_coupon->mentee_id, 'users')->name ?></td>
                                <td><?php echo get_by_id($used_coupon->mentee_id, 'users')->email ?></td>
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