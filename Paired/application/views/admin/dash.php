<div class="content-wrapper">

      <?php 
        $current_version = phpversion();
        $allowed_versions = ['7.4', '8.2'];
        $current_major_minor_version = implode('.', array_slice(explode('.', $current_version), 0, 2));
      ?>

      <?php if (!in_array($current_major_minor_version, $allowed_versions)): ?>
        <div class="content pt-4 mb-2">
            <div class="container">
              <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="bg-danger-soft py-3 px-4 rounded" role="alert">
                      <div class="mb-0 d-flex justify-content-between align-items-center">
                        <div><h6 class="fs-18 mb-0"><i class="bi bi-exclamation-circle-fill mr-2"></i> This script requires PHP version 7.4 or 8.2. But your current PHP version is <?php echo $current_version; ?> which is not compatible with this script.</h6></div>
                        </div>
                    </div>
                </div>
              </div>
            </div>
        </div>
      <?php endif; ?>
 
    <div class="content pt-4 mb-4">
      <div class="container">
        <div class="row box-dash-areas">
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-light shadow-sm" data-aos="fade-up" data-aos-delay="100">
              <span class="info-box-icon bg-primary-soft"><i class="bi bi-people fs-20"></i></span>

              <div class="info-box-content">
                <span class="info-box-number"><?php echo get_count_mentors(); ?></span>
                <span class="info-box-text"><?php echo trans('mentors') ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-light shadow-sm" data-aos="fade-up" data-aos-delay="150">
              <span class="info-box-icon bg-success-soft"><i class="bi bi-journal-text fs-20"></i></span>

              <div class="info-box-content">
                <span class="info-box-number"><?php echo get_count_mentees(); ?></span>
                <span class="info-box-text"><?php echo trans('mentees') ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-light shadow-sm" data-aos="fade-up" data-aos-delay="200">
              <span class="info-box-icon bg-danger-soft"><i class="bi bi-view-list fs-20"></i></span>

              <div class="info-box-content">
                <span class="info-box-number"><?php echo get_count('sessions') ?></span>
                <span class="info-box-text"><?php echo trans('sessions') ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-12">
            <div class="info-box bg-light shadow-sm" data-aos="fade-up" data-aos-delay="250">
              <span class="info-box-icon bg-info-soft"><i class="bi bi-calendar2-check fs-20"></i></span>

              <div class="info-box-content">
                <span class="info-box-number"><?php echo get_count('session_booking') ?></span>
                <span class="info-box-text"><?php echo trans('bookings') ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
        </div>
      </div>
    </div>

    <!-- Main content -->
    <!---<div class="content">
      <div class="container">
        <div class="row">
          <div class="col-md-12">
            <div class="card" data-aos="fade-up">
              <div class="card-header">
                <h5 class="mb-0"><?php echo trans('last-12-months-income') ?></h5>
              </div>

              <div class="card-body">
                <div id="adminIncomeChart"></div>
              </div>
            </div>
          </div>
          -->

          <!-- /.col-md-6 -->
          <!-- <div class="col-md-12">

            <div class="card mb-2" data-aos="fade-up">
              <div class="card-header">
                <h5 class="mb-0"><?php echo trans('latest-mentors') ?></h5>
              </div>
    
              <div class="card-body table-responsive p-0">
                <table class="table table-hover table-valign-middle">
                  <thead>
                  <tr>
                    <th><?php echo trans('user') ?></th>
                    <th><?php echo trans('country') ?></th>
                    <th><?php echo trans('joining-date') ?></th>
                  </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($users as $user): ?>
                      <tr>
                        <td>
                          <img src="<?php echo base_url($user->thumb); ?>" alt="user" class="avatar-xs mr-2">
                          <?php echo html_escape($user->name) ?>
                        </td>
                        <td>
                          <?php $code = get_by_id($user->country, 'country')->code; ?>
                          <span class=""><img class="flag-img-booking ml-1" src="<?php echo base_url('assets/images/flags/'.strtolower($code).'.png') ?>">
                          <?php echo get_by_id($user->country, 'country')->name; ?></span>
                        </td>
                        <td>
                            <span class="small text-muted"><i class="fas fa-clock"></i> <?php echo get_time_ago($user->created_at) ?></span>
                        </td>
                      </tr>
                    <?php endforeach ?>
                  </tbody>
                </table>
              </div>
            </div>
            <?php if (count($users) > 5): ?>
              <div class="text-center mb-2">
                <a href="<?php echo base_url('admin/users') ?>" class="badge bg-secondary"><?php echo trans('see-all') ?></a>
              </div>
            <?php endif ?>
          
            <div class="card" data-aos="fade-up">
              <div class="card-header">
                <h5 class="mb-0"><?php echo trans('net-income') ?></h5>
              </div>
              
              <div class="card-body p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th><?php echo trans('fiscal-year') ?> <i class="fa fa-info-circle" data-toggle="tooltip" data-title="<?php echo trans('fiscal-year-title') ?>"></i></th>
                      <?php foreach ($net_income as $netincome): ?>
                        <th><?php echo show_year($netincome->created_at) ?></th>
                      <?php endforeach ?>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><?php echo trans('income') ?></td>
                      <?php foreach ($net_income as $netincome): ?>
                        <td><span class="badge badge-success-soft"><?php echo settings()->currency_symbol ?> <?php echo html_escape(number_format($netincome->total,2)) ?></span></td>
                      <?php endforeach ?>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            -->
          </div>
          <!-- /.col-md-6 -->
        </div>
        <!-- /.row -->
      </div><!-- /.container -->
    </div>
    <!-- /.content -->
  </div>