<div class="content-wrapper">
    
    <!-- Content Header (Page header) -->
    <?php include"include/breadcrumb.php"; ?>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- /.col-md-6 -->
          <div class="col-lg-12">
            <div class="card">

              <div class="">
                <div class="row">
                  <div class="col-md-3">
                    <div class="card-body mr-md-4">
                      <ul class="nav nav-pills flex-column" id="myTab" role="tablist">
                        <li class="nav-item">
                          <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><i class="bi bi-grid-1x2 mr-1"></i> <?php echo trans('website-settings') ?></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="appearance-tab" data-toggle="tab" href="#appearance" role="tab" aria-controls="appearance" aria-selected="false"><i class="bi bi-brush mr-1"></i> <?php echo trans('appearance') ?> </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="prefrences-tab" data-toggle="tab" href="#prefrences" role="tab" aria-controls="prefrences" aria-selected="false"><i class="bi bi-sliders mr-1"></i> <?php echo trans('prefrences') ?></a>
                        </li>
                        
                        <li class="nav-item">
                          <a class="nav-link" id="zoom-tab" data-toggle="tab" href="#zoom" role="tab" aria-controls="zoom" aria-selected="false"><i class="bi bi-person-video mr-1"></i> <?php echo trans('zoom-api') ?></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="calendar-tab" data-toggle="tab" href="#calendar" role="tab" aria-controls="calendar" aria-selected="false"><i class="far fa-calendar-alt mr-1"></i> <?php echo trans('google-calendar') ?> </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="sms-tab" data-toggle="tab" href="#sms" role="tab" aria-controls="sms" aria-selected="false"><i class="far fa-comment-dots mr-1"></i><?php echo trans('twilio') ?><?php echo trans('sms-settings') ?></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="sociallog-tab" data-toggle="tab" href="#sociallog" role="tab" aria-controls="sociallog" aria-selected="false"><i class="fas fa-sign-in-alt mr-1"></i> <?php echo trans('social-login') ?></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="mail-tab" data-toggle="tab" href="#mail" role="tab" aria-controls="mail" aria-selected="false"><i class="bi bi-envelope-plus mr-1"></i> <?php echo trans('email-settings') ?></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="whatsapp-tab" data-toggle="tab" href="#whatsapp" role="tab" aria-controls="mail" aria-selected="false"><i class="bi bi-whatsapp mr-1"></i> <?php echo trans('whatsapp-settings') ?></a>
                        </li>

                        <li class="nav-item">
                          <a class="nav-link" id="pwa-tab" data-toggle="tab" href="#pwa" role="tab" aria-controls="mail" aria-selected="false"><i class="bi bi-phone mr-1"></i> <?php echo trans('pwa-settings') ?></a>
                        </li>
                        
                        <li class="nav-item">
                          <a class="nav-link" id="captcha-tab" data-toggle="tab" href="#captcha" role="tab" aria-controls="captcha" aria-selected="false"><i class="lnib lni-lock-alt mr-1"></i> <?php echo trans('recaptcha-v2') ?> <?php echo trans('settings') ?></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="script-tab" data-toggle="tab" href="#script" role="tab" aria-controls="script" aria-selected="false"><i class="bi bi-code-slash mr-1"></i> <?php echo trans('header-script-codes') ?> </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="css-tab" data-toggle="tab" href="#css" role="tab" aria-controls="script" aria-selected="false"><i class="bi bi-braces-asterisk mr-1"></i> <?php echo trans('custom-css') ?> </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="social-tab" data-toggle="tab" href="#social" role="tab" aria-controls="social" aria-selected="false"><i class="bi bi-gear-wide-connected mr-1"></i> <?php echo trans('social-settings') ?></a>
                        </li>
                      </ul>
                    </div>
                  </div>

                  <!-- col-md-4 -->
                  <div class="col-md-9">
                    <form method="post" enctype="multipart/form-data" action="<?php echo base_url('admin/settings/update') ?>" role="form" class="form-horizontal pl-20">
                      <div class="tab-content custom card-body" id="myTabContent">
                        
                        <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                  
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                      <div class="col-sm-12">
                                        <div class="mih-100">
                                          <img class="m-auto" width="50px" src="<?php echo base_url($settings->favicon); ?>">
                                        </div>

                                        <div class="form-group">
                                          <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="photo1"  id="customFile">
                                            <label class="custom-file-label" for="customFile"><?php echo trans('upload-favicon') ?></label>
                                          </div>
                                        </div>

                                      </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                      <div class="col-sm-12">
                                        <div class="mih-100">
                                          <img class="m-auto" width="150px" src="<?php echo base_url($settings->logo); ?>">
                                        </div>

                                        <div class="form-group mb-0">
                                            <div class="custom-file">
                                              <input type="file" class="custom-file-input" name="photo2" id="customFile">
                                              <label class="custom-file-label" for="customFile"><?php echo trans('upload-logo') ?></label>
                                            </div>
                                        </div>
                                        <p class="text-muted mt-1 fs-12 small"><?php echo trans('logo-suggestions') ?></p>
                                      </div>
                                    </div>
                                </div>
                                <div class="col-sm-4 d-none">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                          <div class="mih-100">
                                          <img class="m-auto" width="100px" src="<?php echo base_url($settings->hero_img); ?>">
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-file">
                                              <input type="file" class="custom-file-input" name="photo3" id="customFile">
                                              <label class="custom-file-label" for="customFile"><?php echo trans('upload-hero-image') ?></label>
                                            </div>
                                        </div>
                                      </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('application-name') ?></label>
                              <input type="text" name="site_name" value="<?php echo html_escape($settings->site_name); ?>" class="form-control" >
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('application-title') ?></label>
                              <input type="text" name="site_title" value="<?php echo html_escape($settings->site_title); ?>" class="form-control">
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('aplication-title-mentor') ?></label>
                              <input type="text" name="site_title_mentor" value="<?php echo html_escape($settings->site_title_mentor); ?>" class="form-control">
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('admin-email') ?></label>
                              <input type="text" name="admin_email" class="form-control" value="<?php echo html_escape(user()->email); ?>">
                              <!-- <p class="small text-muted"><i class="fa fa-info-circle"></i> <?php //echo trans('settings-email-info') ?></p> -->
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('keywords') ?></label>
                                <input type="text" data-role="tagsinput" name="keywords" value="<?php echo html_escape($settings->keywords); ?>" class="form-control" >
                            </div>

                            <div class="form-group">
                                <label><?php echo trans('description') ?></label>
                                <textarea class="form-control" name="description" rows="4"><?php echo html_escape($settings->description); ?></textarea>
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('footer-about') ?></label>
                              <textarea class="form-control" name="footer_about"><?php echo html_escape($settings->footer_about); ?></textarea>
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('copyright') ?></label>
                              <input type="text" name="copyright" class="form-control" value="<?php echo html_escape($settings->copyright); ?>">
                            </div>

                            <div class="row">

                              <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo trans('currency') ?></label>
                                    <select class="form-control single_select" name="country">
                                        <option value=""><?php echo trans('select') ?></option>
                                        <?php foreach ($currencies as $currency): ?>
                                            <?php if (!empty($currency->currency_name)): ?>
                                              <option value="<?php echo html_escape($currency->id); ?>" 
                                                <?php echo (settings()->country == $currency->id) ? 'selected' : ''; ?>>
                                                <?php echo html_escape($currency->name.'  -  '.$currency->currency_code.' ('.$currency->currency_symbol.')'); ?>
                                              </option>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                              </div>

                              <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo trans('currency-position') ?></label>
                                    <select class="form-control" name="curr_locate">
                                        <option value=""><?php echo trans('select') ?></option>
                                        <option value="0" <?php if(settings()->curr_locate == 0){echo "selected";} ?>>$ 100 </option>
                                        <option value="1" <?php if(settings()->curr_locate == 1){echo "selected";} ?>>100 $</option>
                                    </select>
                                </div>
                              </div>
                              
                              <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo trans('number-format') ?></label>
                                    <select class="form-control" name="num_format">
                                        <option value=""><?php echo trans('select') ?></option>
                                        <option value="0" <?php if(settings()->num_format == 0){echo "selected";} ?>>100 </option>
                                        <option value="2" <?php if(settings()->num_format == 2){echo "selected";} ?>>100.00</option>
                                    </select>
                                </div>
                              </div>
                             
                              <div class="col-md-6">
                                <div class="form-group">
                                  <label><?php echo trans('set-trial-days') ?></label>
                                  <input type="number" name="trial_days" class="form-control" value="<?php echo html_escape($settings->trial_days); ?>">
                                  <p class="small text-muted"><i class="fa fa-info-circle"></i> <?php echo trans('set-0-to-disable-the-trial-option') ?></p>
                                </div>
                              </div>

                              <div class="col-md-6">
                                <div class="form-group">
                                    <label><?php echo trans('time-zone') ?></label>
                                    <select class="cus_lh select2 w-100" name="time_zone">
                                        <option value=""><?php echo trans('select') ?></option>
                                        <?php foreach ($time_zones as $time): ?>
                                            <option value="<?php echo html_escape($time->id); ?>" 
                                                <?php echo (settings()->time_zone == $time->id) ? 'selected' : ''; ?>>
                                                <?php echo html_escape($time->name); ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                              </div>

                              <div class="col-md-6">
                                <div class="form-group">
                                    <label>Booking Date Type</label>
                                    <select class="select form-control w-100" name="booking_date_type">
                                        <option value=""><?php echo trans('select') ?></option>
                                        <option value="slot" <?php if(settings()->booking_date_type == 'slot'){echo 'selected';} ?>>Slot</option>
                                        <option value="calendar" <?php if(settings()->booking_date_type == 'calendar'){echo 'selected';} ?>>Calendar</option>
                                    </select>
                                </div>
                              </div>

                              <div class="col-md-6">
                                <div class="form-group">
                                    <label>Booking Reminder Time 1</label>
                                    <select class="select form-control w-100" name="booking_reminder_time">
                                        <option value=""><?php echo trans('select') ?></option>
                                        <?php for ($i = 1; $i <= 24; $i++): ?>
                                            <option value="<?php echo $i; ?>" <?php if(settings()->booking_reminder_time == $i){echo 'selected';} ?>><?php echo $i ?> Hour</option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                              </div>



                              <div class="col-md-6">
                                <div class="form-group">
                                    <label>Booking Reminder Time 2</label>
                                    <select class="select form-control w-100" name="second_booking_reminder_time">
                                        <option value=""><?php echo trans('select') ?></option>
                                          <?php for ($i = 1; $i <= 24; $i++): ?>
                                              <option value="<?php echo $i; ?>" <?php if(settings()->second_booking_reminder_time == $i){echo 'selected';} ?>><?php echo $i ?> Hour</option>
                                          <?php endfor; ?>
                                        
                                    </select>
                                </div>
                              </div>


                            </div>

                        </div>


                        <!-- apprance tab -->
                        <div class="tab-pane fade" id="appearance" role="tabpanel" aria-labelledby="appearance-tab">
                            <div class="row">
                              <div class="col-6">
                                  <div class="form-group mb-4">
                                    <label><?php echo trans('custom-font') ?></label><br>
                                    <select class="cus_lh select2" name="font" style="width: 80%;">
                                        <option value="0"><?php echo trans('default') ?></option>
                                        <?php foreach ($fonts as $font): ?>
                                            <option value="<?php echo html_escape($font->id); ?>" 
                                                <?php echo ($settings->site_font == $font->id) ? 'selected' : ''; ?>>
                                                <?php echo html_escape($font->name); ?>
                                            </option>
                                        <?php endforeach ?>
                                    </select>
                                    <p class="mt-2"><a class="badge badge-secondary" href="<?php echo base_url('admin/font') ?>"><i class="bi bi-gear"></i> <?php echo trans('manage-fonts') ?></a></p>
                                  </div>
                              </div>

                              <div class="col-6">
                                <div class="d-hides form-group">
                                    <label><?php echo trans('frontend-color') ?></label>
                                    <div class="d-flex justify-content-start">
                                      <div>
                                        <input type="text" name="site_color" value="<?php echo html_escape($settings->site_color); ?>" class="form-control w-50 colorpicker d-block mb-3" autocomplete="off">
                                      </div>
                                      <div>
                                        <i class="fas fa-square fa-3x" style="color: #<?php echo html_escape($settings->site_color); ?>; margin-top: -1px; margin-left: -80px;"></i>
                                      </div>
                                    </div>
                                </div>
                              </div>
                            </div>

                            <p class="mb-2 mt-3 pt-3 font-weight-bold"></p>

                            <div class="row d-noneg mb-5">
                              <div class="col-12 mb-2">
                                <h6>Landing Page Layout</h6>
                              </div>

                              <div class="col-6">
                                <label class="add-pointer">
                                <div class="icheck-primary text-left radio mt-2 pb-3">
                                  <input type="radio" id="radioPrimaryfl" value="1" name="front_layout" <?php if($settings->front_layout == '1'){echo "checked";} ?>>
                                  <label for="radioPrimaryfl"> Layout 1
                                  </label>
                                </div>
                                <img width="90%" class="img-thumbnail d-noneg shadow" src="<?php echo base_url('assets/admin/images/l2.png') ?>">
                                </label>
                              </div>

                              <div class="col-6">
                                <label class="add-pointer">
                                <div class="icheck-primary text-left radio mt-2 pb-3">
                                  <input type="radio" id="radioPrimarydl" value="2" name="front_layout" <?php if($settings->front_layout == '2'){echo "checked";} ?>>
                                  <label for="radioPrimarydl"> Layout 2
                                  </label>
                                </div>
                                <img width="90%" class="img-thumbnail d-noneg shadow" src="<?php echo base_url('assets/admin/images/l1.png') ?>">
                                </label>
                              </div>
                            </div>


                            <div class="row d-noneg">
                              <div class="col-12 mb-2">
                                <h6><?php echo trans('site-color-mode') ?></h6>
                              </div>

                              <div class="col-6">
                                <label class="add-pointer">
                                <div class="icheck-primary text-left radio mt-2 pb-3">
                                  <input type="radio" id="radioPrimaryl" value="light" name="site_mode" <?php if($settings->site_mode == 'light'){echo "checked";} ?>>
                                  <label for="radioPrimaryl"> <?php echo trans('light') ?>
                                  </label>
                                </div>
                                <img width="90%" class="img-thumbnail d-noneg shadow" src="<?php echo base_url('assets/images/light_panel.png') ?>">
                                </label>
                              </div>

                              <div class="col-6">
                                <label class="add-pointer">
                                <div class="icheck-primary text-left radio mt-2 pb-3">
                                  <input type="radio" id="radioPrimaryd" value="dark" name="site_mode" <?php if($settings->site_mode == 'dark'){echo "checked";} ?>>
                                  <label for="radioPrimaryd"> <?php echo trans('dark') ?>
                                  </label>
                                </div>
                                <img width="90%" class="img-thumbnail d-noneg shadow" src="<?php echo base_url('assets/images/dark_panel.png') ?>">
                                </label>
                              </div>
                            </div>



                            <div class="row d-none">
                              <div class="col-12 mb-2">
                                <h6><?php echo trans('admin-leftsidebar-style') ?></h6>
                              </div>

                              <div class="col-6">
                                <label class="add-pointer">
                                <div class="icheck-primary text-left radio mt-2 pb-3">
                                  <input type="radio" id="radioPrimaryl" value="1" name="layout" <?php if($settings->layout == '1'){echo "checked";} ?>>
                                  <label for="radioPrimaryl"> <?php echo trans('light') ?>
                                  </label>
                                </div>
                                <img width="90%" class="img-thumbnail d-noneg shadow" src="<?php echo base_url('assets/admin/images/light.png') ?>">
                                </label>
                              </div>

                              <div class="col-6">
                                <label class="add-pointer">
                                <div class="icheck-primary text-left radio mt-2 pb-3">
                                  <input type="radio" id="radioPrimaryd" value="0" name="layout" checked <?php if($settings->layout == '0'){echo "checked";} ?>>
                                  <label for="radioPrimaryd"> <?php echo trans('dark') ?>
                                  </label>
                                </div>
                                <img width="90%" class="img-thumbnail d-noneg shadow" src="<?php echo base_url('assets/admin/images/dark.png') ?>">
                                </label>
                              </div>
                            </div>
                        </div>

                        <!-- prefrences tab -->
                        <div class="tab-pane fade" id="prefrences" role="tabpanel" aria-labelledby="prefrences-tab">
                            <div class="form-group">
                                <div class="col-sm-12 mt-15">

                                  <div class="custom-control custom-switch prefrence-item ml-10">
                                      <input type="checkbox" name="enable_multilingual" class="custom-control-input" value="1" id="switch-88" <?php if($settings->enable_multilingual == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-88"><?php echo trans('multilingual-system') ?></label>
                                      <p class="text-muted"><small><?php echo trans('enable-multilingual') ?>.</small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10">
                                      <input type="checkbox" name="enable_frontend" class="custom-control-input" value="1" id="switch-11" <?php if($settings->enable_frontend == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-11"><?php echo trans('enable-frontend') ?></label>
                                      <p class="text-muted"><small><?php echo trans('enable-to-show-frontend-site') ?>.</small></p>
                                  </div>
                                  
                                  <div class="custom-control custom-switch prefrence-item ml-10">
                                      <input type="checkbox" name="enable_registration" class="custom-control-input" value="1" id="switch-2" <?php if($settings->enable_registration == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-2"><?php echo trans('registration-system') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('registration-title') ?>.</small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="enable_captcha" class="custom-control-input" value="1" id="switch-1" <?php if($settings->enable_captcha == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-1">reCaptcha</label>
                                      <p class="text-muted mb-2"><small><?php echo trans('recaptcha-title') ?></small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25 d-none">
                                      <input type="checkbox" name="enable_lifetime" class="custom-control-input" value="1" id="switch-12" <?php if($settings->enable_lifetime == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-12"><?php echo trans('enable-lifetime') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('enable-lifetime-title') ?>.</small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25 d-none">
                                      <input type="checkbox" name="enable_coupon" class="custom-control-input" value="1" id="switch-13" <?php if($settings->enable_coupon == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-13"><?php echo trans('coupons') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('enable-coupon-title') ?>.</small></p>
                                  </div>

                                  <?php if (get_user_info() == TRUE): ?>
                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25 d-none">
                                      <input type="checkbox" name="enable_payment" class="custom-control-input" value="1" id="switch-9" <?php if($settings->enable_payment == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-9"><?php echo trans('payment') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('payment-title') ?>.</small></p>
                                  </div>
                                  <?php else: ?>
                                      <input type="hidden" name="enable_payment" value="0">
                                  <?php endif ?>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25 d-none">
                                      <input type="checkbox" name="enable_sms_verify" class="custom-control-input" value="1" id="switch-10" <?php if($settings->enable_sms == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-10"><?php echo trans('sms-verification') ?></label>
                                      <p class="text-muted mb-0"><small><?php echo trans('sms-title1') ?></small></p>

                                      <p class="text-danger mb-2"><small><?php echo trans('sms-title2') ?>.</small></p>
                                  </div>


                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="enable_email_verify" class="custom-control-input" value="1" id="switch-3" <?php if($settings->enable_email_verify == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-3"><?php echo trans('email-verification') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('email-verify-title') ?>.</small></p>
                                  </div>


                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="enable_kyc" class="custom-control-input" value="1" id="switch-kyc" <?php if($settings->enable_kyc == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-kyc"><?php echo trans('kyc-verification') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('enable-kyc-title') ?>.</small></p>
                                  </div>


                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="enable_mentor_auto_approve" class="custom-control-input" value="1" id="mentor_auto_approve" <?php if($settings->enable_mentor_auto_approve == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="mentor_auto_approve"><?php echo trans('mentor-auto-approve') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('enable-to-allow-mentor-auto-approve-for-new-registered-mentors') ?>.</small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25 d-none">
                                      <input type="checkbox" name="enable_users" class="custom-control-input" value="1" id="switch-4" <?php if($settings->enable_users == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-4"><?php echo trans('company-list') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('company-list-title') ?></small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="enable_blog" class="custom-control-input" value="1" id="switch-5" <?php if($settings->enable_blog == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-5"><?php echo trans('blogs') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('blogs-title') ?></small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="enable_faq" class="custom-control-input" value="1" id="switch-6" <?php if($settings->enable_faq == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-6"><?php echo trans('faqs') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('faq-title') ?></small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25 d-none">
                                      <input type="checkbox" name="enable_feature" class="custom-control-input" value="1" id="switch-8" <?php if($settings->enable_feature == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-8"><?php echo trans('features-intro') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('features-intro-title') ?></small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="enable_workflow" class="custom-control-input" value="1" id="switch-7" <?php if($settings->enable_workflow == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-7"><?php echo trans('workflow') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('workflow-title') ?></small></p>
                                  </div>

                                  <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                      <input type="checkbox" name="enable_animation" class="custom-control-input" value="1" id="switch-an" <?php if($settings->enable_animation == 1){echo "checked";} ?>>
                                      <label class="custom-control-label" for="switch-an"><?php echo trans('site-animation') ?></label>
                                      <p class="text-muted mb-2"><small><?php echo trans('site-animation-title') ?></small></p>
                                  </div>

                                </div>
                            </div>
                        </div>


                        <!-- zoom tab -->
                        <div class="tab-pane fade" id="zoom" role="tabpanel" aria-labelledby="zoom-tab">
                         
                        
                          <div class="alert bg-success-soft conn_info hide brd-2 ml-1" role="alert">
                       
                          </div>

             
                          <div class="alert bg-danger-soft conn_error hide brd-2 ml-1" role="alert">
                    
                          </div>
                         

                          <div class="form-group row">
                              <div class="col-6">
                                <a class="pull-left badge badge-danger-soft brd-20 mr-3" target="_blank" href="https://marketplace.zoom.us/"><i class="fa fa-plus-circle"></i> <?php echo trans('create-zoom-app') ?></a>
                              </div>

                              <div class="col-6 text-right">
                                <a class="pull-right badge badge-success-soft brd-20" target="_blank" href="http://mentorship.originlabsoft.com/docs/#idocs_zoom"><i class="fa fa-question-circle"></i> <?php echo trans('zoom-integration-doc') ?></a>
                              </div>
                          </div>

                          <div class="form-group">
                              <label class="col-sm-12 control-label"><?php echo trans('api-usage') ?></label>
                              <div class="col-sm-12">
                                <select name="zoom_api_user" class="form-control custom-select">
                                    <option class="fs-12" selected value="2" <?php echo ($settings->zoom_api_user == "2") ? "selected" : ""; ?>><?php echo trans('utilize-the-admin-zoom-api') ?></option>
                                    <option class="fs-12" value="1" <?php echo ($settings->zoom_api_user == "1") ? "selected" : ""; ?>><?php echo trans('allow-users-to-manage-their-zoom-api') ?></option>
                                </select>
                              </div>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('zoom-account-id') ?></label>
                            <div class="col-sm-12">
                              <input type="text" name="zoom_account_id" value="<?php echo html_escape($settings->zoom_account_id); ?>" class="form-control">
                            </div>
                          </div>

                          <div class="form-group m-t-20">
                            <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('zoom-client-id') ?></label>
                            <div class="col-sm-12">
                              <input type="text" name="zoom_client_id" value="<?php echo html_escape($settings->zoom_client_id); ?>" class="form-control">
                            </div>
                          </div>

                          <div class="form-group m-t-20">
                            <label class="col-sm-12 control-label" for="example-input-normal"><?php echo trans('zoom-client-secret') ?></label>
                            <div class="col-sm-12">
                              <input type="password" name="zoom_client_secret" value="<?php echo html_escape($settings->zoom_client_secret); ?>" class="form-control">
                            </div>
                          </div>


                          <div class="form-group mb-5">
                            <div class="col-sm-12">
                              <?php if (!empty(settings()->zoom_account_id) && !empty(settings()->zoom_client_id) && !empty(settings()->zoom_client_secret)): ?>    
                                <a class="btn btn-danger pull-right mb-5 check_zoom_api" href="#"><i class="bi bi-check-circle"></i> <?php echo trans('check-api-connection') ?></a><br><br>
                              <?php endif; ?>
                            </div>
                          </div>

                        </div>


                        <!-- calendar tab -->
                        <div class="tab-pane fade" id="calendar" role="tabpanel" aria-labelledby="calendar-tab">
                            <p>
                              <span class="badge badge-primary-soft fs-15 mr-4"><?php echo trans('google-calendar') ?></span>
                              <a target="_blank" class="pull-right" href="<?php echo base_url('assets/admin/files/google_calendar.pdf') ?>"><span class="btn btn-outline-danger btn-sm"><i class="fas fa-info-circle"></i> <?php echo trans('google-calendar-integration') ?></span></a>
                            </p>

                            <div class="form-group">
                              <label><?php echo trans('client-id') ?></label>
                                <input type="text" name="google_client_id" value="<?php echo html_escape($settings->google_client_id); ?>" class="form-control" >
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('client-secret') ?></label>
                                <input type="text" name="google_client_secret" value="<?php echo html_escape($settings->google_client_secret); ?>" class="form-control" >
                            </div>

                            <div class="form-group bg-light-primary">
                              <p class="mb-1 mt-4"><?php echo trans('google-callback-url') ?></p>
                              <code class="badge badge-secondary-soft fs-14"><?php echo base_url('gc/auth/oauth') ?></code>
                            </div>
                        </div>


                        <!-- sms tab -->
                        <div class="tab-pane fade" id="sms" role="tabpanel" aria-labelledby="sms-tab">

                            <div class="form-group mb-2">
                                <div class="custom-control custom-switch pt-10">
                                  <input type="checkbox" value="1" name="enable_sms" class="custom-control-input" id="switch-sms" <?php if($settings->enable_sms == 1){echo "checked";} ?>>
                                  <label class="custom-control-label" for="switch-sms"><?php echo trans('enable-booking-sms') ?></label>
                                  <p class="small text-muted"><?php echo trans('enable-to-send-booking-notification-message-to-your-customers-after-make-a-appointment') ?></p>
                                </div>
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('account-sid') ?></label>
                                <input type="text" name="twillo_account_sid" value="<?php echo html_escape($settings->twillo_account_sid); ?>" class="form-control" >
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('auth-token') ?></label>
                                <input type="text" name="twillo_auth_token" value="<?php echo html_escape($settings->twillo_auth_token); ?>" class="form-control" >
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('sender-number-tw') ?></label>
                                <input type="text" name="twillo_number" value="<?php echo html_escape($settings->twillo_number); ?>" class="form-control" >
                            </div>
                        </div>


                        <!-- social login tab -->
                        <div class="tab-pane fade" id="sociallog" role="tabpanel" aria-labelledby="sociallog-tab">
                            <div class="row">
                              <div class="col-md-6 pr-3">
                                <div class="form-group">
                                  <a target="_blank" href="<?php echo base_url('docs/#idocs_google') ?>"><span class="badge badge-danger"><i class="bi bi-file-text"></i> <?php echo trans('integration-docs') ?> <i class="bi bi-arrow-right"></i></span></a>

                                  <div class="custom-control custom-switch prefrence-item pt-6">
                                    <input type="checkbox" <?php if(get_system_settings('enable_google') == 1){echo "checked";} ?> value="1" name="enable_google" class="custom-control-input" id="switch-glog" aria-invalid="false">
                                    <label class="custom-control-label" for="switch-glog"><?php echo trans('google-login') ?></label>
                                    <p class="mb-2"></p>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label><?php echo trans('google') ?> <?php echo trans('client-id') ?></label>
                                    <input type="text" name="google_client_id_log" value="<?php echo html_escape(get_system_settings('google_client_id')); ?>" class="form-control">
                                </div>

                                <div class="form-group">
                                  <label><?php echo trans('google') ?> <?php echo trans('secret-key') ?></label>
                                    <input type="text" name="google_secret_key" value="<?php echo html_escape(get_system_settings('google_secret_key')); ?>" class="form-control" >
                                </div>

                                <div class="form-group">
                                  <label><?php echo trans('redirect-url') ?></label>
                                    <input type="text" name="google_redirect" value="<?php echo base_url('login') ?>" class="form-control" disabled>
                                </div>
                              </div>

                              <div class="col-md-6 pl-3 d-none">
                                <div class="form-group">
                                  <a target="_blank" href="<?php echo base_url('docs/#idocs_facebook') ?>"><span class="badge badge-info"><i class="bi bi-file-text"></i> <?php echo trans('integration-docs') ?> <i class="bi bi-arrow-right"></i></span></a>

                                  <div class="custom-control custom-switch prefrence-item pt-6">
                                    <input type="checkbox" <?php if(get_system_settings('enable_facebook') == 1){echo "checked";} ?> value="1" name="enable_facebook" class="custom-control-input" id="switch-flog" aria-invalid="false">
                                    <label class="custom-control-label" for="switch-flog"><?php echo trans('facebook').' '.trans('login') ?></label>
                                    <p class="mb-2"></p>
                                  </div>
                                </div>

                                <div class="form-group">
                                  <label><?php echo trans('facebook-app-id') ?></label>
                                    <input type="text" name="facebook_app_id" value="<?php echo html_escape(get_system_settings('facebook_app_id')); ?>" class="form-control" >
                                </div>

                                <div class="form-group">
                                  <label><?php echo trans('facebook-app-secret') ?></label>
                                    <input type="text" name="facebook_app_secret" value="<?php echo html_escape(get_system_settings('facebook_app_secret')); ?>" class="form-control">
                                </div>
                                <div class="form-group">
                                  <label><?php echo trans('graph-version') ?></label>
                                    <input type="text" name="facebook_graph_version" value="<?php echo html_escape(get_system_settings('facebook_graph_version')); ?>" class="form-control" >
                                </div>
                              </div>
                            </div>
                        </div>


                        <!-- mail tab -->
                        <div class="tab-pane fade" id="mail" role="tabpanel" aria-labelledby="mail-tab">
                            
                          <div class="col-sm-12 mt-15">

                              <div class="callout callout-default">
                                  <h4><?php echo trans('gmail-smtp') ?></h4>
                                  <p><?php echo trans('Gmail Host:') ?>&nbsp;&nbsp;<?php echo trans('smtp.gmail.com') ?> <br>
                                  <?php echo trans('gmail-port') ?>&nbsp;&nbsp;465</p>

                                  <p class="text-dark mb-2"><b><i class="fa fa-info-circle"></i> <?php echo trans('mail-info-title') ?></b></p>
                                  <p><i class="fas fa-times-circle text-danger"></i> <?php echo trans('two-factor-authentication-off') ?><br>
                                  <i class="fas fa-check-circle text-success"></i> <?php echo trans('less-secure-app-on') ?></p>
                              </div>

                              <div class="form-group">
                                  <label class="control-label"><?php echo trans('mail-type') ?></label>
                                  <select name="mail_protocol" class="form-control custom-select">
                                      <option value="smtp" <?php echo ($settings->mail_protocol == "smtp") ? "selected" : ""; ?>><?php echo trans('smtp') ?></option>
                                      <option value="mail" <?php echo ($settings->mail_protocol == "mail") ? "selected" : ""; ?>><?php echo trans('codeigniter-mail') ?></option>
                                  </select>
                              </div>

                              <div class="form-group">
                                  <label class="control-label"><?php echo trans('mail-title') ?></label>
                                  <input type="text" class="form-control" name="mail_title" value="<?php echo html_escape($settings->mail_title); ?>">
                              </div>
                              
                              <div class="form-group">
                                <label class="control-label"><?php echo trans('sender-mail') ?></label>
                                <input type="text" class="form-control" name="sender_mail" value="<?php echo html_escape($settings->sender_mail); ?>">
                              </div>

                              <div class="form-group">
                                  <label class="control-label"><?php echo trans('mail-host') ?></label>
                                  <input type="text" class="form-control" name="mail_host" value="<?php echo html_escape($settings->mail_host); ?>">
                              </div>

                              <div class="form-group">
                                  <label class="control-label"><?php echo trans('mail-port') ?></label>
                                  <input type="text" class="form-control" name="mail_port" value="<?php echo html_escape($settings->mail_port); ?>">
                              </div>

                              <div class="form-group">
                                  <label class="control-label"><?php echo trans('mail-username') ?></label>
                                  <input type="text" class="form-control" name="mail_username" value="<?php echo html_escape($settings->mail_username); ?>" autocomplete="off">
                              </div>

                              <div class="form-group">
                                  <label class="control-label"><?php echo trans('mail-password') ?></label>
                                  <input type="password" class="form-control" name="mail_password" value="<?php echo base64_decode($settings->mail_password); ?>" autocomplete="off">
                              </div>

                              <div class="form-group">
                                  <label class="control-label"><?php echo trans('mail-encryption') ?></label>
                                  <select name="mail_encryption" class="form-control custom-select">
                                      <option value="ssl" <?php echo ($settings->mail_encryption == "ssl") ? "selected" : ""; ?>>SSL</option>
                                      <option value="tls" <?php echo ($settings->mail_encryption == "tls") ? "selected" : ""; ?>>TLS</option>
                                  </select>
                                  <p class="small"><i class="fa fa-info-circle"></i> <?php echo trans('mail-port-help') ?> </p>
                              </div>

                              <?php if (!empty($settings->mail_username)): ?>
                                <div class="form-group">
                                  <a target="_blank" href="<?php echo base_url('auth/test_mail') ?>" class="btn btn-secondary mb-50 pull-right"><i class="fa fa-paper-plane"></i> <?php echo trans('send-test-mail') ?></a>
                                </div>
                              <?php endif ?>
                          </div>

                        </div>

                        <div class="tab-pane fade" id="whatsapp" role="tabpanel" aria-labelledby="whatsapp-tab">
                            <div class="form-group mb-2">
                                <div class="custom-control custom-switch pt-10">
                                  <input type="checkbox" value="1" name="enable_whatsapp_msg" class="custom-control-input" id="switch-whatsapp" <?php if($settings->enable_whatsapp_msg == 1){echo "checked";} ?>>
                                  <label class="custom-control-label" for="switch-whatsapp"><?php echo trans('enable-ultra-message') ?></label>
                                  <p class="small text-muted"><?php echo trans('enable-ultra-message-tiitle') ?></p>
                                </div>
                            </div>

                            
                            <div class="form-group">
                              <p><?php echo trans('whatsapp') ?> (<b><a class="text-success" target="_blank" href="https://ultramsg.com"><?php echo trans('ultramsg-api') ?></a></b>)</p>
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('instance-id') ?></label>
                                <input type="text" name="ultramsg_instance_id" value="<?php echo html_escape($settings->ultramsg_instance_id); ?>" class="form-control">
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('token') ?></label>
                                <input type="text" name="ultramsg_token" value="<?php echo html_escape($settings->ultramsg_token); ?>" class="form-control">
                            </div>
                        </div>

                        <div class="tab-pane fade" id="pwa" role="tabpanel" aria-labelledby="pwa-tab">
                              
                              <div class="form-group">
                                <div class="custom-control custom-switch prefrence-item ml-10 mt-25">
                                    <input type="checkbox" name="enable_pwa" class="custom-control-input" value="1" id="switch-pwa" <?php if($settings->enable_pwa == 1){echo "checked";} ?>>
                                    <label class="custom-control-label" for="switch-pwa"><?php echo trans('enable-pwa') ?></label>
                                    <p class="text-muted mb-2"><small><?php echo trans('pwa-enable-title') ?></small></p>
                                </div>
                              </div>

                              <div class="form-group">
                                  <div class="col-sm-4">
                                    <div class="mih-100">
                                      <?php $pwa_thumb = !empty(settings()->pwa_logo)? settings()->pwa_logo :"assets/pwa/logo-bk.png"; ?>
                                      <img class="m-auto" width="100px" src="<?php echo base_url($pwa_thumb); ?>">
                                    </div>

                                  <div class="form-group">
                                      <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="pwa_logo" id="customFile">
                                        <label class="custom-file-label" for="customFile"><?php echo trans('upload-logo') ?></label>
                                      </div>
                                      <p class="mt-1 small text-danger"><i class="bi bi-info-circle"></i> Image size 512x512</p>
                                  </div>
                                </div>
                              </div>
                         
                        </div>

                        <!-- script tab -->
                        <div class="tab-pane fade" id="script" role="tabpanel" aria-labelledby="script-tab">
                            <div class="form-group">
                              <label><?php echo trans('header-script-codes-title') ?></label>
                              <textarea class="form-control" name="google_analytics" rows="16"><?php echo base64_decode($settings->google_analytics) ?></textarea>
                            </div>
                        </div>

                        <!-- css tab -->
                        <div class="tab-pane fade" id="css" role="tabpanel" aria-labelledby="script-tab">
                            <div class="form-group">
                              <label><?php echo trans('add-your-own-css-code-here') ?></label>
                              <textarea class="form-control" name="custom_css" rows="16"><?php echo json_decode($settings->custom_css) ?></textarea>
                            </div>
                        </div>


                        <!-- social tab -->
                        <div class="tab-pane fade" id="social" role="tabpanel" aria-labelledby="social-tab">
                            <div class="form-group">
                              <label><?php echo trans('facebook') ?></label>
                                <input type="text" name="facebook" value="<?php echo html_escape($settings->facebook); ?>" class="form-control" >
                            </div>
                            <div class="form-group">
                              <label><?php echo trans('twitter') ?></label>
                                <input type="text" name="twitter" value="<?php echo html_escape($settings->twitter); ?>" class="form-control" >
                            </div>
                            <div class="form-group">
                              <label><?php echo trans('instagram') ?></label>
                                <input type="text" name="instagram" value="<?php echo html_escape($settings->instagram); ?>" class="form-control" >
                            </div>
                            <div class="form-group">
                              <label><?php echo trans('linkedin') ?></label>
                                <input type="text" name="linkedin" value="<?php echo html_escape($settings->linkedin); ?>" class="form-control" >
                            </div>
                        </div>

                        <!-- captcha tab -->
                        <div class="tab-pane fade" id="captcha" role="tabpanel" aria-labelledby="captcha-tab">
                            <div class="form-group mb-4">
                              <label><?php echo trans('recaptcha') ?></label>
                              <?php if (settings()->captcha_site_key != ''): ?>
                                  <div class="g-recaptcha pull-left m-10" data-sitekey="<?php echo html_escape(settings()->captcha_site_key); ?>"></div>
                              <?php endif ?>
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('captcha-site-key') ?></label>
                                <input type="text" name="captcha_site_key" value="<?php echo html_escape($settings->captcha_site_key); ?>" class="form-control" >
                            </div>

                            <div class="form-group">
                              <label><?php echo trans('captcha-secret-key') ?></label>
                                <input type="text" name="captcha_secret_key" value="<?php echo html_escape($settings->captcha_secret_key); ?>" class="form-control" >
                            </div>
                        </div>
                      </div>

                      <!-- csrf token -->
                      <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">

                      <button type="submit" class="btn btn-primary btn-lg btn-block mt-3 fs-14"> <?php echo trans('save-changes') ?></button>
                        
                    </form>
                  </div>
                </div>
                
              </div>
            </div>

          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
