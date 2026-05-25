
<section class="h-100 h-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">

      <div class="col-12">
        <div class="mb-4 mt-4">
            <div class="success text-success"></div>
            <div class="error text-danger"></div>
            <div class="warning text-warning"></div>
        </div>
      </div>

      <div class="col-12">
        <div class="card card-registration card-registration-2 overhidden">
          <div class="card-body p-0">
            <form id="checkout_form" action="<?php echo base_url('home/session_booking/'.$session->uid) ?>" method="post" enctype="multipart/form-data">
              <div class="row g-0">
                <div class="col-lg-7">

                  <?php if(check_auth() == false): ?>
                    <div class="p-5">
                      <div class="d-flex justify-content-between mb-4 bm-1 pb-3">
                        <div>
                          <?php if (isset($_GET) && $_GET['type'] == 'login'): ?>
                            <h5 class="mb-0 font-weight-normal"><?php echo trans('login') ?></h5>
                          <?php else: ?>
                            <h5 class="mb-0 font-weight-normal"><?php echo trans('create-new-account') ?></h5>
                          <?php endif ?> 
                          
                        </div>
                        <div>
                          <?php if (isset($_GET) && $_GET['type'] == 'login'): ?>
                            <a class="badge badge-secondary-soft badge-pill" href="<?php echo base_url('booking/'.$session->slug.'/'.$session->uid.'?type=register') ?>"><i class="bi bi-person-add"></i> <?php echo trans('create-new-account') ?></a>
                          <?php else: ?>
                            <a class="badge badge-secondary-soft badge-pill" href="<?php echo base_url('booking/'.$session->slug.'/'.$session->uid.'?type=login') ?>"><i class="bi bi-box-arrow-in-left"></i> <?php echo trans('login') ?></a>
                          <?php endif ?>
                        </div>
                      </div>

                      <hr class="my-4">

                      <div class="container p-0">
                        <?php if(isset($_GET) && $_GET['type'] == 'login'): ?>
                          <div class="row">
                            <div class="box col-md-12 m-auto text-center">
                              <div class="box-body text-left">
                                <div class="row ">
                                  <div class="col-md-12">
                                    <div class="form-group">
                                      <label><?php echo trans('email') ?><span class="text-danger">*</span></label>
                                      <input type="text" class="form-control requ1" name="user_name" value="" required>
                                    </div>
                                  </div>
                                </div>
                                <div class="row ">
                                  <div class="col-md-12">
                                    <div class="form-group">
                                      <label><?php echo trans('password') ?><span class="text-danger">*</span></label>
                                      <input type="password" class="form-control requ1" name="password" value="" required>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        <?php else: ?>
                          <div class="row">
                            <div class="box col-md-12 m-auto text-center">
                                <div class="box-body text-left">
                                    <div class="row">
                                      <div class="col-md-12">
                                        <div class="form-group">
                                          <label><?php echo trans('name') ?><span class="text-danger">*</span></label>
                                          <input type="text" class="form-control" name="name" value="" required>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="row ">
                                      <div class="col-md-12">
                                          <div class="form-group">
                                            <label><?php echo trans('email') ?><span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="email" value="" required>
                                          </div>
                                        </div>
                                    </div>

                                    <div class="row ">
                                      <div class="col-md-12">
                                          <div class="form-group">
                                            <label><?php echo trans('password') ?><span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="password" value="" required>
                                          </div>
                                        </div>
                                    </div>

                                    <div class="row ">
                                        <div class="col-md-12">
                                          <div class="form-group">
                                            <label><?php echo trans('country') ?><span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="country" required>
                                              <option value=""><?php echo trans('select') ?></option>
                                              <?php foreach ($countries as $country): ?>
                                                <option value="<?php echo html_escape($country->id) ?>">
                                                  <?php echo html_escape($country->name) ?>
                                                </option>
                                              <?php endforeach ?>
                                            </select>
                                          </div>
                                        </div>

                                        <div class="col-12">
                                          <div class="form-group">
                                            <label><?php echo trans('time-zone') ?><span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="time_zone" required>
                                                <option value=""><?php echo trans('select') ?></option>
                                                <?php foreach ($time_zones as $time): ?>
                                                  <option value="<?php echo html_escape($time->id) ?>"><?php echo html_escape($time->name) ?></option>
                                                <?php endforeach ?>                 
                                            </select>
                                          </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                          </div>
                        <?php endif; ?>

                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                        <input type="hidden" class="is_customer_exist" name="type" value="<?php if(isset($_GET) && $_GET['type'] == 'login'){echo "login";}else{echo "register";} ?>">
                        <input type="hidden" name="date" value="<?php echo html_escape($this->session->userdata('date')) ; ?>">
                        <input type="hidden" name="time" value="<?php echo html_escape($this->session->userdata('time')); ?>">

                        <button type="submit" class="btn btn-dark btn-block btn-lg fs-14 mt-5 checkout_btn"
                          data-mdb-ripple-color="dark"><?php echo trans('confirm-booking') ?></button>
                      </div>
                    </div>
                  <?php endif; ?>


                  <?php if(check_auth() == true): ?>
                  <div class="p-5">
                    <div class="container p-0">


                      
                      <p class="p-3 bg-success-soft text-light rounded"><i class="bi bi-info-circle-fill text-success"></i> <?php echo trans('already-signed-in-msg') ?></p>

                      <button type="submit" class="btn btn-dark btn-block btn-lg fs-14 mt-5 checkout_btn"
                        data-mdb-ripple-color="dark" ><?php echo trans('confirm-booking') ?></button>
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                        <input type="hidden" name="date" value="<?php echo html_escape($this->session->userdata('date')) ; ?>">
                        <input type="hidden" name="time" value="<?php echo html_escape($this->session->userdata('time')); ?>">
                    </div>
                  </div>
                  <?php endif; ?>



                </div>
                
                <div class="col-lg-5 bl-1">
                  <div class="p-5">
                    <h5 class="font-weight-normal pb-3"><?php echo trans('booking-info') ?></h5>
                    
                    <div class="booking-item mb-5 mt-2">
                      <p class="mb-0 text-muted"><i class="bi bi-view-list"></i> <?php echo trans('session') ?></p>
                      <p><?php echo html_escape($session->name) ?></p>
                    </div>

                    <div class="booking-item mb-5 mt-2">
                      <p class="mb-0 text-muted"><i class="bi bi-tag"></i> <?php echo trans('price') ?></p>

                      <?php if($session->price != 0): ?>
                        <p>
                          <?php if(settings()->curr_locate == 0){echo settings()->currency_symbol;} ?>
                          <?php echo number_format($session->price, settings()->num_format) ?>
                          <?php if(settings()->curr_locate == 1){echo settings()->currency_symbol;} ?>
                        </p>
                      <?php else: ?>
                        <p><?php echo trans('free') ?></p>
                      <?php endif; ?>
                    </div>

                    <div class="booking-item mb-5 mt-2">
                      <p class="mb-0 text-muted"><i class="bi bi-hourglass-bottom"></i> <?php echo trans('duration') ?></p>
                      <p><?php echo html_escape($session->duration) ?> Minutes</p>
                    </div>

                    <div class="booking-item mb-5 mt-2">
                      <p class="mb-0 text-muted"><i class="bi bi-calendar2-check"></i> <?php echo trans('date') ?></p>
                      <p><?php echo html_escape($this->session->userdata('date')) ?></p>
                    </div>
                    
                    <div class="booking-item mb-5 mt-2">
                      <p class="mb-0 text-muted"><i class="bi bi-clock"></i> <?php echo trans('time') ?></p>
                      <?php if (!empty(!empty($this->session->userdata('convert_time_slot')))): ?>
                        <p><?php echo html_escape($this->session->userdata('convert_time_slot')) ?></p>
                      <?php else: ?>
                        <p><?php echo html_escape($this->session->userdata('time')) ?></p>
                      <?php endif ?>
                    </div>

                    <input type="hidden" name="time_slot_id" value="<?php echo html_escape($this->session->userdata('time_slot_id')) ?>">

                  </div>
                </div>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>