<section class="bg-grey pb-15">
    <div class="container">

        <div class="text-center mx-md-auto mb-5 mb-md-7 mb-lg-9">
            <h2 class="mb-1 custom-font"><?php echo trans('pricing-title') ?></h2>
            <p><?php echo trans('pricing-desc') ?></p>

            <div class="btn-group btn-group-toggle mt-4" data-toggle="buttons">
              <label class="btn btn-outline-primary btn-pill custom-btngp active">
                <input type="radio" name="price_type" value="monthly" class="switch_price" checked> <?php echo trans('monthly') ?>
              </label>
              <label class="btn btn-outline-primary btn-pill custom-btngp">
                <input type="radio" name="price_type" value="yearly" class="switch_price"> <?php echo trans('yearly') ?>
              </label>

              <?php if (settings()->enable_lifetime == 1): ?>
              <label class="btn btn-outline-primary btn-pill custom-btngp">
                <input type="radio" name="price_type" value="lifetime" class="switch_price"> <?php echo trans('lifetime') ?>
              </label>
              <?php endif ?>
            </div>
        </div>


        <!-- Price -->
        <div class="row">
            <?php $i=1; foreach ($packages as $package): ?>
              <div class="col-md-<?php echo(12/count($packages)) ?>" data-aos="fade-up" data-aos-delay="<?= $i*100;?>">
                <div class="pricing-table text-center purple shadow-hover">

                    <p><span class="package_titles mb-1 text-<?php if ($package->is_special != 1){echo "dark";}else{echo "dark";} ?> "><?php echo html_escape($package->name); ?></span></p>

                    <!-- Price -->
                    <div class="price-tag m-0 mt-0 mb-3 text-center text-<?php if ($package->is_special != 1){echo "dark";}else{echo "dark";} ?>">
                      <div class="lifetime_price d-hide">
                          <span class="symbol <?php if(settings()->curr_locate == 0){echo "d-show";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span>
                          <span class="amount"><?php echo number_format($package->lifetime_price, settings()->num_format); ?></span>
                          <span class="symbol <?php if(settings()->curr_locate == 1){echo "d-show";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span> <br>
                          <span class="fs-16 text-<?php if ($package->is_special != 1){echo "muted";}else{echo "muted";} ?>"> <?php echo ucfirst(trans('lifetime')) ?></span>
                      </div>

                      <div class="yearly_price d-hide">
                          <span class="symbol <?php if(settings()->curr_locate == 0){echo "d-show";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span>
                          <span class="amount"><?php echo number_format($package->price, settings()->num_format); ?></span>
                          <span class="symbol <?php if(settings()->curr_locate == 1){echo "d-show";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span> <br>
                          <span class="fs-16 text-<?php if ($package->is_special != 1){echo "muted";}else{echo "muted";} ?>"> <?php echo ucfirst(trans('yearly')) ?></span>
                      </div>

                      <div class="monthly_price">
                          <span class="symbol <?php if(settings()->curr_locate == 0){echo "d-show";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span>
                          <span class="amount"><?php echo number_format($package->monthly_price, settings()->num_format); ?></span>
                          <span class="symbol <?php if(settings()->curr_locate == 1){echo "d-show";}else{echo "d-hide";} ?>"><?php echo settings()->currency_symbol ?></span> <br>
                          <span class="fs-16 text-<?php if ($package->is_special != 1){echo "muted";}else{echo "muted";} ?>"> <?php echo ucfirst(trans('monthly')) ?></span>
                      </div>
                    </div>
                    
                    <!-- Features -->
                      <div class="pricing-features text-center">
                          <?php if (empty($package->features)): ?>
                            <?php echo trans('features-not-selected-') ?>
                          <?php else: ?>
                            <?php foreach ($features as $all_feature): ?>
                              <?php foreach ($package->features as $feature): ?>
                                  <?php if ($feature->feature_id == $all_feature->id): ?>
                                      <?php $spani = 3; $icon = 'text-success bi bi-check-circle'; break; ?>
                                  <?php else: ?>
                                      <?php $spani = 5; $icon = 'text-danger bi bi-x-circle'; ?>
                                  <?php endif ?>
                              <?php endforeach ?>

                              <?php $package_slug = $package->slug; $limit = get_feature_limit($all_feature->id)->$package_slug; ?>

                              <div class="feature">
                                  <span class="list-style fs-16 <?= $spani; ?> mr-2"><i class="<?php echo html_escape($icon); ?>"></i></span> 
                                 
                                  <span class="pt-2 text-dark fs-16">
                                      <?php if ($all_feature->is_limit != 0): ?>
                                        <b>
                                          <?php if(isset($limit) && $limit > 0){
                                            if ($limit == '256' || $limit == '512' || $limit == '1024') {
                                              echo html_escape($limit.'x'.$limit);
                                            }else{
                                              echo html_escape($limit);
                                            }
                                          }else{
                                            echo 'Unlimited';
                                          }; ?>
                                        </b>
                                      <?php endif ?>
                                    <?php echo trans($all_feature->slug) ?>
                                  </span>
                              </div>
                            <?php endforeach ?>
                          <?php endif ?>
                      </div>
                    <!-- Button -->
                    <input type="hidden" name="billing_type" value="monthly" class="billing_type">
                    <a class="btn btn<?php if ($package->is_special != 1){echo "-light";} ?>-primary btn-block package_btn" href="<?php echo base_url('register?plan='.$package->slug) ?>"><?php echo trans('select-plan') ?></a>
                </div>
              </div>
            <?php $i++; endforeach ?>
        </div>
        <!-- End Price -->

    </div>
</section>

<section class="bg-white mt-7 py-6 py-md-7">
    <div class="container">
      <div class="text-center">
        <p class="mb-0"><i class="bi bi-question-circle fs-25 text-primary"></i></p>
        <p class="badge badge-secondary-soft text-primary badge-square mt-3">
          <?php echo trans('frequently-asked') ?>
        </p>
      </div>

      <div class="row justify-content-center mt-5">
        <div class="col-lg-10">
          <div class="row">
            
            <?php foreach ($faqs as $row): ?>
              <div class="col-md-6 mb-4">
                <h5 class="font-weight-normal">
                  <?php echo html_escape($row->title); ?>
                </h5>
                <p class="w-lg-90 text-muted mt-3">
                  <?php echo strip_tags($row->details); ?>
                </p>
              </div>
            <?php endforeach ?>

          </div>
        </div>
      </div>
    </div>
  </section>
