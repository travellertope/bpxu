<div class="login-bg login-main-container d-none d-lg-block bg-light"> </div>

<div class="d-flex align-items-center position-relative min-vh-100">
    
    <!-- Login form -->
    <div class="container">
        <div class="row justify-content-center justify-content-lg-start">
            
            <div class="col-md-5 p-5 mx-auto my-5" data-aos="fade-up" data-aos-duration="400">
                <div class="shadow rounded bg-white py-6 px-6 border-0" id="login-area">

                    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
                        <div class="alert alert-success alert-dismissible mb-4 log_alert">
                          <button type="button" class="close" data-dismiss="alert"><i class="bi bi-x"></i></button>
                          <?php echo trans('logout-successfully-') ?>
                        </div>
                    <?php endif ?>

                    <div class="mb-6 text-center">
                        <?php get_last_logins(); ?>
                        <h4 class="font-weight-bold mb-2"><a href="<?php echo base_url() ?>"><img class="w-60" src="<?php echo base_url(settings()->logo) ?>"></a></h4>
                        <p class="mb-0 mt-2"><?php echo trans('sign-in-to-your') ?> <?php echo trans('account') ?></p>
                    </div>

                    <div class="mb-4 mt-4">
                        <div class="success text-success"></div>
                        <div class="error text-danger"></div>
                        <div class="warning text-warning"></div>
                    </div>

                    <?php if (settings()->type == 'demo'): ?>
                    <div class="alert alert-default mb-4">
                        <div class="rows badge badge-pill bg-primary-soft">
                            <div class="col-6 mb-2">
                                admin
                            </div>
                            <div class="col-6">
                                1234
                            </div>
                        </div>
                        <div class="rows badge badge-pill bg-primary-soft">
                            <div class="col-6 mb-2">
                                mentor
                            </div>
                            <div class="col-6">
                                1234
                            </div>
                        </div>
                        <div class="rows badge badge-pill bg-primary-soft">
                            <div class="col-6 mb-2">
                                mentee
                            </div>
                            <div class="col-6">
                                1234
                            </div>
                        </div>
                    </div>
                    <?php endif ?>

                    <form id="login-form" method="post" action="<?php echo base_url('auth/log'); ?>">

                        <div class="row">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><?php echo trans('user-name') ?></label>
                                    <input type="text" class="form-control" name="user_name" placeholder="<?php echo trans('enter-email-or-username') ?>" autocomplete="off">
                                </div>
                            </div>

                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <label><?php echo trans('password') ?></label>
                                    <input type="password" class="form-control" name="password" placeholder="<?php echo trans('enter-password') ?>" autocomplete="off">
                                </div>

                                <div class="text-left text-sm-left">
                                    <a href="#" class="m-link-muted small forgot_pass text-dark"><?php echo trans('forgot-password') ?></a>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            
                            <div class="col-md-12 center">
                                <!-- csrf token -->
                                <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">
                                <button type="submit" class="btn btn-primary btn-block mt-4 mb-0 signin_btn"><?php echo trans('sign-in') ?> </button>
                            </div>

                            <div class="col-md-12 center">
                                <?php if (get_system_settings('enable_google') == 1): ?>
                                    <div class="py-3 w-50 m-auto or"><span>or</span></div>
                                <?php endif ?>
                            </div>

               
                            <div class="col-md-12 center">
                                <?php
                                if($google_url && get_system_settings('enable_google') == 1):
                                ?>
                                    <div class="form-group text-center mb-3">
                                        <a data-toggle="modal" href="#acModal" class="btn btn-light-danger btn-block w-100"><i class="fab fa-google"></i>&nbsp;&nbsp;<?php echo trans('continue-with-google') ?></a>
                                    </div>
                                <?php
                                endif;
                                ?>
                            </div>

                            <?php if (settings()->enable_frontend == 0): ?>
                                <div class="col-md-12 center">
                                    <a href="<?php echo base_url('mentors') ?>" class="btn btn-outline-secondary btn-block mb-0"><i class="bi bi-person-circle"></i> <?php echo trans('mentors') ?> </a>
                                </div>
                            <?php endif ?>

                        </div>



                        <div class="text-center text-small mt-4">
                            <span><?php echo trans('an-account-yet') ?> <a href="<?php echo base_url('register?register_type=mentor') ?>"><?php echo trans('register') ?></a></span>
                        </div>

                    </form>
                </div>



                <div id="forgot-area" class="shadow-sm rounded bg-white py-6 px-8 border-0 d-hide">
                    
                    <div class="mb-6 text-center">
                        <h2 class="font-weight-bold mb-0"><a href="<?php echo base_url() ?>"><img width="30%" src="<?php echo base_url(settings()->logo) ?>"></a></h2>
                        <p class="font-weight-normal mb-0"><?php echo trans('recover-password') ?></p>
                    </div>

                    <!-- Form -->
                    <form id="lost-form" method="post" action="<?php echo base_url('auth/forgot_password'); ?>">

                        <div class="row d-none">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <select class="form-control" name="role" id="exampleFormControlSelect1">
                                        <option value=""><?php echo trans('select-your-role') ?></option>
                                        <option value="users"><?php echo trans('admin') ?></option>
                                        <option value="mentor"><?php echo trans('mentor').'/'.trans('mentee') ?></option>
                                        <option value="customers"><?php echo trans('customer') ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 mb-2">
                                <div class="form-group">
                                    <input type="text" class="form-control" name="email" required placeholder="<?php echo trans('enter-your-email') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-6 text-left text-sm-left">
                                <a href="#" class="small back_login"><i class="fas fa-long-arrow-left"></i> <?php echo trans('back') ?></a>
                            </div>
                            <div class="col-md-12 center">
                                <!-- csrf token -->
                                <input type="hidden" name="<?php echo html_escape($this->security->get_csrf_token_name());?>" value="<?php echo html_escape($this->security->get_csrf_hash());?>">
                                <button type="submit" class="btn btn-primary btn-block mt-4 mb-0"><?php echo trans('submit') ?></button>
                            </div>
                        </div>

                    </form>
                    <!-- End Form -->

                </div>

            </div>
        </div>
    </div>
    <!-- End Login form -->
   

</div>



<!-- Modal -->
<div class="modal fade" id="acModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel"><?php echo trans('select') ?> <?php echo ucfirst(trans('account')) ?> <?php echo trans('type') ?></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true"><i class="bi bi-x"></i></span>
        </button>
      </div>

      <div class="modal-body py-8">
        <div class="account-type row">
            <label class="col-md-6">
                <input type="radio" class="account_type" name="account_type" value="mentor">
                <div class="radio-card">
                    <p class="mb-0 fs-18"><i class="bi bi-person-workspace"></i><br>  <?php echo trans('mentor') ?></p>
                </div>
            </label>

            <label class="col-md-6">
                <input type="radio" class="account_type" name="account_type" value="mentee">
                <div class="radio-card">
                    <p class="mb-0 fs-18"><i class="bi bi-person-badge"></i> <br> <?php echo trans('mentee') ?></p>
                </div>
            </label>
        </div>
      </div>

      <div class="modal-footer accountm_footer d-hide">
        <a href="<?= $google_url ?>" class="btn btn-danger btn-block w-100"><i class="fab fa-google"></i>&nbsp;&nbsp;<?php echo trans('continue-with-google') ?></a>
      </div>
    </div>
  </div>
</div>