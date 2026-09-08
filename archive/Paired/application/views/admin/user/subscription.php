<div class="content-wrapper">

    <!-- Content Header (Page header) -->
    <?php $this->load->view('admin/include/breadcrumb'); ?>

    <!-- Main content -->
    <div class="content">

      <div class="row">

            <?php if ($type == 0): ?>
            <div class="col-md-6 m-auto pr-2">
                <div class="card">
                    
                    <div class="card-bodys p-0">

                        <ul class="list-group">
                            <h5 class="mb-3"><?php echo  trans('subscriptions') ?></h5>
                            <?php if (user()->user_type == 'trial'): ?>
                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold"><?php echo trans('plan') ?></span>
                                <span><?php echo settings()->trial_days; ?> <?php echo trans('days-free-trial') ?></span>
                              </li>

                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold"><?php echo trans('trial-expire') ?></span>
                                <span>
                                    <strong><?php echo my_date_show(user()->trial_expire); ?></strong> 
                                    <strong class="text-danger">(<?php echo date_dif(date('Y-m-d'), user()->trial_expire) ?> <?php echo trans('days-left') ?>)</strong>
                                </span>
                              </li>
                            <?php else: ?>

                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold"><?php echo trans('plan') ?></span>
                                <span><?php echo html_escape($user->package_name) ?></span>
                              </li>

                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold"><?php echo trans('price') ?></span>
                                <span>
                                    <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?><?php echo number_format($user->amount, settings()->num_format) ?>
                                    <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                                </span>
                              </li>

                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold"><?php echo trans('billing-cycle') ?></span>
                                <span><?php echo trans(html_escape($user->billing_type)) ?></span>
                              </li>

                              <?php if ($user->status != 'expire'): ?>
                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold"><?php echo trans('last-billing') ?></span>
                                <span><?php echo my_date_show($user->created_at) ?></span>
                              </li>
                              <?php endif; ?>

                              <?php if ($user->billing_type != 'lifetime'): ?>
                              <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span class="font-weight-bold"><?php echo trans('expire') ?></span>
                                <span><?php echo my_date_show($user->expire_on).' ('.date_dif(date('Y-m-d'), $user->expire_on).' days left)' ?></span>
                              </li>
                              <?php endif; ?>

                              <?php if ($user->status == 'verified'): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold"><?php echo trans('payment-status') ?></span>
                                    <span class="badge badge-success-soft"><i class="fas fa-check-circle"></i> <?php echo trans('paid') ?></span>
                                </li>
                                <?php else: ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold"><?php echo trans('payment-status') ?></span>
                                    <span class="badge badge-danger-soft"><i class="fas fa-clock"></i> <?php echo trans($user->status);?></span>
                                </li>
                              <?php endif ?>

                              <?php if ($user->amount != '0'): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span class="font-weight-bold"><?php echo trans('invoice') ?></span>
                                    <span class="text-danger"><a href="<?php echo base_url('admin/payment/lists') ?>"><i class="bi bi-eye"></i> <?php echo trans('view') ?></a></span>
                                </li>
                              <?php endif ?>
                            <?php endif ?>
                        </ul>

                        <div class="d-flex justify-content-between mt-2">
                            <a href="<?php echo base_url('admin/subscription/index/1') ?>" class="btn btn-lg btn-secondary btn-block fs-14"><?php echo trans('view-plans') ?></a>
                        </div>
                      
                    </div>
                    
                </div>
            </div>
            <?php endif ?>

            <?php if ($type == 1): ?>
            <div class="text-center col-md-12 mb-4 mt-3">
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-outline-primary custom-btngp <?php if($user->billing_type == 'monthly'){echo "focus actives";} ?>">
                      <input type="radio" name="price_type" value="monthly" class="switch_price"> <?php echo trans('monthly') ?>
                    </label>
                    <label class="btn btn-outline-primary custom-btngp <?php if($user->billing_type == 'yearly'){echo "focus actives";} ?>">
                      <input type="radio" name="price_type" value="yearly" class="switch_price"> <?php echo trans('yearly') ?>
                    </label>
                    <?php if (settings()->enable_lifetime == 1): ?>
                    <label class="btn btn-outline-primary custom-btngp <?php if($user->billing_type == 'lifetime'){echo "focus actives";} ?>">
                      <input type="radio" name="price_type" value="lifetime" class="switch_price"> <?php echo trans('lifetime') ?>
                    </label>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-12 m-auto">
                <div class="row">

                    <?php $i=1; foreach ($packages as $package): ?>
                    <div class="col-md-<?php echo(12/count($packages)) ?> col-xs-12">
                        <div class="pricing-table purple text-center shadow-sm">

                          
                            <h4 class="mb-0 mt-2 mb-2">
                                <?php if ($user->package_id == $package->id): ?>
                                    <i class="lnib lni-checkmark-circle text-success"></i>
                                <?php endif; ?>
                                <?php echo html_escape($package->name); ?>
                            </h4>


                            <!-- Price -->
                            <div class="price-tag mt-0">
                                <div class="yearly_price <?php if($user->billing_type == 'yearly'){echo 'd-show';}else{echo "d-hide";} ?>">
                                    <span class="symbol <?php if(settings()->curr_locate == 0){echo "d-inline-block";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span>
                                    <span class="amount-sm"><?php echo number_format($package->price, settings()->num_format); ?></span>
                                    <span class="symbol <?php if(settings()->curr_locate == 1){echo "d-inline-block";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span>
                                    <span class="after">/<?php echo trans('year') ?></span>
                                </div>

                                
                                <div class="monthly_price <?php if($user->billing_type == 'monthly'){echo 'd-show';}else{echo "d-hide";} ?>">
                                  <span class="symbol <?php if(settings()->curr_locate == 0){echo "d-inline-block";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span>
                                  <span class="amount-sm"><?php echo number_format($package->monthly_price, settings()->num_format); ?></span>
                                  <span class="symbol <?php if(settings()->curr_locate == 1){echo "d-inline-block";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span>
                                  <span class="after">/<?php echo trans('month') ?></span>
                                </div>

                                <div class="lifetime_price <?php if($user->billing_type == 'lifetime'){echo 'd-show';}else{echo "d-hide";} ?>">
                                  <span class="symbol <?php if(settings()->curr_locate == 0){echo "d-inline-block";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span>
                                  <span class="amount-sm"><?php echo number_format($package->lifetime_price, settings()->num_format); ?></span>
                                  <span class="symbol <?php if(settings()->curr_locate == 1){echo "d-inline-block";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span>
                                  <span class="after">/<?php echo trans('lifetime') ?></span>
                                </div>
                            </div>

                            <?php $package_slug = $package->slug; ?>

                            <!-- Features -->
                            <div class="pricing-features">
                                <?php if (empty($package->features)): ?>
                                    <?php echo trans('features-not-selected-') ?>
                                <?php else: ?>
                                <?php foreach ($features as $all_feature): ?>

                                <?php foreach ($package->features as $feature): ?>
                                    <?php if ($feature->feature_id == $all_feature->id): ?>
                                        <?php $icon = 'lnib lni-checkmark text-success'; break; ?>
                                    <?php else: ?>
                                        <?php $icon = 'lnib lni-close text-danger'; ?>
                                    <?php endif ?>
                                <?php endforeach ?>

                                <?php $limit = get_feature_limit($all_feature->id)->$package_slug; ?>

                                <div class="features flex-between">
                                    <div class="feature-item-left">
                                        <b><?php if(isset($limit) && $limit > 0){
                                            if ($limit == '256' || $limit == '512' || $limit == '1024') {
                                              echo html_escape($limit.'x'.$limit);
                                            }else{
                                              echo html_escape($limit);
                                            }
                                        }else{ echo '&#8734;';}; ?></b>
                                        <span><?php echo trans($all_feature->slug) ?></span>
                                    </div>
                                    <span class="limits"><i class="<?php echo html_escape($icon); ?>"></i></span>
                                </div>
                                <?php endforeach ?>
                                <?php endif ?>
                            </div>
                            <!-- Button -->

                            <input type="hidden" name="billing_type" value="<?php echo html_escape($user->billing_type) ?>" class="billing_type">

                            <?php //if ($user->billing_type != 'lifetime'): ?>
                                <?php if ($user->package_id == $package->id): ?>
                                <a class="btn btn-primary btn-block mt-4 package_btn"
                                    href="<?php echo base_url('admin/subscription/upgrade/'.$package->slug.'/1') ?>"> <?php echo trans('your-selected-plan') ?></a>
                                <?php else: ?>
                                <a class="btn btn-secondary btn-block mt-4 package_btn"
                                    href="<?php echo base_url('admin/subscription/upgrade/'.$package->slug.'/0') ?>"><?php echo trans('upgrade') ?></a>
                                <?php endif ?>
                            <?php //endif ?>

                        </div>
                    </div>
                    <?php endforeach ?>
                </div>
            </div>
            <?php endif ?>
        </div>
    </div>
    <!-- /.content -->
</div>