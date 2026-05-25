<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="content-wrapper">
    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
          <div class="row">
              <div class="col-md-10">
                <div class="card mt-4">
                  <div class="card-header border-0">
                    <h3 class="card-title"><?php echo trans('app-info') ?></h3>
                  </div>
                  <div class="card-body p-0">
                    
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-3 pl-4 pr-4 pt-4 pb-0">
                      <p class="text-md font-weight-bold">
                        <i class="text-primary lnib lni-code"></i><?php echo trans('script-version') ?>
                      </p>
                      <p class="d-flex flex-column text-right">
                        <span class="badge badge-primary-soft text-md badge-pill"><?php echo html_escape(settings()->version) ?></span>
                      </p>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-bottom mb-3 pl-4 pr-4 pt-0 pb-0">
                      <p class="text-md font-weight-bold">
                        <i class="text-primary lnib lni-book"></i><?php echo trans('documentation') ?>
                      </p>
                      <p class="d-flex flex-column text-right">
                        <a target="_blank" href="<?php echo base_url('docs') ?>"><?php echo html_escape(settings()->site_name) ?> <?php echo trans('documentation') ?></a>
                      </p>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-bottom mb-0 pl-4 pr-4 pt-0 pb-0 b-0">
                      <p class="text-md font-weight-bold">
                        <i class="text-primary lnib lni-question-circle"></i> <?php echo trans('support') ?>
                      </p>
                      <p class="d-flex flex-column text-right">
                        <span class="text-primary"><?php echo trans('codericks.envatogmail.com') ?></span>
                        <span class="text-muted"><?php echo trans('please-mention-purchase-code-with-your-support-mail') ?></span>
                      </p>
                    </div>


                  </div>
                </div>
              </div>
          </div>
      </div>
    </div>
</div>